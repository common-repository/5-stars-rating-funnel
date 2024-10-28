<?php
    class RRTNGG_Core {
        protected static $_instance = null;

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct() {
            $this->includes();
            $this->init();
            $this->init_hooks();
        }

        public function includes() {
            include_once RRTNGG_ABS_PATH . 'includes/class_Helpers.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_License_Manager.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Email.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_BG_Import.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_BG_Email.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Manager.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Services_Manager.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Leads_Model.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Feedbacks_Model.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Field_Generator.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Ajax.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Funnel_Template.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Funnel_CPT.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Cron.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Rest.php';
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Notices.php';
            if ( is_admin() && ! class_exists( 'RRTNGG_Admin' ) ) {
                include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Admin.php';
            }
        }

        public static function init() {
            $is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

            if ( ! $is_ajax ) {
                self::check_version();
            }

            RRTNGG_License_Manager::init();
        }

        public static function check_version() {
            $version   = get_option( 'rrtngg_version' );
            $dbversion = get_option( 'rrtngg_db_version' );

            if (
                empty( $version ) || version_compare( $version, RRTNGG_VERSION, '<' ) ||
                empty( $dbversion ) || version_compare( $dbversion, RRTNGG_DB_VERSION, '<' )
            ) {
                self::install();
                do_action( 'rrtngg_updated' );
            }

            update_option( 'rrtngg_version', RRTNGG_VERSION );
            update_option( 'rrtngg_db_version', RRTNGG_DB_VERSION );

            $check_1_2_2_upgrade = get_option( 'rrtngg_1_2_2_upgrade' );
            if ( empty( $check_1_2_2_upgrade ) ) {
                include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Upgrade.php';
                RRTNGG_Upgrade::set_1_2_2_upgrade();
            }

            $check_1_2_13_upgrade = get_option( 'rrtngg_1_2_13_upgrade' );
            if ( empty( $check_1_2_13_upgrade ) ) {
                include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Upgrade.php';
                RRTNGG_Upgrade::set_1_2_13_upgrade();
            }

            $check_1_2_58_upgrade = get_option( 'rrtngg_1_2_58_upgrade' );
            if ( empty( $check_1_2_58_upgrade ) ) {
                include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Upgrade.php';
                RRTNGG_Upgrade::set_1_2_58_upgrade();
            }
          
            include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Upgrade.php';
            RRTNGG_Upgrade::create_rating_storage_table();
            
        }

        public static function install() {
            RRTNGG_Leads_Model::install();
            RRTNGG_Feedbacks_Model::install();
        }

        public function init_hooks() {
            add_action( 'wp_enqueue_scripts', ['RRTNGG_Core', 'enqueue_scripts'] );
            add_action( 'rest_api_init', ['RRTNGG_Core', 'rest_api_init'] );
            add_action( 'plugins_loaded', ['RRTNGG_Core', 'load_plugin_textdomain'] );

            add_action( 'init', ['RRTNGG_Funnel_CPT', 'create'], 0 );
            add_action( 'wp_loaded', ['RRTNGG_Funnel_CPT', 'flush_rewrite_rules'], 999 );
            add_action( 'add_meta_boxes', ['RRTNGG_Funnel_CPT', 'metaboxes'] );
            add_action( 'post_submitbox_misc_actions', ['RRTNGG_Funnel_CPT', 'make_active'] );
            add_action( 'wp_insert_post', ['RRTNGG_Funnel_CPT', 'new_funnel'], 10, 3 );
            add_action( 'save_post', ['RRTNGG_Funnel_CPT', 'save_fields'] );
            add_filter( 'post_row_actions', ['RRTNGG_Core', 'post_row_actions'], 999999, 2 );

            add_filter( 'mime_types', ['RRTNGG_Core', 'mime_types'], 999999 );
            add_filter( 'upload_mimes', ['RRTNGG_Core', 'mime_types'], 999999 );
            add_filter( 'single_template', ['RRTNGG_Funnel_Template', 'load_template'], 999999 );
            add_filter( 'template_include', ['RRTNGG_Funnel_Template', 'load_template'], 999999 );
            add_filter( 'body_class', ['RRTNGG_Core', 'body_class'], 50, 2 );
            add_filter( 'display_post_states', ['RRTNGG_Core', 'display_post_states'], 50, 2 );
            add_filter( 'edit_form_top', ['RRTNGG_Core', 'display_funnel_id'], 50 );
            add_filter( 'wp_kses_allowed_html', ['RRTNGG_Core', 'custom_wpkses_post_tags'], 10, 2 );

            add_filter( 'rrtngg_email_body', ['RRTNGG_Email', 'replace_placeholders'], 50, 3 );
            add_filter( 'rrtngg_page_content', ['RRTNGG_Email', 'replace_placeholders'], 50, 3 );

            add_filter( 'rrtngg_mail_steps', ['RRTNGG_License_Manager', 'mail_steps'], 50 );
            add_filter( 'rrtngg_lead_statuses', ['RRTNGG_License_Manager', 'lead_statuses'], 50 );

            add_shortcode( 'rratingg_funnel', ['RRTNGG_Funnel_Template', 'shortcode'] );

            $cron_manager = new RRTNGG_Cron();
            $cron_manager->set_schedule_hook();
        }

        public static function custom_wpkses_post_tags( $tags, $context ) {
            if ( 'post' === $context ) {
                $tags['iframe'] = [
                    'src'             => true,
                    'height'          => true,
                    'width'           => true,
                    'frameborder'     => true,
                    'allowfullscreen' => true
                ];
            }

            return $tags;
        }

        public static function post_row_actions( $actions, $post ) {
            if ( 'rratingg' !== $post->post_type || empty( $actions['view'] ) ) {
                return $actions;
            }

            $title = _draft_or_post_title();
            $link  = get_permalink( $post->ID );

            if ( false === strpos( $link, 'preview=' ) ) {
                if ( false === strpos( $link, '?' ) ) {
                    $link = $link . '?preview=true';
                } else {
                    $link = $link . '&preview=true';
                }
            }

            $actions['view'] = sprintf(
                '<a href="%s" rel="bookmark" target="_blank" aria-label="%s">%s</a>',
                $link,
                /* translators: %s: Post title. */
                esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ),
                __( 'View' )
            );

            return $actions;
        }

        public static function display_post_states( $post_states, $post ) {
            if ( 'rratingg' !== $post->post_type ) {
                return $post_states;
            }

            $id = $post->ID;

            $post_states['rrtngg_ID'] = '(ID: ' . $id . ')';

            $active_funnel = RRTNGG_Funnel_CPT::get_active();

            if ( ! in_array( $post->ID, $active_funnel ) ) {
                $post_states['rrtngg_inactive'] = __( 'Inactive', '5-stars-rating-funnel' );
            }

            return $post_states;
        }

        public static function display_funnel_id( $post ) {
            if ( 'rratingg' !== $post->post_type ) {
                return '';
            }

            $id = $post->ID;

        ?>
        <h3 style="padding: 0 10px;margin-bottom: 0;">
            <?php _e( 'Funnel ID', '5-stars-rating-funnel' );?>: <strong><?php echo esc_html( $id ); ?></strong>
        </h3>
        <?php
            }

                public static function rest_api_init() {
                    $controller = new RRTNGG_Rest();
                    $controller->register_routes();
                }

                public static function mime_types( $existing_mimes ) {
                    $existing_mimes['csv'] = 'text/csv';

                    return $existing_mimes;
                }

                public static function enqueue_scripts() {
                    if ( is_singular( 'rratingg' ) ) {
                        wp_enqueue_style( 'rratingg_icons', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/fonts/rrtngg_icons/css/fontello.css', [], RRTNGG_VERSION . time(), 'all' );
                        wp_enqueue_style( 'rratingg', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/css/style.css', [], RRTNGG_VERSION . time(), 'all' );
                        wp_enqueue_script( 'rratingg', plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/js/script.js', ['jquery'], RRTNGG_VERSION . time(), true );

                        $localize_array = [
                            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                            'nonce'     => wp_create_nonce( 'snth_nonce' ),
                            'server_ip' => ip2long( Helpers::get_user_ip_address() ),
                            'post_id'   => get_the_ID()
                        ];

                        wp_localize_script( 'rratingg', 'rratinggJsObj', $localize_array );
                    }
                }

                public static function body_class( $classes, $class ) {
                    if ( ! is_singular() ) {
                        return $classes;
                    }

                    global $wp_query;

                    $post_id   = $wp_query->get_queried_object_id();
                    $post      = $wp_query->get_queried_object();
                    $post_type = $post->post_type;
                    $content   = $post->post_content;

                    if ( 'rratingg' === $post_type || has_shortcode( $content, 'rratingg_funnel' ) ) {
                        $classes[] = 'rratingg-funnel-page';
                    }

                    if ( 'rratingg' === $post_type ) {
                        $classes[] = 'rratingg-funnel-page-template';
                    }

                    return $classes;
                }

                public static function get_plugin_name() {
                    $data = get_plugin_data( RRTNGG_PLUGIN_FILE );

                    return $data['Name'];
                }

                public static function load_plugin_textdomain() {
                    add_filter( 'plugin_locale', 'RRTNGG_Core::check_de_locale' );

                    load_plugin_textdomain(
                        '5-stars-rating-funnel',
                        false,
                        RRTNGG_REL_PLUGIN_REL_FILE . '/languages/'
                    );

                    remove_filter( 'plugin_locale', 'RRTNGG_Core::check_de_locale' );
                }

                public static function check_de_locale( $domain ) {
                    $site_lang    = get_user_locale();
                    $de_lang_list = [
                        'de_CH_informal',
                        'de_DE_formal',
                        'de_AT',
                        'de_CH',
                        'de_DE'
                    ];

                    if ( in_array( $site_lang, $de_lang_list ) ) {
                        return 'de_DE';
                    }
                    return $domain;
                }
        }
