<?php
/**
 * File settings_tr.php
 *
 * @package RRatingg
 * @var $label
 * @var $input
 * @var $description
 * @var $short_description
 * @var $attributes
 * @var $field
 */

$allowed_tags = array_merge( RRTNGG_Manager::get_allowed_tags(), RRTNGG_Manager::get_fields_allowed_tags() );
?>
<tr<?php RRTNGG_Field_Generator::container_attributes( $field ); ?>>
    <th scope="row"><?php echo wp_kses( $label, RRTNGG_Manager::get_allowed_tags() ); ?></th>
    <td>
        <?php
        echo wp_kses( $input, $allowed_tags );
        if ( ! empty( $short_description ) ) {
            ?>
            <p><?php echo wp_kses( $short_description, RRTNGG_Manager::get_allowed_tags() ); ?></p>
            <?php
        }
        if ( ! empty( $description ) ) {
            echo wp_kses( $description, RRTNGG_Manager::get_allowed_tags() );
        }
        ?>
    </td>
</tr>
