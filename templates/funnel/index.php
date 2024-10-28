<?php
/**
 * Funnel Page Template
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 */

RRTNGG_Leads_Model::set_global_lead_by_hash();
?>

<?php RRTNGG_Funnel_Template::header(); ?>

<div class="rrtngg-container">
    <?php
    RRTNGG_Funnel_Template::content();
    ?>
</div>

<?php RRTNGG_Funnel_Template::footer(); ?>
