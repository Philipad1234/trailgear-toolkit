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

new TG_Checkout_Fields();
new TG_Admin_Settings();
new TG_REST_API();