<?php
/*
* Plugin Name: Vr-modal
* Description: Modal-popup plugin for VR Wordpress sites.
* Version: 1.0
* Text Domain: vr-modal
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'VR_MODAL_VERSION', '1.0.0' );
define( 'VR_MODAL_DIR', 'vr-modal' );

require plugin_dir_path( __FILE__ ) . 'includes/class-vr-modal.php';
function run_vr_modal() {
    $plugin = new Vr_Modal();
    $plugin->init();
}
run_vr_modal();


// // Activate plugin
// function activate_vr_popup() {
//     // Activation actions, if any
// }
// register_activation_hook(__FILE__, 'activate_vr_popup');

// // Deactivate plugin
// function deactivate_vr_popup() {
//     // Deactivation actions, if any
// }
// register_deactivation_hook(__FILE__, 'deactivate_vr_popup');

// Enqueue Vue.js files on WordPress pages
function enqueue_vue_scripts() {
    wp_enqueue_script('vr-modal', plugin_dir_url(__FILE__) . 'dist/index.js', array('tooltip-frontend-js-js'), '1.0', true);
    wp_enqueue_style('vr-modal', plugin_dir_url(__FILE__) . 'dist/assets/main.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_vue_scripts', 99);

add_action( 'wp_print_footer_scripts', 'test' );
function test() {
    ?>
<div id="app"></div>
<script type="text/javascript" src="http://genteknik.local/app/plugins/vr-modal/dist/index.js?ver=1.0" id="modal-js"></script>
<?php
    
}