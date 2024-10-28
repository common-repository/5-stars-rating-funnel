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
 * @var $enabled_services
 * @var $rratingg_lead
 */

$services_by_rows = array();

$row = 0;
$col = 1;

foreach ( $enabled_services as $sid => $service ) {
    if ( 'button' !== $service['type'] ) {
        continue;
    }
    ob_start();
    include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/services/' . $sid . '.php';
    $content = ob_get_clean();

    if ( empty( $content ) ) {
        continue;
    }

    $services_by_rows[ $row ][ $sid ] = $content;

    if ( 2 === $col ) {
        $row++;
        $col = 1;
    } else {
        $col++;
    }

    unset( $enabled_services[ $sid ] );
}

if ( ! empty( $services_by_rows ) ) {
    foreach ( $services_by_rows as $row => $columns ) {
        ?>
        <div id="rrtngg_btns_container"<?php echo ! empty( $is_dummy ) ? ' data-dummy="1"' : ''; ?>>
            <div class="rrtngg-row">
                <?php
                foreach ( $columns as $sid => $content ) {
                    if ( 2 === count( $columns ) ) {
                        ?>
                        <div class="rrtngg-col-12 rrtngg-col-md-6">
                        <?php
                    } else {
                        ?>
                        <div class="rrtngg-col-12">
                        <?php
                    }
                    ?>
                    <div class="rrtngg-btn-holder">
                    <?php
                    echo wp_kses(
                        $content,
                        array(
                            'style'  => array(),
                            'div'    => array(
                                'id' => array(),
                            ),
                            'p'      => array(),
                            'button' => array(
                                'id'           => array(),
                                'class'        => array(),
                                'data-url'     => array(),
                                'data-service' => array(),
                                'data-lead-id' => array(),
                                'data-step'    => array(),
                                'data-funnel-id'=> array(),
                            ),
                        )
                    );
                    ?>
                    </div></div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
}

if ( empty( $is_dummy ) ) {
    foreach ( $enabled_services as $sid => $service ) {
        if ( file_exists( RRTNGG_ABS_PATH . 'templates/funnel/partials/services/' . $sid . '.php' ) ) {
            include_once RRTNGG_ABS_PATH . 'templates/funnel/partials/services/' . $sid . '.php';
        }
    }
}
?>
