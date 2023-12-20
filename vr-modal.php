<?php
/*
Plugin Name: Vr-modal
Description: Modal-popup plugin for VR Wordpress sites.
Version: 1.0
*/

// Activate plugin
function activate_vr_popup() {
    // Activation actions, if any
}
register_activation_hook(__FILE__, 'activate_vr_popup');

// Deactivate plugin
function deactivate_vr_popup() {
    // Deactivation actions, if any
}
register_deactivation_hook(__FILE__, 'deactivate_vr_popup');

// Enqueue Vue.js files on WordPress pages
function enqueue_vue_scripts() {
    wp_enqueue_script('vr-modal', plugin_dir_url(__FILE__) . 'dist/index.js', array('tooltip-frontend-js-js'), '1.0', true);
    wp_enqueue_style('vr-modal', plugin_dir_url(__FILE__) . 'dist/assets/main.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_vue_scripts', 99);

// Create a shortcode to embed the Vue.js app
function vr_popup_shortcode() {
    ob_start();
    ?>
<div id="app"></div>
<?php
    return ob_get_clean();
}