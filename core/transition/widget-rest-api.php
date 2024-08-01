<?php

add_action('rest_api_init', 'kira_widget_rest_api');
function kira_widget_rest_api()
{
    $version   = '1';
    $namespace = 'kiranime/v' . $version;
    register_rest_route($namespace, '/widget', [
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'args'                => [],
        'callback'            => 'kiranime_get_widget_data',
    ]);
}

function kiranime_get_widget_data(WP_REST_Request $args)
{
    $params = $args->get_json_params();
    $name   = $args['name'];
    $result = null;

    $result = match ($name) {
        'listing' => load_widget_listing_data($params),
        default => null,
    };

    return new WP_REST_Response(['status' => true, 'result' => $result]);
}
