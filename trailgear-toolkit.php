<?php
/*
 * Plugin Name:       TrailGear
 * Description:       A custom PHP toolkit for outdoor-gear WooCommerce stores: rentable products, custom checkout fields, a REST API endpoint, low-stock automation, and cart pricing rules.
 * Version:           1.0.0
 * Author:            Philip
 * Text Domain:       trailgear-toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-checkout-fields.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/class-tg-admin-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-rest-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-rental-product.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-cart-pricing.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-stock-cron.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-email-customizer.php';

register_activation_hook( __FILE__, array( 'TG_Stock_Cron', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TG_Stock_Cron', 'deactivate' ) );

new TG_Checkout_Fields();
new TG_Admin_Settings();
new TG_REST_API();
new TG_Cart_Pricing();
new TG_Rental_Product();
new TG_Stock_Cron();
new TG_Email_Customizer();