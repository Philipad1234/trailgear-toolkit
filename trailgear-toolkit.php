<?php
/*
 * Plugin Name:       TrailGear
 * Description:       A custom PHP toolkit for outdoor-gear WooCommerce stores: rentable products, custom checkout fields, a REST API endpoint, low-stock automation, and cart pricing rules.
 * Version:           1.0.0
 * Author:            Philip Adams
 * Text Domain:       trailgear-toolkit
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-tg-checkout-fields.php';

new TG_Checkout_Fields();