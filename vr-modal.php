<?php
/*
 * Plugin Name: Vr-modal
 * Description: Modal-popup plugin for VR WordPress sites.
 * Version: 1.0
 * Text Domain: vr-modal
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('VR_MODAL_VERSION', '1.0.0');
define('VR_MODAL_DIR', 'vr-modal');

require_once plugin_dir_path(__FILE__) . 'includes/class-vr-modal.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-type-modal.php';

// Run the plugin
function run_vr_modal() {
    $plugin = new Vr_Modal();
    $plugin->init();
}
add_action('plugins_loaded', 'run_vr_modal');

// Enqueue Vue.js files on WordPress pages
function enqueue_vue_scripts() {
    wp_enqueue_script('vr-modal', plugin_dir_url(__FILE__) . 'dist/index.js', array('tooltip-frontend-js-js'), '1.0', true);
    wp_enqueue_style('vr-modal', plugin_dir_url(__FILE__) . 'dist/assets/main.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_vue_scripts', 999);

// Add shortcode
add_action( 'wp_print_footer_scripts', 'modal' );
function modal() {
    ?>
<div id="app"></div>
<script type="text/javascript" src="http://genteknik.local/app/plugins/vr-modal/dist/index.js?ver=1.0" id="modal-js"></script>
<?php
    
}
