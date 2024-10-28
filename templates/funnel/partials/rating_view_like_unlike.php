<?php
/**
 * Funnel Page Landing Like Unlike Rating View Template
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 * @var $id
 * @var $funnel
 * @var $funnel_settings
 */

$is_rating_require_confirm = ! empty( $funnel_settings['rating_require_confirm'] );

$like_unlike_btn_style = ! empty( $funnel_settings['like_unlike_btn_style'] ) ? $funnel_settings['like_unlike_btn_style'] : 'thumbs_1';
?>

<div id="rrtng_like_unlike_rating_buttons_container" class="<?php echo $is_rating_require_confirm ? 'rating_require_confirm' : 'rating_no_confirm'; ?>">
    <div class="rrtngg-row">
        <div class="rrtngg-col-12 rrtng_like_unlike_rating_button_holder">
            <div class="rrtng_like_unlike_rating_button rrtng_rating_button rrtngg_rating_positive_button" data-id="<?php echo esc_attr( $id ); ?>" data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>>
                <i class="icon-rrtngg_like icon-rrtngg_like_unlike icon-rrtngg_like_unlike_<?php echo esc_attr( $like_unlike_btn_style ); ?>_like"></i>
            </div>

            <div class="rrtng_like_unlike_rating_button rrtng_rating_button rrtngg_rating_negative_button" data-id="<?php echo esc_attr( $id ); ?>" data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>>
                <i class="icon-rrtngg_unlike icon-rrtngg_like_unlike icon-rrtngg_like_unlike_<?php echo esc_attr( $like_unlike_btn_style ); ?>_unlike"></i>
            </div>
        </div>
    </div>

    <?php
    if ( $is_rating_require_confirm ) {
        ?>
        <div id="rrtng_confirm_btn_container" class="rrtngg-row rating_no_confirm" style="display: none;">
            <div class="rrtngg-col-12">
                <div style="text-align: center; margin-top: 25px;">
                    <button
                            id="rrtng_confirm_btn"
                            class="rrtngg-btn rrtngg-lg" data-id="<?php echo esc_attr( $id ); ?>"
                            data-id="<?php echo esc_attr( $id ); ?>"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>
                    ><?php esc_html_e( 'Rate Us', '5-stars-rating-funnel' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
