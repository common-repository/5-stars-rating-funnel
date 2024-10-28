<?php


class RRTNGG_License_Manager {
    private static $first_month_invitation_limit = 50;
    private static $invitation_limit             = 10;
    private static $trial_period_limit           = 100;

    private static $trial_period        = 7;
    private static $trial_period_expire = false;

    public static function init() {
        self::set_limitation();
        $is_premium = self::is_premium();
        if ( empty( $is_premium['is_paying'] ) ) {

            RRTNGG_Notices::admin_notice( 'free_limitation' );
        }
    }

    private static function set_limitation() {
        $limitation       = get_option( 'rrtngg_invitation_limit' );
        $limitation_reset = get_option( 'rrtngg_invitation_limit_reset' );
        $now_timestamp    = current_time( 'timestamp' );

        if ( empty( $limitation ) || empty( $limitation_reset ) ) {
            $limitation_reset = $now_timestamp + ( 30 * 24 * 60 * 60 );
            update_option( 'rrtngg_invitation_limit', self::$first_month_invitation_limit );
            update_option( 'rrtngg_invitation_limit_reset', $limitation_reset );
            update_option( 'rrtngg_invitation_limit_counter', 0 );
            update_option( 'rrtngg_feedback_limit_counter', 0 );
        } else {
            if ( ! self::is_premium() && ! self::is_trial() ) {
                if (
                    (int) $now_timestamp > (int) $limitation_reset
                ) {
                    $limitation_reset = $now_timestamp + ( 30 * 24 * 60 * 60 );
                    update_option( 'rrtngg_invitation_limit', self::$invitation_limit );
                    update_option( 'rrtngg_invitation_limit_reset', $limitation_reset );
                    update_option( 'rrtngg_invitation_limit_counter', 0 );
                    update_option( 'rrtngg_feedback_limit_counter', 0 );
                }
            } else {
                if (
                    (int) $now_timestamp < (int) $limitation_reset
                ) {
                    $limitation_reset = $now_timestamp + ( 30 * 24 * 60 * 60 );
                    update_option( 'rrtngg_invitation_limit_reset', $limitation_reset );
                }
            }
        }
    }

    public static function is_premium() {
        if ( ! function_exists( 'rrtngg_fs' ) ) {
            return false;
        }

        $is_paying = rrtngg_fs()->is_paying();
        $is_trial  = self::is_trial();
        $is_pro  = rrtngg_fs()->is_plan('1pro', true);
        $is_agency  = rrtngg_fs()->is_plan('2agency' , true);
        $is_unlimited  = rrtngg_fs()->is_plan('3unlimited', true);

        if ( ! $is_paying && ! $is_trial ) {
            return false;
        }

        return array(
            'is_paying' => $is_paying,
            'is_trial'  => $is_trial,
            'is_pro'  => $is_pro,
            'is_agency'  => $is_agency,
            'is_unlimited'  => $is_unlimited,
        );
    }

    public static function is_trial() {
        if ( ! function_exists( 'rrtngg_fs' ) ) {
            return false;
        }
        $is_trial = rrtngg_fs()->is_trial();

        if ( $is_trial && empty( get_option( 'rrtngg_freemius_trial_applied' ) ) ) {
            update_option( 'rrtngg_freemius_trial_applied', 1 );
        }

        return rrtngg_fs()->is_trial();
    }

    public static function is_trial_available() {
        return empty( get_option( 'rrtngg_freemius_trial_applied' ) );
    }

    public static function get_upgrade_link() {
        if ( ! function_exists( 'rrtngg_fs' ) ) {
            return '';
        }
        return rrtngg_fs()->get_upgrade_url();
    }

    public static function get_trial_link() {
        if ( ! function_exists( 'rrtngg_fs' ) || empty( self::is_trial_available() ) ) {
            return '';
        }
        return rrtngg_fs()->get_trial_url();
    }

    public static function mail_steps( $mail_steps ) {
        if ( self::is_premium() ) {
            return $mail_steps;
        }

        foreach ( $mail_steps as $id => $mail_step ) {
            if ( ! empty( $mail_step['premium'] ) ) {
                $mail_steps[ $id ]['available'] = false;
            }
        }

        return $mail_steps;
    }

    public static function lead_statuses( $lead_statuses ) {
        if ( self::is_premium() ) {
            return $lead_statuses;
        }

        $mail_steps = RRTNGG_Manager::get_mail_steps();

        foreach ( $lead_statuses as $id => $lead_status ) {
            if ( ! empty( $lead_status['next_mail'] ) && empty( $mail_steps[ $lead_status['next_mail'] ]['available'] ) ) {
                unset( $lead_statuses[ $id ]['next_mail'] );
            }
        }

        return $lead_statuses;
    }

    public static function increment_invitation_counter() {
        if ( empty( self::get_limit() ) ) {
            return;
        }

        $invitation_limit_counter = get_option( 'rrtngg_invitation_limit_counter', 0 ) + 1;
        update_option( 'rrtngg_invitation_limit_counter', $invitation_limit_counter );
    }

    public static function get_feedback_limit(){
        return 10;
    }

    public static function get_limit() {
        if ( self::is_trial() ) {
            return self::$trial_period_limit;
        }
        if ( ! self::is_premium() ) {
            return get_option( 'rrtngg_invitation_limit', 0 );
        }

        return false;
    }

    public static function get_first_month_invitation_limit() {
        return self::$first_month_invitation_limit;
    }

    public static function get_invitation_limit() {
        return self::$invitation_limit;
    }

    public static function get_trial_limit() {
        return self::$trial_period_limit;
    }

    public static function get_feedback_counter() {
        return get_option( 'rrtngg_feedback_limit_counter', 0 );
    }

    public static function get_counter() {
        return get_option( 'rrtngg_invitation_limit_counter', 0 );
    }

    public static function is_limit_reached() {
        $limit                    = self::get_limit();
        $invitation_limit_counter = get_option( 'rrtngg_invitation_limit_counter', 0 );

        return ! empty( $limit ) && (int) self::get_limit() <= (int) $invitation_limit_counter;
    }

    public static function is_feedback_limit_reached() {
        $limit                    = self::get_feedback_limit();
        $invitation_limit_counter = get_option( 'rrtngg_feedback_limit_counter', 0 );

        return ! empty( $limit ) && (int) self::get_feedback_limit() <= (int) $invitation_limit_counter;
    }
}
