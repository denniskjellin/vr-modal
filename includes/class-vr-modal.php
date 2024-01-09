<?php
/**
 * VR modal classes
 *
 * @author Knowit Experience Norrland
 * @package vr-modal
 */
class Vr_Modal {
	// Define plugin constants
	const ID = 'vr-modal';

	// Define custom post types
	const TEXT_DOMAIN = 'vr-modal';

	// Define plugin variables
	public function __construct() {
		// Add hooks for registering custom post type
		add_action('init', array($this, 'register_custom_post_type'));
	}

	// Define custom post types
	public function register_custom_post_type() {
		$menu_slug = $this->get_id();

		$labels = array(
			'name'               => __('VR Modaler', self::TEXT_DOMAIN),
			'singular_name'      => __('VR Modal', self::TEXT_DOMAIN),
			'menu_name'          => __('Modaler', self::TEXT_DOMAIN),
			'add_new'            => __('Lägg till', self::TEXT_DOMAIN),
			'add_new_item'       => __('Lägg till VR Modal', self::TEXT_DOMAIN),
			'edit_item'          => __('Redigera VR Modal', self::TEXT_DOMAIN),
			'new_item'           => __('Ny VR Modal', self::TEXT_DOMAIN),
			'view_item'          => __('Granska VR Modal', self::TEXT_DOMAIN),
			'search_items'       => __('Sök VR Modal', self::TEXT_DOMAIN),
			'not_found'          => __('Inga VR Modaler funna', self::TEXT_DOMAIN),
			'not_found_in_trash' => __('Inga VR Modaler funna i papperskorgen', self::TEXT_DOMAIN),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => $menu_slug,
			'show_in_rest'        => true,
			'query_var'           => true,
			'rewrite'             => array('slug' => 'vr-modal'),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array('title', 'editor'),
			'menu_icon'           => 'dashicons-nametag',
			'menu_position'       => 105,
		);

		register_post_type('custom_post_type', $args);
	}


	// Define plugin variables
	public static function init()
	{
		// Add menu page
		add_action('admin_menu', array(new self, 'add_menu_page'), 1);
		add_action('admin_init', array(new self, 'register_settings'));

		// Register REST API endpoint
		add_action('rest_api_init', array(new self, 'register_rest_endpoint'));
	}

	// Define plugin functions
	public function get_id()
	{
		return self::ID;
	}

	public function add_menu_page()
	{
		// Check if the menu page already exists
		$menu_page_exists = false;
		global $menu;
		foreach ($menu as $item) {
			if ($item[2] === $this->get_id()) {
				$menu_page_exists = true;
				break;
			}
		}

		// If the menu page doesn't exist, add it
		if (!$menu_page_exists) {
			add_menu_page(
				esc_html__('VR Modal', 'vr-modal-admin'),
				esc_html__('VR Modal', 'vr-modal-admin'),
				'manage_options',
				$this->get_id(),
				array($this, 'load_view'),
				'dashicons-admin-page'
			);

			// Add submenu page
			add_submenu_page(
				$this->get_id(),
				esc_html__('Settings', 'vr-modal-admin'),
				esc_html__('Settings', 'vr-modal-admin'),
				'manage_options',
				$this->get_id() . '_settings',
				array($this, 'load_settings_page')
			);
		}
	}

	// Register settings
	public function register_settings()
	{
		register_setting($this->get_id() . '_settings_group', $this->get_id() . '_settings_data');
	}

	// Register REST API endpoint
	public function register_rest_endpoint() {
		register_rest_route('vr-modal/v1', '/modal-data', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_modal_data'),
			'permission_callback' => '__return_true',
		));
	}

	// Get modal data
	public function get_modal_data() {

		$args = array(
			'post_type' => 'custom_post_type',
			'posts_per_page' => -1,
		);

		$posts = get_posts($args);

		$_posts = array();
		foreach ($posts as $key => $post) {
			$_posts[$key]['title'] = $post->post_title;
			$_posts[$key]['description'] = $post->post_content;
		}

		//print_r( $posts);

		// $popup_data = array(
		// 	'title'       => get_option($this->get_id() . '_settings_data')['popup_title'],
		// 	'description' => get_option($this->get_id() . '_settings_data')['popup_description'],
		// 	'linkTitle'   => get_option($this->get_id() . '_settings_data')['popup_link_title'],
		// 	'link'        => get_option($this->get_id() . '_settings_data')['popup_link'],
		// );

		return rest_ensure_response($_posts);
	}

	// Load settings page - wp admin
	public function load_settings_page()
	{
		?>
<div class="wrap">
	<h1><?php esc_html_e('VR Modal Settings', 'vr-modal-admin'); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields($this->get_id() . '_settings_group'); ?>
		<?php do_settings_sections($this->get_id() . '_settings_group'); ?>

		<table class="form-table">
			<tr>
				<th scope="row"><label for="popup-title">Popup Title:</label></th>
				<td><input type="text" name="<?php echo $this->get_id(); ?>_settings_data[popup_title]" value="<?php echo esc_attr(get_option($this->get_id() . '_settings_data')['popup_title'] ?? ''); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="popup-description">Popup Description:</label></th>
				<td>
					<textarea name="<?php echo $this->get_id(); ?>_settings_data[popup_description]" style="width: 400px; height: 150px;"><?php echo esc_textarea(get_option($this->get_id() . '_settings_data')['popup_description'] ?? ''); ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="popup-link-title">Popup Link Title:</label></th>
				<td><input type="text" name="<?php echo $this->get_id(); ?>_settings_data[popup_link_title]" value="<?php echo esc_attr(get_option($this->get_id() . '_settings_data')['popup_link_title'] ?? ''); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="popup-link">Popup Link:</label></th>
				<td><input type="text" name="<?php echo $this->get_id(); ?>_settings_data[popup_link]" value="<?php echo esc_url(get_option($this->get_id() . '_settings_data')['popup_link'] ?? ''); ?>" /></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
		<?php
	}

	// Load view - wp admin (might change later)
	public function load_view()
	{
		?>
		<div class="wrap">
			<h1><?php esc_html_e('My Menu Section', 'vr-modal-admin'); ?></h1>
			<p><?php esc_html_e('This is where the page content goes.', 'vr-modal-admin'); ?></p>
		</div>
		<?php
	}
}

// Initialize the class on the 'plugins_loaded' action
add_action('plugins_loaded', array('Vr_Modal', 'init'));
