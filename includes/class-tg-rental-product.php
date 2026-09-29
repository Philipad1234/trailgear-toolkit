<?php

if (! defined('ABSPATH')) {
    exit;
}

class TG_Rental_Product
{
    public function __construct()
    {
        //add_filter( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 )
        add_filter('product_type_selector', array($this, 'select_product_type'));
        add_filter('woocommerce_product_class', array($this, 'product_class_filter'), 10, 4);
        add_action('woocommerce_loaded', array($this, 'load_woocommerce'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_rental_tabs'));
        add_action('woocommerce_product_data_panels', array($this, 'add_rental_panels'));
        add_action('woocommerce_process_product_meta_rentable', array($this, 'save_rental_meta'));
    }

    public function select_product_type(array $types)
    {
        $types['rentable'] = 'Rentable Gear';
        return $types;
    }

    //function wp_kama_woocommerce_product_class_filter( $classname, $product_type, $context, $product_id ){
    // filter...
    // }
    public function product_class_filter($classname, $product_type, $context, $product_id)
    {
        if ($product_type === 'rentable') {
            return "TG_Product_Rentable";
        }
        return $classname;
    }

    public function load_woocommerce()
    {
        require_once plugin_dir_path(__FILE__) . 'class-tg-product-rentable.php';
    }

    public function add_rental_tabs($tabs)
    {
        $tabs['rental'] = array(
            'label' => 'Rental',
            'target' => 'rental_product_data',
            'class' => array('hide_if_grouped')
        );
        return $tabs;
    }

    public function add_rental_panels()
    {
        echo '<div id="rental_product_data" class="panel woocommerce_options_panel">';
        echo '<div class="options_group">';
        $saved_value = get_post_meta(get_the_ID(), '_rental_rate_per_day', true);
        woocommerce_wp_text_input([
            'id' => '_rental_rate_per_day',
            'label' => 'Daily Rental Rate ($)',
            'data_type' => 'price',
            'value' => $saved_value
        ]);
        echo '</div>';
        echo '</div>';
    }

    public function save_rental_meta($post_id)
    {
        if (isset($_POST['_rental_rate_per_day'])) {
            $formatted_rate = wc_format_decimal($_POST['_rental_rate_per_day']);
            update_post_meta($post_id, '_rental_rate_per_day', $formatted_rate);
        }
    }
}
