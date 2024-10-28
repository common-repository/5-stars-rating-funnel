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

if (
    empty( $funnel_settings['services_goggle_review_field_merchant_id'] )
    || empty( $funnel_settings['services_goggle_review_field_delivery_country'] )
    || empty( $rratingg_lead['email'] )
) {
    return '';
}

$estimated_delivery_delay          = ! empty( $funnel_settings['services_goggle_review_field_delay'] ) ? (int) $funnel_settings['services_goggle_review_field_delay'] : 1;
$merchant_id                       = $funnel_settings['services_goggle_review_field_merchant_id'];
$delivery_country                  = $funnel_settings['services_goggle_review_field_delivery_country'];
$estimated_delivery_date_timestamp = current_time( 'timestamp' ) + $estimated_delivery_delay * 24 * 60 * 60;
$estimated_delivery_date           = date( 'Y-m-d', $estimated_delivery_date_timestamp );

$order_id = ! empty( $rratingg_lead['order_id'] ) ? $rratingg_lead['order_id'] : (int) $rratingg_lead['ID'] . date( 'mY', $estimated_delivery_date_timestamp );
?>


<!-- BEGIN GCR Opt-in Module Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>

<script>
    window.renderOptIn = function() {
        window.gapi.load('surveyoptin', function() {
            window.gapi.surveyoptin.render(
                {
                    "merchant_id": "<?php echo esc_html( $merchant_id ); ?>",
                    "order_id": "<?php echo esc_html( $order_id ); ?>",
                    "email": "<?php echo esc_html( $rratingg_lead['email'] ); ?>",
                    "delivery_country": "<?php echo esc_html( $delivery_country ); ?>",
                    "estimated_delivery_date": "<?php echo esc_html( $estimated_delivery_date ); ?>"
                });
        });
    }
</script>

<!-- END GCR Opt-in Module Code -->

