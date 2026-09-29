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
        echo '<p>Rental fields go here.</p>';
        echo '</div>';
    }
}
