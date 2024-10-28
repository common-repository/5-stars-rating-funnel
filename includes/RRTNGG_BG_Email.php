<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RRTNGG_BG_Email {
    protected static $bg;

    public static function init() {
        include_once RRTNGG_ABS_PATH . 'includes/background/Abstract_RRTNGG_BG.php';
        include_once RRTNGG_ABS_PATH . 'includes/background/RRTNGG_BG_Email_Request.php';

        self::$bg = new RRTNGG_BG_Email_Request();
    }

    public static function run( $data ) {
        foreach ( $data as $item ) {
            self::$bg->push_to_queue( $item );
        }

        self::$bg->save()->dispatch();
    }
}

add_action( 'init', array( 'RRTNGG_BG_Email', 'init' ) );
