<?php
/**
 * Funnel Page Header
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 *
 * @var $funnel_settings
 */

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="robots" content="noindex, nofollow" />
	<?php wp_head(); ?>

	<?php
	if ( ! empty( $funnel_settings['page_style'] ) && 'custom' === $funnel_settings['page_style'] ) {
		$page_bg_color   = ! empty( $funnel_settings['page_bg_color'] ) ? $funnel_settings['page_bg_color'] : '';
		$page_txt_color  = ! empty( $funnel_settings['page_txt_color'] ) ? $funnel_settings['page_txt_color'] : '';
		$page_link_color = ! empty( $funnel_settings['page_link_color'] ) ? $funnel_settings['page_link_color'] : '';
		$btn_colors      = rrtngg_get_btn_colors( $page_bg_color );

		if ( 'black' === $btn_colors['text_color'] ) {
			$input_border = 'rgba(0,0,0,0.6)';
			$input_bg     = 'rgba(0,0,0,0.2)';
		} else {
			$input_border = 'rgba(255,255,255,0.6)';
			$input_bg     = 'rgba(255,255,255,0.2)';
		}
		?>
		<style>
			.rratingg-funnel-page-template {

			<?php
			if ( ! empty( $page_bg_color ) ) {
				?>
				background-color: <?php echo esc_html( $page_bg_color ); ?>!important;
				<?php
			}
			?>

			<?php
			if ( ! empty( $page_txt_color ) ) {
				?>
				color: <?php echo esc_html( $page_txt_color ); ?>!important;
				<?php
			}
			?>
			}

			.rratingg-funnel-page-template label,
			.rratingg-funnel-page-template h1,
			.rratingg-funnel-page-template h2,
			.rratingg-funnel-page-template h3,
			.rratingg-funnel-page-template h4,
			.rratingg-funnel-page-template h5,
			.rratingg-funnel-page-template h6 {
				<?php
				if ( ! empty( $page_txt_color ) ) {
					?>
				color: <?php echo esc_html( $page_txt_color ); ?>!important;
					<?php
				}
				?>
			}

			.rratingg-funnel-page-template a {
				<?php
				if ( ! empty( $page_link_color ) ) {
					?>
				color: <?php echo esc_html( $page_link_color ); ?>!important;
					<?php
				}
				?>
			}

			.rratingg-funnel-page-template input[type=text],
			.rratingg-funnel-page-template input[type=number],
			.rratingg-funnel-page-template input[type=email],
			.rratingg-funnel-page-template input[type=tel],
			.rratingg-funnel-page-template input[type=url],
			.rratingg-funnel-page-template input[type=password],
			.rratingg-funnel-page-template input[type=search],
			.rratingg-funnel-page-template textarea,
			.rratingg-funnel-page-template .rrtngg-form-control {
				border: 1px solid <?php echo esc_html( $input_border ); ?>!important;
				background-color: <?php echo esc_html( $input_bg ); ?>!important;
			}
		</style>
		<?php
	}
	?>
</head>

<body <?php body_class(); ?>>
<?php 
$is_feedback_limit_reached = RRTNGG_License_Manager::is_feedback_limit_reached();

if ( 'on' === $funnel_settings['content_hide_header'] || $is_feedback_limit_reached ) {
	get_header();
}

?>
