<?php

if (! defined('ABSPATH')) {
    exit;
}


class TG_REST_API
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    //register_rest_route( string $route_namespace, string $route, array $args = array(), bool $override = false ): bool
    public function register_rest_routes()
    {
        register_rest_route(
            'trailgear/v1',
            '/low-stock',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_low_stock_response'),
                'permission_callback' => array($this, 'check_api_key')
            )
        );
    }

    public function get_low_stock_response()
    {
        $threshold = get_option('tg_low_stock_threshold');
        $products = wc_get_products(array(
            'limit' => -1,
        ));
        $results = [];
        foreach ($products as $product) {
            if ($product->get_stock_quantity() > (int)$threshold) {
                continue;
            }
            $product_data = array('name' => $product->get_name(), 'stock' => $product->get_stock_quantity());
            array_push($results, $product_data);
        }
        return $results;
    }


    public function check_api_key($request)
    {
        $saved_key = get_option('tg_api_key');
        $incoming_header = $request->get_header('X-TrailGear-API-Key');
        if (empty($saved_key)) {
            return false;
        }
        if ($saved_key === $incoming_header) {
            return true;
        }
        return false;
    }
}
