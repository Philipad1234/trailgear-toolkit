<?php

if (! defined('ABSPATH')) {
    exit; // Or use die();
}

class TG_Checkout_Fields
{
    public function __construct()
    {
        add_action('woocommerce_after_order_notes', array($this, 'render_delivery_date_field'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'save_delivery_date_field'));
        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'display_delivery_date_admin_order_meta'));
    }

    public function render_delivery_date_field()
    {
        echo '<div class="form-row form-row-wide">';
        echo '<label for="tg_delivery_date">Preferred Delivery Date</label>';
        echo '<input type="date" class="input-text" name="tg_delivery_date" id="tg_delivery_date" />';
        echo '</div>';
    }

    public function save_delivery_date_field($order_id)
    {
        if (isset($_POST['tg_delivery_date'])) {
            $delivery_date = sanitize_text_field($_POST['tg_delivery_date']);
            update_post_meta($order_id, '_tg_delivery_date', $delivery_date);
        }
    }

    public function display_delivery_date_admin_order_meta($order)
    {
        $delivery_date = get_post_meta($order->get_id(), '_tg_delivery_date', true);

        if ($delivery_date) {
            echo '<p><strong>Preferred Delivery Date:</strong> ' . esc_html($delivery_date) . '</p>';
        }
    }
}
