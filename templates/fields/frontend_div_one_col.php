<?php
/**
 * File frontend_div_one_col.php
 *
 * @package RRatingg
 * @var $label
 * @var $input
 * @var $description
 * @var $short_description
 * @var $attributes
 * @var $field
 */

?>

<div<?php RRTNGG_Field_Generator::container_attributes( $field ); ?>>
    <?php echo wp_kses( $label, RRTNGG_Manager::get_allowed_tags() ); ?>
    <?php
    echo wp_kses(
        $input,
        array(
            'input'    => array(
                'id'    => array(),
                'name'  => array(),
                'type'  => array(),
                'value' => array(),
                'class' => array(),
            ),
            'textarea' => array(
                'id'    => array(),
                'name'  => array(),
                'class' => array(),
            ),
        )
    );
    ?>
</div>
