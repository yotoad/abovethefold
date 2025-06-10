<?php
namespace ROCKET_WP_CRAWLER;

class rocket_wpc_plugin_class
{
    public function __construct()
    {
        (new \ROCKET_WP_CRAWLER\service\assets_service())->register();
        (new \ROCKET_WP_CRAWLER\api\api_controller())->register();
        (new \ROCKET_WP_CRAWLER\admin\admin_controller())->register();
    }

    public static function wpc_activate(): void {
        global $wpdb;
        $table           = $wpdb->prefix . 'abtf_links';
        $charset_collate = $wpdb->get_charset_collate();
        $sql             = "CREATE TABLE $table (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            screen_width INT NOT NULL,
            screen_height INT NOT NULL,
            link_url TEXT NOT NULL,
            link_text TEXT NOT NULL,
            created_at DATETIME NOT NULL
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function wpc_uninstall(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'abtf_links';
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}
