<?php
if (! defined('ABSPATH')) {
    exit;
}

class TG_Cart_Pricing
{
    public function __construct()
    {
        add_action('woocommerce_cart_calculate_fees', array($this, 'apply_bulk_discount_fee'));
        add_action('woocommerce_cart_calculate_fees', array($this, 'find_rentable_products'));
    }

    public function apply_bulk_discount_fee()
    {
        $cart_items = WC()->cart->get_cart();
        $category_counts = array();
        $category_totals = array();
        foreach ($cart_items as $cart_item) {
            $product = $cart_item['data'];
            $category_ids = $product->get_category_ids();
            foreach ($category_ids as $category_id) {
                if (! isset($category_counts[$category_id])) {
                    $category_counts[$category_id] = 0;
                }
                $category_counts[$category_id] += $cart_item['quantity'];
                if (! isset($category_totals[$category_id])) {
                    $category_totals[$category_id] = 0;
                }
                $category_totals[$category_id] += $cart_item['line_total'];
            }
        }
        foreach ($category_counts as $category_id => $count) {
            if ($count >= 3) {
                $discount_amount = $category_totals[$category_id] * -0.1;
                WC()->cart->add_fee('Bulk Discount (10%)', $discount_amount);
                break;
            }
        }
    }

    public function find_rentable_products()
    {
        $cart_items = WC()->cart->get_cart();
        $running_total = 0;
        foreach ($cart_items as $cart_item) {
            if ($cart_item['data']->get_type() === 'rentable') {
                $running_total += $cart_item['line_total'];
            }
        }
        if ($running_total > 0) {
            $deposit_percent = get_option('tg_rental_deposit_percent');
            $deposit_amount = $running_total * ($deposit_percent / 100);
            WC()->cart->add_fee('Rental Deposit', $deposit_amount);
        }
    }
}
