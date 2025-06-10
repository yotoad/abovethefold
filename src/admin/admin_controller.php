<?php
namespace ROCKET_WP_CRAWLER\admin;

class admin_controller
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'add_admin_page']);
    }

    public function add_admin_page(): void
    {
        add_menu_page(
            'ABTF Link Visits',
            'ABTF Link Visits',
            'manage_options',
            'abtf-link-visits',
            [$this, 'render_admin_page']
        );
    }

    /**
     * Render the admin page.
     */
    public function render_admin_page(): void {
        global $wpdb;
        $table          = $wpdb->prefix . 'abtf_links';
        $seven_days_ago = date( 'Y-m-d H:i:s', strtotime( '-7 days' ) );

        // Clean up old entries.
        $wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE created_at < %s", $seven_days_ago ) );

        // Filters.
        $screen_width  = isset( $_GET['screen_width'] ) ? intval( $_GET['screen_width'] ) : '';
        $screen_height = isset( $_GET['screen_height'] ) ? intval( $_GET['screen_height'] ) : '';
        $link_url      = isset( $_GET['link_url'] ) ? sanitize_text_field( $_GET['link_url'] ) : '';

        $where = "created_at >= %s";
        $args  = [ $seven_days_ago ];

        if ( $screen_width ) {
            $where .= " AND screen_width = %d";
            $args[] = $screen_width;
        }
        if ( $screen_height ) {
            $where .= " AND screen_height = %d";
            $args[] = $screen_height;
        }
        if ( $link_url ) {
            $where .= " AND link_url LIKE %s";
            $args[] = '%' . $wpdb->esc_like( $link_url ) . '%';
        }

        // Get filtered results.
        $query   = "SELECT * FROM $table WHERE $where ORDER BY created_at DESC";
        $results = $wpdb->get_results( $wpdb->prepare( $query, ...$args ) );

        // Totals.
        $total_visits = count( $results );
        $total_links  = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE created_at >= %s", $seven_days_ago ) );
        $unique_links = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT link_url) FROM $table WHERE created_at >= %s", $seven_days_ago ) );

        // Output.
        echo '<div class="wrap"><h1>ABTF Link Visits (Last 7 Days)</h1>';

        // Filters form.
        echo '<form method="get" action="">';
        echo '<input type="hidden" name="page" value="abtf-link-visits" />';
        echo 'Screen Width: <input type="number" name="screen_width" value="' . esc_attr( $screen_width ) . '" />';
        echo 'Screen Height: <input type="number" name="screen_height" value="' . esc_attr( $screen_height ) . '" />';
        echo 'Link URL: <input type="text" name="link_url" value="' . esc_attr( $link_url ) . '" />';
        echo '<input type="submit" class="button" value="Filter" />';
        echo '</form>';

        // Totals.
        echo '<p><strong>Total Visits (filtered):</strong> ' . esc_html( $total_visits ) . '</p>';
        echo '<p><strong>Total Links (all):</strong> ' . esc_html( $total_links ) . '</p>';
        echo '<p><strong>Unique Links (all):</strong> ' . esc_html( $unique_links ) . '</p>';

        // Table.
        echo '<table class="widefat"><thead><tr><th>Date</th><th>Screen Size</th><th>Link URL</th><th>Link Text</th></tr></thead><tbody>';
        foreach ( $results as $row ) {
            echo '<tr>';
            echo '<td>' . esc_html( $row->created_at ) . '</td>';
            echo '<td>' . esc_html( $row->screen_width ) . ' x ' . esc_html( $row->screen_height ) . '</td>';
            echo '<td><a href="' . esc_url( $row->link_url ) . '" target="_blank">' . esc_html( $row->link_url ) . '</a></td>';
            echo '<td>' . esc_html( $row->link_text ) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
}
