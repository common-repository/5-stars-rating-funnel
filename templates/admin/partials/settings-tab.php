<?php
/**
 * @var $active_tab
 * @var $settings_tabs
 */
?>

<?php
if ( ! empty( $settings_tabs ) ) {
    ?>
    <h2 id="wtsr-nav-tab" class="nav-tab-wrapper">
        <?php
        foreach ( $settings_tabs as $slug => $data ) {
            ?>
            <a
                href="?post_type=rratingg&page=rratingg-settings&tab=<?php echo esc_html( $slug ); ?>"
                class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>"
            ><?php echo esc_html( $data['title'] ); ?></a>
            <?php
        }
        ?>
    </h2>
    <?php
}
?>
