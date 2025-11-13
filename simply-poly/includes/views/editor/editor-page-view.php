<?php

namespace SimplyPoly\Views;

use JetBrains\PhpStorm\NoReturn;

if (!defined('ABSPATH')) exit;

class EditorPageView extends AbstractView
{
    #[NoReturn]
    public function render($attrs): string
    {
        if (!current_user_can('edit_pages')) wp_die(__('Доступ заборонено!', 'simply-poly'));

        $post = isset($_GET['post']) ? intval($_GET['post']) : 0;
        if (!$post) wp_die(__('Немає ідентифікатора публікації!', 'simply-poly'));

        $preview_url = get_permalink($post) . '?simplypoly_preview=1';
        ?>

        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <title>SimplyPoly Editor</title>

            <?php
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            wp_enqueue_style('simply-poly-editor', SIMPLY_POLY_URL . 'assets/css/editor.css', [], null);
            wp_head();
            ?>
        </head>
        <body class="simplypoly-editor">
        <div class="editor-toolbar">
            <img class="logo" src="<?php echo esc_url(SIMPLY_POLY_URL . 'assets/img/logo.png'); ?>" alt="Logo"/>
            <div class="controls">
                <button onclick="location.reload()">🔄 Оновити</button>
                <button onclick="window.location='<?php echo admin_url('edit.php?post_type=page'); ?>'">↩ Вийти</button>
            </div>
        </div>

        <div class="editor-iframe-wrapper">
            <iframe id="editor-frame" sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-top-navigation-by-user-activation"
                    class="editor-iframe" src="<?php echo esc_url($preview_url); ?>" data-css="<?php echo esc_url(SIMPLY_POLY_URL . 'assets/css/editor-frame-view.css'); ?>">
            </iframe>
        </div>

        <div class="zoom-controls">
            <button id="zoom-in">−</button>
            <button id="zoom-out">+</button>
        </div>

        <?php
        wp_enqueue_script(
                'simply-poly-editor',
                SIMPLY_POLY_URL . 'assets/js/editor.js',
                [],
                null,
                true
        );

        wp_add_inline_script(
                'simply-poly-editor',
                'window.simplyPolyPluginUrl = "' . SIMPLY_POLY_URL . '";',
                'before'
        );

        add_filter('script_loader_tag', function($tag, $handle, $src) {
            if ($handle === 'simply-poly-editor') {
                return '<script type="module" src="' . esc_url($src) . '"></script>';
            }
            return $tag;
        }, 10, 3);

        wp_footer();
        ?>
        </body>
        </html>

        <?php
        exit;
    }
}

?>