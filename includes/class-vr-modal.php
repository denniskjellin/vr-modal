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
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box') );
		add_action( 'save_post', array( $this, 'save_post'), 10, 2 );
	}

	public function save_post( $post_id, $post  ){

		if (
			/**
			 * Wrong post type
			 */
			( $post->post_type !== 'custom_post_type' ) ||

			/**
			 * Autosave is triggered
			 */
			( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( $post->post_status === 'auto-draft' ) || wp_is_post_revision( $post ) )
		) {
			return false;
		}

		// echo $post_id;

		// echo get_post_type( $post_id );
		// echo "<pre>";
		// print_r($_POST);

		add_post_meta( $post_id, 'vrm_title', $_POST['vrm_title'], true );
		add_post_meta( $post_id, 'vrm_content', $_POST['vrm_content'], true );
		add_post_meta( $post_id, 'vrm_button_title', $_POST['vrm_button_title'], true );
		add_post_meta( $post_id, 'vrm_button_url', $_POST['vrm_button_url'], true );
		
		
	}

	public function add_meta_box( ){
		add_meta_box(
			'vr-modal-meta-box',
			'VR Modal',
			array($this, 'render_meta_box'),
			'custom_post_type',
			'normal',
			'high'
		);
	}

	public function render_meta_box( $post ){
	
	//	echo "<pre>";
	//	print_r( $post );
		?>
		 <div>
			<label for="vrm-title">Rubrik</label><br>
			<input name="vrm_title" type="text" id="vrm-title" value="<?php echo get_post_meta( $post->ID, 'vrm_title', true );?>">
		</div> 
		<div>
			<label for="vrm-content">Innehåll</label>
			<textarea name="vrm_content" rows="10" cols="" id="vrm_content" class="large-text"><?php echo esc_textarea(get_post_meta($post->ID, 'vrm_content', true)); ?></textarea>
		</div>
		<div>
			<label for="vrm_button_title">Knapp rubrik</label><br>
			<input name="vrm_button_title" type="text" id="vrm_button_title" value="<?php echo get_post_meta( $post->ID, 'vrm_button_title', true );?>">
		</div>
		<div>
			<label for="vrm_button_url">Knapp url</label><br>
			<input name="vrm_button_url" type="text" id="vrm_button_url" value="<?php echo get_post_meta( $post->ID, 'vrm_button_url', true );?>">
		</div>
		
			<?php
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
			'supports'            => array('title', 'thumbnail','page-attributes'),
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
    $enable_feature = get_option($this->get_id() . '_settings_data')['enable_feature'] ?? 0;

    if ($enable_feature) {
        $args  = array(
            'post_type'      => 'custom_post_type',
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        $_posts = array();
        foreach ($posts as $key => $post) {
            $_posts[$key]['vrm_title']       = $post->post_title;
            $_posts[$key]['vrm_content'] = get_post_meta($post->ID, 'vrm_content', true);
            $_posts[$key]['vrm_button_title']   = get_post_meta($post->ID, 'vrm_button_title', true);
            $_posts[$key]['vrm_button_url']        = get_post_meta($post->ID, 'vrm_button_url', true);
        }

        return rest_ensure_response($_posts);
    } else {
        // Feature is disabled, return an empty response or an appropriate message
        return rest_ensure_response(array());
    }
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
                <!-- Existing fields -->

                <!-- Toggle switch for enabling/disabling feature -->
                <tr>
                    <th scope="row"><label for="enable_feature">Enable Feature:</label></th>
                    <td>
                        <input type="checkbox" name="<?php echo $this->get_id(); ?>_settings_data[enable_feature]" <?php checked(1, get_option($this->get_id() . '_settings_data')['enable_feature'] ?? 0); ?> value="1" />
                    </td>
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
