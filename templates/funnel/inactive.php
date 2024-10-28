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
 */

RRTNGG_Funnel_Template::logo( $id, $funnel, $funnel_settings );

?>
<div id="rrtng_funnel_content">
<div id="rrtng_funnel_description">
    <div class="rrtngg-row">
        <div class="rrtngg-col-12">
            <h3 style="text-align: center;"><?php esc_html_e( 'Funnel is inactive, please contact site owner/support.', '5-stars-rating-funnel' ); ?></h3>
        </div>
<!-- 
        <div class="rrtngg-col-12">
            <p style="text-align: center;"><?php //esc_html_e( 'Rating could not be taken, limit reached, please contact site owner/suppor.', '5-stars-rating-funnel' ); ?></p>
        </div> -->
    </div>
</div>
</div>
<?php

RRTNGG_Funnel_Template::footer_content( $id, $funnel, $funnel_settings );
