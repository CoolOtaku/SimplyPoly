<?php

namespace SimplyPoly;

if (!defined('ABSPATH')) exit;

class Helper
{
    public const PLUGIN_NAME = 'Simply Poly';
    public const PLUGIN_DOMAIN = 'simply-poly';

    public const LANGUAGES = 'simplypoly_languages';
    public const LANGUAGES_GROUP = 'simplypoly_languages_group';
    public const DEFAULT_LANGUAGE = 'simplypoly_default_language';
    public const ORIGINAL_IS_DEFAULT = 'simplypoly_original_is_default';

    public const SHOW_FLAGS = 'simplypoly_show_flags';
    public const SHOW_CODES = 'simplypoly_show_codes';
    public const SHOW_NAMES = 'simplypoly_show_names';

    public static array $ALL_LANGUAGES = [];

    public static function init(): void
    {
        self::$ALL_LANGUAGES = json_decode(file_get_contents(SIMPLY_POLY_PATH . 'all-languages.json'), true);
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
        $langs = get_option(self::LANGUAGES, []);
        if (!is_array($langs)) $langs = [$langs];

        if (!empty($_GET['lang'])) {
            $lang = sanitize_text_field($_GET['lang']);
            if (in_array($lang, $langs, true)) return $lang;
        }

        if (!empty($_COOKIE['simplypoly_lang'])) {
            $cookie = sanitize_text_field($_COOKIE['simplypoly_lang']);
            if (in_array($cookie, $langs, true)) return $cookie;
        }

        return get_option(self::DEFAULT_LANGUAGE);
    }

    public static function isFrontendRequest(): bool
    {
        if (is_admin()) return false;
        if (defined('DOING_AJAX') && DOING_AJAX) return false;
        if (defined('REST_REQUEST') && REST_REQUEST) return false;
        if (defined('WP_CLI') && WP_CLI) return false;

        if (isset($_GET['elementor-preview'])) return false;
        if (isset($_GET['action']) && $_GET['action'] === 'elementor') return false;
        if (!empty($_SERVER['HTTP_SEC_FETCH_DEST']) && $_SERVER['HTTP_SEC_FETCH_DEST'] === 'iframe') return false;

        return true;
    }
}
