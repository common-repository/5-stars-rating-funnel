<?php


class RRTNGG_Manager {
    public static function generate_order_id( $id ) {
        return $id . '-' . date( 'dmy', current_time( 'timestamp' ) );
    }

    public static function generate_api_key() {
        $lenght = 13;

        $domain        = rrtngg_get_current_domain_name();
        $now_timestamp = (int) current_time( 'timestamp' );

        if ( function_exists( 'random_bytes' ) ) {
            $bytes = random_bytes( ceil( $lenght / 2 ) );
        } elseif ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
            $bytes = openssl_random_pseudo_bytes( ceil( $lenght / 2 ) );
        }

        if ( ! empty( $bytes ) ) {
            $unique_id = substr( bin2hex( $bytes ), 0, $lenght );
        } else {
            $unique_id = uniqid();
        }

        $key = 'rrtng_' . sha1( $domain . $now_timestamp . $unique_id );

        return $key;
    }

    public static function get_published_fnnels() {
        if ( RRTNGG_License_Manager::is_premium() ) {
            $args = array(
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC',
                'post_type'   => 'rratingg',
                'post_status' => 'publish',
            );

            return get_posts( $args );
        } else {
            $active = RRTNGG_Funnel_CPT::get_active();

            return array( $active );
        }
    }

    public static function get_lead_statuses() {
        $lead_statuses = array(
            'new'                       => array(
                'label'      => __( 'Just Imported', '5-stars-rating-funnel' ),
                'template'   => 'landing',
                'new_status' => 'funnel_visited',
                'next_mail'  => 'invitation_reminder',
            ),
            'invite_sent'               => array(
                'label'      => __( 'Invite Sent', '5-stars-rating-funnel' ),
                'template'   => 'landing',
                'new_status' => 'funnel_visited',
                'next_mail'  => 'invitation_reminder',
            ),
            'funnel_visited'            => array(
                'label'      => __( 'Funnel Page Visited', '5-stars-rating-funnel' ),
                'template'   => 'landing',
                'new_status' => 'funnel_visited',
                'next_mail'  => 'invitation_reminder',
            ),
            'positive_rating_visited'   => array(
                'label'      => __( 'Positive Rating Page Visited', '5-stars-rating-funnel' ),
                'template'   => 'positive',
                'new_status' => 'positive_rating_visited',
                'next_mail'  => 'positive_rating_reminder',
            ),
            'negative_feedback_visited' => array(
                'label'      => __( 'Negative Feedback Page Visited', '5-stars-rating-funnel' ),
                'template'   => 'feedback',
                'new_status' => 'negative_feedback_visited',
                'next_mail'  => 'negative_feedback_reminder',
            ),
            'negative_rating_visited'   => array(
                'label'      => __( 'Negative Rating Page Visited', '5-stars-rating-funnel' ),
                'template'   => 'negative',
                'new_status' => 'negative_rating_visited',
                'next_mail'  => 'no_mail',
            ),
        );

        return apply_filters( 'rrtngg_lead_statuses', $lead_statuses );
    }

    public static function get_mail_steps() {
        $mail_steps = array(
            'invitation'                 => array(
                'label'           => __( 'First Invitation Email', '5-stars-rating-funnel' ),
                'subject'         => __( 'Did we meet your expectations?', '5-stars-rating-funnel' ),
                'default_content' => '',
                'weight'          => 1,
                'available'       => true,
            ),
            'invitation_reminder'        => array(
                'label'           => __( 'Invitation Reminder Email', '5-stars-rating-funnel' ),
                'subject'         => __( 'Were you satisfied with our service/product?', '5-stars-rating-funnel' ),
                'default_content' => '',
                'weight'          => 2,
                'premium'         => true,
                'available'       => true,
            ),
            'positive_rating_reminder'   => array(
                'label'           => __( 'Leave us a review', '5-stars-rating-funnel' ),
                'subject'         => __( 'Do not forget to rate us!!!', '5-stars-rating-funnel' ),
                'default_content' => '',
                'weight'          => 5,
                'premium'         => true,
                'available'       => true,
            ),
            'negative_feedback_reminder' => array(
                'label'           => __( 'Negative Feedback Reminder Email', '5-stars-rating-funnel' ),
                'subject'         => __( 'What can we do better next time?', '5-stars-rating-funnel' ),
                'default_content' => '',
                'weight'          => 5,
                'premium'         => true,
                'available'       => true,
            ),
            'no_mail'                    => array(
                'label'           => __( 'No email will be sent', '5-stars-rating-funnel' ),
                'default_content' => '',
                'available'       => true,
                'weight'          => 10,
            ),
        );

        return apply_filters( 'rrtngg_mail_steps', $mail_steps );
    }

    public static function get_next_mail_statuses() {
        return array(
            'scheduled' => array(),
            'sent'      => array(),
        );
    }

    public static function get_placeholders( $lead = null, $funnel = null ) {
        if ( $lead ) {
            $lead_email      = ! empty( $lead['email'] ) ? $lead['email'] : '';
            $lead_title      = ! empty( $lead['title'] ) ? $lead['title'] : '';
            $lead_first_name = ! empty( $lead['first_name'] ) ? $lead['first_name'] : '';
            $lead_last_name  = ! empty( $lead['last_name'] ) ? $lead['last_name'] : '';

            $lead_full_name_array = array();
            if ( ! empty( $lead_first_name ) ) {
                $lead_full_name_array['first_name'] = $lead_first_name;
            }
            if ( ! empty( $lead_last_name ) ) {
                $lead_full_name_array['last_name'] = $lead_last_name;
            }
            $lead_full_name = implode( ' ', $lead_full_name_array );

            $lead_order_id = ! empty( $lead['order_id'] ) ? $lead['order_id'] : '';
            $funnel_url    = ! empty( $lead['link'] ) ? $lead['link'] : '';
        } else {
            $lead_email      = '';
            $lead_title      = '';
            $lead_first_name = '';
            $lead_last_name  = '';
            $lead_full_name  = '';
            $lead_order_id   = '';
            $funnel_url      = '';
        }

        $placeholders = array(
            'lead_email'          => array(
                'label' => __( 'Lead email', '5-stars-rating-funnel' ),
                'value' => $lead_email,
            ),
            'lead_title'          => array(
                'label' => __( 'Lead title (f.e. Mr, Mrs etc.)', '5-stars-rating-funnel' ),
                'value' => $lead_title,
            ),
            'lead_firstname'      => array(
                'label' => __( 'Lead first name', '5-stars-rating-funnel' ),
                'value' => $lead_first_name,
            ),
            'lead_lastname'       => array(
                'label' => __( 'Lead last name', '5-stars-rating-funnel' ),
                'value' => $lead_last_name,
            ),
            'lead_full_name'      => array(
                'label' => __( 'Lead full name', '5-stars-rating-funnel' ),
                'value' => $lead_full_name,
            ),
            'lead_order_id'       => array(
                'label' => __( 'Lead order ID', '5-stars-rating-funnel' ),
                'value' => $lead_order_id,
            ),
            'lead_funnel_url'     => array(
                'label' => __( 'Funnel URL with lead id in query parameter', '5-stars-rating-funnel' ),
                'value' => $funnel_url,
            ),
            'funnel_url'          => array(
                'label' => __( 'Funnel URL with lead id in query parameter', '5-stars-rating-funnel' ),
                'value' => $funnel_url,
                'hide'  => true,
            ),
            'current_year'        => array(
                'label' => __( 'Just current year', '5-stars-rating-funnel' ),
                'value' => date( 'Y', current_time( 'timestamp' ) ),
            ),
            'current_domain'      => array(
                'label' => __( 'Current site domain name (without http(s):// and www.) - ' . rrtngg_get_current_domain_name(), '5-stars-rating-funnel' ),
                'value' => rrtngg_get_current_domain_name(),
            ),
            'current_domain_link' => array(
                'label' => __( 'Current site link with _blank target - ' . rrtngg_get_current_domain_name(), '5-stars-rating-funnel' ),
                'value' => '<a href="' . get_site_url() . '">' . rrtngg_get_current_domain_name() . '</a>',
            ),
        );

        $services              = RRTNGG_Services_Manager::get_services();
        $services_placeholders = array();

        $funnel_settings = array();

        if ( ! empty( $funnel->funnel_settings ) ) {
            $funnel_settings = $funnel->funnel_settings;
        }

        foreach ( $services as $sid => $sdata ) {
            if ( 'button' !== $sdata['type'] ) {
                continue;
            }

            $surl   = RRTNGG_Services_Manager::get_service_url( $sid, $funnel_settings, $lead );
            $slabel = '';

            if ( ! empty( $surl ) ) {
                if ( ! empty( $funnel_settings[ 'services_' . $sid . '_link_text' ] ) ) {
                    $slabel = $funnel_settings[ 'services_' . $sid . '_link_text' ];
                } else {
                    $slabel = __( 'Rate Us on', '5-stars-rating-funnel' ) . ' ' . $sdata['label'];
                }
            }

            $services_placeholders[ 'review_' . $sid . '_url' ]  = array(
                'label' => '"' . $sdata['label'] . '" ' . __( 'URL Address', '5-stars-rating-funnel' ),
                'value' => $surl,
            );
            $services_placeholders[ 'review_' . $sid . '_link' ] = array(
                'label' => '"' . $sdata['label'] . '" ' . __( 'Link with text from settings', '5-stars-rating-funnel' ),
                'value' => '<a href="' . $surl . '">' . $slabel . '</a>',
                'hide'  => true,
            );
        }

        $placeholders = array_merge( $placeholders, $services_placeholders );

        return $placeholders;
    }

    public static function get_fields_allowed_tags() {
        return array(
            'style'    => array(),
            'input'    => array(
                'id'      => array(),
                'class'   => array(),
                'name'    => array(),
                'type'    => array(),
                'value'   => array(),
                'checked' => array(),
                'step'    => array(),
                'style'   => array(),
            ),
            'select'   => array(
                'id'           => array(),
                'class'        => array(),
                'name'         => array(),
                'value'        => array(),
                'data-control' => array(),
                'style'        => array(),
            ),
            'option'   => array(
                'selected' => array(),
                'value'    => array(),
                'style'    => array(),
            ),
            'table'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'tbody'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'tr'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'th'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
                'scope' => array(),
            ),
            'td'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'div'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'span'     => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'hr'     => array(),
            'link'     => array(
                'id'    => array(),
                'href'  => array(),
                'rel'   => array(),
                'media' => array(),
                'style' => array(),
            ),
            'button'   => array(
                'id'                => array(),
                'class'             => array(),
                'type'              => array(),
                'data-editor'       => array(),
                'data-wp-editor-id' => array(),
                'style'             => array(),
            ),
            'textarea' => array(
                'id'           => array(),
                'class'        => array(),
                'name'         => array(),
                'rows'         => array(),
                'cols'         => array(),
                'autocomplete' => array(),
                'style'        => array(),
            ),
            'p'        => array(
                'style' => array(),
            ),
        );
    }

    public static function get_allowed_tags() {
        return array(
            'a'       => array(
                'href'   => array(),
                'title'  => array(),
                'target' => array(),
            ),
            'abbr'    => array( 'title' => array() ),
            'acronym' => array( 'title' => array() ),
            'code'    => array(),
            'pre'     => array(),
            'em'      => array(),
            'strong'  => array(),
            'div'     => array(),
            'p'       => array(
                'style' => array(),
            ),
            'ul'      => array(),
            'ol'      => array(),
            'li'      => array(),
            'h1'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'h2'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'h3'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'h4'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'h5'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'h6'      => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
            ),
            'img'     => array(
                'src'   => array(),
                'class' => array(),
                'alt'   => array(),
            ),
            'label'   => array(
                'id'    => array(),
                'class' => array(),
                'style' => array(),
                'for' => array(),
            ),
        );
    }
}
