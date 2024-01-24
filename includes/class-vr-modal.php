<?php
/**
 * VR modal classes
 *
 * @author Knowit Experience Norrland
 * @package vr-modal
 */

 use knowit\helper\Util;
class Vr_Modal {
	// Define plugin constants
	const ID = 'vr-modal';

	// Define custom post types
	const TEXT_DOMAIN = 'vr-modal';

	// Define plugin variables
	public function __construct() {
		// register custom post type
		add_action('init', array($this, 'register_custom_post_type'));
		
		// Add meta box to custom post type
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box') );

		// Save post meta
		add_action( 'save_post', array( $this, 'save_post'), 10, 2 );

		// Validation script for custom post type
		add_action('admin_enqueue_scripts', array($this, 'validate_script'));

		// Custom css for custom post type
		add_action( 'admin_head', array( $this, 'custom_css' ) );

		//change title hook
		add_filter( 'enter_title_here', array( $this, 'change_post_title_placeholder' ), 20, 2 );
		
		// Util::logger('Vr_Modal class loaded');
	}
	



	// validation script for custom post type (jquery)
	public function validate_script(){    
  	wp_enqueue_script('vr-validate', plugin_dir_url(__FILE__) . 'jquery-validation-1.19.5/dist/jquery.validate.min.js', array('jquery'));
  	wp_enqueue_script('vr-validate-admin-script', plugin_dir_url(__FILE__) . 'vr-modal-validation.js', array('jquery','vr-validate'));
	}

	// custom css for validation on custom post type vr-modal
	public function custom_css() {
		$screen = get_current_screen();
		if ( 'vr_modal_post_type' !== $screen->id && 'post' !== $screen->base ) {
			return;
		}
		?>
		<style>
		#vr-modal-meta-box input[type="text"].error {
			border: 2px solid red;
		}
		#vr-modal-meta-box textarea.error {
			border: 2px solid red;
		}
		</style>
		<?php
	}

	// Custom title placeholder for infobox. */
public function change_post_title_placeholder( $title, $post ) {
    $post_type = 'vr_modal_post_type';

    if ( post_type_exists( $post_type ) && $post->post_type == $post_type ) {
		$my_title = 'Ange modalens namn här (ex. Nyhetsbrev december)';
        return $my_title;
    }

    return $title;
}



// save function for custom post type	
public function save_post($post_id, $post)
{
	// Add nonce for security and authentication.
	$nonce_name   = isset($_POST['vrm_meta_box_nonce']) ? $_POST['vrm_meta_box_nonce'] : '';

    // Check the user's capabilities
    if (!current_user_can('edit_post', $post_id)) {
        return false;
    }

	  // check if form is set or not.
	if (!isset($_POST['vrm_title']) || !isset($_POST['vrm_content']) || !isset($_POST['vrm_button_title']) || !isset($_POST['vrm_button_url'])) {
		return false;
	}
	
    // Save post meta
    update_post_meta($post_id, 'vrm_title', sanitize_text_field($_POST['vrm_title']));
    update_post_meta($post_id, 'vrm_content', sanitize_textarea_field($_POST['vrm_content']));
    update_post_meta($post_id, 'vrm_button_title', sanitize_text_field($_POST['vrm_button_title']));
    update_post_meta($post_id, 'vrm_button_url', sanitize_text_field($_POST['vrm_button_url']));
}

	// Add meta box to custom post type
	public function add_meta_box( ){
		add_meta_box(
			'vr-modal-meta-box',
			'VR Modal',
			array($this, 'render_meta_box'),
			'vr_modal_post_type',
			'normal',
			'high'
		);
	}

public function render_meta_box( $post ){
    // Add nonce for security and authentication.
    wp_nonce_field('vrm_meta_box_nonce', 'vrm_meta_box_nonce');
    ?>
    <!-- Add fields for data entry. -->
    <div>
        <label for="vrm-title">Ange rubrik för din modal.</label><br>
        <input name="vrm_title" type="text" placeholder="Rubriken som syns i modalen för besökaren" id="vrm-title" value="<?php echo get_post_meta( $post->ID, 'vrm_title', true );?>" style="width: 100%;">
    </div> 
    <div>
        <label for="vrm-content">Ange innehållet för din modal.</label><br>
        <textarea name="vrm_content" rows="10" cols="" placeholder="Textuellt innehåll som syns för besökaren" id="vrm_content" class="large-text" style="width: 100%;"><?php echo esc_textarea(get_post_meta($post->ID, 'vrm_content', true)); ?></textarea>
    </div>
    <div>
        <label for="vrm_button_title">Ange rubrik för länken i din modal.</label><br>
        <input name="vrm_button_title" type="text" placeholder="Vidare till nyhetsbrevet" id="vrm_button_title" value="<?php echo get_post_meta( $post->ID, 'vrm_button_title', true );?>" style="width: 100%;">
    </div>
    <div>
        <label for="vrm_button_url">Ange URL för din länk i modalen.</label><br>
        <input name="vrm_button_url" type="text" placeholder="https://www.genteknik.se/nyhetsbrev" id="vrm_button_url" value="<?php echo get_post_meta( $post->ID, 'vrm_button_url', true );?>" style="width: 100%;">
    </div>
    <div>
        <p class="description">* samtliga fält är obligatoriska.</p>
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
			'supports'            => array('title', 'author', 'revisions'),
			'menu_icon'           => 'dashicons-nametag',
			'menu_position'       => 105,
		);

		register_post_type('vr_modal_post_type', $args);
	}


	// Define plugin variables
	public function init()
	{
		// Add menu page
		add_action('admin_menu', array($this, 'add_menu_page'), 1);
		add_action('admin_init', array($this, 'register_settings'));

		// Register REST API endpoint
		add_action('rest_api_init', array($this, 'register_rest_endpoint'));
	}

	// Define plugin functions
	public function get_id()
	{
		return self::ID;
	}

	// Add menu page
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
            'post_type'      => 'vr_modal_post_type',
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        $_posts = array();
        foreach ($posts as $key => $post) {
            $_posts[$key]['vrm_title']       = get_post_meta($post->ID, 'vrm_title', true);
            $_posts[$key]['vrm_content']     = get_post_meta($post->ID, 'vrm_content', true);
            $_posts[$key]['vrm_button_title'] = get_post_meta($post->ID, 'vrm_button_title', true);
            $_posts[$key]['vrm_button_url']   = get_post_meta($post->ID, 'vrm_button_url', true);
        }

        return rest_ensure_response($_posts);
    } else {
        // Feature is disabled, return an empty response or an appropriate message
        return rest_ensure_response(array());
    }
}


	// Load view - wp admin
public function load_view()
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('VR Modal Kontrollpanel', 'vr-modal-admin'); ?></h1>
        <h2><?php esc_html_e('FAQ', 'vr-modal-admin'); ?></h2>
        
            <li>
                <strong>Aktivering:</strong>
                För att aktivera Vr Modal funktionen, gå till inställningar nedan och klicka i rutan "Aktivera VR Modal", avaktivera om ingen modal finns att visa.
            </li>
            <li>
                <strong>Skapa Modal:</strong>
                Gå till fliken "Modaler" och skapa en ny modalpost.
            </li>
			<li>
                <strong>Innehåll:</strong> Ange vad du vill kalla modalen i titeln, exempelvis 'Nyhetsbrev' för att enkelt hålla koll på vilka modaler du har att arbeta med. Ange sedan rubrik och innehåll och resterande fält för att anpassa vad själva modalen ska visa.
            </li>
            <li>
			<strong>Visningsordning:</strong>
			Den nyaste modalen kommer att visas först. Genom att ändra en befintlig modal till utkast och samtidigt markera en annan som publicerad kommer den sistnämnda att visas istället.
            </li>
			<li>
				<strong>Avpublicering:</strong>
				Om du vill avpublicera en modal, sätt den till utkast. Om det inte finns någon annan modal publicerad kommer ingen modal att visas.
			</li>
            <li>
                <strong>Schemaläggning:</strong>
                Schemalägg modaler genom att ange publiceringstid. Om det redan finns en aktiv modal kommer den att ersättas med den som publiceras därefter.
            </li>
    
	        <form method="post" action="options.php">
            <?php settings_fields($this->get_id() . '_settings_group'); ?>
            <?php do_settings_sections($this->get_id() . '_settings_group'); ?>
			<br><h2><?php esc_html_e('Inställningar', 'vr-modal-admin'); ?></h2>
				<p>* Modalen visas endast när funktionen är aktiverad.</p>
				<p>* Slå av funktionen om ingen modal ska användas.</p>
				            <table class="form-table">
                <!-- Toggle switch for enabling/disabling feature -->
                <tr>
                    <th scope="row"><label for="enable_feature">Aktivera VR Modal:</label></th>
                    <td>
                        <input type="checkbox" name="<?php echo $this->get_id(); ?>_settings_data[enable_feature]" <?php checked(1, get_option($this->get_id() . '_settings_data')['enable_feature'] ?? 0); ?> value="1" />
                    </td>
                </tr>
            </table>
            <?php submit_button('Spara ändringar'); ?>
        </form>
	</div>
    <?php
 }
}