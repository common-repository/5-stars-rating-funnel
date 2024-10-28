<?php


class RRTNGG_Notices {
    private static $notices = array(
        'free_limitation' => array(),
    );
    public static function admin_notice( $notice_id ) {
        add_action( 'admin_notices', 'RRTNGG_Notices::' . $notice_id );
    }

    public static function free_limitation() {
        $is_trial             = RRTNGG_License_Manager::is_trial();
        $upgrade_link         = RRTNGG_License_Manager::get_upgrade_link();
        $trial_link           = RRTNGG_License_Manager::get_trial_link();
        $is_limit_reached     = RRTNGG_License_Manager::is_feedback_limit_reached();
        $limit                = RRTNGG_License_Manager::get_feedback_limit();
        $counter              = RRTNGG_License_Manager::get_feedback_counter();
        $trial_limit          = RRTNGG_License_Manager::get_trial_limit();
        $invitation_available = $limit - $counter;
        $trial_available      = $trial_limit - $counter;

        $invitation_limit             = RRTNGG_License_Manager::get_invitation_limit();
        $first_month_invitation_limit = RRTNGG_License_Manager::get_first_month_invitation_limit();

        $limit_type = 'free_limit';

        if ( $is_trial ) {
            $limit_type = 'trial_limit';
        } elseif ( (int) $limit > (int) $invitation_limit || (int) $limit === (int) $first_month_invitation_limit ) {
            $limit_type = 'first_month_limit';
        }

        $message = '';

        if ( $is_trial ) {
            $plan = 'Trial';
            $days = 7;
        } else {
            $plan = 'Free';
            $days = 30;

            $limitation_reset = get_option( 'rrtngg_invitation_limit_reset' );
            $now_timestamp    = current_time( 'timestamp' );
            $days_left        = ceil( ( $limitation_reset - $now_timestamp ) / ( 60 * 60 * 24 ) );
        }

        if ( ! $is_limit_reached ) {
            if ( ! empty( $days_left ) ) {
                $msg = sprintf( __( 'You still have %1$s out of %2$s feedbacks left in the next %3$s days.', '5-stars-rating-funnel' ), $invitation_available, $limit, $days_left, $plan );
            } else {
                $msg = sprintf( __( 'You still have %1$s out of %2$s feedbacks left in the next %3$s days..', '5-stars-rating-funnel' ), $invitation_available, $limit, $days, $plan );
            }

            if ( $limit_type === 'first_month_limit' ) {
                $msg = sprintf( __( 'You still have %1$s out of %2$s feedbacks left in the next %3$s days.', '5-stars-rating-funnel' ), $invitation_available, $limit, $days_left, $plan, $invitation_limit );
            }

            $message .= $msg;
        } else {
            $message .= sprintf( __( 'You reached limit of %1$s feedbacks per %2$s days available in your %3$s plan.', '5-stars-rating-funnel' ), $limit, $days, $plan );
        }

        $message .= ' ' . sprintf( __( '%1$sUpgrade now%2$s to unlock all premium features and unlimited feedback (clicks on a referral button).', '5-stars-rating-funnel' ), '<a class="button button-small button-primary" href="' . esc_url( $upgrade_link ) . '" target="_blank">', '</a>' );

        ?>
        <div class="notice notice-info">
            <p>
                <strong><?php echo RRTNGG_Core::get_plugin_name(); ?> - <?php echo esc_html( $plan ) . ' ' . __( 'plan', '5-stars-rating-funnel' ); ?>:</strong>
                <?php echo wp_kses( $message, RRTNGG_Manager::get_allowed_tags() ); ?>
            </p>
        </div>
        <?php
    }
}
