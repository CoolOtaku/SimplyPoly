<?php

namespace SimplyPoly\Controllers;

use SimplyPoly\Helper;
use SimplyPoly\Controllers\Contracts\UpdatableDeletableInterface;

if (!defined('ABSPATH')) exit;

class TranslationController extends AbstractController implements UpdatableDeletableInterface
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($attrs = null): bool
    {
        return true;
    }

    public function post($attrs = null): bool
    {
        if (
            empty($_POST) ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce($_POST['nonce'], 'simplypoly_save_translation') ||
            !current_user_can('edit_pages')
        ) {
            wp_send_json_error([
                'message' => __('Security check failed', Helper::PLUGIN_DOMAIN)
            ], 403);
        }

        $post_id = intval($_POST['post_id']);
        $lang = sanitize_text_field($_POST['lang']);
        $text = wp_kses_post($_POST['text']);

        if (!$post_id || !$lang) {
            wp_send_json_error([
                'message' => __('Invalid data', Helper::PLUGIN_DOMAIN)
            ], 400);
        }

        $translations = get_post_meta($post_id, '_simplypoly_translations', true);
        if (!is_array($translations)) $translations = [];
        $translations[$lang] = $text;

        update_post_meta($post_id, '_simplypoly_translations', $translations);

        wp_send_json_success([
            'message' => __('Translation saved', Helper::PLUGIN_DOMAIN),
            'data'    => $translations
        ]);

        return true;
    }

    public function update($attrs = null): bool
    {
        //return update_post_meta($id, '_simplypoly_translations', $data);
        return true;
    }

    public function delete($attrs = null): bool
    {
        //return delete_post_meta($id, '_simplypoly_translations');
        return true;
    }
}

?>