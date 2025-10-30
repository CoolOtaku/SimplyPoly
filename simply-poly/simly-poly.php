<?php

/*
Plugin Name: SimplyPoly
Description: A lightweight multilingual plugin for WordPress that manages translations inside a single page using a visual translation builder.
Version: 0.1
Author: CoolOtaku
License: GNU General Public License
*/

namespace SimplyPoly;

use Throwable;

use SimplyPoly\Controllers\EditorPageController;

if (!defined('ABSPATH')) exit;

class SimplyPolyPlugin
{
    public function __construct()
    {
        if (!defined('ABSPATH')) exit;

        define('SECURITY_TOKEN_LIFETIME', 3600);
        define('SIMPLY_POLY_URL', plugin_dir_url(__FILE__));
        define('SIMPLY_POLY_PATH', plugin_dir_path(__FILE__));
    }

    public function init(): void
    {
        add_action('plugins_loaded', function() {
            load_plugin_textdomain('simply-poly', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        });

        $this->importFile('*.php');
        $this->importFile('assets/*.php');

        $this->importFile('includes/models/*.php');
        $this->importFile('includes/services/*.php');
        $this->importFile('includes/interfaces/*.php');

        $this->importFile('includes/views/*.php');
        $this->importFile('includes/views/admin/*.php');
        $this->importFile('includes/views/editor/*.php');

        $this->importFile('includes/controllers/*.php');
        $this->importFile('includes/controllers/admin/*.php');
        $this->importFile('includes/controllers/editor/*.php');

        Dotenv::loadEnvFile(SIMPLY_POLY_PATH . '.env');

        new EditorPageController();
    }

    private function importFile(string $path): void
    {
        foreach (glob(SIMPLY_POLY_PATH . $path) as $file) {
            try {
                if (!class_exists(basename($file, '.php'))) include_once $file;
            } catch (Throwable $e) {
                error_log("Error loading file: {$file} - " . $e->getMessage());
            }
        }
    }
}

$simplyPolyPlugin = new SimplyPolyPlugin();
$simplyPolyPlugin->init();

?>