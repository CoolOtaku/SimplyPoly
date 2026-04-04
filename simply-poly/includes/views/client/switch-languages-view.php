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
            $output = '<div class="simply-poly-switcher" style="display:flex;flex-wrap:wrap;gap:10px;">';

            foreach ($langs as $lang) {
                $output .= '<a href="?lang=' . esc_attr($lang) . '" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">';

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
        <div class="simply-poly-switcher-dropdown" style="position:relative;display:inline-block;">
            <button type="button" class="simply-poly-switcher-toggle" style="display:flex;align-items:center;gap:6px;padding:8px 12px;cursor:pointer;">
                ' . $current_label . '
                <span>▼</span>
            </button>

            <div class="simply-poly-switcher-menu" style="display:none;position:absolute;top:100%;left:0;background:#fff;border:1px solid #ddd;min-width:180px;z-index:999;padding:5px 0;">
        ';

        foreach ($langs as $lang) {
            $output .= '<a href="?lang=' . esc_attr($lang) . '" style="display:flex;align-items:center;gap:6px;padding:8px 12px;text-decoration:none;">';

            if ($show_flags) $output .= '<img src="https://flagcdn.com/' . esc_attr($lang) . '.svg" width="16" alt="' . esc_attr($lang) . '" />';
            if ($show_codes) $output .= '<span>' . esc_html(strtoupper($lang)) . '</span>';
            if ($show_names) $output .= '<span>' . esc_html(Helper::$ALL_LANGUAGES[$lang] ?? $lang) . '</span>';

            $output .= '</a>';
        }

        $output .= '
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".simply-poly-switcher-dropdown").forEach(function(dropdown) {
                const button = dropdown.querySelector(".simply-poly-switcher-toggle");
                const menu = dropdown.querySelector(".simply-poly-switcher-menu");

                if (button.dataset.initialized) return;
                button.dataset.initialized = "true";

                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isVisible = menu.style.display === "block";
                    document.querySelectorAll(".simply-poly-switcher-menu").forEach(el => el.style.display = "none");
                    menu.style.display = isVisible ? "none" : "block";
                });
            });

            document.addEventListener("click", function() {
                document.querySelectorAll(".simply-poly-switcher-menu").forEach(el => el.style.display = "none");
            });
        });
        </script>
        ';

        return $output;
    }
}
