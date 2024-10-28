<?php


class RRTNGG_Rest extends WP_REST_Controller {
    protected $namespace;

    private $version = '1';

    public function __construct() {
        $this->namespace = 'rrtngg/v' . $this->version;
        $this->rest_base = 'events';
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/import',
            array(
                array(
                    'methods'             => array( WP_REST_Server::READABLE ),
                    'callback'            => array( $this, 'import_lead' ),
                    'permission_callback' => array( $this, 'permissions_check' ),
                    'args'                => array(
                        'funnel_id'   => array(
                            'required' => true,
                            'type'     => 'integer',
                        ),
                        'email'       => array(
                            'required' => true,
                            'type'     => 'string',
                            'format'   => 'email',
                        ),
                        'just_import' => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'title'       => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'first_name'  => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'last_name'   => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'order_id'    => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                    ),
                ),
                array(
                    'methods'             => array( WP_REST_Server::CREATABLE ),
                    'callback'            => array( $this, 'import_lead' ),
                    'permission_callback' => array( $this, 'permissions_check_post' ),
                    'args'                => array(
                        'funnel_id'   => array(
                            'required' => true,
                            'type'     => 'integer',
                        ),
                        'email'       => array(
                            'required' => true,
                            'type'     => 'string',
                            'format'   => 'email',
                        ),
                        'just_import' => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'title'       => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'first_name'  => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'last_name'   => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                        'order_id'    => array(
                            'required' => false,
                            'type'     => 'string',
                        ),
                    ),
                ),
            )
        );
    }

    public function import_lead( $request ) {
        $funnel_id = $request->get_param( 'funnel_id' );
        $email     = $request->get_param( 'email' );

        $funnel = RRTNGG_Funnel_CPT::get( $funnel_id );

        if ( empty( $funnel ) ) {
            return new WP_REST_Response(
                array(
                    'message' => __( 'Funnel was not found', '5-stars-rating-funnel' ),
                ),
                404
            );
        }

        $hash         = sha1( $email . $funnel_id );
        $existed_lead = RRTNGG_Leads_Model::get_by_hash( $hash );

        if ( ! empty( $existed_lead ) ) {
            return new WP_REST_Response(
                array(
                    'message' => __( 'This email already exists for selected funnel', '5-stars-rating-funnel' ),
                ),
                400
            );
        }

        $send_invitation = empty( $request->get_param( 'just_import' ) );
        $permalink       = get_permalink( $funnel_id );
        $funnel_settings = $funnel->funnel_settings;
        $delay           = ! empty( $funnel_settings['invitation_delay'] ) ? (int) $funnel_settings['invitation_delay'] : 4;

        if ( false === strpos( $permalink, '?' ) ) {
            $permalink = $permalink . '?rratingg_id=' . $hash;
        } else {
            $permalink = $permalink . '&rratingg_id=' . $hash;
        }

        $now     = current_time( 'mysql' );
        $now_gmt = current_time( 'mysql', 1 );

        $lead_data = array(
            'funnel_id'        => $funnel_id,
            'email'            => $email,
            'status'           => 'new',
            'hash'             => $hash,
            'link'             => $permalink,
            'next_mail'        => 'invitation',
            'next_mail_status' => 'scheduled',
            'created_at'       => $now,
            'created_at_gmt'   => $now_gmt,
            'updated_at'       => $now,
            'updated_at_gmt'   => $now_gmt,
        );

        if ( ! empty( $request->get_param( 'title' ) ) ) {
            $lead_data['title'] = sanitize_text_field( $request->get_param( 'title' ) );
        }
        if ( ! empty( $request->get_param( 'first_name' ) ) ) {
            $lead_data['first_name'] = sanitize_text_field( $request->get_param( 'first_name' ) );
        }
        if ( ! empty( $request->get_param( 'last_name' ) ) ) {
            $lead_data['last_name'] = sanitize_text_field( $request->get_param( 'last_name' ) );
        }
        if ( ! empty( $request->get_param( 'order_id' ) ) ) {
            $lead_data['order_id'] = sanitize_text_field( $request->get_param( 'order_id' ) );
        }

        $now_timestamp = current_time( 'timestamp' );

        if ( $send_invitation ) {
            $lead_data['next_mail_at'] = $now_timestamp - 10;
        } else {
            $lead_data['next_mail_at'] = $now_timestamp + ( $delay * 24 * 60 * 60 );
        }

        $lead_id = RRTNGG_Leads_Model::create( $lead_data );

        if ( empty( $lead_data['order_id'] ) ) {
            $lead             = RRTNGG_Leads_Model::get_by_id( $lead_id );
            $lead['order_id'] = RRTNGG_Manager::generate_order_id( $lead_id );
            RRTNGG_Leads_Model::create( $lead );
        }

        if ( $send_invitation ) {
            RRTNGG_Email::send_email_by_lead( $lead_id );
        }

        return new WP_REST_Response( 'OK', 200 );
    }

    public function permissions_check_post( $request ) {
        if ( empty( RRTNGG_License_Manager::is_premium() ) ) {
            return false;
        }

        $authorization = $request->get_header( 'Authorization' );
        if ( empty( $authorization ) ) {
            $authorization = ! empty( $request->get_param( 'token' ) ) ? $request->get_param( 'token' ) : '';
        }
        if ( empty( $authorization ) ) {
            return false;
        }
        $keys = get_option( 'rrtngg_api_keys', array() );
        if ( empty( $keys ) ) {
            return false;
        }
        if ( empty( $keys[ $authorization ] ) ) {
            return false;
        }

        return true;
    }

    public function permissions_check( $request ) {
        if ( empty( RRTNGG_License_Manager::is_premium() ) ) {
            return false;
        }

        $authorization = $request->get_header( 'Authorization' );
        if ( empty( $authorization ) ) {
            $authorization = ! empty( $request->get_param( 'token' ) ) ? $request->get_param( 'token' ) : '';
        }
        if ( empty( $authorization ) ) {
            return false;
        }
        $keys = get_option( 'rrtngg_api_keys', array() );
        if ( empty( $keys ) ) {
            return false;
        }
        if ( empty( $keys[ $authorization ] ) ) {
            return false;
        }

        return true;
    }
}
