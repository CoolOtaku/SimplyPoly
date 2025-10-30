<?php

namespace SimplyPoly\Views;

if (!defined('ABSPATH')) exit;

class EditorPageView extends AbstractView
{
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
                remove_action('wp_head', 'print_emoji_detection_script', 7);
                remove_action('wp_print_styles', 'print_emoji_styles');
                remove_action('admin_print_scripts', 'print_emoji_detection_script');
                remove_action('admin_print_styles', 'print_emoji_styles');
                remove_action('wp_head', 'wp_admin_bar_header');
                remove_action('wp_footer', 'wp_admin_bar_render', 1000);
                show_admin_bar(false);

                wp_enqueue_style('simply-poly-editor-style', SIMPLY_POLY_URL . 'assets/css/editor-style.css', [], null);

                wp_head();
            ?>
        </head>
        <body class="simplypoly-editor">
            <div class="editor-toolbar">
                <h1>SimplyPoly Editor</h1>
                <div class="controls">
                    <button onclick="location.reload()">🔄 Оновити</button>
                    <button onclick="window.location='<?php echo admin_url('edit.php?post_type=page'); ?>'">↩ Вийти</button>
                </div>
            </div>

            <div class="editor-iframe-wrapper">
                <iframe id="editor-frame" class="editor-iframe" src="<?php echo esc_url($preview_url); ?>"></iframe>
            </div>

            <div class="zoom-controls">
                <button onclick="zoomOut()">−</button>
                <button onclick="zoomIn()">+</button>
            </div>
            
            <?php
                wp_enqueue_script('simply-poly-editor-script', SIMPLY_POLY_URL . 'assets/js/editor-scripts.js', [], null, true);

                wp_footer();
            ?>
        </body>
        </html>

        <?php
        exit;
    }
}

?>