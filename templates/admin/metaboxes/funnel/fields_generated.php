<?php
/**
 * File fields_generated.php
 *
 * @package RRatingg
 * @var $post
 * @var $fields
 */

$allowed_tags = array_merge( RRTNGG_Manager::get_allowed_tags(), RRTNGG_Manager::get_fields_allowed_tags() );
if ( ! empty( $fields ) ) {
    $funnel_settings = get_post_meta( $post->ID, 'rrtngg_funnel_settings', true );
    ?>
    <table class="form-table"><tbody>
        <?php
        foreach ( $fields as $fid => $field ) {
            $name  = substr( $fid, 7 );
            $value = get_post_meta( $post->ID, $fid, true );

            if ( empty( $value ) ) {
                if ( ! empty( $field['allow_empty'] ) && isset( $funnel_settings[ $name ] ) ) {
                    $value = $funnel_settings[ $name ];
                } elseif ( isset( $field['default'] ) ) {
                    $value = $field['default'];
                }
            }

            $label = ! empty( $field['label'] ) ? $field['label'] : '';
            $ftype = ! empty( $field['type'] ) ? $field['type'] : '';

            echo wp_kses( RRTNGG_Field_Generator::form_field( $fid, $field, $value ), $allowed_tags );
        }
        ?>
    </tbody></table>
    <?php
}
