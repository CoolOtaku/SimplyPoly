<?php

namespace SimplyPoly\Views;

use JetBrains\PhpStorm\NoReturn;

use SimplyPoly\Helper;

if (!defined('ABSPATH')) exit;

class EditorPageView extends AbstractView
{
    #[NoReturn]
    public function render($attrs): string
    {
        if (!current_user_can('edit_pages')) wp_die(__('Access denied!', Helper::PLUGIN_DOMAIN));

        $post = isset($_GET['post']) ? intval($_GET['post']) : 0;
        if (!$post) wp_die(__('No post ID provided!', Helper::PLUGIN_DOMAIN));

        $preview_url = get_permalink($post) . '?simplypoly_preview=1';
        $langs = get_option(Helper::LANGUAGES, []);
        if (!is_array($langs)) $langs = [$langs];
?>

        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <title><?php echo esc_html__('SimplyPoly Editor', Helper::PLUGIN_DOMAIN); ?></title>
            <?php
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            wp_enqueue_style('simply-poly-editor', SIMPLY_POLY_URL . 'assets/css/editor.css', [], null);
            wp_head();
            ?>
        </head>

        <body class="simplypoly-editor">
            <!-- TOOLBAR -->
            <div class="editor-toolbar">
                <img class="logo" src="<?php echo esc_url(SIMPLY_POLY_URL . 'assets/img/logo.png'); ?>" alt="Logo" />
                <div class="controls">
                    <select id="simplypoly-preview-lang" required="true">
                        <option value="">
                            <?php echo esc_html__('Preview: Off', Helper::PLUGIN_DOMAIN); ?>
                        </option>

                        <?php foreach ($langs as $lang): ?>
                            <option value="<?php echo esc_attr($lang); ?>">
                                <?php echo esc_html(strtoupper($lang)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button class="save" onclick="" disabled="true">
                        💾 <?php echo esc_html__('Save', Helper::PLUGIN_DOMAIN); ?>
                    </button>
                    <button onclick="location.reload()">
                        🗘 <?php echo esc_html__('Refresh', Helper::PLUGIN_DOMAIN); ?>
                    </button>
                    <button class="exit" onclick="window.location='<?php echo admin_url('edit.php?post_type=page'); ?>'">
                        ❌ <?php echo esc_html__('Exit', Helper::PLUGIN_DOMAIN); ?>
                    </button>
                </div>
            </div>

            <!-- EDITOR IFRAME -->
            <div class="editor-iframe-wrapper">
                <iframe id="editor-frame" sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-top-navigation-by-user-activation"
                    class="editor-iframe" src="<?php echo esc_url($preview_url); ?>" data-css="<?php echo esc_url(SIMPLY_POLY_URL . 'assets/css/editor-frame-view.css'); ?>">
                </iframe>
            </div>

            <!-- TRANSLATION PANEL -->
            <div id="simplypoly-panel" class="simplypoly-panel hidden">
                <h3><?php echo esc_html__('Translate', Helper::PLUGIN_DOMAIN); ?></h3>
                <div class="simplypoly-source-text"></div>
                <div class="simplypoly-languages"></div>
            </div>

            <!-- ZOOM BUTTONS -->
            <div class="zoom-controls">
                <button id="zoom-out">−</button>
                <button id="zoom-in">+</button>
            </div>

            <?php
            wp_enqueue_script(
                'simply-poly-editor',
                SIMPLY_POLY_URL . 'assets/js/editor.js',
                [],
                null,
                true
            );

            wp_localize_script('simply-poly-editor', 'params', [
                'langs' => $langs
            ]);

            wp_add_inline_script(
                'simply-poly-editor',
                'window.simplyPolyPluginUrl = "' . SIMPLY_POLY_URL . '";',
                'before'
            );

            add_filter('script_loader_tag', function ($tag, $handle, $src) {
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