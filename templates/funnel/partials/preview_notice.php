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
 */

?>

<div id="preview_notice_container" style="margin-bottom: 15px; background-color: rgba(0,153,41,0.2);padding: 15px;">
    <div class="rrtngg-row">
        <div class="rrtngg-col-12">
            <h3 style="text-align: center;"><?php esc_html_e( 'Preview only mode', '5-stars-rating-funnel' ); ?></h3>
            <p style="text-align: center; margin: 10px 0 2px 0;">
                <?php
                echo esc_html( __( 'In preview mode you can ONLY check how your funnel looks like. No real data will be updated and no email will be sent.', '5-stars-rating-funnel' ) );
                ?>
                <br>
                <strong>
                    <?php echo esc_html( __( 'Buttons, with links to rating platforms, will not work!', '5-stars-rating-funnel' ) ); ?>
                </strong>
            </p>

            <p style="text-align: center; margin: 10px 0 2px 0;">
                <?php
                echo sprintf(
                    /* translators: %s: <a> tag */
                    esc_html__( 'If you want to fully test your current funnel and see if your buttons are working - visit %1$ssettings page%2$s and add a single lead. You can delete it after testing.', '5-stars-rating-funnel' ),
                    '<a href="/wp-admin/edit.php?post_type=rratingg&page=rratingg-settings" target="_blank" class="rrtngg-btn rrtngg-xs">',
                    '</a>'
                )
                ?>
            </p>

            <p style="text-align: center; margin: 10px 0 2px 0;">
                <?php
                echo sprintf(
                    /* translators: %s: <button> tag */
                    esc_html__( 'To start preview from beginning just %1$sReload page%2$s', '5-stars-rating-funnel' ),
                    '<button id="rrtngg_reload_preview_page_btn" class="rrtngg-btn rrtngg-xs">',
                    '</button>'
                )
                ?>
            </p>
        </div>
    </div>
</div>
