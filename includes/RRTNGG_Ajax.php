<?php


class RRTNGG_Ajax {

	public static function service_rating_count_increase(){
		check_ajax_referer( 'snth_nonce', 'nonce' );
		$user_ip = $_SERVER['REMOTE_ADDR'];
		$funnel_id = $_POST['funnel_id'];
		$restrict_ip_op = Helpers::get_ip_restriction_setting_status();

		// Get the last four digits of the user's IP address
		$last_four_digits = substr($user_ip, -4);
        $already_clicked_users =  get_option( 'rrtngg_feedback_already_users', array() );
		$total_redirect_c       = get_post_meta($funnel_id , 'rrtngg_feedback_btn_count', true);
		$total_redirect_u       = get_post_meta($funnel_id , 'rrtngg_feedback_btn_already_users', true);
        $total_redirect_c       = !empty($total_redirect_c) && $total_redirect_c != '' ? $total_redirect_c : 0;
        $total_redirect_u       = !empty($total_redirect_u) && $total_redirect_u != '' ? $total_redirect_u : array();
        $is_premium = ! empty( RRTNGG_License_Manager::is_premium() );
		if($restrict_ip_op){
			if(!in_array($last_four_digits ,$already_clicked_users ) && !$is_premium ){
				$invitation_limit_counter = get_option( 'rrtngg_feedback_limit_counter', 0 ) + 1;
				$total_redirect_c = $total_redirect_c + 1;
				$already_clicked_users[] = $last_four_digits;
				update_option( 'rrtngg_feedback_limit_counter', $invitation_limit_counter );
				update_option( 'rrtngg_feedback_already_users', $already_clicked_users );
				update_post_meta( $funnel_id, 'rrtngg_feedback_btn_count', $total_redirect_c );
	
				self::success_response( array( 'consoleLog' => __( 'count increase Success'.$user_ip, '5-stars-rating-funnel' ) ) );
	
			}elseif(!in_array($last_four_digits ,$total_redirect_u ) && $is_premium ){
				$total_redirect_u[] = $last_four_digits;
				$total_redirect_c = $total_redirect_c + 1;
				update_post_meta( $funnel_id, 'rrtngg_feedback_btn_count', $total_redirect_c );
				update_post_meta( $funnel_id, 'rrtngg_feedback_btn_already_users', $total_redirect_u );
				self::success_response( array( 'consoleLog' => __( 'post id count increased', '5-stars-rating-funnel' ) ) );
	
			}else{
				self::success_response( array( 'consoleLog' => __( 'user already clicked', '5-stars-rating-funnel' ) ) );
			}
		}else{
			if(!$is_premium ){
				$invitation_limit_counter = get_option( 'rrtngg_feedback_limit_counter', 0 ) + 1;
				$total_redirect_c = $total_redirect_c + 1;
				$already_clicked_users[] = $last_four_digits;
				update_option( 'rrtngg_feedback_limit_counter', $invitation_limit_counter );
				update_post_meta( $funnel_id, 'rrtngg_feedback_btn_count', $total_redirect_c );
	
				self::success_response( array( 'consoleLog' => __( 'count increase Success'.$user_ip, '5-stars-rating-funnel' ) ) );
	
			}elseif( $is_premium ){
				$total_redirect_u[] = $last_four_digits;
				$total_redirect_c = $total_redirect_c + 1;
				update_post_meta( $funnel_id, 'rrtngg_feedback_btn_count', $total_redirect_c );
				self::success_response( array( 'consoleLog' => __( 'post id count increased', '5-stars-rating-funnel' ) ) );
	
			}else{
				self::success_response( array( 'consoleLog' => __( 'user already clicked', '5-stars-rating-funnel' ) ) );
			}
		}
		
	}

	public static function service_rating() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		if (
				empty( $_POST['lead_id'] )
				|| empty( $_POST['step'] )
				|| empty( $_POST['service'] )
		) {
			return;
			// self::error_response(
			// array(
			// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( '1st Cheating, huh!!!', '5-stars-rating-funnel' ),
			// )
			// );
		}

		$lead_id = sanitize_text_field( $_POST['lead_id'] );
		$lead    = RRTNGG_Leads_Model::get_by_id( $lead_id );

		if ( empty( $lead ) ) {
			return;
			// self::error_response(
			// array(
			// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( '2nd Cheating, huh!!!', '5-stars-rating-funnel' ),
			// )
			// );
		}

		$lead_meta = ! empty( $lead['meta'] ) ? unserialize( $lead['meta'] ) : array();
		$lead_meta['services'][ sanitize_text_field( $_POST['service'] ) ] = array(
			'step' => sanitize_text_field( $_POST['step'] ),
		);

		$lead['meta']             = serialize( $lead_meta );
		$lead['next_mail']        = 'no_mail';
		$lead['next_mail_status'] = '';
		$lead['next_mail_at']     = '';

		RRTNGG_Leads_Model::create( $lead );

		self::success_response( array( 'consoleLog' => __( 'Success', '5-stars-rating-funnel' ) ) );
	}

	public static function generate_api_key() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		if ( empty( $_POST['title'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Add short description, please!', '5-stars-rating-funnel' ),
				)
			);
		}

		$title   = sanitize_text_field( $_POST['title'] );
		$keys    = get_option( 'rrtngg_api_keys', array() );
		$new_key = RRTNGG_Manager::generate_api_key();

		$keys[ $new_key ] = $title;

		update_option( 'rrtngg_api_keys', $keys );

		self::success_response(
			array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Key created', '5-stars-rating-funnel' ),
				'reload'  => 1,
			)
		);
	}

	public static function delete_api_key() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', '5-stars-rating-funnel' ) );
		}
		check_ajax_referer( 'snth_nonce', 'nonce' );

		if ( empty( $_POST['key'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		$key  = sanitize_text_field( $_POST['key'] );
		$keys = get_option( 'rrtngg_api_keys', array() );

		if ( ! empty( $keys[ $key ] ) ) {
			unset( $keys[ $key ] );
		}

		update_option( 'rrtngg_api_keys', $keys );

		self::success_response(
			array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Key deleted', '5-stars-rating-funnel' ),
				'reload'  => 1,
			)
		);
	}

	public static function delete_feedbacks() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', '5-stars-rating-funnel' ) );
		}

		check_ajax_referer( 'snth_nonce', 'nonce' );
		if ( empty( $_POST['feedback_ids'] ) || ! is_array( $_POST['feedback_ids'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		$feedback_ids = array_map( 'sanitize_text_field', $_POST['feedback_ids'] );

		foreach ( $feedback_ids as $fi => $feedback_id ) {
			$feedback_exists = RRTNGG_Feedbacks_Model::get_by_id( $feedback_id, 'ID' );

			if ( empty( $feedback_exists ) ) {
				unset( $feedback_id[ $fi ] );
			}
		}

		if ( empty( $feedback_ids ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		RRTNGG_Feedbacks_Model::delete( $feedback_ids );

		if ( count( $feedback_ids ) > 1 ) {
			$message = __( 'Feedbacks deleted', '5-stars-rating-funnel' );
		} else {
			$message = __( 'Feedback deleted', '5-stars-rating-funnel' );
		}

		self::success_response(
			array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . $message,
				'reload'  => 1,
			)
		);
	}

	public static function send_email() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		if ( empty( $_POST['lead_id'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		$lead_id    = sanitize_text_field( $_POST['lead_id'] );
		$email_sent = RRTNGG_Email::send_email_by_lead( $lead_id );

		if ( empty( $email_sent ) || ! empty( $email_sent['error'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Email could not be sent', '5-stars-rating-funnel' ),
				)
			);
		}

		self::success_response(
			array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Email sent', '5-stars-rating-funnel' ),
				'reload'  => 1,
			)
		);
	}

	public static function invite_all() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		$leads = RRTNGG_Leads_Model::get( 'ID, email', "next_mail = 'invitation' AND next_mail_status = 'scheduled'" );

		if ( empty( $leads ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'No leads to be invited', '5-stars-rating-funnel' ),
				)
			);
		}

		$bg_data = array();
		foreach ( $leads as $lead ) {
			$bg_data[ $lead['ID'] ] = $lead['email'];
		}
		$ids = array_keys( $bg_data );

		RRTNGG_BG_Email::run( $ids );

		self::success_response(
			array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Email will be sent in background', '5-stars-rating-funnel' ),
				'reload'  => 1,
			)
		);
	}

	public static function send_feedback() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		$data = self::get_form_data();

		// if ( empty( $data['feedback_lead_id'] ) || empty( $data['feedback_funnel_id'] ) ) {
		// self::error_response(
		// array(
		// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
		// )
		// );
		// }

		$funnel_id = sanitize_text_field( $data['feedback__funnel_id'] );
		$funnel    = RRTNGG_Funnel_CPT::get( $funnel_id );

		// if ( empty( $funnel ) ) {
		// self::error_response(
		// array(
		// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
		// )
		// );
		// }

		$funnel_settings = get_post_meta( $funnel_id, 'rrtngg_funnel_settings', true );
		$lead_id         = sanitize_text_field( $data['feedback_lead_id'] );

		global $rratingg_lead;

		$is_dummy = ! empty( $_POST['is_dummy'] );

		if ( $is_dummy ) {
			$rratingg_lead = RRTNGG_Leads_Model::get_dummy_user( 'negative_feedback_visited', $funnel_id );
		} else {
			$rratingg_lead = $lead = RRTNGG_Leads_Model::get_by_id( $lead_id );
		}

		// if ( empty( $rratingg_lead ) ) {
		// self::error_response(
		// array(
		// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
		// )
		// );
		// }

		// if ( (int) $funnel_id !== (int) $rratingg_lead['funnel_id'] ) {
		// self::error_response(
		// array(
		// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
		// )
		// );
		// }

		// unset( $data['feedback_funnel_id'] );
		unset( $data['feedback_lead_id'] );

		$form_fields       = RRTNGG_Funnel_Template::get_feedback_form_fields( $funnel_settings );
		$validation_errors = array();

		foreach ( $form_fields as $form_field_id => $form_field ) {
			if ( 'hidden' === $form_field['type'] || 'html' === $form_field['type'] ) {
				continue;
			}

			if ( empty( $data[ $form_field_id ] ) ) {
				$validation_errors[ $form_field_id ] = $form_field['label'] . ' ' . __( 'is required', '5-stars-rating-funnel' );
			} else {
				$value = $data[ $form_field_id ];

				if ( 'textarea' === $form_field['type'] ) {
					$value = sanitize_textarea_field( $value );
				} else {
					$value = sanitize_text_field( $value );
				}

				$data[ $form_field_id ] = $value;

				if ( 'email' === $form_field['type'] && empty( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) ) {
					$validation_errors[ $form_field_id ] = $form_field['label'] . ' ' . __( 'must be valid email', '5-stars-rating-funnel' );
				}
			}
		}

		if ( ! empty( $validation_errors ) ) {
			self::error_response(
				array(
					'validation_errors' => $validation_errors,
				)
			);
		}

		$feedback_data = array(
			'lead_id' => $lead_id,
		);

		foreach ( $data as $field_id => $value ) {
			$field_id_array                      = explode( '__', $field_id );
			$feedback_data[ $field_id_array[1] ] = $value;
		}

		$now     = current_time( 'mysql' );
		$now_gmt = current_time( 'mysql', 1 );

		$feedback_data['created_at']     = $now;
		$feedback_data['created_at_gmt'] = $now_gmt;

		$lead_statuses           = RRTNGG_Manager::get_lead_statuses();
		$rratingg_lead['status'] = $lead_statuses['negative_rating_visited']['new_status'];

		if ( ! $is_dummy ) {
			$feedback_id = RRTNGG_Feedbacks_Model::create( $feedback_data );
			RRTNGG_Email::send_admin_feedback_email( $feedback_id );
			if ( ! empty( $lead_id ) ) {
				error_log( 'runned at 304' );
				RRTNGG_Leads_Model::create( $rratingg_lead );
			}
		}

		ob_start();
		?>
		<div id="rrtng_funnel_content">
			<?php RRTNGG_Funnel_Template::template( $funnel_id, $funnel, $funnel_settings ); ?>
		</div>
		<?php
		$funnel_content = ob_get_clean();

		$params = array(
			'fragments' => array(
				'#rrtng_funnel_content' => $funnel_content,
			),
			'reload'    => 0,
		);

		self::success_response( $params );
	}

	public static function get_template() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		$user_ip = $_SERVER['REMOTE_ADDR'];
		// Get the last four digits of the user's IP address
		$user_ip = substr($user_ip, -4);
		if ( empty( $_POST['id'] ) || empty( $_POST['lead_id'] ) || empty( $_POST['step'] ) || empty( $_POST['rating'] )) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!! Post ID', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		$id     = sanitize_text_field( $_POST['id'] );
		$funnel = get_post( $id );

		if ( empty( $funnel ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!! from funner', '5-stars-rating-funnel' ),
				'reload'  => 1,
			);

			self::error_response( $params );
		}

		global $rratingg_lead;

		$is_dummy = ! empty( $_POST['is_dummy'] );

		if ( $is_dummy ) {
			$rratingg_lead = RRTNGG_Leads_Model::get_dummy_user( sanitize_text_field( $_POST['step'] ), $id );
		} else {
			$rratingg_lead = RRTNGG_Leads_Model::get_by_hash( sanitize_text_field( $_POST['lead_id'] ) );

		}

		// if ( empty( $rratingg_lead ) ) {
		// $params = array(
		// 'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!! Empty Rating Lead', '5-stars-rating-funnel' ),
		// 'reload'  => 0,
		// );

		// self::error_response( $params );
		// }

		$lead_statuses = RRTNGG_Manager::get_lead_statuses();

		if ( empty( $lead_statuses[ sanitize_text_field( $_POST['step'] ) ] ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!! Empty Lead Status', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		$rratingg_lead['status'] = $lead_statuses[ sanitize_text_field( $_POST['step'] ) ]['new_status'];
		$funnel_settings         = get_post_meta( $id, 'rrtngg_funnel_settings', true );
		$lead_id                 = sanitize_text_field( wp_unslash( $_POST['lead_id'] ) );
		if ( 'unknown' !== $lead_id && ! $is_dummy ) {
			RRTNGG_Leads_Model::create( $rratingg_lead );
		}
		
			/**
			 * Save Data In wp_rating table
			 */
			$ip 		    = $user_ip;
			error_log('user $ip');
			error_log(var_export($ip , true));
			$rating_value   = isset( $_POST['rating'] ) ? $_POST['rating'] : 0;
			$all_ips        = Helpers::get_users_ips();
			$restrict_ip_op = Helpers::get_ip_restriction_setting_status();
			$all_funnel_ids = Helpers::get_funnel_ids();
			error_log('user all$ip');
			error_log(var_export($all_ips , true));
			if($restrict_ip_op){
				if( ! in_array( $ip, $all_ips ) && ! in_array( $id, $all_funnel_ids ) ) {
					error_log('user $ip 1');
		
						Helpers::insert_rating_data($ip, $id, $rating_value);
					} elseif( in_array( $ip, $all_ips ) && in_array( $id, $all_funnel_ids ) ) {
						$params = array(
							'message' => __( 'Ratings Already given you cannot give rating again.', '5-stars-rating-funnel' ),
							'reload'  => 0,
						);
			
						self::error_response( $params );
					} elseif( in_array( $ip, $all_ips ) && ! in_array( $id, $all_funnel_ids ) ) {
					error_log('user $ip 3');
		
						Helpers::insert_rating_data($ip, $id, $rating_value);
					} elseif( ! in_array( $ip, $all_ips ) && in_array( $id, $all_funnel_ids ) ) {
					error_log('user $ip 4');
		
						Helpers::insert_rating_data($ip, $id, $rating_value);
					}
			}else{
				Helpers::insert_rating_data('', $id, $rating_value);
			}

		$template = ! empty( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : 'landing';

		ob_start();
		?>
		<div id="rrtng_funnel_content">
			<?php RRTNGG_Funnel_Template::template( $id, $funnel, $funnel_settings ); ?>
		</div>
		<?php
		$funnel_content = ob_get_clean();

		$params = array(
			'fragments' => array(
				'#rrtng_funnel_content' => $funnel_content,
			),
			'reload'    => 0,
		);

		self::success_response( $params );
	}

	public static function delete_csv() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', '5-stars-rating-funnel' ) );
		}
		check_ajax_referer( 'snth_nonce', 'nonce' );
		delete_option( 'rrtngg_current_import' );

		$params = array(
			'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Import canceled', '5-stars-rating-funnel' ),
			'reload'  => 1,
		);

		self::success_response( $params );
	}

	public static function upload_csv() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		if ( empty( $_POST['id'] ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		$id = sanitize_text_field( $_POST['id'] );

		$path = get_attached_file( $id );

		$current_import = array(
			'id'             => $id,
			'file_name'      => basename( $path ),
			'file_path'      => $path,
			'fields_mapping' => array(
				'title'      => '',
				'first_name' => '',
				'last_name'  => '',
				'email'      => '',
				'order_id'   => '',
			),
			'status'         => 'uploaded',
		);

		update_option( 'rrtngg_current_import', $current_import );

		$params = array(
			'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . $path,
			'reload'  => 1,
		);

		self::success_response( $params );
	}

	public static function add_single_lead() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', '5-stars-rating-funnel' ) );
		}
		check_ajax_referer( 'snth_nonce', 'nonce' );
		$data = self::get_form_data();

		foreach ( $data as $field => $val ) {
			if ( 'email' === $field ) {
				$data[ $field ] = sanitize_email( $val );
			} else {
				$data[ $field ] = sanitize_text_field( $val );
			}
		}

		if ( empty( $data['email'] ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Email field is required', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		$funnel_id       = self::get_funnel_id( $data );
		$funnel          = self::get_funnel( $funnel_id );
		$funnel_settings = $funnel->funnel_settings;
		$delay           = ! empty( $funnel_settings['invitation_delay'] ) ? (int) $funnel_settings['invitation_delay'] : 4;
		$permalink       = get_permalink( $funnel_id );
		$send_invitation = ! empty( $_POST['send_invitation'] );

		if ( empty( $permalink ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Wrong funnel selected', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		$hash         = sha1( $data['email'] . $funnel_id );
		$existed_lead = RRTNGG_Leads_Model::get_by_hash( $hash );

		if ( ! empty( $existed_lead ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'This email already exists for selected funnel', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		if ( false === strpos( $permalink, '?' ) ) {
			$permalink = $permalink . '?rratingg_id=' . $hash;
		} else {
			$permalink = $permalink . '&rratingg_id=' . $hash;
		}

		$now     = current_time( 'mysql' );
		$now_gmt = current_time( 'mysql', 1 );

		$data['hash']             = $hash;
		$data['status']           = 'new';
		$data['link']             = $permalink;
		$data['created_at']       = $now;
		$data['created_at_gmt']   = $now_gmt;
		$data['updated_at']       = $now;
		$data['updated_at_gmt']   = $now_gmt;
		$data['next_mail']        = 'invitation';
		$data['next_mail_status'] = 'scheduled';

		$now_timestamp = current_time( 'timestamp' );

		if ( $send_invitation ) {
			$data['next_mail_at'] = $now_timestamp - 10;
		} else {
			$data['next_mail_at'] = $now_timestamp + ( $delay * 24 * 60 * 60 );
		}

		$lead_id = RRTNGG_Leads_Model::create( $data );

		if ( empty( $data['order_id'] ) ) {
			$lead             = RRTNGG_Leads_Model::get_by_id( $lead_id );
			$lead['order_id'] = RRTNGG_Manager::generate_order_id( $lead_id );

			RRTNGG_Leads_Model::create( $lead );
		}

		if ( $send_invitation ) {
			RRTNGG_Email::send_email_by_lead( $lead_id );

			$params = array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Lead added', '5-stars-rating-funnel' ),
				'reload'  => 1,
			);
		} else {
			$params = array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Lead added', '5-stars-rating-funnel' ),
				'reload'  => 1,
			);
		}

		self::success_response( $params );
	}

	public static function delete_leads() {
		if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', '5-stars-rating-funnel' ) );
		}
		check_ajax_referer( 'snth_nonce', 'nonce' );
		if ( empty( $_POST['lead_ids'] ) || ! is_array( $_POST['lead_ids'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		$lead_ids = array_map( 'sanitize_text_field', $_POST['lead_ids'] );

		if ( count( $lead_ids ) < 1 ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Select at least one lead to delete', '5-stars-rating-funnel' ),
				)
			);
		}

		foreach ( $lead_ids as $li => $lead_id ) {
			$lead_exists = RRTNGG_Leads_Model::get_by_id( $lead_id, 'ID' );

			if ( empty( $lead_exists ) ) {
				unset( $lead_ids[ $li ] );
			}
		}

		if ( empty( $lead_ids ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Select at least one lead to delete', '5-stars-rating-funnel' ),
				)
			);
		}

		RRTNGG_Leads_Model::delete( $lead_ids );
		RRTNGG_Feedbacks_Model::delete_leads( $lead_ids );

		if ( count( $lead_ids ) > 1 ) {
			$message = __( 'Leads deleted', '5-stars-rating-funnel' );
		} else {
			$message = __( 'Lead deleted', '5-stars-rating-funnel' );
		}

		self::success_response(
			array(
				'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . $message,
				'reload'  => 1,
			)
		);
	}

	public static function import_leads() {
		check_ajax_referer( 'snth_nonce', 'nonce' );
		$data = self::get_form_data();

		foreach ( $data as $field => $val ) {
			$data[ $field ] = sanitize_text_field( $val );
		}

		$funnel_id = self::get_funnel_id( $data );
		$funnel    = self::get_funnel( $funnel_id );

		$permalink       = get_permalink( $funnel_id );
		$funnel_settings = $funnel->funnel_settings;
		$delay           = ! empty( $funnel_settings['invitation_delay'] ) ? (float) $funnel_settings['invitation_delay'] : 4;

		if ( empty( $permalink ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Wrong funnel selected', '5-stars-rating-funnel' ),
					'reload'  => 0,
				)
			);
		}

		$current_csv     = get_option( 'rrtngg_current_import' );
		$send_invitation = ! empty( $_POST['send_invitation'] );

		if (
			! empty( $current_csv['status'] )
			&& ! empty( $current_csv['file_name'] )
			&& ! empty( $current_csv['file_path'] )
			&& 'uploaded' === $current_csv['status']
		) {
			$csv_fields_val = rrtngg_parse_csv_file( $current_csv['file_path'] );

			$now     = current_time( 'mysql' );
			$now_gmt = current_time( 'mysql', 1 );

			if ( ! empty( $csv_fields_val['values'] ) ) {
				$import_data = array();
				foreach ( $csv_fields_val['values'] as $lead ) {
					$lead_data = array(
						'funnel_id' => $funnel_id,
					);

					if ( ! empty( $data['map_email'] ) && ! empty( $lead[ $data['map_email'] ] ) ) {
						$email              = sanitize_email( $lead[ $data['map_email'] ] );
						$hash               = sha1( $email . $funnel_id );
						$lead_data['email'] = strtolower( $email );
						$lead_data['hash']  = $hash;
					} else {
						continue;
					}

					if ( ! empty( $data['map_title'] ) && ! empty( $lead[ $data['map_title'] ] ) ) {
						$title              = sanitize_text_field( $lead[ $data['map_title'] ] );
						$lead_data['title'] = $title;
					}

					if ( ! empty( $data['map_first_name'] ) && ! empty( $lead[ $data['map_first_name'] ] ) ) {
						$first_name              = sanitize_text_field( $lead[ $data['map_first_name'] ] );
						$lead_data['first_name'] = $first_name;
					}

					if ( ! empty( $data['map_last_name'] ) && ! empty( $lead[ $data['map_last_name'] ] ) ) {
						$last_name              = sanitize_text_field( $lead[ $data['map_last_name'] ] );
						$lead_data['last_name'] = $last_name;
					}

					if ( ! empty( $data['map_order_id'] ) && ! empty( $lead[ $data['map_order_id'] ] ) ) {
						$order_id              = sanitize_text_field( $lead[ $data['map_order_id'] ] );
						$lead_data['order_id'] = $order_id;
					}

					if ( false === strpos( $permalink, '?' ) ) {
						$lead_link = $permalink . '?rratingg_id=' . $hash;
					} else {
						$lead_link = $permalink . '&rratingg_id=' . $hash;
					}

					$lead_data['status']           = 'new';
					$lead_data['link']             = $lead_link;
					$lead_data['next_mail']        = 'invitation';
					$lead_data['next_mail_status'] = 'scheduled';

					$now_timestamp = current_time( 'timestamp' );

					if ( $send_invitation ) {
						$lead_data['next_mail_at'] = $now_timestamp - 10;
					} else {
						$lead_data['next_mail_at'] = $now_timestamp + ( $delay * 24 * 60 * 60 );
					}

					$lead_data['created_at']     = $now;
					$lead_data['created_at_gmt'] = $now_gmt;
					$lead_data['updated_at']     = $now;
					$lead_data['updated_at_gmt'] = $now_gmt;

					$import_data[] = $lead_data;
				}

				RRTNGG_BG_Import::run( $import_data );

				delete_option( 'rrtngg_current_import' );

				$params = array(
					'message' => __( 'Success', '5-stars-rating-funnel' ) . ': ' . __( 'Import started in Background', '5-stars-rating-funnel' ),
					'reload'  => 1,
				);

				self::success_response( $params );
			} else {
				$params = array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'No data to import. Upload another csv file', '5-stars-rating-funnel' ),
					'reload'  => 0,
				);

				self::success_response( $params );
			}
		}

		$params = array(
			'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Wrong CSV file, please upload another one', '5-stars-rating-funnel' ),
			'reload'  => 0,
		);

		self::error_response( $params );
	}

	// Helpers
	public static function get_funnel( $funnel_id ) {
		$funnel = RRTNGG_Funnel_CPT::get( $funnel_id );

		if ( empty( $funnel ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		return $funnel;
	}

	public static function get_form_data() {
		if ( empty( $_POST['formData'] ) || ! is_array( $_POST['formData'] ) ) {
			self::error_response(
				array(
					'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Cheating, huh!!!', '5-stars-rating-funnel' ),
				)
			);
		}

		$data = array();

		foreach ( $_POST['formData'] as $form_data ) {
			$data[ sanitize_text_field( $form_data['name'] ) ] = $form_data['value'];
		}

		return $data;
	}

	public static function get_funnel_id( $data ) {
		if ( empty( $data['funnel_id'] ) ) {
			$params = array(
				'message' => __( 'Error', '5-stars-rating-funnel' ) . ': ' . __( 'Select funnel', '5-stars-rating-funnel' ),
				'reload'  => 0,
			);

			self::error_response( $params );
		}

		return sanitize_text_field( $data['funnel_id'] );
	}

	public static function error_response( $params = array() ) {
		$response = array(
			'success' => 0,
			'error'   => 1,
		);

		if ( ! empty( $params ) ) {
			$response = array_merge( $response, $params );
		}

		echo json_encode( $response );
		wp_die();
	}

	public static function success_response( $params = array() ) {
		$response = array(
			'success' => 1,
			'error'   => 0,
		);

		if ( ! empty( $params ) && is_array( $params ) ) {
			$response = array_merge( $response, $params );
		}

		echo json_encode( $response );
		wp_die();
	}
}

add_action( 'wp_ajax_nopriv_rrtng_delete_api_key', array( 'RRTNGG_Ajax', 'delete_api_key' ) );
add_action( 'wp_ajax_rrtng_delete_api_key', array( 'RRTNGG_Ajax', 'delete_api_key' ) );

add_action( 'wp_ajax_nopriv_rrtng_generate_api_key', array( 'RRTNGG_Ajax', 'generate_api_key' ) );
add_action( 'wp_ajax_rrtng_generate_api_key', array( 'RRTNGG_Ajax', 'generate_api_key' ) );

add_action( 'wp_ajax_nopriv_rrtngg_service_count_increase', array( 'RRTNGG_Ajax', 'service_rating_count_increase' ) );
add_action( 'wp_ajax_rrtngg_service_count_increase', array( 'RRTNGG_Ajax', 'service_rating_count_increase' ) );

add_action( 'wp_ajax_nopriv_rrtngg_service_rating', array( 'RRTNGG_Ajax', 'service_rating' ) );
add_action( 'wp_ajax_rrtngg_service_rating', array( 'RRTNGG_Ajax', 'service_rating' ) );

add_action( 'wp_ajax_nopriv_rrtng_invite_all', array( 'RRTNGG_Ajax', 'invite_all' ) );
add_action( 'wp_ajax_rrtng_invite_all', array( 'RRTNGG_Ajax', 'invite_all' ) );

add_action( 'wp_ajax_nopriv_rrtngg_delete_leads', array( 'RRTNGG_Ajax', 'delete_leads' ) );
add_action( 'wp_ajax_rrtngg_delete_leads', array( 'RRTNGG_Ajax', 'delete_leads' ) );

add_action( 'wp_ajax_nopriv_rrtng_delete_feedbacks', array( 'RRTNGG_Ajax', 'delete_feedbacks' ) );
add_action( 'wp_ajax_rrtng_delete_feedbacks', array( 'RRTNGG_Ajax', 'delete_feedbacks' ) );

add_action( 'wp_ajax_nopriv_rrtngg_send_email', array( 'RRTNGG_Ajax', 'send_email' ) );
add_action( 'wp_ajax_rrtngg_send_email', array( 'RRTNGG_Ajax', 'send_email' ) );

add_action( 'wp_ajax_nopriv_rrtngg_send_feedback', array( 'RRTNGG_Ajax', 'send_feedback' ) );
add_action( 'wp_ajax_rrtngg_send_feedback', array( 'RRTNGG_Ajax', 'send_feedback' ) );

add_action( 'wp_ajax_nopriv_rrtngg_get_positive_template', array( 'RRTNGG_Ajax', 'get_template' ) );
add_action( 'wp_ajax_rrtngg_get_positive_template', array( 'RRTNGG_Ajax', 'get_template' ) );

add_action( 'wp_ajax_nopriv_rrtngg_get_negative_template', array( 'RRTNGG_Ajax', 'get_template' ) );
add_action( 'wp_ajax_rrtngg_get_negative_template', array( 'RRTNGG_Ajax', 'get_template' ) );

add_action( 'wp_ajax_nopriv_rrtngg_upload_csv', array( 'RRTNGG_Ajax', 'upload_csv' ) );
add_action( 'wp_ajax_rrtngg_upload_csv', array( 'RRTNGG_Ajax', 'upload_csv' ) );

add_action( 'wp_ajax_nopriv_rrtngg_delete_csv', array( 'RRTNGG_Ajax', 'delete_csv' ) );
add_action( 'wp_ajax_rrtngg_delete_csv', array( 'RRTNGG_Ajax', 'delete_csv' ) );

add_action( 'wp_ajax_nopriv_rrtngg_add_single_lead', array( 'RRTNGG_Ajax', 'add_single_lead' ) );
add_action( 'wp_ajax_rrtngg_add_single_lead', array( 'RRTNGG_Ajax', 'add_single_lead' ) );

add_action( 'wp_ajax_nopriv_rrtngg_import_leads', array( 'RRTNGG_Ajax', 'import_leads' ) );
add_action( 'wp_ajax_rrtngg_import_leads', array( 'RRTNGG_Ajax', 'import_leads' ) );
