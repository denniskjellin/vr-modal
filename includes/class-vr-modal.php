<?php
// /wp-content/plugins/ct-wp-admin-form/includes/class-ct-wp-admin-form.php
class Vr_Modal
{
    const ID = 'vr-modal';
    public function init()
    {
        add_action('admin_menu', array($this, 'add_menu_page'), 1);
    }
    public function get_id()
    {
        return self::ID;
    }
    public function add_menu_page()
    {
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
            esc_html__('Submenu', 'vr-modal-admin'),
            esc_html__('Submenu', 'vr-modal-admin'),
            'manage_options',
            $this->get_id() . '_view1',
            array(&$this, 'load_view')
        );
    }

    public function load_view() {
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