<?php

namespace SimplyPoly\Views;

use SimplyPoly\Helper;

if (!defined('ABSPATH')) exit;

class AdminPageView extends AbstractView
{
    public function render($attrs): bool
    {
        if (!is_admin()) wp_die('security_check_failed', 'security_check_failed', array('response' => 403));
?>

        <div class="wrap">
            <h1><?php echo get_admin_page_title() ?></h1>
            <form method="POST" action="options.php">
                <?php
                settings_fields(Helper::LANGUAGES_GROUP);
                do_settings_sections(Helper::LANGUAGES_GROUP);

                $selected_values = get_option(Helper::LANGUAGES, []);
                if (!is_array($selected_values)) $selected_values = [$selected_values];

                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo '🌐 ' . esc_html__('Languages for translation', Helper::PLUGIN_DOMAIN); ?></th>
                        <td>
                            <select id="language-select" name="<?php echo esc_attr(Helper::LANGUAGES); ?>[]" multiple size="5" style="min-width:250px;">
                                <?php foreach (Helper::$ALL_LANGUAGES as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"
                                        data-flag="https://flagcdn.com/<?php echo esc_attr($key); ?>.svg"
                                        <?php selected(in_array($key, $selected_values)); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <p class="description">
                                <?php echo esc_html__('Select one or more languages for translation (Ctrl/Command + click)', Helper::PLUGIN_DOMAIN); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>

<?php

        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery']);
        wp_enqueue_script('simply-poly-admin', SIMPLY_POLY_URL . 'assets/js/admin.js', [], null, true);

        return true;
    }
}

?>