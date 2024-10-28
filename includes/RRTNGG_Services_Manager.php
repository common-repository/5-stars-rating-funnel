<?php
class RRTNGG_Services_Manager {
	public static function get_services() {
		ob_start();
		?>
		<p>
			<?php
			echo sprintf(
				__( 'Do not have TrustedShops account? You can %1$sget one here%2$s!', '5-stars-rating-funnel' ),
				'<a href="https://checkout.trustedshops.com/?a_aid=009FONUKQKP9NSUBEMU6" target="_blank">',
				'</a>'
			);
			?>
		</p>
		<?php
		$trustedshops_description = ob_get_clean();
		$services                 = array(
			'mybusiness'      => array(
				'label'              => __( 'Google MyBusiness', '5-stars-rating-funnel' ),
				'type'               => 'button',
				'available'          => true,
				'defaults'           => array(
					'btn_color'      => '',
					'btn_text'       => __( '>> Rate us on Google <<', '5-stars-rating-funnel' ),
					'btn_text_color' => '',
					'below_btn_text' => __( 'Google-Account is needed', '5-stars-rating-funnel' ),
				),
				'short_descriptions' => array(
					'btn_color'      => '',
					'btn_text'       => '',
					'btn_text_color' => '',
					'below_btn_text' => '',
				),
				'fields'             => array(
					'link' => array(
						'label'       => __( 'Place ID (required)', '5-stars-rating-funnel' ),
						'type'        => 'text',
						'description' => rrtngg_get_service_mybusiness_link_description(),
					),
				),
			),
			'trustedshops'    => array(
				'label'              => __( 'Trustedshops', '5-stars-rating-funnel' ),
				'description'        => $trustedshops_description,
				'type'               => 'button',
				'available'          => true,
				'premium'            => false,
				'defaults'           => array(
					'btn_color'      => '',
					'btn_text'       => __( '>> Rate us on TrustedShops <<', '5-stars-rating-funnel' ),
					'btn_text_color' => '',
					'below_btn_text' => __( 'No account needed', '5-stars-rating-funnel' ),
				),
				'short_descriptions' => array(
					'btn_color'      => '',
					'btn_text'       => '',
					'btn_text_color' => '',
					'below_btn_text' => '',
				),
				'fields'             => array(
					'shop_id' => array(
						'label' => __( 'TrustedShops Id', '5-stars-rating-funnel' ),
						'type'  => 'text',
					),
				),
			),
			'custom_link'     => array(
				'label'              => __( 'Custom Link', '5-stars-rating-funnel' ),
				'type'               => 'button',
				'available'          => true,
				'defaults'           => array(
					'btn_color'      => '',
					'btn_text'       => __( '>> Rate us on Facebook <<', '5-stars-rating-funnel' ),
					'btn_text_color' => '',
					'below_btn_text' => __( 'Account is needed', '5-stars-rating-funnel' ),
				),
				'short_descriptions' => array(
					'btn_color'      => '',
					'btn_text'       => '',
					'btn_text_color' => '',
					'below_btn_text' => '',
				),
				'fields'             => array(
					'link' => array(
						'label'   => __( 'Set custom link (required)', '5-stars-rating-funnel' ),
						'default' => __( 'https://facebook.com/YourPageName/reviews/', '5-stars-rating-funnel' ),
						'type'    => 'text',
					),
				),
			),
			'custom_link_pro' => array(
				'label'              => __( 'Additional Custom Link', '5-stars-rating-funnel' ),
				'type'               => 'button',
				'available'          => true,
				'premium'            => false,
				'defaults'           => array(
					'btn_color'      => '',
					'btn_text'       => __( '>> Rate us on TrustPilot <<', '5-stars-rating-funnel' ),
					'btn_text_color' => '',
					'below_btn_text' => __( 'No account needed', '5-stars-rating-funnel' ),
				),
				'short_descriptions' => array(
					'btn_color'      => '',
					'btn_text'       => '',
					'btn_text_color' => '',
					'below_btn_text' => '',
				),
				'fields'             => array(
					'link' => array(
						'label'   => __( 'Set custom link (required)', '5-stars-rating-funnel' ),
						'default' => __( 'YourTrustPilotInvitationLink', '5-stars-rating-funnel' ),
						'type'    => 'text',
					),
				),
			),
			'goggle_review'   => array(
				'label'     => __( 'Google Review Survey', '5-stars-rating-funnel' ),
				'type'      => 'code',
				'available' => true,
				'premium'   => false,
				'fields'    => array(
					'merchant_id'      => array(
						'label'       => __( 'Merchant ID (required)', '5-stars-rating-funnel' ),
						'type'        => 'text',
						'description' => rrtngg_get_service_goggle_review_merchant_id_description(),
					),
					'delay'            => array(
						'label'       => __( 'Delay for rating (days)', '5-stars-rating-funnel' ),
						'type'        => 'number',
						'description' => __( '1 day minimal', '5-stars-rating-funnel' ),
					),
					'delivery_country' => array(
						'label'       => __( 'Country code (required)', '5-stars-rating-funnel' ),
						'type'        => 'text',
						'description' => rrtngg_get_service_goggle_review_delivery_country_description(),
					),
				),
			),
		);

		return apply_filters( 'rrtngg_mail_steps', $services );

		// return $services;
	}

	public static function get_service_url( $service_id, $funnel_settings, $lead = array() ) {
		$src = '';

		switch ( $service_id ) {
			case 'mybusiness':
				if ( ! empty( $funnel_settings['services_mybusiness_field_link'] ) ) {
					$place_id = sanitize_text_field( $funnel_settings['services_mybusiness_field_link'] );
					$hl       = '';
					$locale   = get_locale();
					if ( ! empty( $locale ) && is_string( $locale ) ) {
						$locale = str_replace( '_', '-', $locale );
						$hl     = 'hl=' . $locale . '&';
					}
					$src = 'http://search.google.com/local/writereview?' . $hl . 'placeid=' . $place_id;
				}
				break;
			case 'trustedshops':
				if ( ! empty( $funnel_settings['services_trustedshops_field_shop_id'] ) && ! empty( $lead['email'] ) ) {
					$shop_id     = sanitize_text_field( $funnel_settings['services_trustedshops_field_shop_id'] );
					$buyerEmail  = base64_encode( $lead['email'] );
					$order_id    = $order_id = ! empty( $lead['order_id'] ) ? $lead['order_id'] : (int) $lead['ID'] . date( 'mY', current_time( 'timestamp' ) );
					$shopOrderID = base64_encode( $order_id );

					$src = 'https://www.trustedshops.de/bewertung/bewerten_' . $shop_id . '.html&buyerEmail=' . $buyerEmail . '&shopOrderID=' . $shopOrderID . '&channel=cmF0ZW5vd2J1dHRvbg';
				}
				break;
			case 'custom_link':
				if ( ! empty( $funnel_settings['services_custom_link_field_link'] ) ) {
					$src = $funnel_settings['services_custom_link_field_link'];
				}
				break;
			case 'custom_link_pro':
				if ( ! empty( $funnel_settings['services_custom_link_pro_field_link'] ) ) {
					$src = $funnel_settings['services_custom_link_pro_field_link'];
				}
				break;
		}

		return $src;
	}
}
