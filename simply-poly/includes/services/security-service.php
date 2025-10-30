<?php

namespace SimplyPoly\Services;

use Exception;

use SimplyPoly\Helper;

if (!defined('ABSPATH')) exit;

class SecurityService
{
    private ?string $SECRET_KEY;
    private const METHOD = 'aes-256-cbc';
    private const IV_LENGTH = 16;

    public function __construct()
    {
        $this->SECRET_KEY = getenv('SECRET_KEY');
    }

    public function generateToken($action = 'my_action'): bool|string
    {
        try {
            $user_id = 0;
            if (is_user_logged_in()) $user_id = get_current_user_id();

            $nonce = wp_create_nonce($action);
            $timestamp = time();

            $data = json_encode(array(
                'uid' => $user_id,
                'nonce' => $nonce,
                'time' => $timestamp,
                'secret_key' => $this->SECRET_KEY
            ));

            $secret_key = wp_salt();
            $signature = hash_hmac('sha256', $data, $secret_key);

            return base64_encode(json_encode(array(
                'data' => $data,
                'sig' => $signature,
            )));
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function verifyToken($token, $action = 'my_action'): bool
    {
        try {
            $decoded = base64_decode($token);
            $arr = json_decode($decoded, true);

            if (!isset($arr['data'], $arr['sig'])) return false;

            $data = $arr['data'];
            $sig = $arr['sig'];

            $secret_key = wp_salt();
            $expected_sig = hash_hmac('sha256', $data, $secret_key);
            $data_array = json_decode($data, true);

            if (!wp_verify_nonce($data_array['nonce'], $action)) return false;
            if (time() - $data_array['time'] > SECURITY_TOKEN_LIFETIME) return false;
            if (!hash_equals($expected_sig, $sig)) return false;
            if (is_user_logged_in() && get_current_user_id() != $data_array['uid']) return false;
            if ($this->SECRET_KEY != $data_array['secret_key']) return false;
            if (!Helper::isIpAllowed(Helper::getUserIp())) return false;

            return true;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function encryptId(int $id): string
    {
        $iv = openssl_random_pseudo_bytes(self::IV_LENGTH);
        $encrypted = openssl_encrypt((string)$id, self::METHOD, $this->SECRET_KEY, 0, $iv);

        if ($encrypted === false) return '';

        $encoded = base64_encode($iv . $encrypted);
        return rtrim(strtr($encoded, '+/', '-_'), '=');
    }

    public function decryptId(string $data): ?int
    {
        $base64 = strtr($data, '-_', '+/');

        $mod4 = strlen($base64) % 4;
        if ($mod4) $base64 .= str_repeat('=', 4 - $mod4);

        $decoded = base64_decode($base64, true);
        if ($decoded === false || strlen($decoded) < self::IV_LENGTH) return null;

        $iv = substr($decoded, 0, self::IV_LENGTH);
        $encrypted = substr($decoded, self::IV_LENGTH);
        $decrypted = openssl_decrypt($encrypted, self::METHOD, $this->SECRET_KEY, 0, $iv);

        return (is_numeric($decrypted) && (int)$decrypted > 0) ? (int)$decrypted : null;
    }
}

?>