<?php

namespace SimplyPoly\Views;

use SimplyPoly\Helper;

if (!defined('ABSPATH')) exit;

class SwitchLanguagesView extends AbstractView
{
    #[NoReturn]
    public function render($attrs): string
    {
        $show_flags = get_option(Helper::SHOW_FLAGS, 1);
        $show_codes = get_option(Helper::SHOW_CODES, 1);
        $show_names = get_option(Helper::SHOW_NAMES, 1);

        foreach ($langs as $lang) {
            echo '<a href="?lang=' . esc_attr($lang) . '">';

            if ($show_flags) echo '<img src="https://flagcdn.com/' . esc_attr($lang) . '.svg" width="16" />';
            if ($show_codes) echo strtoupper($lang) . ' ';
            if ($show_names) echo esc_html(Helper::$ALL_LANGUAGES[$lang] ?? $lang);
            
            echo '</a>';
        }
      
        exit;
    }
}

?>