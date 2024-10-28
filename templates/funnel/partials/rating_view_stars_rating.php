<?php
/**
 * Funnel Page Landing Stars Rating View Template
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $funnel_settings
 * @var $rratingg_lead
 * @var $is_preview
 */

$is_premium                = ! empty( RRTNGG_License_Manager::is_premium() );
$is_rating_require_confirm = ! empty( $funnel_settings['rating_require_confirm'] );

if ( ! $is_premium ) {
	$stars_rating_like_unlike = 'five_four_stars';
} else {
	$stars_rating_like_unlike = ! empty( $funnel_settings['stars_rating_like_unlike'] ) ? $funnel_settings['stars_rating_like_unlike'] : 'five_four_stars';
}
?>
<style>
	<?php
	if ( ! empty( $funnel_settings['stars_rating_fill_color'] ) ) {
		?>
	.rrtng_stars_rating_button_holder .rrtng_stars_rating_button i.icon-rrtngg-star-fill {
		color: <?php echo esc_html( $funnel_settings['stars_rating_fill_color'] ); ?>;
	}
	.rrtng_stars_rating_button_holder .rrtng_stars_rating_button:hover, .rrtng_stars_rating_button_holder .rrtng_stars_rating_button.active  {
		border-color: <?php echo esc_html( $funnel_settings['stars_rating_fill_color'] ); ?>;
	}
		<?php
	}

	if ( ! empty( $funnel_settings['stars_rating_empty_color'] ) ) {
		?>
	.rrtng_stars_rating_button_holder .rrtng_stars_rating_button i.icon-rrtngg-star-empty {
		color: <?php echo esc_html( $funnel_settings['stars_rating_empty_color'] ); ?>;
	}
		<?php
	}
	?>
</style>

<div id="rrtng_stars_rating_buttons_container" class="<?php echo $is_rating_require_confirm ? 'rating_require_confirm' : 'rating_no_confirm'; ?>">
	<div class="rrtngg-row">
		<div class="rrtngg-col-12 rrtng_stars_rating_button_holder">
			<div
					class="rrtng_stars_rating_button rrtng_rating_button rrtngg_rating_positive_button"
					data-rating="5"
					data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
			>
				<i  class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i>
			</div>
		</div>

		<div class="rrtngg-col-12 rrtng_stars_rating_button_holder">
			<div
				data-rating="4"
				class="rrtng_stars_rating_button rrtng_rating_button <?php echo 'five_four_stars' === $stars_rating_like_unlike ? 'rrtngg_rating_positive_button' : 'rrtngg_rating_negative_button'; ?>"
				data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
			>
				<i  class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-empty"></i>
			</div>
		</div>

		<div class="rrtngg-col-12 rrtng_stars_rating_button_holder">
			<div class="rrtng_stars_rating_button rrtng_rating_button rrtngg_rating_negative_button" data-rating="3"	data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
			>
				<i  class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-empty"></i><i
					class="icon-rrtngg-star-empty"></i>
			</div>
		</div>

		<div class="rrtngg-col-12 rrtng_stars_rating_button_holder">
			<div class="rrtng_stars_rating_button rrtng_rating_button rrtngg_rating_negative_button" data-rating="2" data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
			>
				<i  class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-empty"></i><i
					class="icon-rrtngg-star-empty"></i><i
					class="icon-rrtngg-star-empty"></i>
			</div>
		</div>

		<div class="rrtngg-col-12 rrtng_stars_rating_button_holder">
			<div class="rrtng_stars_rating_button rrtng_rating_button rrtngg_rating_negative_button" data-rating="1" data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
			>
				<i  class="icon-rrtngg-star-fill"></i><i
					class="icon-rrtngg-star-empty"></i><i
					class="icon-rrtngg-star-empty"></i><i
					class="icon-rrtngg-star-empty"></i><i
					class="icon-rrtngg-star-empty"></i>
			</div>
		</div>
	</div>

	<?php
	if ( $is_rating_require_confirm ) {
		?>
		<div id="rrtng_confirm_btn_container" class="rrtngg-row rating_no_confirm" style="display: none;">
			<div class="rrtngg-col-12">
				<div style="text-align: center; margin-top: 25px;">
					<button
							id="rrtng_confirm_btn"
							class="rrtngg-btn rrtngg-lg" data-id="<?php echo esc_attr( $id ); ?>"
							data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
					><?php esc_html_e( 'Rate Us', '5-stars-rating-funnel' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}
	?>
</div>
