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

    public function getByPostId($post_id): array
    {
        if (!$post_id) return [];

        $translations = get_post_meta($post_id, '_simplypoly_translations', true);
        return is_array($translations) ? $translations : [];
    }

    public function get($attrs = null): array
    {
        if (
            empty($_POST) ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce($_POST['nonce'], 'simplypoly_get_translation') ||
            !current_user_can('read')
        ) wp_send_json_error(['message' => 'Security check failed'], 403);

        $post_id = intval($_POST['post_id'] ?? 0);

        return $this->getByPostId($post_id);
    }

    public function post($attrs = null): bool
    {
        if (
            empty($_POST) ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce($_POST['nonce'], 'simplypoly_post_translation') ||
            !current_user_can('edit_pages')
        ) wp_send_json_error(['message' => 'Security check failed'], 403);

        $post_id = intval($_POST['post_id']);
        $translations_raw = $_POST['translations'] ?? '';

        if (!$post_id || empty($translations_raw)) wp_send_json_error(['message' => 'Invalid data'], 400);

        $translations = json_decode(stripslashes($translations_raw), true);

        if (!is_array($translations)) wp_send_json_error(['message' => 'Invalid JSON format'], 400);

        update_post_meta($post_id, '_simplypoly_translations', $translations);

        wp_send_json_success([
            'message' => __('Translations saved successfully!', Helper::PLUGIN_DOMAIN),
            'data' => $translations
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
