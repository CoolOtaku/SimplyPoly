<?php

namespace SimplyPoly\Assets;

use SimplyPoly\Helper;

if (!defined('ABSPATH')) exit;

class AssetsLoader
{
    public function __construct()
    {
        if (!defined('ABSPATH')) exit;

        if (is_admin()) add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminAssets']);
        else add_action('wp_enqueue_scripts', [self::class, 'enqueuePublicAssets']);
    }

    public static function enqueueAdminAssets(): void
    {

    }

    public static function enqueuePublicAssets(): void
    {
        
    }
}

?>