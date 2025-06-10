<?php
namespace ROCKET_WP_CRAWLER\service;

class assets_service
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }

    public function enqueue_frontend_assets(): void
    {
        if (is_front_page() || is_home()) {
            wp_enqueue_script(
                'abtf-link-collector',
                plugins_url('../assets/js/link_collector.js', __FILE__),
                [],
                '1.0',
                true
            );
            wp_localize_script('abtf-link-collector', 'abtfCollector', [
                'endpoint' => rest_url('abtf/v1/links')
            ]);
        }
    }
}
