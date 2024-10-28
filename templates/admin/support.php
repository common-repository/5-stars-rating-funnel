<?php
/**
 * File support.php
 *
 * @package RRatingg
 * @var $support_link
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders the contents of the settings submenu page
 *
 * @since    2.2.7   *
 */
?>

<div class="wrap">
    <div class="row">
        <div class="col-xs-12">
            <iframe id="rratingg_kb_iframe" width="100%" height="100" src="<?php echo esc_url( $support_link ); ?>" title="5 Stars Rating Funnel Support" style="border:none;"></iframe>
        </div>
    </div>
</div>

<script>
    (function ($) {
        $(document).ready(function () {
            recalculateIframeHeight();
        });

        $(window).on('resize', function () {
            recalculateIframeHeight();
        });

        function recalculateIframeHeight() {
            var windowHeight = $(window).height();
            var wpadminbarHeight = $('#wpadminbar').outerHeight(true) + 20;
            var noticesHeight = 0;

            var notices = $('#wpbody-content > .notice');

            if (notices.length) {
                notices.each(function() {
                    noticesHeight = noticesHeight + $(this).outerHeight(true) + 5;
                });
            }

            var iframe_height = windowHeight - wpadminbarHeight - noticesHeight;

            $('#rratingg_kb_iframe').css(
                {
                    'height': iframe_height + 'px'
                }
            );
        }
    })(jQuery);
</script>


<style>
    iframe{
    // overflow:hidden;
    }
</style>
