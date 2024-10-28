<?php
/**
 * Funnel Page Landing Template
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $funnel_settings
 * @var $rratingg_lead
 */

if ( empty( $funnel_settings['services_custom_link_pro_field_link'] ) ) {
    return '';
}
$service_id = 'custom_link_pro';

$src = RRTNGG_Services_Manager::get_service_url( $service_id, $funnel_settings, $rratingg_lead );

$btn_text = empty( $funnel_settings[ 'services_' . $service_id . '_btn_text' ] ) ? __( 'Rate Us', '5-stars-rating-funnel' ) : sanitize_text_field( $funnel_settings[ 'services_' . $service_id . '_btn_text' ] );

rrtngg_show_buttons_styles( $service_id, $funnel_settings );
?>

<button
        id="rrtngg_services_<?php echo esc_attr( $service_id ); ?>_btn"
        class="rrtngg-btn rrtngg-lg rrtngg_services_btn"
        data-url="<?php echo esc_url( $src ); ?>"
        data-service="<?php echo esc_attr( $service_id ); ?>"
        data-lead-id="<?php echo esc_attr( $id ); ?>"
        data-funnel-id="<?php echo esc_attr( get_the_ID() ); ?>"
        data-step="<?php echo esc_attr( $rratingg_lead['status'] ); ?>"
>
    <?php echo esc_html( $btn_text ); ?>
</button>

<?php rrtngg_show_below_button_text( $service_id, $funnel_settings ); ?>
