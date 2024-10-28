<?php
/**
 * @var $active_tab
 * @var $settings_tabs
 */

$is_premium = RRTNGG_License_Manager::is_premium();

if ( empty( $is_premium ) ) {
    $version = ' (' . __( 'Free', '5-stars-rating-funnel' ) . ')';
} elseif ( ! empty( $is_premium['is_trial'] ) ) {
    $version = ' (' . __( 'Trial', '5-stars-rating-funnel' ) . ')';
} else {
    $version = '';
}
?>
<div class="wrap">
    <h1><?php echo __( 'Rating Funnels', '5-stars-rating-funnel' ) . esc_html( $version ) . ' - ' . __( 'Settings', '5-stars-rating-funnel' ); ?></h1>

    <?php settings_errors(); ?>

    <?php require_once RRTNGG_ABS_PATH . 'templates/admin/partials/settings-tab.php'; ?>

    <?php require_once RRTNGG_ABS_PATH . 'templates/admin/partials/settings-' . $active_tab . '.php'; ?>
</div>
