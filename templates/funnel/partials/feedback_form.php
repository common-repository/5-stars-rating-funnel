<?php
/**
 * Funnel Page Content Controller
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $funnel_settings
 * @var $feedback_fields
 * @var $rratingg_lead
 */

$allowed_tags = array_merge( RRTNGG_Manager::get_allowed_tags(), RRTNGG_Manager::get_fields_allowed_tags() );

?>
<style>
	<?php
	if ( ! empty( $funnel_settings['feedback_button_fill_color'] ) ) {
		?>
	.rrtngg-btn.rrtngg-lg {
		background-color: <?php echo esc_html( $funnel_settings['feedback_button_fill_color'] ); ?>;
	}
		<?php
	}

	if ( ! empty( $funnel_settings['feedback_button_text_color'] ) ) {
		?>
	.rrtngg-btn.rrtngg-lg {
		color: <?php echo esc_html( $funnel_settings['feedback_button_text_color'] ); ?>;
	}
		<?php
	}
	if ( ! empty( $funnel_settings['feedback_button_fill_hover_color'] ) ) {
		?>
	.rrtngg-btn.rrtngg-lg:hover  {
		background-color: <?php echo esc_html( $funnel_settings['feedback_button_fill_hover_color'] ); ?>;
	}
		<?php
	}
	?>
</style>
<form id="rrtng_send_feedback_form">
	<div id="rrtngg_feedback_form_container">
		<div class="rrtngg_feedback_form_inner">
			<div class="rrtngg-row">
				<div class="rrtngg-col-12">
					<?php
					foreach ( $feedback_fields as $field_id => $field ) {
						$value = '';

						if ( 'feedback_lead_id' === $field_id ) {
							$value = $rratingg_lead['ID'];
						}
						if ( 'feedback__funnel_id' === $field_id ) {
							$value = $id;
						}

						echo wp_kses( RRTNGG_Field_Generator::form_field( $field_id, $field, $value ), $allowed_tags );
					}
					?>
				</div>
			</div>
		</div>

		<div class="rrtngg-row">
			<div class="rrtngg-col-12">
				<div style="text-align: center; margin-top: 25px;">
					<button id="rrtng_send_feedback_btn" class="rrtngg-btn rrtngg-lg" type="button"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>>
						<?php esc_html_e( 'Send feedback', '5-stars-rating-funnel' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
