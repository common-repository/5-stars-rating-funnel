<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Async_Request', false ) ) {
    include_once RRTNGG_ABS_PATH . 'includes/background/wp-background-processing/classes/wp-async-request.php';
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
    include_once RRTNGG_ABS_PATH . 'includes/background/wp-background-processing/classes/wp-background-process.php';
}

abstract class Abstract_RRTNGG_BG extends WP_Background_Process {

    /**
     * Is queue empty.
     *
     * @return bool
     */
    protected function is_queue_empty() {
        global $wpdb;
        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';
        $sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s", $key );

        if ( is_multisite() ) {
            $sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s", $key );
        }

        $count = $wpdb->get_var( $sql ); // @codingStandardsIgnoreLine.

        return ! ( $count > 0 );
    }

    /**
     * Get batch.
     *
     * @return stdClass Return the first batch from the queue.
     */
    protected function get_batch() {
        global $wpdb;

        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';
        $column       = 'option_name';
        $value_column = 'option_value';
        $sql = $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s ORDER BY option_id ASC LIMIT 1", $key );

        if ( is_multisite() ) {
            $column       = 'meta_key';
            $value_column = 'meta_value';
            $sql = $wpdb->prepare( "SELECT * FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s ORDER BY meta_id ASC LIMIT 1", $key );
        }

        $query = $wpdb->get_row( $sql );

        $batch       = new stdClass();
        $batch->key  = $query->$column;
        $batch->data = array_filter( (array) maybe_unserialize( $query->$value_column ) );

        return $batch;
    }

    /**
     * See if the batch limit has been exceeded.
     *
     * @return bool
     */
    protected function batch_limit_exceeded() {
        return $this->time_exceeded() || $this->memory_exceeded();
    }

    /**
     * Handle.
     *
     * Pass each queue item to the task handler, while remaining
     * within server memory and time limit constraints.
     */
    protected function handle() {
        $this->lock_process();

        do {
            $batch = $this->get_batch();

            $count = count( $batch->data );

            foreach ( $batch->data as $key => $value ) {
                $task = $this->task( $value );

                if ( false !== $task ) {
                    $batch->data[ $key ] = $task;
                } else {
                    unset( $batch->data[ $key ] );
                }

                if ( $this->batch_limit_exceeded() ) {
                    // Batch limits reached.
                    break;
                }
            }

            // Update or delete current batch.
            if ( ! empty( $batch->data ) ) {
                $this->update( $batch->key, $batch->data );
            } else {
                $this->delete( $batch->key );
            }
        } while ( ! $this->batch_limit_exceeded() && ! $this->is_queue_empty() );

        $this->unlock_process();

        // Start next batch or complete process.
        if ( ! $this->is_queue_empty() ) {
            $this->dispatch();
        } else {
            $this->complete();
        }
    }

    /**
     * Get memory limit.
     *
     * @return int
     */
    protected function get_memory_limit() {
        if ( function_exists( 'ini_get' ) ) {
            $memory_limit = ini_get( 'memory_limit' );
        } else {
            // Sensible default.
            $memory_limit = '128M';
        }

        if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
            // Unlimited, set to 32GB.
            $memory_limit = '32000M';
        }

        return intval( $memory_limit ) * 1024 * 1024;
    }

    /**
     * Schedule cron healthcheck.
     *
     * @param array $schedules Schedules.
     * @return array
     */
    public function schedule_cron_healthcheck( $schedules ) {
        $interval = apply_filters( $this->identifier . '_cron_interval', 5 );

        if ( property_exists( $this, 'cron_interval' ) ) {
            $interval = apply_filters( $this->identifier . '_cron_interval', $this->cron_interval );
        }

        // Adds every 5 minutes to the existing schedules.
        $schedules[ $this->identifier . '_cron_interval' ] = array(
            'interval' => MINUTE_IN_SECONDS * $interval,
            /* translators: %d: interval */
            'display'  => sprintf( __( 'Every %d minutes', 'woocommerce' ), $interval ),
        );

        return $schedules;
    }

    /**
     * Delete all batches.
     *
     * @return Abstract_RRTNGG_BG
     */
    public function delete_all_batches() {
        global $wpdb;

        $key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';
        $sql = $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $key );

        if ( is_multisite() ) {
            $sql = $wpdb->prepare( "DELETE FROM {$wpdb->sitemeta} WHERE option_name LIKE %s", $key );
        }

        $wpdb->query( $sql );

        return $this;
    }

    /**
     * Kill process.
     *
     * Stop processing queue items, clear cronjob and delete all batches.
     */
    public function kill_process() {
        if ( ! $this->is_queue_empty() ) {
            $this->delete_all_batches();
            wp_clear_scheduled_hook( $this->cron_hook_identifier );
        }
    }
}
