<?php

/*
Plugin Name: Simply Poly
Description: A lightweight multilingual plugin for WordPress that manages translations inside a single page using a visual translation builder.
Text Domain: simply-poly
Domain Path: /languages
Version: 1.0
Author: CoolOtaku
License: GNU General Public License
*/

namespace SimplyPoly;

use SimplyPoly\Helper;
use SimplyPoly\Controllers\ClientController;
use SimplyPoly\Controllers\AdminPageController;
use SimplyPoly\Controllers\EditorPageController;

if (!defined('ABSPATH')) exit;

class SimplyPolyPlugin
{
    public function __construct()
    {
        if (!defined('ABSPATH')) exit;

        define('SIMPLY_POLY_URL', plugin_dir_url(__FILE__));
        define('SIMPLY_POLY_PATH', plugin_dir_path(__FILE__));

        foreach (
            [
                '*.php',

                'includes/{models,services,interfaces}/*.php',
                'includes/views/{,admin,editor,client}/*.php',
                'includes/controllers/{,admin,editor,client}/*.php',

            ] as $path
        ) foreach (glob(SIMPLY_POLY_PATH . $path, GLOB_BRACE) as $file) include_once $file;

        Helper::init();
        new ClientController();
        new AdminPageController();
        new EditorPageController();
    }

    public function activateRoutes(): void
    {
        flush_rewrite_rules();
    }

    public function deactivateRoutes(): void
    {
        flush_rewrite_rules();
    }
}

$simplyPolyPlugin = new SimplyPolyPlugin();

register_activation_hook(__FILE__, [$simplyPolyPlugin, 'activateRoutes']);
register_deactivation_hook(__FILE__, [$simplyPolyPlugin, 'deactivateRoutes']);
