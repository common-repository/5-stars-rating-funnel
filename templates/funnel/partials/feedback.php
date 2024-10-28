<?php
/**
 * Funnel Page Landing Template
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $rratingg_lead
 * @var $funnel_settings
 */

if ( ! empty( $funnel_settings['feedback_page_content'] ) ) {
    $page_content = $funnel_settings['feedback_page_content'];
    $page_content = apply_filters( 'rrtngg_page_content', $page_content, $rratingg_lead, $funnel );
    ?>
    <div id="rrtng_funnel_description">
        <div class="rrtngg-row">
            <div class="rrtngg-col-12">
                <?php echo wp_kses_post( wpautop( $page_content ) ); ?>
            </div>
        </div>
    </div>
    <?php
}

RRTNGG_Funnel_Template::feedback_form( $id, $funnel, $funnel_settings );
