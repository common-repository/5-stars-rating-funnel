<?php
/**
 * Funnel Page Footer
 *
 * @package RRatingg
 * @version 1.0.0
 * @since 1.0.0
 */

?>

<?php wp_footer(); ?>
<?php
$is_feedback_limit_reached = RRTNGG_License_Manager::is_feedback_limit_reached();

if ( 'on' === $funnel_settings['content_hide_header'] || $is_feedback_limit_reached ) {
	get_footer();
}
?>
</body>
</html>
