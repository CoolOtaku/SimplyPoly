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
        add_filter('page_row_actions', [$this, 'addButton'], 10, 2);

        $this->translationController = new TranslationController();
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