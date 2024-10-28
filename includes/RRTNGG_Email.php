<?php


class RRTNGG_Email {
    public static function send( $email, $subject, $body, $headers = array() ) {
        $headers = array_merge(
            array(
                'Content-Type: text/html; charset=UTF-8',
            ),
            $headers
        );

        return wp_mail( $email, $subject, $body, $headers );
    }

    public static function get_email_template( $content = '', $subject = '', $footer = '' ) {
        ob_start();
        include RRTNGG_ABS_PATH . 'templates/emails/email-content.php';
        $email_template = ob_get_clean();

        if ( false !== strpos( $email_template, '{{email_content}}' ) ) {
            $email_template = str_replace( '{{email_content}}', $content, $email_template );
        }

        if ( false !== strpos( $email_template, '{{email_heading}}' ) ) {
            $email_template = str_replace( '{{email_heading}}', $subject, $email_template );
        }

        if ( false !== strpos( $email_template, '{{email_footer}}' ) ) {
            $email_template = str_replace( '{{email_footer}}', $footer, $email_template );
        }

        return $email_template;
    }

    public static function send_admin_feedback_email( $feedback_id ) {
        $feedback = RRTNGG_Feedbacks_Model::get_by_id( $feedback_id );
        if ( empty( $feedback['lead_id'] ) ) {
            return false;
        }

        $lead = RRTNGG_Leads_Model::get_by_id( $feedback['lead_id'] );
        if ( empty( $lead['funnel_id'] ) ) {
            return false;
        }

        $funnel = RRTNGG_Funnel_CPT::get( $lead['funnel_id'] );
        if ( empty( $lead['funnel_id'] ) ) {
            return false;
        }

        $funnel_settings = $funnel->funnel_settings;

        $feedback_admin_email = ! empty( $funnel_settings['feedback_admin_email'] ) ? $funnel_settings['feedback_admin_email'] : '';
        $email                = filter_var( $feedback_admin_email, FILTER_VALIDATE_EMAIL );
        if ( empty( $email ) ) {
            return false;
        }

        $email_content = self::get_admin_feedback_email_content( $lead, $feedback );
        if ( empty( $email_content ) ) {
            return false;
        }

        $subject = sprintf( __( 'Feedback from funnel: %s', '5-stars-rating-funnel' ), $funnel->post_title );

        if ( ! empty( $funnel_settings['feedback_admin_email_subject'] ) ) {
            $subject = sanitize_text_field( $funnel_settings['feedback_admin_email_subject'] );
        }

        $email_body = apply_filters( 'rrtngg_email_body', self::get_email_template( $email_content, $subject ), $lead, $funnel );
        $email_sent = self::send( $email, $subject, $email_body );

        return $email_sent;
    }

    public static function get_admin_feedback_email_content( $lead, $feedback ) {
        ob_start();
        $email      = ! empty( $feedback['email'] ) ? $feedback['email'] : $lead['email'];
        $first_name = ! empty( $feedback['first_name'] ) ? $feedback['first_name'] : $lead['first_name'];
        $last_name  = ! empty( $feedback['last_name'] ) ? $feedback['last_name'] : $lead['last_name'];
        $message    = $feedback['message'];
        ?>
        <p><?php _e( 'Email', '5-stars-rating-funnel' ); ?>: <strong><?php echo esc_html( $email ); ?></strong></p>

        <?php
        if ( ! empty( $first_name ) ) {
            ?>
            <p><?php _e( 'First Name', '5-stars-rating-funnel' ); ?>: <strong><?php echo esc_html( $first_name ); ?></strong></p>
            <?php
        }

        if ( ! empty( $last_name ) ) {
            ?>
            <p><?php _e( 'Last Name', '5-stars-rating-funnel' ); ?>: <strong><?php echo esc_html( $last_name ); ?></strong></p>
            <?php
        }
        ?>

        <p><?php _e( 'Message', '5-stars-rating-funnel' ); ?>:</p>

        <?php echo wp_kses_post( wpautop( esc_html( $message ) ) ); ?>
        <?php
        return ob_get_clean();
    }

    /**
     * @param $lead_id
     *
     * @return false|int|mixed
     */
    public static function send_email_by_lead( $lead_id ) {
        
        $lead = RRTNGG_Leads_Model::get_by_id( $lead_id );

        if ( empty( $lead['funnel_id'] ) || empty( $lead['email'] ) || empty( $lead['next_mail'] ) ) {
            return false;
        }

        $funnel = RRTNGG_Funnel_CPT::get( $lead['funnel_id'] );
        if ( empty( $funnel ) || empty( $funnel->funnel_settings ) ) {
            return false;
        }

        $email_content = self::get_email_content( $lead, $funnel );
        if ( empty( $email_content ) ) {
            return false;
        }
        $mail_steps = RRTNGG_Manager::get_mail_steps();

        if ( empty( $mail_steps[ $lead['next_mail'] ] ) ) {
            return false;
        }

        $next_mail_id    = $lead['next_mail'];
        $funnel_settings = $funnel->funnel_settings;

        if ( 'invitation' === $next_mail_id ) {
            $funnel_settings[ $next_mail_id . '_enabled' ] = 'on';
        }

        if ( empty( $funnel_settings[ $next_mail_id . '_enabled' ] ) || 'on' !== $funnel_settings[ $next_mail_id . '_enabled' ] ) {
            return false;
        }

        $current_mail_step = $mail_steps[ $lead['next_mail'] ];

        if ( ! empty( $current_mail_step['available'] ) ) {
            $current_mail_default_subject = $current_mail_step['subject'];

            $email      = $lead['email'];
            $subject    = ! empty( $funnel_settings[ $next_mail_id . '_subject' ] ) ? $funnel_settings[ $next_mail_id . '_subject' ] : $current_mail_default_subject;
            $email_body = apply_filters( 'rrtngg_email_body', self::get_email_template( $email_content ), $lead, $funnel );

            $email_sent = self::send( $email, $subject, $email_body );

            if ( 'invitation' === $next_mail_id ) {
                RRTNGG_License_Manager::increment_invitation_counter();
            }
        } else {
            $email_sent = true;
        }

        if ( $email_sent ) {
            $now_timestamp = current_time( 'timestamp' );

            if ( ! empty( $current_mail_step['available'] ) ) {
                if ( 'invitation' === $lead['next_mail'] ) {
                    $delay                    = ! empty( $funnel_settings['invitation_reminder_delay'] ) ? (int) $funnel_settings['invitation_reminder_delay'] : 4;
                    $lead['status']           = 'invite_sent';
                    $lead['next_mail']        = 'invitation_reminder';
                    $lead['next_mail_status'] = 'scheduled';
                    $lead['next_mail_at']     = $now_timestamp + ( $delay * 24 * 60 * 60 );
                } else {
                    $lead['next_mail_status'] = 'sent';
                    $lead['next_mail_at']     = $now_timestamp;
                }

                return RRTNGG_Leads_Model::create( $lead );
            }
        } else {
            if ( ! empty( $lead['next_mail_status'] ) && 'sending' === $lead['next_mail_status'] ) {
                RRTNGG_Leads_Model::create(
                    array(
                        'ID'               => $lead['ID'],
                        'next_mail_status' => 'scheduled',
                    )
                );
            }
            return false;
        }
    }

    /**
     * @param $lead
     * @param $funnel
     *
     * @return string
     */
    public static function get_email_content( $lead, $funnel ) {
        if ( empty( $funnel->funnel_settings ) ) {
            return '';
        }
        $next_mail       = $lead['next_mail'];
        $funnel_settings = $funnel->funnel_settings;

        if ( 'invitation' === $next_mail ) {
            $is_enabled = true;
        } else {
            $is_enabled = ! empty( $funnel_settings[ $next_mail . '_enabled' ] );
        }
        if ( ! $is_enabled || empty( $funnel_settings[ $next_mail . '_content' ] ) ) {
            return '';
        }

        $email_content = wp_kses_post( wpautop( $funnel_settings[ $next_mail . '_content' ] ) );

        return apply_filters( 'rrtngg_email_content', $email_content, $lead, $funnel );
    }

    /**
     * @param $email_content
     * @param $lead
     * @param $funnel
     *
     * @return mixed
     */
    public static function replace_placeholders( $email_content, $lead, $funnel ) {
        $placeholders = RRTNGG_Manager::get_placeholders( $lead, $funnel );

        foreach ( $placeholders as $placeholder => $placeholder_data ) {
            if ( false !== strpos( $email_content, '{' . $placeholder . '}' ) ) {
                $email_content = str_replace( '{' . $placeholder . '}', $placeholder_data['value'], $email_content );
            }
        }

        $email_content = str_replace( 'http:// https://', 'https://', $email_content );
        $email_content = str_replace( 'http://https://', 'https://', $email_content );
        $email_content = str_replace( 'https:// https://', 'https://', $email_content );
        $email_content = str_replace( 'https://https://', 'https://', $email_content );

        $email_content = str_replace( 'http:// http://', 'https://', $email_content );
        $email_content = str_replace( 'http://http://', 'https://', $email_content );
        $email_content = str_replace( 'https:// http://', 'https://', $email_content );
        $email_content = str_replace( 'https://http://', 'https://', $email_content );

        return $email_content;
    }
}
