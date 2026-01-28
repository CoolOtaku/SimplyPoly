<?php

namespace SimplyPoly\Controllers;

use SimplyPoly\Helper;
use SimplyPoly\Views\EditorPageView;
use SimplyPoly\Controllers\TranslationController;
use SimplyPoly\Controllers\Contracts\UpdatableDeletableInterface;

if (!defined('ABSPATH')) exit;

class EditorPageController extends AbstractController implements UpdatableDeletableInterface
{
    private $translationController;

    public function __construct()
    {
        parent::__construct();

        add_action('admin_action_simplypoly', [new EditorPageView(), 'render']);
        add_filter('page_row_actions', [$this, 'addButtonForPage'], 10, 2);
        add_action('admin_bar_menu', [$this, 'addButtonForAdminBar'], 100);

        $this->translationController = new TranslationController();
        add_action('wp_ajax_simplypoly_get_translations', [$this, 'get']);
        add_action('wp_ajax_simplypoly_save_translation', [$this, 'post']);
    }

    public function get($attrs = null): bool
    {
        return $this->translationController->get($attrs);
    }

    public function post($attrs = null): bool
    {
        return $this->translationController->post($attrs);
    }

    public function update($attrs = null): bool
    {
        return $this->translationController->update($attrs);
    }

    public function delete($attrs = null): bool
    {
        return $this->translationController->delete($attrs);
    }

    public function addButtonForPage($actions, $post)
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

    public function addButtonForAdminBar($wp_admin_bar)
    {
        if (!is_user_logged_in()) return;
        if (!current_user_can('edit_pages')) return;
        if (!is_singular('page')) return;

        $post_id = get_the_ID();
        if (!$post_id) return;

        $url = admin_url('post.php?post=' . $post_id . '&action=simplypoly');

        $wp_admin_bar->add_node([
            'id'    => 'simplypoly_translate',
            'title' => '🌐 ' . __('Translate', Helper::PLUGIN_DOMAIN),
            'href'  => esc_url($url),
            'meta'  => [
                'class' => 'simplypoly-adminbar-button'
            ]
        ]);
    }
}

?>