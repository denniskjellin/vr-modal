<?php
class Vr_Modal {
    const ID = 'vr-modal';

    public function init()
    {
        add_action('admin_menu', array($this, 'add_menu_page'), 1);
        add_action('admin_init', array($this, 'register_settings'));

        // Register REST API endpoint
        add_action('rest_api_init', array($this, 'register_rest_endpoint'));
    }

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
                array(&$this, 'load_view'),
                'dashicons-admin-page'
            );

            add_submenu_page(
                $this->get_id(),
                esc_html__('Settings', 'vr-modal-admin'),
                esc_html__('Settings', 'vr-modal-admin'),
                'manage_options',
                $this->get_id() . '_settings',
                array(&$this, 'load_settings_page')
            );
        }
    }

    public function register_settings()
    {
        register_setting($this->get_id() . '_settings_group', $this->get_id() . '_settings_data');
    }

    public function register_rest_endpoint() {
        register_rest_route('vr-modal/v1', '/modal-data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_modal_data'),
            'permission_callback' => '__return_true',
        ));
    }

    public function get_modal_data() {
        $popup_data = array(
            'title' => get_option($this->get_id() . '_settings_data')['popup_title'],
            'description' => get_option($this->get_id() . '_settings_data')['popup_description'],
            'linkTitle' => get_option($this->get_id() . '_settings_data')['popup_link_title'],
            'link' => get_option($this->get_id() . '_settings_data')['popup_link'],
        );

        return rest_ensure_response($popup_data);
    }

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

    public function load_view()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('My Menu Section', 'vr-modal-admin'); ?></h1>
            <div id="app"></div> <!-- This is where your Vue.js app will mount -->
        </div>

        <script>
            // Inline script to initialize the Vue.js app
            new Vue({
                el: '#app',
                data: {
                    message: 'Hello Vue.js!',
                },
                template: '<div>Celsius{{ message }}</div>',
            });
        </script>
        <?php
    }
}


// Instantiate the class
$vr_modal = new Vr_Modal();
$vr_modal->init();
