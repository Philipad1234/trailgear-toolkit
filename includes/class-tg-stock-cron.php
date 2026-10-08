<?php

if (! defined('ABSPATH')) {
    exit;
}

class TG_Stock_Cron
{
    // wp_schedule_event( int $timestamp, string $recurrence, string $hook )
    // wp_next_scheduled( string $hook)
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
}
