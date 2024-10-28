<?php


class RRTNGG_Cron {
    private $interval = 5 * 60;

    public function __construct() {
        $this->add_filter();
        $this->add_action();
    }

    /**
     * Add Cron Filter
     *
     * @access private
     * @return void
     */
    private function add_filter() {
        add_filter( 'cron_schedules', array( $this, 'cron_time_intervals' ) );
    }

    /**
     * Add new schedule for WordPress cron
     *
     * @access public
     * @return array
     */
    public function cron_time_intervals( $schedules ) {
        $schedules['five_minutes'] = array(
            'interval' => $this->interval,
            'display'  => __( 'Every 5 minutes', '5-stars-rating-funnel' ),
        );

        return $schedules;
    }

    /**
     * Set the schedule hooks
     *
     * @access public
     * @return void
     */
    public function set_schedule_hook() {
        if ( ! wp_next_scheduled( 'rrtngg_send_scheduled_emails' ) ) {
            wp_schedule_event( time(), 'five_minutes', 'rrtngg_send_scheduled_emails' );
        }
    }

    /**
     * Add the WP Cron Action
     *
     * @access private
     * @return void
     */
    private function add_action() {
        add_action( 'rrtngg_send_scheduled_emails', array( $this, 'send_scheduled_emails' ) );
    }

    /**
     * Execute plugin cron jobs
     *
     * Debug cron with the following url
     * http://[URL]/wp-cron.php?doing_cron
     *
     * @return void
     */
    public function send_scheduled_emails() {
        $leads = RRTNGG_Leads_Model::get( 'ID', "next_mail_status = 'sending'" );

        if (!empty($leads)) {
            global $wpdb;
            $sql = "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '%rrtngg_email_background%'";
            $result = $wpdb->get_results($sql);

            if (empty($result)) {
                $table_name = $wpdb->prefix . 'rratingg_leads';
                $sql_update = "UPDATE {$table_name} SET next_mail_status = 'scheduled' WHERE next_mail_status = 'sending'";
                $wpdb->query($wpdb->prepare($sql_update));
            }
        }

        $now_timestamp = (int) current_time( 'timestamp' );

        $leads = RRTNGG_Leads_Model::get( 'ID, email, next_mail, next_mail_status', "next_mail_status = 'scheduled' AND next_mail_at < {$now_timestamp}" );

        if ( empty( $leads ) ) {
            return;
        }
        $bg_data = array();

        foreach ( $leads as $lead ) {
            $bg_data[ $lead['ID'] ] = $lead['email'];
            RRTNGG_Leads_Model::create(
                array(
                    'ID'               => $lead['ID'],
                    'next_mail_status' => 'sending',
                )
            );
        }

        $ids = array_keys( $bg_data );

        RRTNGG_BG_Email::run( $ids );
    }
}
