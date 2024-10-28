<?php
/**
 * Funnel Page Footer Content
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $rratingg_lead
 * @var $funnel_settings
 */

?>
<style>
	#rrtng_funnel_footer {
		padding: 25px 10px;
		/*border-top: 1px solid #eee;*/
	}
	#rrtng_funnel_footer *:last-child {
		margin-bottom: 0;
	}
</style>
	<?php
	if ( ! empty( $funnel_settings['footer_content'] ) && 'on' !== $funnel_settings['content_hide_footer'] ) {
		$page_content = apply_filters( 'rrtngg_page_content', $funnel_settings['footer_content'], $rratingg_lead, $funnel );
		?>
		<div class="rrtngg-row">
			<div class="rrtngg-col-12">
				<div id="rrtng_funnel_footer">
				<?php echo wp_kses_post( wpautop( $page_content ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}
	?>
