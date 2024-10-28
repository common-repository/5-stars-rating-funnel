<?php
/**
 * File admin_two_col_simple.php
 *
 * @package RRatingg
 * @var $label
 * @var $input
 * @var $description
 * @var $short_description
 * @var $attributes
 */

$allowed_tags = array_merge( RRTNGG_Manager::get_allowed_tags(), RRTNGG_Manager::get_fields_allowed_tags() );
?>

<div class="wptl-row">
    <div class="wptl-col-xs-12 wptl-col-md-4">
        <p>
            <?php echo wp_kses( $label, $allowed_tags ); ?>
        </p>
    </div>

    <div class="wptl-col-xs-12 wptl-col-md-8">
        <p>
            <?php echo wp_kses( $input, $allowed_tags ); ?>
        </p>
    </div>
</div>
