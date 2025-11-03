<?php

namespace SimplyPoly\Controllers;

use SimplyPoly\Helper;
use SimplyPoly\Views\AdminPageView;

if (!defined('ABSPATH')) exit;

class AdminPageController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();

        add_action('admin_init', [$this, 'get']);
        add_action('admin_menu', [$this, 'post']);
    }

    public function get($attrs = null): bool
    {
        register_setting(Helper::LANGUAGES_GROUP, Helper::LANGUAGES);

        return true;
    }

    public function post($attrs = null): bool
    {
        add_menu_page(
            __('SimplyPoly Settings', 'simply-poly'),
            esc_html(Helper::PLUGIN_NAME),
            'manage_options',
            'simply-poly-main',
            [new AdminPageView(), 'render'],
            'dashicons-admin-site-alt3',
            25
        );
        
        return true;
    }
}

?>