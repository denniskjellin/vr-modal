<?php
/**
 * Plugin Name: Vr-modal
 * Description: Modal-popup plugin for VR WordPress sites.
 * Version: 1.0
 * Text Domain: vr-modal
 *
 * @author Knowit Experience Norrland
 * @package vr-modal
 */

use knowit\helper\Util;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants
define('VR_MODAL_VERSION', '1.0.0');
define('VR_MODAL_DIR', 'vr-modal');

require_once plugin_dir_path(__FILE__) . 'includes/class-vr-modal.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-util.php';
 

// Run the plugin
function run_vr_modal() {
	static $plugin_instance = null;

	if (null === $plugin_instance) {
		$plugin_instance = new Vr_Modal();
		$plugin_instance->init();
	}
}
add_action('plugins_loaded', 'run_vr_modal');


// Enqueue Vue.js files on WordPress pages
function enqueue_vue_scripts() {
	wp_enqueue_script('vr-modal', plugin_dir_url(__FILE__) . 'dist/index.js', array(), '1.0', true);
	wp_enqueue_style('vr-modal', plugin_dir_url(__FILE__) . 'dist/assets/main.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_vue_scripts', 9999);


// Add shortcode
add_action('wp_footer', 'vr_modal_output');

function vr_modal_output() {
    // Check if the vr-modal is activated in settings
    $enable_vr_modal = get_option('vr-modal_settings_data')['enable_vr_modal'] ?? 0;

    // Return if the feature is not activated
    if (!$enable_vr_modal) {
        echo 'The feature is not activated';
        return;
    }

// Output the vr-modal if data is available
$vr_modal = new Vr_Modal();

// Check if the vr_modal_cookie is present
$vr_modal_cookie_present = isset($_COOKIE['vr_modal_cookie']);

// Return if the feature is not activated or if the cookie is present
if ($vr_modal_cookie_present) {
    echo 'the modal has already been shown.';
    return;
}

// Get modal data with error handling
$modal_data = $vr_modal->get_modal_data();
echo 'Modal Data vr-modal.php: ' . print_r($modal_data, true);

// Check if there is data available
if ($modal_data instanceof WP_REST_Response) {
    $response_data = $modal_data->get_data();

    // Log or echo the modal data
    // error_log('Modal Data: ' . print_r($modal_data, true));

    // Log or echo the response data
    // error_log('Response Data: ' . print_r($response_data, true));

    // Check if the response data is an empty array
    if (is_array($response_data) && empty($response_data)) {
        echo 'Data is an empty array, do not render the app';
    } else {
        echo 'Data is available, render the app here';

        // Set the vr_modal_cookie to prevent the modal from showing again
        // setcookie('vr_modal_cookie', 'vr_modal_shown', 0, '/');

        // Echo or log the response data
        // echo '<pre>';
        // print_r($response_data);
        // echo '</pre>';
        ?>
        <div id="vr-modal"></div>
        <script type="text/javascript" src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'dist/index.js'); ?>?ver=<?php echo esc_attr(VR_MODAL_VERSION); ?>" id="modal-js"></script>
        <?php
   
    }
} else {
    // Log an error if the modal data is not an instance of WP_REST_Response
    error_log('Invalid modal data format');
}

}



