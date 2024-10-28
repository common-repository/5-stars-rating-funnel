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
    <?php RRTNGG_Funnel_Template::template( $id, $funnel, $funnel_settings ); ?>
</div>
<?php

RRTNGG_Funnel_Template::footer_content( $id, $funnel, $funnel_settings );
