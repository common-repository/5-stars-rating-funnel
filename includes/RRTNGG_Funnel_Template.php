<?php

class RRTNGG_Funnel_Template {
	public static function load_template( $template ) {
		if ( is_singular( 'rratingg' ) ) {
			$template = RRTNGG_ABS_PATH . 'templates/funnel/index.php';
		}

		return $template;
	}

	public static function header() {
		global $rratingg_lead;
		$funnel_id = ! empty( $rratingg_lead['funnel_id'] ) ? $rratingg_lead['funnel_id'] : get_the_ID();
		$funnel    = RRTNGG_Funnel_CPT::get( $funnel_id, empty( $rratingg_lead['is_dummy'] ), ! empty( $rratingg_lead['is_dummy'] ) );
		if ( ! empty( $funnel ) ) {
			$funnel_settings = $funnel->funnel_settings;
		}
		include_once RRTNGG_ABS_PATH . 'templates/funnel/header.php';
	}

	public static function footer() {
		global $rratingg_lead;
		$funnel_id = ! empty( $rratingg_lead['funnel_id'] ) ? $rratingg_lead['funnel_id'] : get_the_ID();
		$funnel    = RRTNGG_Funnel_CPT::get( $funnel_id, empty( $rratingg_lead['is_dummy'] ), ! empty( $rratingg_lead['is_dummy'] ) );
		if ( ! empty( $funnel ) ) {
			$funnel_settings = $funnel->funnel_settings;
		}
		include_once RRTNGG_ABS_PATH . 'templates/funnel/footer.php';
	}

	public static function shortcode( $atts = array() ) {
		$params = shortcode_atts( array( 'id' => '' ), $atts );

		/**
		 * @var int $id
		 */
		extract( $params );

		if ( empty( $id ) ) {
			return '';
		}

		RRTNGG_Leads_Model::set_global_lead_by_hash();

		return self::get_content( $id );
	}

	public static function get_content( $id = null ) {
		ob_start();
		self::content( $id );
		return ob_get_clean();
	}

	public static function content( $id = null ) {
		if ( empty( $id ) ) {
			global $post;
			if ( empty( $post ) || $post->post_type !== 'rratingg' ) {
				return '';
			}
			$id = $post->ID;
		}
		global $rratingg_lead;

		$funnel = RRTNGG_Funnel_CPT::get( $id, empty( $rratingg_lead['is_dummy'] ), ! empty( $rratingg_lead['is_dummy'] ) );

		if ( ! empty( $funnel ) ) {
			$funnel_settings = $funnel->funnel_settings;
			if ( is_singular() && get_post_type() == 'rratingg') {
				error_log('updateing rating count running 2');
		
					$post_id = get_the_ID();
					$visit_count = get_post_meta( $post_id, 'rrating_visit_count', true );
					$visit_count = (int) $visit_count + 1;
					update_post_meta( $post_id, 'rrating_visit_count', $visit_count );
				}
			include_once RRTNGG_ABS_PATH . 'templates/funnel/content.php';
		}else{
			include_once RRTNGG_ABS_PATH . 'templates/funnel/inactive.php';
		}
	}

	public static function get_logo( $id, $funnel, $funnel_settings ) {
		ob_start();
		self::logo( $id, $funnel, $funnel_settings );
		return ob_get_clean();
	}

	public static function logo( $id, $funnel, $funnel_settings ) {
		global $rratingg_lead;

		if ( ! empty( $rratingg_lead ) && (int) $id === (int) $rratingg_lead['funnel_id'] ) {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/logo.php';
		} elseif ( $funnel_settings['public_feedback'] ) {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/logo.php';
		}
	}

	public static function footer_content( $id, $funnel, $funnel_settings ) {
		global $rratingg_lead;

		if ( ! empty( $rratingg_lead ) && (int) $id === (int) $rratingg_lead['funnel_id'] ) {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/footer_content.php';
		} elseif ( $funnel_settings['public_feedback'] ) {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/footer_content.php';
		}
	}

	// public static function footer_content( $id, $funnel, $funnel_settings ) {
	// global $rratingg_lead;

	// if ( ! empty( $rratingg_lead ) && (int) $id === (int) $rratingg_lead['funnel_id'] ) {
	// include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/footer_content.php';
	// }
	// }

	public static function template( $id, $funnel, $funnel_settings, $template = 'landing' ) {
		global $rratingg_lead;

		$lead_statuses = RRTNGG_Manager::get_lead_statuses();
		$lead_status   = ! empty( $rratingg_lead['status'] ) ? $rratingg_lead['status'] : 'new';
		$is_premium = ! empty( RRTNGG_License_Manager::is_premium() );
        $is_feedback_limit_reached = RRTNGG_License_Manager::is_feedback_limit_reached();
		if ( empty( $lead_statuses[ $lead_status ] ) ) {
				$template = 'forbidden';
		} else {
				$lead_status = $lead_statuses[ $lead_status ];
				$template    = $lead_status['template'];
		}

		if ( ! $funnel_settings['public_feedback'] && ( empty( $rratingg_lead ) || (int) $id !== (int) $rratingg_lead['funnel_id'] ) ) {
				$template = 'forbidden';
		}

		if ( ! file_exists( RRTNGG_ABS_PATH . 'templates/funnel/partials/' . $template . '.php' ) ) {
			$template = 'forbidden';
		}

		if ( ! $is_premium &&  $is_feedback_limit_reached ) {
			$template = 'forbidden';
		}

		if ( 'forbidden' !== $template ) {
			$hash     = ! empty( $rratingg_lead['hash'] ) ? $rratingg_lead['hash'] : 'unknown';
			$is_dummy = ! empty( $rratingg_lead['is_dummy'] );
			if ( $is_dummy ) {
				include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/preview_notice.php';
			}
			?>
			<div id="rrtng_funnel_content_lead" data-lead-id="<?php echo esc_attr( $hash ); ?>">
				<?php
				include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/' . $template . '.php';
				?>
			</div>
			<?php
		} else {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/' . $template . '.php';
		}

		if ( ! $funnel_settings['public_feedback'] ) {
			if ( 'forbidden' !== $template && empty( $rratingg_lead['is_dummy'] ) ) {
				if ( $rratingg_lead['status'] !== $lead_status['new_status'] ) {
					$rratingg_lead['status'] = sanitize_text_field( $lead_status['new_status'] );
				}
				$now     = current_time( 'mysql' );
				$now_gmt = current_time( 'mysql', 1 );

				$rratingg_lead['updated_at']     = $now;
				$rratingg_lead['updated_at_gmt'] = $now_gmt;

				if ( ! empty( $lead_status['next_mail'] ) ) {
					$lead_status_next_mail = $lead_status['next_mail']; // Current template email reminder

					$lead_next_mail        = $rratingg_lead['next_mail']; // Current user email reminder
					$lead_next_mail_status = $rratingg_lead['next_mail_status'];
					$lead_next_mail_at     = $rratingg_lead['next_mail_at'];

					if (
					$lead_next_mail !== $lead_status_next_mail
					) {
						$mail_steps                   = RRTNGG_Manager::get_mail_steps();
						$lead_status_next_mail_weight = $mail_steps[ $lead_status_next_mail ]['weight'];
						$lead_next_mail_weight        = $mail_steps[ $lead_next_mail ]['weight'];

						if ( $lead_status_next_mail_weight > $lead_next_mail_weight ) {
							$delay         = ! empty( $funnel_settings[ $lead_status_next_mail . '_delay' ] ) ? (int) $funnel_settings[ $lead_status_next_mail . '_delay' ] : 4;
							$now_timestamp = current_time( 'timestamp' );

							$rratingg_lead['next_mail']        = $lead_status_next_mail;
							$rratingg_lead['next_mail_status'] = 'scheduled';

							$rratingg_lead['next_mail_at'] = $now_timestamp + ( $delay * 24 * 60 * 60 );
						}
					}
				}

				RRTNGG_Leads_Model::create( $rratingg_lead );
			}
		}
	}

	public static function rating_view( $id, $funnel, $funnel_settings ) {
		global $rratingg_lead;
		$rating_view = ! empty( $funnel_settings['rating_view'] ) ? $funnel_settings['rating_view'] : 'stars_rating';
		$is_dummy    = ! empty( $rratingg_lead['is_dummy'] );

		include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/rating_view_' . $rating_view . '.php';
	}

	public static function get_feedback_form_fields( $funnel_settings ) {
		$feedback_fields = array();

		if ( ! empty( $funnel_settings['feedback_first_name_field_enabled'] ) ) {
			$feedback_fields['feedback__first_name'] = array(
				'label'             => __( 'Your First Name', '5-stars-rating-funnel' ),
				'type'              => 'text',
				'classes'           => 'rrtngg-form-control',
				'row_template'      => 'frontend_div_one_col',
				'container_classes' => 'rrtngg-form-group',
			);

			$feedback_fields['feedback__first_name_notice'] = array(
				'type' => 'html',
				'html' => '<p id="feedback__first_name_notice" class="feedback_field_notice"></p>',
			);
		}

		if ( ! empty( $funnel_settings['feedback_last_name_field_enabled'] ) ) {
			$feedback_fields['feedback__last_name'] = array(
				'label'             => __( 'Your Last Name', '5-stars-rating-funnel' ),
				'type'              => 'text',
				'classes'           => 'rrtngg-form-control',
				'row_template'      => 'frontend_div_one_col',
				'container_classes' => 'rrtngg-form-group',
			);

			$feedback_fields['feedback__last_name_notice'] = array(
				'type' => 'html',
				'html' => '<p id="feedback__last_name_notice" class="feedback_field_notice"> </p>',
			);
		} elseif ( ! empty( $funnel_settings['feedback_first_name_field_enabled'] ) ) {
				$feedback_fields['feedback__first_name']['label'] = __( 'Your Name', '5-stars-rating-funnel' );
		}

		if ( ! empty( $funnel_settings['feedback_email_field_enabled'] ) ) {
			$feedback_fields['feedback__email'] = array(
				'label'             => __( 'Your Email', '5-stars-rating-funnel' ),
				'type'              => 'email',
				'classes'           => 'rrtngg-form-control',
				'row_template'      => 'frontend_div_one_col',
				'container_classes' => 'rrtngg-form-group',
			);

			$feedback_fields['feedback__email_notice'] = array(
				'type' => 'html',
				'html' => '<p id="feedback__email_notice" class="feedback_field_notice"> </p>',
			);
		}

		$feedback_fields['feedback__message'] = array(
			// 'label'        => __( 'Your Message', '5-stars-rating-funnel' ),
			'type'         => 'textarea',
			'classes'      => 'rrtngg-form-control',
			'row_template' => 'frontend_div_one_col',
		);

		$feedback_fields['feedback__message_notice'] = array(
			'type' => 'html',
			'html' => '<p id="feedback__message_notice" class="feedback_field_notice"> </p>',
		);

		$feedback_fields['feedback_lead_id'] = array(
			'type' => 'hidden',
		);

		$feedback_fields['feedback__funnel_id'] = array(
			'type' => 'hidden',
		);

		return $feedback_fields;
	}

	public static function feedback_form( $id, $funnel, $funnel_settings ) {
		global $rratingg_lead;
		$is_dummy        = ! empty( $rratingg_lead['is_dummy'] );
		$feedback_fields = self::get_feedback_form_fields( $funnel_settings );

		if ( ! empty( $feedback_fields ) ) {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/feedback_form.php';
		}
	}

	public static function rating_services( $id, $funnel, $funnel_settings ) {
		global $rratingg_lead;
		$is_dummy = ! empty( $rratingg_lead['is_dummy'] );

		$services         = RRTNGG_Services_Manager::get_services();
		$enabled_services = array();

		foreach ( $services as $sid => $service ) {
			if (
				! empty( $service['available'] )
				&& ! empty( $funnel_settings[ 'services_' . $sid . '_enabled' ] )
				&& 'enabled' === $funnel_settings[ 'services_' . $sid . '_enabled' ]
				&& file_exists( RRTNGG_ABS_PATH . 'templates/funnel/partials/services/' . $sid . '.php' )
			) {
				$enabled_services[ $sid ] = $service;
			}
		}

		if ( ! empty( $enabled_services ) ) {
			include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/services.php';
		}
	}
}
