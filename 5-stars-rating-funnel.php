<?php

/**
 * Plugin Name:     5 star review funnel for Google, Trustpilot, ProvenExpert and more | RRatingg
 * Description:     Get more and only top Google reviews from your customers. Ensure that critical voices do not share their feedback publicly. Other platforms supported.
 * Version:         1.4.01
 * Author:          Saleswonder.biz
 * Author URI:      https://saleswonder.biz/
 * License:         GPL v3 or later
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:     5-stars-rating-funnel
 * Requires PHP: 7.3
 *
 * Domain Path:     /languages
 */
if ( !defined( 'WPINC' ) ) {
    die;
}
define('RRTNGG_VERSION', '1.4.01');
define('RRTNGG_DB_VERSION', '1.2.39');
define('RRTNGG_PLUGIN_FILE', __FILE__);
define('RRTNGG_PLUGIN_BASENAME', plugin_basename(RRTNGG_PLUGIN_FILE));
define('RRTNGG_REL_PATH', dirname(RRTNGG_PLUGIN_FILE ));
define('RRTNGG_ABS_PATH', dirname(RRTNGG_PLUGIN_FILE ) . '/');
define('RRTNGG_REL_PLUGIN_REL_FILE', dirname(plugin_basename(__FILE__ )));

if ( !function_exists('rrtngg_fs') ) {
    // Create a helper function for easy SDK access.
    function rrtngg_fs()
    {
        global  $rrtngg_fs ;

        if ( !isset( $rrtngg_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_8833_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_8833_MULTISITE', true );
            }
            // Include Freemius SDK.
			require_once __DIR__ . '/freemius/start.php';
            $rrtngg_fs = fs_dynamic_init( array(
                'id'             => '8833',
                'slug'           => '5-stars-rating-funnel',
                'type'           => 'plugin',
                'public_key'     => 'pk_4e6558661c8f89ec3576ba1e7ab0f',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_premium_version' => false,
                'has_paid_plans' => true,
                'trial'          => array(
                    'days'               => 14,
                    'is_require_payment' => true,
                ),
                'menu'           => array(
                    'slug'       => 'edit.php?post_type=rratingg',
                    'first-path' => 'edit.php?post_type=rratingg',
                    'contact'    => false,
                    'support'    => false,
                ),
                'is_live'        => true,
            ) );
        }

        return $rrtngg_fs;
    }

    // Init Freemius.
    rrtngg_fs();
    // Signal that SDK was initiated.
    do_action( 'rrtngg_fs_loaded' );
}

require_once RRTNGG_ABS_PATH . 'includes/functions.php';
if (!class_exists('RRTNGG_Core')) {
    include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Core.php';
}
function RRTNGG()
{
    return RRTNGG_Core::instance();
}

$GLOBALS['RRTNGG'] = RRTNGG();
