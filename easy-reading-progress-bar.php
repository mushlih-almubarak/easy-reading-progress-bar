<?php

/**
 * Plugin Name:       Easy Reading Progress Bar
 * Description:       Displays a simple, sticky progress bar on single posts to indicate reading progress.
 * Version:           1.0.0
 * Author:            Mushlih Almubarak
 * Author URI:        https://github.com/mushlih-almubarak
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       easy-reading-progress-bar
 */

// If this file is accessed directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Add a "Settings" link to the plugin's action links on the plugins page.
 *
 * @param array $links An array of plugin action links.
 * @return array An array of plugin action links.
 */
function erpb_add_settings_link($links)
{
    $settings_url  = esc_url(admin_url('options-general.php?page=easy-reading-progress-bar'));
    $settings_link = '<a href="' . $settings_url . '">' . __('Settings', 'easy-reading-progress-bar') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'erpb_add_settings_link');

/**
 * Enqueue scripts and styles for the frontend.
 * These assets are loaded only on single post pages for maximum performance.
 */
function erpb_enqueue_frontend_assets()
{
    if (! is_single()) {
        return;
    }

    $plugin_version = '1.0.0';

    // Register a dedicated style handle with a version number to prevent caching issues.
    wp_register_style('erpb-style', false, [], $plugin_version);
    wp_enqueue_style('erpb-style');

    // Register a dedicated script handle with a version, no dependencies, and loaded in the footer.
    wp_register_script('erpb-script', '', [], $plugin_version, true);
    wp_enqueue_script('erpb-script');

    // Get saved options or set defaults.
    $bar_color    = get_option('erpb_color_setting', '#ffbf16');
    $bar_location = get_option('erpb_location_setting', 'top');
    $bar_height   = apply_filters('erpb_bar_height', '7');

    // Create the dynamic CSS and add it inline.
    $custom_css = "
        #erpb-progress-container {
            position: fixed;
            height: " . esc_attr($bar_height) . "px;
            width: 100%;
            top: " . ($bar_location === 'top' ? '0' : 'auto') . ";
            bottom: " . ($bar_location === 'bottom' ? '0' : 'auto') . ";
            left: 0;
            z-index: 99999;
        }
        #erpb-progress-bar {
            height: 100%;
            width: 0%;
            background-color: " . esc_attr($bar_color) . ";
            transition: width 0.1s linear;
        }
    ";
    wp_add_inline_style('erpb-style', $custom_css);

    // Create the dynamic, dependency-free JavaScript and add it inline.
    $custom_js = "
    document.addEventListener('DOMContentLoaded', function() {
        'use strict';
        const contentArea = document.querySelector('.wp-block-post-content') || document.querySelector('#primary') || document.querySelector('article.post') || document.querySelector('.post') || document.querySelector('main#main') || document.body;
        const progressBar = document.getElementById('erpb-progress-bar');
        if ( ! contentArea || ! progressBar ) {
            return;
        }

        function updateProgressBar() {
            const contentTop = contentArea.offsetTop;
            const contentHeight = contentArea.clientHeight;
            const windowHeight = window.innerHeight;
            const scrollY = window.scrollY || window.pageYOffset;
            const scrollableDistance = contentHeight - windowHeight;
            const scrolledPastTop = scrollY - contentTop;
            let progress = 0;
            if ( scrolledPastTop > 0 && scrollableDistance > 0 ) {
                progress = ( scrolledPastTop / scrollableDistance ) * 100;
            }
            progress = Math.max( 0, Math.min( progress, 100 ) );
            const baseWidth = 2;
            const finalWidth = baseWidth + ( progress * ( 100 - baseWidth ) / 100 );
            progressBar.style.width = finalWidth + '%';
        }

        window.addEventListener( 'scroll', updateProgressBar, { passive: true } );
        window.addEventListener( 'resize', updateProgressBar, { passive: true } );
        updateProgressBar();
    });";
    wp_add_inline_script('erpb-script', $custom_js);
}
add_action('wp_enqueue_scripts', 'erpb_enqueue_frontend_assets');

/**
 * Add the progress bar HTML element to the site's footer.
 */
function erpb_add_footer_html()
{
    if (is_single()) {
        echo '<div id="erpb-progress-container"><div id="erpb-progress-bar"></div></div>';
    }
}
add_action('wp_footer', 'erpb_add_footer_html');


// --- Admin Settings Page --- //

/**
 * Add the options page to the Settings menu in the admin dashboard.
 */
function erpb_add_admin_menu()
{
    add_options_page(
        __('Easy Reading Progress Bar', 'easy-reading-progress-bar'), // Page Title
        __('Reading Progress Bar', 'easy-reading-progress-bar'),  // Menu Title
        'manage_options',                                           // Capability
        'easy-reading-progress-bar',                                // Menu Slug
        'erpb_options_page_html'                                    // Callback function
    );
}
add_action('admin_menu', 'erpb_add_admin_menu');

/**
 * Register the settings, section, and fields using the Settings API.
 */
function erpb_settings_init()
{
    // Register the settings group.
    register_setting('erpb_settings_group', 'erpb_color_setting', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#ffbf16',
    ]);

    register_setting('erpb_settings_group', 'erpb_location_setting', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'top',
    ]);

    // Add the main settings section.
    add_settings_section(
        'erpb_main_section',        // ID
        null,                       // Title (not needed)
        null,                       // Callback (not needed)
        'easy-reading-progress-bar' // Page slug
    );

    // Add the color picker field.
    add_settings_field(
        'erpb_color_field',         // ID
        __('Bar Color', 'easy-reading-progress-bar'), // Title
        'erpb_color_field_cb',      // Callback
        'easy-reading-progress-bar', // Page slug
        'erpb_main_section'         // Section
    );

    // Add the position radio button field.
    add_settings_field(
        'erpb_location_field',      // ID
        __('Bar Position', 'easy-reading-progress-bar'), // Title
        'erpb_location_field_cb',   // Callback
        'easy-reading-progress-bar', // Page slug
        'erpb_main_section'         // Section
    );
}
add_action('admin_init', 'erpb_settings_init');

/**
 * Callback function to render the color picker field.
 */
function erpb_color_field_cb()
{
    $color = get_option('erpb_color_setting', '#ffbf16');
    echo '<input type="text" name="erpb_color_setting" value="' . esc_attr($color) . '" class="erpb-color-picker" />';
}

/**
 * Callback function to render the position radio buttons.
 */
function erpb_location_field_cb()
{
    $location = get_option('erpb_location_setting', 'top');
?>
    <label>
        <input type="radio" name="erpb_location_setting" value="top" <?php checked($location, 'top'); ?>>
        <?php esc_html_e('Top of the page', 'easy-reading-progress-bar'); ?>
    </label>
    <br>
    <label>
        <input type="radio" name="erpb_location_setting" value="bottom" <?php checked($location, 'bottom'); ?>>
        <?php esc_html_e('Bottom of the page', 'easy-reading-progress-bar'); ?>
    </label>
<?php
}

/**
 * Enqueue the WordPress color picker script on plugin's specific admin page.
 *
 * @param string $hook_suffix The hook suffix for the current admin page.
 */
function erpb_admin_enqueue_scripts($hook_suffix)
{
    // Only load on plugin's settings page.
    if ('settings_page_easy-reading-progress-bar' !== $hook_suffix) {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_add_inline_script('wp-color-picker', 'jQuery(function($){ $(".erpb-color-picker").wpColorPicker(); });');
}
add_action('admin_enqueue_scripts', 'erpb_admin_enqueue_scripts');

/**
 * Render the HTML for the options page.
 */
function erpb_options_page_html()
{
    if (! current_user_can('manage_options')) {
        return;
    }

    // Define allowed HTML tags for the credit text for security.
    $allowed_html = [
        'span' => ['style' => true],
        'a'    => ['href' => true, 'target' => true, 'rel' => true],
    ];
?>
    <div class="wrap" style="position: relative;">

        <div class="erpb-credit" style="position: absolute; top: 10px; right: 15px; font-size: 12px; color: #666;">
            <?php
            echo wp_kses(
                sprintf(
                    /* translators: 1: Heart icon (HTML span), 2: Author name (HTML link). */
                    __('Made with %1$s by %2$s from Indonesia', 'easy-reading-progress-bar'),
                    '<span style="color: #e25555;">&hearts;</span>',
                    '<a href="https://github.com/mushlih-almubarak" target="_blank" rel="noopener noreferrer">Mushlih Almubarak</a>'
                ),
                $allowed_html
            );
            ?>
        </div>

        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form action="options.php" method="post">
            <?php
            // Output security fields for the registered setting sections.
            settings_fields('erpb_settings_group');
            // Output the settings sections and their fields.
            do_settings_sections('easy-reading-progress-bar');
            // Output the submit button.
            submit_button(__('Save Settings', 'easy-reading-progress-bar'));
            ?>
        </form>

    </div>
<?php
}
