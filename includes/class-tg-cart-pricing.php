<?php
if (! defined('ABSPATH')) {
    exit;
}

class TG_Cart_Pricing
{
    public function __construct()
    {
        add_action('woocommerce_cart_calculate_fees', array($this, 'apply_bulk_discount_fee'));
    }

    public function apply_bulk_discount_fee()
    {
        $cart_items = WC()->cart->get_cart();
        $category_counts = array();
        foreach ($cart_items as $cart_item) {
            $product = $cart_item['data'];
            $category_ids = $product->get_category_ids();
            foreach ($category_ids as $category_id) {
                if (! isset($category_counts[$category_id])) {
                    $category_counts[$category_id] = 0;
                }
                $category_counts[$category_id] += $cart_item['quantity'];
            }
        }
        foreach ($category_counts as $category_id => $count) {
            if($count >= 3){
                WC()->cart->add_fee('Bulk Discount', -5.00);
                break;
            }
        }
    }
}
