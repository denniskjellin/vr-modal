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
	wp_enqueue_script('vr-modal', plugin_dir_url(__FILE__) . 'dist/index.js', array('tooltip-frontend-js-js'), '1.0', true);
	wp_enqueue_style('vr-modal', plugin_dir_url(__FILE__) . 'dist/assets/main.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_vue_scripts', 999);


// Add shortcode
add_action('wp_footer', 'modal');

function modal() {
    // Check if the feature is enabled in the settings
    $enable_feature = get_option('vr-modal_settings_data')['enable_feature'] ?? 0;

    // Output the modal if the feature is enabled and data is available
    if ($enable_feature) {
        $vr_modal = new VR_Modal();

        $modal_data = $vr_modal->get_modal_data();

        // Check if there is data available
        if (!empty($modal_data)) {
            ?>
            <div id="vr-modal"></div>
            <script type="text/javascript" src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'dist/index.js'); ?>?ver=<?php echo esc_attr(VR_MODAL_VERSION); ?>" id="modal-js"></script>
            <?php
        }
    }
}



