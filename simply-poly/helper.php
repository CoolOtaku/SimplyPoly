<?php

namespace SimplyPoly;

if (!defined('ABSPATH')) exit;

class Helper
{
    public const PLUGIN_NAME = 'SimplyPoly';
    public const PLUGIN_DOMAIN = 'simply-poly';
    
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

    public static function cssToXpath($css)
    {
        $parts = explode(' > ', trim($css));
        $xpath = '';

        foreach ($parts as $part) {
            if (!preg_match('/^([a-z0-9]+):nth-of-type\((\d+)\)$/i', $part, $matches)) return null;

            $tag = $matches[1];
            $index = $matches[2];

            $xpath .= '/' . $tag . '[' . $index . ']';
        }

        return $xpath;
    }

    public static function getCurrentLang(): ?string
    {
        $lang = get_query_var('lang');

        if ($lang) return sanitize_text_field($lang);
        if (!empty($_COOKIE['simplypoly_lang'])) return sanitize_text_field($_COOKIE['simplypoly_lang']);

        return null;
    }
}

?>