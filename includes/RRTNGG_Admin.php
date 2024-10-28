<?php

class RRTNGG_Admin {

    public static function reset_count_data() {
        $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
        $funnel_id = isset( $_GET['funnel_id'] ) ? $_GET['funnel_id'] : '';
        $action    = isset( $_GET['funnel_action'] ) ? $_GET['funnel_action'] : '';
        if ( $action == 'reset' && $post_type == 'rratingg' ) {

            $result = Helpers::delete_row( $funnel_id );
            $redirect_url = admin_url() . 'edit.php?post_type=rratingg' ;
            header('Location: ' . $redirect_url);
            exit; // Make sure to exit after the redirect
        }
    }

    public static function insert_reset_button( $actions, $post ) {
        if ( get_post_type() == 'rratingg' ) {
            $nonce = wp_create_nonce( 'reset-row' );
            $url   = admin_url( 'edit.php?post_type=rratingg&funnel_id=' . $post->ID . '&funnel_action=reset' );
            $url   = add_query_arg(
                'reset-row',
                $nonce,
                $url
            );
            $actions['reset'] = '<a href=' . esc_url( $url ) . ' rel="bookmark">Reset Row</a>';
        }
        return $actions;
    }

    public static function insert_average_rating_column( $posts_columns ) {
        $new_column = [
            'cb'               => '<input type="checkbox" />',
            'title'            => __( 'Title', '5-stars-rating-funnel' ),
            'unique_visitors'  => __( 'Count Visitors', '5-stars-rating-funnel' ),
            'total_rating'     => __( "Total Rating", '5-stars-rating-funnel' ),
            'specific_ratings' => __( 'Specific Ratings', '5-stars-rating-funnel' ),
            'average_rating'   => __( "Average Rating", '5-stars-rating-funnel' ),
            'redirect_count'   => __( "Total Redirect Count", '5-stars-rating-funnel' ),
            'date'             => __( 'Date', '5-stars-rating-funnel' )
        ];

        return $new_column;
    }

    public static function average_rating_column_data( $column, $post_id ) {
        $average_rating         = Helpers::get_funnel_average( $post_id );
        $total_count            = Helpers::get_funnel_total_ratings( $post_id );
        $unique_visitors        = get_post_meta($post_id , 'rrating_visit_count', true);
        $one_star               = Helpers::get_specific_funnel_rating( $post_id, 1 );
        $two_star               = Helpers::get_specific_funnel_rating( $post_id, 2 );
        $three_star             = Helpers::get_specific_funnel_rating( $post_id, 3 );
        $four_star              = Helpers::get_specific_funnel_rating( $post_id, 4 );
        $five_star              = Helpers::get_specific_funnel_rating( $post_id, 5 );
        $total_redirect_c       = get_post_meta($post_id , 'rrtngg_feedback_btn_count', true);
        $total_redirect_c       = !empty($total_redirect_c) && $total_redirect_c != '' ? $total_redirect_c : 0;
        $unique_visitors       = !empty($unique_visitors) && $unique_visitors != '' ? $unique_visitors : 0;


        switch ( $column ) {
            case 'average_rating':
                echo '<span>' . $average_rating . '</span>';
                break;
            case 'total_rating':
                echo '<span>' . $total_count . '</span>';
                break;
            case 'unique_visitors':
                echo '<span>' . $unique_visitors . '</span>';
                break;
            case 'redirect_count':
                echo '<span>' . $total_redirect_c . '</span>';
                break;
            case 'specific_ratings':
                echo '<ul class="ratings-group">
                         <li>
                            <div class="individual-rating">
                                <span>1</span>
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="20" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;fill-rule:evenodd;clip-rule:evenodd" viewBox="0 0 6.827 6.827"><path style="fill:#ff8f00;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923-1.343.923-.239.164.082-.278.462-1.564-1.292-.992-.23-.176.29-.008 1.63-.044.544-1.535.097-.274z"/><path style="fill:#e68100;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923V.98z"/><path style="fill:none" d="M0 0h6.827v6.827H0z"/></svg>
                                <p>' . $one_star . '</p>
                            </div>
                         </li>
                         <li>
                            <div class="individual-rating">
                                <span>2</span>
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="20" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;fill-rule:evenodd;clip-rule:evenodd" viewBox="0 0 6.827 6.827"><path style="fill:#ff8f00;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923-1.343.923-.239.164.082-.278.462-1.564-1.292-.992-.23-.176.29-.008 1.63-.044.544-1.535.097-.274z"/><path style="fill:#e68100;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923V.98z"/><path style="fill:none" d="M0 0h6.827v6.827H0z"/></svg>
                                <p>' . $two_star . '</p>
                            </div>
                         </li>
                         <li>
                            <div class="individual-rating">
                                <span>3</span>
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="20" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;fill-rule:evenodd;clip-rule:evenodd" viewBox="0 0 6.827 6.827"><path style="fill:#ff8f00;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923-1.343.923-.239.164.082-.278.462-1.564-1.292-.992-.23-.176.29-.008 1.63-.044.544-1.535.097-.274z"/><path style="fill:#e68100;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923V.98z"/><path style="fill:none" d="M0 0h6.827v6.827H0z"/></svg>
                                <p>' . $three_star . '</p>
                            </div>
                         </li>
                         <li>
                            <div class="individual-rating">
                                <span>4</span>
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="20" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;fill-rule:evenodd;clip-rule:evenodd" viewBox="0 0 6.827 6.827"><path style="fill:#ff8f00;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923-1.343.923-.239.164.082-.278.462-1.564-1.292-.992-.23-.176.29-.008 1.63-.044.544-1.535.097-.274z"/><path style="fill:#e68100;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923V.98z"/><path style="fill:none" d="M0 0h6.827v6.827H0z"/></svg>
                                <p>' . $four_star . '</p>
                            </div>
                         </li>
                         <li>
                            <div class="individual-rating">
                                <span>5</span>
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="20" height="20" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;fill-rule:evenodd;clip-rule:evenodd" viewBox="0 0 6.827 6.827"><path style="fill:#ff8f00;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923-1.343.923-.239.164.082-.278.462-1.564-1.292-.992-.23-.176.29-.008 1.63-.044.544-1.535.097-.274z"/><path style="fill:#e68100;fill-rule:nonzero" d="m3.51 1.252.546 1.536 1.628.043.29.008-.23.176-1.293.993.463 1.563.082.277-.239-.163-1.344-.923V.98z"/><path style="fill:none" d="M0 0h6.827v6.827H0z"/></svg>
                                <p>' . $five_star . '</p>
                            </div>
                         </li>
                       </ul>';
                break;
        }
    }
    public static function enqueue_scripts() {
        if ( ! empty( $_GET['post_type'] ) && $_GET['post_type'] === 'rratingg' ) {
            if ( did_action( 'wp_enqueue_editor' ) ) {
                wp_enqueue_editor();
            }

            if ( ! did_action( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'rratingg_icons', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/fonts/rrtngg_icons/css/fontello.css', [], RRTNGG_VERSION . time(), 'all' );
            wp_enqueue_style( 'rratingg', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/admin/css/style.css', [], RRTNGG_VERSION . time(), 'all' );
            wp_enqueue_script( 'rratingg', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/admin/js/script.js', ['jquery', 'wp-color-picker'], RRTNGG_VERSION . time(), true );

            $localize_array = [
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'snth_nonce' ),
                'server_ip' => ip2long( Helpers::get_user_ip_address() ),
                'post_id'   => get_the_ID()
            ];

            wp_localize_script( 'rratingg', 'rratinggJsObj', $localize_array );
        }
    }

    public static function enqueue_post_scripts() {
        global $typenow;

        if (  ( ! empty( $typenow ) && $typenow === 'rratingg' ) ) {
            if ( did_action( 'wp_enqueue_editor' ) ) {
                wp_enqueue_editor();
            }

            if ( ! did_action( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            }
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'rratingg_icons', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/fonts/rrtngg_icons/css/fontello.css', [], RRTNGG_VERSION . time(), 'all' );
            wp_enqueue_style( 'rratingg', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/admin/css/style.css', [], RRTNGG_VERSION . time(), 'all' );
            wp_enqueue_script( 'rratingg', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/admin/js/script.js', ['jquery', 'wp-color-picker'], RRTNGG_VERSION . time(), true );

            //localize script of the data
            $localize_array = [
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'snth_nonce' ),
                'server_ip' => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'no-ip',
                'post_id'   => get_the_ID()
            ];

            wp_localize_script( 'rratingg', 'rratinggJsObj', $localize_array );
        }
    }

    public static function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=rratingg',
            __( '5 stars rating funnel Settings', '5-stars-rating-funnel' ),
            __( 'Leads / Feedbacks', '5-stars-rating-funnel' ),
            'manage_options',
            'rratingg-settings',
            'RRTNGG_Admin::display_settings_page',
            10
        );
        add_submenu_page(
            'edit.php?post_type=rratingg',
            __( 'RRatingg Solution & Support', '5-stars-rating-funnel' ),
            __( 'Support', '5-stars-rating-funnel' ),
            'manage_options',
            'rratingg-support',
            'RRTNGG_Admin::display_support_page',
            10
        );
        if ( false ) {
            add_submenu_page(
                'edit.php?post_type=rratingg',
                __( 'RRatingg Changelog', '5-stars-rating-funnel' ),
                __( 'Changelog', '5-stars-rating-funnel' ),
                'manage_options',
                'rratingg-changelog',
                'RRTNGG_Admin::display_changelog_page',
                10
            );
        }

        if ( function_exists( 'rrtngg_fs' ) ) {
            $is_registered = rrtngg_fs()->is_registered();
            if ( ! $is_registered ) {
                add_submenu_page(
                    'edit.php?post_type=rratingg',
                    __( 'Opt-in to see account', '5-stars-rating-funnel' ),
                    __( 'Opt-in to see account', '5-stars-rating-funnel' ),
                    'manage_options',
                    'rratingg-optin',
                    'RRTNGG_Admin::display_support_page'
                );
            }
        }
    }

    public static function plugin_menu_optin() {
        global $submenu;

        if ( function_exists( 'rrtngg_fs' ) ) {
            $reconnect_url = rrtngg_fs()->get_activation_url(
                [
                    'nonce'     => wp_create_nonce( rrtngg_fs()->get_unique_affix() . '_reconnect' ),
                    'fs_action' => ( rrtngg_fs()->get_unique_affix() . '_reconnect' )
                ]
            );

            $is_registered = rrtngg_fs()->is_registered();

            if ( ! $is_registered && isset( $submenu['edit.php?post_type=rratingg'] ) ) {
                foreach ( $submenu['edit.php?post_type=rratingg'] as $i => $subitem ) {
                    if ( $subitem[2] === 'rratingg-optin' ) {
                        $submenu['edit.php?post_type=rratingg'][$i] = [
                            __( 'Opt-in to see account', '5-stars-rating-funnel' ),
                            'administrator',
                            $reconnect_url
                        ];
                    }
                }
            }
        }
    }

    public static function display_settings_page() {
        $settings_tabs         = self::get_settings_tabs();
        $settings_tabs_allowed = array_keys( $settings_tabs );
        $active_tab            = 'general';

        if (
            ! empty( $_GET['tab'] )
            && in_array( sanitize_text_field( $_GET['tab'] ), $settings_tabs_allowed )
        ) {
            $active_tab = sanitize_text_field( $_GET['tab'] );
        }

        include_once RRTNGG_ABS_PATH . 'templates/admin/settings.php';
    }

    public static function get_settings_tabs() {
        $settings_tab = [
            'general'   => [
                'title' => __( 'Import', '5-stars-rating-funnel' )
            ],
            'leads'     => [
                'title' => __( 'Leads', '5-stars-rating-funnel' )
            ],
            'feedbacks' => [
                'title' => __( 'Feedbacks', '5-stars-rating-funnel' )
            ],
            'settings' => [
                'title' => __( 'Settings', '5-stars-rating-funnel' )
            ]
        ];

        foreach ( $settings_tab as $id => $tab ) {
            $template = RRTNGG_ABS_PATH . 'templates/admin/partials/settings-' . $id . '.php';

            if ( ! file_exists( $template ) ) {
                unset( $settings_tab[$id] );
            }
        }

        return $settings_tab;
    }

    public static function display_support_page() {
        $lang = get_locale();
        if ( strlen( $lang ) > 0 ) {
            $lang = explode( '_', $lang )[0];
        }

        $support_link = ( $lang == 'en' ) ? 'https://rratingg.tawk.help/' : 'https://rratingg.tawk.help/' . $lang;

        include_once RRTNGG_ABS_PATH . 'templates/admin/support.php';

        return;
    }

    public static function display_changelog_page() {
        $changelog_link = get_admin_url() . 'plugin-install.php?tab=plugin-information&plugin=webinar-ignition&section=changelog';

        include_once RRTNGG_ABS_PATH . 'templates/admin/changelog.php';

        return;
    }

    public static function flush_rewrite_rules() {
        if ( ! get_option( 'rrtngg_flush_rewrite_rules' ) ) {
            flush_rewrite_rules();

            update_option( 'rrtngg_flush_rewrite_rules', 1 );
        }
    }

    public static function save_general_settings(){
        if( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$is_settings_page = isset($_GET['page']) && array_key_exists('page' , $_GET) && $_GET['page'] === 'rratingg-settings' && array_key_exists('tab' , $_GET) && $_GET['tab'] === 'settings';
		if( $is_settings_page ) {
			if( !empty( $_POST ) && isset( $_POST['save_rrating_funnel_gen_settings'] )){
				$general_settings = array();
				$general_settings['rrating_enable_ip_restriction'] = ( isset( $_POST[ 'rrating_enable_ip_restriction' ] )  ) ? $_POST[ 'rrating_enable_ip_restriction' ] : '';
				update_option( 'rrating_funnel_gen_settings', $general_settings );
			}
		}
    }
}

add_action( 'admin_menu', ['RRTNGG_Admin', 'add_menu_page'] );
add_action( 'admin_menu', ['RRTNGG_Admin', 'plugin_menu_optin'], 50 );
add_action( 'admin_enqueue_scripts', ['RRTNGG_Admin', 'enqueue_scripts'] );
add_action( 'admin_notices', ['RRTNGG_Admin' , 'save_general_settings'] );
$is_premium = ! empty( RRTNGG_License_Manager::is_premium() );

if ( $is_premium  ) {
    add_filter( 'manage_rratingg_posts_custom_column', ['RRTNGG_Admin', 'average_rating_column_data'], 10, 2 );
    add_filter( 'manage_rratingg_posts_columns', ['RRTNGG_Admin', 'insert_average_rating_column'], 10, 1 );
}

add_action( 'init', ['RRTNGG_Admin', 'reset_count_data'], 10 );
add_filter( 'post_row_actions', ['RRTNGG_Admin', 'insert_reset_button'], 10, 2 );
add_action( 'admin_print_scripts-edit.php', ['RRTNGG_Admin', 'enqueue_post_scripts'] );
add_action( 'admin_print_scripts-post.php', ['RRTNGG_Admin', 'enqueue_post_scripts'] );
add_action( 'admin_print_scripts-post-new.php', ['RRTNGG_Admin', 'enqueue_post_scripts'] );

add_action( 'admin_init', ['RRTNGG_Admin', 'flush_rewrite_rules'] );
