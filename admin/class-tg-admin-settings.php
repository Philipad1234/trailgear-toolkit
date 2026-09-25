<?php

if (! defined('ABSPATH')) {
    exit;
}

class TG_Admin_Settings
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page()
    {
        add_submenu_page(
            'woocommerce',
            'TrailGear Settings',
            'TrailGear',
            'manage_options',
            'trailgear-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page()
    {
?>
        <div class="wrap">
            <h1>TrailGear Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('trailgear_settings_group');
                do_settings_sections('trailgear-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    public function render_add_settings_section()
    {
        echo '<p>Configure thresholds used by TrailGear\'s automation features.</p>';
    }

    public function render_add_settings_field($args)
    {
        $current_value = get_option($args['option_name']);

        echo sprintf(
            '<input type="number" name="%s" value="%s">',
            esc_attr($args['option_name']),
            esc_attr($current_value)
        );
    }

    public function register_settings()
    {
        //register_setting( string $option_group, string $option_name, array $args = array() )
        register_setting('trailgear_settings_group', 'tg_low_stock_threshold');
        register_setting('trailgear_settings_group', 'tg_rental_deposit_percent');

        //add_settings_section( string $id, string $title, callable $callback, string $page, array $args = array() )
        add_settings_section('add-settings-section', 'Add Settings Section', '', 'trailgear-settings', array($this, 'render_add_settings_section'));

        //add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() )
        add_settings_field('tg_low_stock_threshold', 'TG Low Stock Threshold', array($this, 'render_add_settings_field'), 'trailgear-settings', 'add-settings-section', array('option_name' => 'tg_low_stock_threshold'));
        add_settings_field('tg_rental_deposit_percent', 'TG Rental Deposit Percent', array($this, 'render_add_settings_field'), 'trailgear-settings', 'add-settings-section', array('option_name' => 'tg_rental_deposit_percent'));
    }
}
