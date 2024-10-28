<?php

class Helpers {

    /**
     * Get User IP Address
     */
    public static function get_user_ip_address() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $server = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $server = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $server = $_SERVER['REMOTE_ADDR'];
        }
 
        return $server;
    }

    /**
     * Get Specific Funnel ID Ratings With Specific Rating Value
     *
     * @param int $funnel_id
     * @param int $rating_value
     *
     * @return array
     */
    public static function get_specific_funnel_rating( $funnel_id, $rating_value ) {
        global $wpdb;
        $custom_query = array_column( $wpdb->get_results( "SELECT COUNT(*) FROM {$wpdb->prefix}ratings WHERE funnel_id = {$funnel_id} AND rating_value = {$rating_value}", ARRAY_A ), 'COUNT(*)' )[0];
        return empty( $custom_query ) ? 0 : $custom_query;
    }

    /**
     * Delete A Specific Funnel Row
     *
     * @param int $funnel_id
     * @return array
     */
    public static function delete_row( $funnel_id ) {
        global $wpdb;
        $custom_query = $wpdb->get_results( "DELETE FROM {$wpdb->prefix}ratings WHERE funnel_id = {$funnel_id}", ARRAY_A );
        error_log('running reset func ');
        
        update_option( 'rrtngg_feedback_limit_counter', 0 );
		update_option( 'rrtngg_feedback_already_users', array() );
		update_post_meta( $funnel_id, 'rrtngg_feedback_btn_count', 0 );
		update_post_meta( $funnel_id, 'rrating_visit_count', 0 );

        return $custom_query;
    }

    /**
     * Get Funnel Visitors(By Default Unique Visitors Are Stored In DB Only)
     *
     * @param int $funnel_id
     * @return int
     */
    public static function get_unique_visitors( $funnel_id ) {
        global $wpdb;
        $custom_query = array_column( $wpdb->get_results( "SELECT COUNT( DISTINCT ip ) 
        FROM {$wpdb->prefix}ratings 
        WHERE funnel_id = {$funnel_id} 
        AND ip != ''",
        ARRAY_A ), 'COUNT( DISTINCT ip )' );
        if ( ! empty( $custom_query ) ) {
            return $custom_query[0];
        } else {
            return 0;
        }
    }

    /**
     * Get Total Ratings Of Single Funnel
     *
     * @param int $funnel_id
     * @return int
     */
    public static function get_funnel_total_ratings( $funnel_id ) {
        global $wpdb;
        $custom_query = array_column( $wpdb->get_results( "SELECT COUNT(rating_value) FROM {$wpdb->prefix}ratings WHERE funnel_id={$funnel_id} AND rating_value != 0", ARRAY_A ), 'COUNT(rating_value)' );
        if ( ! empty( $custom_query ) ) {
            return $custom_query[0];
        } else {
            return 0;
        }
    }

     /**
     * Get IP restriction settings
     *
     * @param int $funnel_id
     * @return int
     */
    public static function get_ip_restriction_setting_status(  ) {
        $general_settings = is_array(get_option( 'rrating_funnel_gen_settings' ))? get_option( 'rrating_funnel_gen_settings' ):array();
        if ( array_key_exists('rrating_enable_ip_restriction',$general_settings) && $general_settings['rrating_enable_ip_restriction'] == 'yes'  ) {
            return true;
        } else {
            return 0;
        }
    }

    /**
     * Get Average Based On Funnel ID
     *
     * @param int $funnel_id
     * @return float|int
     */
    public static function get_funnel_average( $funnel_id ) {
        global $wpdb;
        $custom_query = array_column( $wpdb->get_results( "SELECT AVG(rating_value) FROM {$wpdb->prefix}ratings where funnel_id={$funnel_id}", ARRAY_A ), 'AVG(rating_value)' );
        if ( ! empty( $custom_query ) ) {
            return round( $custom_query[0], 1 );
        } else {
            return 0;
        }
    }

    /**
     * Get All User IP's from 'wp_rating' table
     *
     * @return array
     */
    public static function get_users_ips() {
        global $wpdb;

         // Calculate the timestamp 3 days ago
        $three_days_ago = strtotime('-3 days');

        // Construct the SQL query to update the ip field for rows older than 3 days
        $query = $wpdb->prepare(
            "UPDATE {$wpdb->prefix}ratings 
            SET ip = NULL 
            WHERE timestamp_column < %s",
            date('Y-m-d H:i:s', $three_days_ago)
        );

        // Execute the query to clear old IPs
        $wpdb->query($query);

        $custom_query = array_column( $wpdb->get_results( "SELECT ip FROM {$wpdb->prefix}ratings", ARRAY_A ), 'ip' );
        if ( ! empty( $custom_query ) ) {
            return $custom_query;
        } else {
            return [];
        }
    }

    /**
     * Get All Funnel ID's From 'wp_rating' table
     *
     * @return array|bool
     */
    public static function get_funnel_ids() {
        global $wpdb;
        $custom_query = array_column( $wpdb->get_results( "SELECT funnel_id FROM {$wpdb->prefix}ratings", ARRAY_A ), 'funnel_id' );
        if ( ! empty( $custom_query ) ) {
            return $custom_query;
        } else {
            return [];
        }
    }

    /**
     * Insert Rating Data
     *
     * @param string $ip
     * @param int $funnel_id
     * @param int $rating_value
     * @return int|bool
     */
    public static function insert_rating_data( $ip, $funnel_id, $rating_value ) {
        global $wpdb;
        return $wpdb->insert(
            "{$wpdb->prefix}ratings",
            [
                'ip'           => $ip,
                'funnel_id'    => $funnel_id,
                'rating_value' => $rating_value
            ]
        );
    }

    /**
     * Update Rating Data
     *
     * @param int $funnel_id
     * @param int $rating_value
     * @param int $ip
     * @return int|bool
     */
    public static function update_rating_data( $funnel_id, $rating_value, $ip ) {
        global $wpdb;
        return $wpdb->update( "{$wpdb->prefix}ratings", [
            'funnel_id'    => $funnel_id,
            'rating_value' => $rating_value
        ], ['funnel_id' => $funnel_id, 'ip' => $ip] );
    }
}