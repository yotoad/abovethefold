<?php
namespace ROCKET_WP_CRAWLER\api;

class api_controller
{
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('abtf/v1', '/links', [
            'methods' => 'POST',
            'callback' => [$this, 'store_links'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function store_links( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'abtf_links';
        $params = $request->get_json_params();

        $screen_width  = isset( $params['screen']['width'] ) ? intval( $params['screen']['width'] ) : 0;
        $screen_height = isset( $params['screen']['height'] ) ? intval( $params['screen']['height'] ) : 0;
        $links         = isset( $params['links'] ) && is_array( $params['links'] ) ? $params['links'] : [];

        $created_at = current_time( 'mysql' );

        foreach ( $links as $link ) {
            $link_url  = isset( $link['href'] ) ? sanitize_text_field( $link['href'] ) : '';
            $link_text = isset( $link['text'] ) ? sanitize_text_field( $link['text'] ) : '';

            $wpdb->insert(
                $table,
                [
                    'screen_width'  => $screen_width,
                    'screen_height' => $screen_height,
                    'link_url'      => $link_url,
                    'link_text'     => $link_text,
                    'created_at'    => $created_at,
                ]
            );
        }

        return rest_ensure_response( [ 'success' => true ] );
    }
}
