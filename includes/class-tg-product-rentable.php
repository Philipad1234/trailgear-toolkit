<?php

if (! defined('ABSPATH')) {
    exit;
}

class TG_Product_Rentable extends WC_Product
{
    public function get_type()
    {
        return 'rentable';
    }

    public function get_price_html($deprecated = '')
    {
        $saved_rate = get_post_meta($this->get_id(), '_rental_rate_per_day', true);
        if (! $saved_rate) {
            return '';
        }
        return wc_price($saved_rate) . ' /day';
    }

    public function get_price($context = 'view')
    {
        $saved_rate = get_post_meta($this->get_id(), '_rental_rate_per_day', true);
        if ($saved_rate) {
            return $saved_rate;
        }
        return  '';
    }
}
