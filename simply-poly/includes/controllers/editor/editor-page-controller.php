<?php

namespace SimplyPoly\Controllers;

use SimplyPoly\Helper;
use SimplyPoly\Views\EditorPageView;

if (!defined('ABSPATH')) exit;

class EditorPageController extends AbstractController
{

    public function __construct()
    {
        parent::__construct();
        
        add_action('admin_action_simplypoly', [new EditorPageView(), 'render']);
        add_filter('page_row_actions', [$this, 'addButton'], 10, 2);
    }

    public function get($attrs = null): bool
    {
        return true;
    }

    public function post($attrs = null): bool
    {
        return true;
    }

    public function addButton($actions, $post)
    {
        if ($post->post_type === 'page' && $post->post_status !== 'trash') {
            $url = admin_url('post.php?post=' . $post->ID . '&action=simplypoly');
            
            $actions['simplypoly_translate'] = sprintf(
                '<a href="%s"><b>%s</b></a>',
                esc_url($url),
                '🌐 ' . __('Translate', Helper::PLUGIN_DOMAIN)
            );
        }

        return $actions;
    }
}

?>