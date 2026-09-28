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
}
