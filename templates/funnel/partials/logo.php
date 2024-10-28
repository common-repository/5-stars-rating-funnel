<?php
/**
 * Funnel Page Logo
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $funnel_settings
 */

$funnel_logo = get_the_post_thumbnail( $id, 'full' );

$max_width = '100%';

if ( ! empty( $funnel_settings['logo_max_width'] ) ) {
	$width     = true;
	$max_width = (int) $funnel_settings['logo_max_width'] . 'px';
}

$padding_top    = 0;
$padding_bottom = 0;

if ( ! empty( $funnel_settings['logo_padding_top'] ) ) {
	$padding_top = (int) $funnel_settings['logo_padding_top'];
}

if ( ! empty( $funnel_settings['logo_padding_bottom'] ) ) {
	$padding_bottom = (int) $funnel_settings['logo_padding_bottom'];
}
?>
	<style>
		#rrtng_funnel_logo {
			padding: 15px 0;
		}

		@media only screen and (min-width: 992px) {
			#rrtng_funnel_logo {
				padding-top: <?php echo esc_html( $padding_top ); ?>px !important;
				padding-bottom: <?php echo esc_html( $padding_bottom ); ?>px !important;
			}
		}

		#rrtng_funnel_logo img {
			display: block;
			height: auto;
			margin: auto;
			<?php
			if ( ! empty( $width ) ) {
				?>
			width: 100%!important;
				<?php
			}
			?>
			max-width: <?php echo esc_html( $max_width ); ?>!important;
		}
	</style>
<?php

if ( ! empty( $funnel_logo ) ) {
	?>
	<div id="rrtng_funnel_logo">
		<?php echo get_the_post_thumbnail( $id, 'full' ); ?>
	</div>
	<?php
}
