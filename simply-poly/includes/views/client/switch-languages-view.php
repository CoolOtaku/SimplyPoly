<?php

namespace SimplyPoly\Views;

use SimplyPoly\Helper;

if (!defined('ABSPATH')) exit;

class SwitchLanguagesView extends AbstractView
{
    public function __construct()
    {
        parent::__construct();

        add_shortcode('simply_poly_switcher', [$this, 'render']);

        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script(
                'simply-poly-switcher',
                SIMPLY_POLY_URL . 'assets/js/switcher.js',
                ['jquery'],
                null,
                true
            );

            wp_enqueue_style(
                'simply-poly-switcher',
                SIMPLY_POLY_URL . 'assets/css/switcher.css',
                [],
                null
            );
        });
    }

    public function render($attrs): string
    {
        $attrs = shortcode_atts(['type' => 'dropdown',], $attrs, 'simply_poly_switcher');

        $type = $attrs['type'];

        $langs = get_option(Helper::LANGUAGES, []);
        if (empty($langs) || !is_array($langs)) return '';

        $show_flags = get_option(Helper::SHOW_FLAGS, 1);
        $show_codes = get_option(Helper::SHOW_CODES, 1);
        $show_names = get_option(Helper::SHOW_NAMES, 1);

        if ($type === 'inline') {
            $output = '<div class="simply-poly-switcher">';

            foreach ($langs as $lang) {
                $output .= '<a href="?lang=' . esc_attr($lang) . '">';

                if ($show_flags) $output .= '<img src="https://flagcdn.com/' . esc_attr($lang) . '.svg" width="16" alt="' . esc_attr($lang) . '" />';
                if ($show_codes) $output .= '<span>' . esc_html(strtoupper($lang)) . '</span>';
                if ($show_names) $output .= '<span>' . esc_html(Helper::$ALL_LANGUAGES[$lang] ?? $lang) . '</span>';

                $output .= '</a>';
            }

            $output .= '</div>';

            return $output;
        }

        $current_lang = Helper::getCurrentLang();
        if (!$current_lang) $current_lang = get_option(Helper::DEFAULT_LANGUAGE, $langs[0] ?? '');

        $current_label = '';

        if ($show_flags) $current_label .= '<img src="https://flagcdn.com/' . esc_attr($current_lang) . '.svg" width="16" alt="' . esc_attr($current_lang) . '" />';
        if ($show_codes) $current_label .= '<span>' . esc_html(strtoupper($current_lang)) . '</span>';
        if ($show_names) $current_label .= '<span>' . esc_html(Helper::$ALL_LANGUAGES[$current_lang] ?? $current_lang) . '</span>';

        $output = '
        <div class="simply-poly-switcher-dropdown">
            <button type="button" class="simply-poly-switcher-toggle">
                ' . $current_label . '
                <span>▼</span>
            </button>

            <div class="simply-poly-switcher-menu">
        ';

        foreach ($langs as $lang) {
            $output .= '<a href="?lang=' . esc_attr($lang) . '">';

            if ($show_flags) $output .= '<img src="https://flagcdn.com/' . esc_attr($lang) . '.svg" width="16" alt="' . esc_attr($lang) . '" />';
            if ($show_codes) $output .= '<span>' . esc_html(strtoupper($lang)) . '</span>';
            if ($show_names) $output .= '<span>' . esc_html(Helper::$ALL_LANGUAGES[$lang] ?? $lang) . '</span>';

            $output .= '</a>';
        }

        $output .= '
            </div>
        </div>
        ';

        return $output;
    }
}
