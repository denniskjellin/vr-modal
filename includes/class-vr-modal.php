<?php
/**
 * VR modal classes
 *
 * @author Knowit Experience Norrland
 * @package vr-modal
 */

 use knowit\helper\Util;
class Vr_Modal {
	/**
	 * Plugin ID.
	 *
	 * @var string
	 */
	const ID = 'vr-modal';

	/**
	 * Text domain.
	 *
	 * @var string
	 */
	const TEXT_DOMAIN = 'vr-modal';

	/**
	 * Constructor.
	 */
	public function __construct() 
	{
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

		// Add custom column to the admin table
		add_action('admin_init', array($this, 'manage_columns'));

	}
	
	// Enqueue validation script for custom post type (jquery)
	public function validate_script()
	{    
		if (!wp_script_is('vr-validate', 'enqueued')) {
			wp_enqueue_script('vr-validate', plugin_dir_url(__FILE__) . 'jquery-validation-1.19.5/dist/jquery.validate.min.js', array('jquery'));
		}
		if (!wp_script_is('vr-validate-admin-script', 'enqueued')) {
			wp_enqueue_script('vr-validate-admin-script', plugin_dir_url(__FILE__) . 'vr-modal-validation.js', array('jquery', 'vr-validate'));
		}

	}

	// custom css for validation on custom post type vr-modal
	public function custom_css() {
		$screen = get_current_screen();
		if ( 'vr_modal_post_type' !== $screen->id && 'post' !== $screen->base ) 
		{
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

		strong.error {
			color: red;
		}

		.vr-modal-box {
			margin-top: 1rem;
		}

		input, textarea {
			margin-top: 0.3rem;
		}
		</style>
		<?php
	}

/**
 * Change title placeholder text.
 *
 * @param string $title The original title placeholder text.
 * @param WP_Post $post The post object.
 * @return string
 */
public function change_post_title_placeholder( $title, $post ) 
{
    $post_type = 'vr_modal_post_type';

    if ( post_type_exists( $post_type ) && $post->post_type == $post_type ) 
	{
		$my_title = 'Ange modalens namn här (ex. Nyhetsbrev december)';
        return $my_title;
    }

    return $title;
}

/**
 * Save post meta.
 *
 * @param int $post_id The post ID.
 * @param WP_Post $post The post object.
 */	
public function save_post($post_id, $post)
{
	// Add nonce for security and authentication.
	$nonce_name   = isset($_POST['vrm_meta_box_nonce']) ? $_POST['vrm_meta_box_nonce'] : '';

    // Check the user's capabilities
    if (!current_user_can('edit_post', $post_id)) 
	{
        return false;
    }

	// check if form is set or not.
	if (!isset($_POST['vrm_title']) || !isset($_POST['vrm_content']) || !isset($_POST['vrm_button_title']) || !isset($_POST['vrm_button_url'])) 
	{
		return false;
	}
	
    // Save post meta
    update_post_meta($post_id, 'vrm_title', sanitize_text_field($_POST['vrm_title']));
    update_post_meta($post_id, 'vrm_content', sanitize_textarea_field($_POST['vrm_content']));
    update_post_meta($post_id, 'vrm_button_title', sanitize_text_field($_POST['vrm_button_title']));
    update_post_meta($post_id, 'vrm_button_url', sanitize_text_field($_POST['vrm_button_url']));
}

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

	public function register_custom_post_type() 
	{
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
			'exclude_from_search' => true,
		);

		register_post_type('vr_modal_post_type', $args);
	}



/**
 * Display custom column content
 *
 * @param string $column
 * @param int $post_id
 */
public function display_custom_column_content($column, $post_id) 
{
	if ($column === 'vrm_title_column' && get_post_type($post_id) === 'vr_modal_post_type') {
		// Get the value of the custom field 'vrm_title'
		$vrm_title = get_post_meta($post_id, 'vrm_title', true);

		// Output the custom field value
		echo esc_html($vrm_title);
	}
}

/**
 * Add custom column
 *
 * @param array $columns
 * @return array
 */

public function add_custom_column($columns) 
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['vrm_title_column'] = 'Modal rubrik';
        }
    }
    return $new_columns;
}

/* Add custom column to the admin table */
public function manage_columns() {
	add_filter('manage_vr_modal_post_type_posts_columns', array($this, 'add_custom_column'));
	add_action('manage_vr_modal_post_type_posts_custom_column', array($this, 'display_custom_column_content'), 10, 2);
}


/**
 * Render meta box content.
 *
 * @param WP_Post $post The post object.
 */
public function init()
{
	// Add menu page
	add_action('admin_menu', array($this, 'add_menu_page'), 1);
	add_action('admin_init', array($this, 'register_settings'));

	// Register REST API endpoint
	add_action('rest_api_init', array($this, 'register_rest_endpoint'));
 
}

/**
 * Get the plugin ID.
 *
 * @return string
 */
public function get_id()
{
	return self::ID;
}


/**
 * Render meta box content.
 *
 * @param WP_Post $post The post object.
 */
public function add_menu_page()
{
	// Check if the menu page already exists
	$menu_page_exists = false;
	global $menu;
	foreach ($menu as $item) {
		if ($item[2] === $this->get_id()) 
		{
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
public function register_rest_endpoint() 
{
	register_rest_route('vr-modal/v1', '/modal-data', array(
		'methods' => 'GET',
		'callback' => array($this, 'get_modal_data'),
		'permission_callback' => '__return_true',
	));
}

/**
 * Get modal data.
 *
 * @return WP_REST_Response
 */
public function get_modal_data() 
{
    $enable_vr_modal = get_option($this->get_id() . '_settings_data')['enable_vr_modal'] ?? 0;

    if ($enable_vr_modal) {
        $args  = array(
            'post_type'      => 'vr_modal_post_type',
            'posts_per_page' => -1,
        );

        $posts = get_posts($args);

        $_posts = array();
        foreach ($posts as $key => $post) 
		{
            $_posts[$key]['vrm_title']       = get_post_meta($post->ID, 'vrm_title', true);
            $_posts[$key]['vrm_content']     = get_post_meta($post->ID, 'vrm_content', true);
            $_posts[$key]['vrm_button_title'] = get_post_meta($post->ID, 'vrm_button_title', true);
            $_posts[$key]['vrm_button_url']   = get_post_meta($post->ID, 'vrm_button_url', true);
        }

		
        return rest_ensure_response($_posts);
    } else {
        // Feature is disabled, return null
        return null;

    }
}

/**
 * Render meta box content.
 *
 * @param WP_Post $post The post object.
 */


public function render_meta_box( $post )
{
    // Add nonce for security and authentication.
    wp_nonce_field('vrm_meta_box_nonce', 'vrm_meta_box_nonce');
    ?>
    <!-- Add fields for data entry. -->
    <div class="vr-modal-box">
        <label for="vrm-title">Ange rubrik *</label><br>
        	<input name="vrm_title" type="text" placeholder="Rubriken som syns i modalen för besökaren" id="vrm-title" value="<?php echo get_post_meta( $post->ID, 'vrm_title', true );?>" style="width: 100%;">
    </div> 
    <div class="vr-modal-box">
        <label for="vrm-content">Ange innehållet *</label><br>
        	<textarea name="vrm_content" rows="10" cols="" placeholder="Textuellt innehåll som syns för besökaren" id="vrm_content" class="large-text" style="width: 100%;"><?php echo esc_textarea(get_post_meta($post->ID, 'vrm_content', true)); ?></textarea>
    </div>
    <div class="vr-modal-box">
        <label for="vrm_button_title">Ange rubrik för länken:</label><br>
        	<input name="vrm_button_title" type="text" placeholder="Vidare till nyhetsbrevet" id="vrm_button_title" value="<?php echo get_post_meta( $post->ID, 'vrm_button_title', true );?>" style="width: 100%;">
    </div>
    <div class="vr-modal-box">
        <label for="vrm_button_url">Ange URL för länk:</label><br>
        	<input name="vrm_button_url" type="text" placeholder="https://exempel.se" id="vrm_button_url" value="<?php echo get_post_meta( $post->ID, 'vrm_button_url', true );?>" style="width: 100%;">
    </div>
    <div class="vr-modal-box">
        <p class="description">* Fält markerade med <em>asterisk <strong>(*)</strong></em> är obligatoriska.</p>
    </div>
    <?php
}

// Render the menu page view.
public function load_view()
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('VR Modal - FAQ', 'vr-modal-admin'); ?></h1>
<h2>Aktivering/Avaktivering</h2>
<p>För att aktivera VR Modal-funktionen, gå till inställningarna nedan och klicka i rutan <strong>"Aktivera VR Modal"</strong>.</p>
<p>Om ingen VR Modal ska visas på sidan är det rekommenderat att <strong>inaktivera</strong> pluginet</p>

<h2>Skapa ny VR Modal</h2>
<p>Öppna fliken <strong>"Modaler"</strong> och skapa en ny modalpost. Observera att alla obligatoriska fält måste fyllas i för att VR Modalen ska kunna publiceras.</p>

<h2>Arkiv</h2>
<p>För att granska tillgängliga VR Modaler, navigera till <strong>"VR Modal"</strong> i wp-admin-panelen och välj <strong>"Modaler"</strong>.</p>
<p>Där kommer du att få en översikt över befintliga VR Modaler samt kort information om dem.</p>

<h2>Visningsordning</h2>
<p>Den senast publicerade VR Modalen är den som visas för besökaren. <br>Genom att ändra en befintlig VR Modal till utkast och samtidigt markera en annan som publicerad, kommer den sistnämnda att visas istället.</p>

<h2>Avpublicering</h2>
<p>För att ta bort en VR Modal, placera den i utkast eller radera den. Om ingen annan modal är publicerad kommer ingen modal att visas.</p>

<h2>Schemaläggning</h2>
<p>Planera modaler genom att specificera publiceringstiden. Om en VR Modal redan är aktiv kommer den att ersättas av den som publiceras senare.</p>

<h2>Besöksparametrar</h2>
<p>Om en besökare når webbplatsen genom att klicka på en länk i ett nyhetsbrev med parametrarna <strong>"?utm_source=Ungapped&utm_medium=email"</strong>, <br>så kommer modalen att förbli dold under resten av besökssessionen för den specifika användaren.</p>

<h2>Visning av VR Modalen</h2>
<p>VR Modalen visas när en besökare kommer in på webbsidan, när VR Modalen har blivit visad så sätts en kaka (vr_modal_cookie) vilket gör att VR Modalen ej syns igen under denna session.</p>
<p>Om besökaren kryssar ner sin webbläsare och går in på webbsidan på nytt, så kommer VR Modalen att återigen synas.</p>


				
	    <form method="post" action="options.php">
            <?php settings_fields($this->get_id() . '_settings_group'); ?>
            	<?php do_settings_sections($this->get_id() . '_settings_group'); ?>
					<br><h2>Inställningar</h2>
						<p>* Modalen visas endast när funktionen är aktiverad.</p>
							<p>* Slå av funktionen om ingen modal ska användas.</p>
				            	<table class="form-table">
                				<!-- Toggle switch for enabling/disabling feature -->
                					<tr>
                    					<th scope="row"><label for="enable_vr_modal">Aktivera VR Modal:</label></th>
                    						<td>
                        						<input type="checkbox" name="<?php echo $this->get_id(); ?>_settings_data[enable_vr_modal]" <?php checked(1, get_option($this->get_id() . '_settings_data')['enable_vr_modal'] ?? 0); ?> value="1" />
                    						</td>
                					</tr>
            					</table>
            				<?php submit_button('Spara ändringar'); ?>
        	</form>
	</div>
    <?php
 }
}
