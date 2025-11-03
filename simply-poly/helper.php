<?php

namespace SimplyPoly;

if (!defined('ABSPATH')) exit;

class Helper
{
    public const PLUGIN_NAME = 'SimplyPoly';
    
    public const LANGUAGES_GROUP = 'simplypoly_languages_group';
    public const LANGUAGES = 'simplypoly_languages';

    public static array $ALL_LANGUAGES = [];

    public static function init(): void {
        self::$ALL_LANGUAGES = json_decode(file_get_contents(SIMPLY_POLY_PATH . 'all-languages.json'), true);
    }

    public static function getUserIp(): string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ip_list[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];

        return 'UNKNOWN';
    }
}

?>