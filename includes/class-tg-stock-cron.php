<?php

if (! defined('ABSPATH')) {
    exit;
}

class TG_Stock_Cron
{

    public function __construct()
    {
        add_action('tg_daily_low_stock_check', array($this, 'send_low_stock_digest'));
    }

    public static function activate()
    {
        $scheduled_event = wp_next_scheduled('tg_daily_low_stock_check');
        if (! $scheduled_event) {
            wp_schedule_event(time(), 'daily', 'tg_daily_low_stock_check');
        }
    }

    public static function deactivate()
    {
        wp_clear_scheduled_hook('tg_daily_low_stock_check');
    }

    public function send_low_stock_digest()
    {
        $rest_api = new TG_REST_API;
        $low_stock_products = $rest_api->get_low_stock_response();
        if (! empty($low_stock_products)) {
            $to = get_option('admin_email');
            $subject = 'Low stock alert';
            $message = '';
            foreach ($low_stock_products as $product) {
                $message .= $product['name'] . ': ' . $product['stock'] . " in stock\n";
            }
            wp_mail($to, $subject, $message);
        }
    }
}
