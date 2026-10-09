<?php

if (! defined('ABSPATH')) {
    exit;
}

class TG_Email_Customizer
{
    public function __construct()
    {
        add_action('woocommerce_email_order_details', array($this, 'add_delivery_date_to_email'), 10, 4);
    }

    public function add_delivery_date_to_email($order, $sent_to_admin, $plain_text, $email)
    {
        if ($sent_to_admin) {
            return;
        }
        $saved_delivery_date = get_post_meta($order->get_id(), '_tg_delivery_date', true);
        if ($saved_delivery_date) {
            if ($plain_text) {
                echo "Preferred Delivery Date: " . $saved_delivery_date . "\n\n";
            } else {
                echo '<p><strong>Preferred Delivery Date:</strong> ' . esc_html($saved_delivery_date) . '</p>';
            }
        }
    }
}
