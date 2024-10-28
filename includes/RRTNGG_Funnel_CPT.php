<?php

    class RRTNGG_Funnel_CPT {
        public static function create() {
            $is_premium = RRTNGG_License_Manager::is_premium();

            if ( empty( $is_premium ) ) {
                $version = ' - ' . __( 'Free', '5-stars-rating-funnel' );
            } elseif ( ! empty( $is_premium['is_trial'] ) ) {
                $version = ' - ' . __( 'Trial', '5-stars-rating-funnel' );
            } else {
                $version = '';
            }

            $labels = [
                'name'                  => _x( 'Rating Funnels', 'Post Type General Name', '5-stars-rating-funnel' ) . $version,
                'singular_name'         => _x( 'Rating Funnel', 'Post Type Singular Name', '5-stars-rating-funnel' ),
                'menu_name'             => _x( 'RRatingg', 'Admin Menu text', '5-stars-rating-funnel' ) . $version,
                'name_admin_bar'        => _x( 'Rating Funnel', 'Add New on Toolbar', '5-stars-rating-funnel' ),
                'archives'              => __( 'Rating Funnel Archives', '5-stars-rating-funnel' ),
                'attributes'            => __( 'Rating Funnel Attributes', '5-stars-rating-funnel' ),
                'parent_item_colon'     => __( 'Parent Rating Funnel:', '5-stars-rating-funnel' ),
                'all_items'             => __( 'All Rating Funnels', '5-stars-rating-funnel' ),
                'add_new_item'          => __( 'Add New Rating Funnel', '5-stars-rating-funnel' ),
                'add_new'               => __( 'Add New', '5-stars-rating-funnel' ),
                'new_item'              => __( 'New Rating Funnel', '5-stars-rating-funnel' ),
                'edit_item'             => __( 'Edit Rating Funnel', '5-stars-rating-funnel' ),
                'update_item'           => __( 'Update Rating Funnel', '5-stars-rating-funnel' ),
                'view_item'             => __( 'View Rating Funnel', '5-stars-rating-funnel' ),
                'view_items'            => __( 'View Rating Funnels', '5-stars-rating-funnel' ),
                'search_items'          => __( 'Search Rating Funnel', '5-stars-rating-funnel' ),
                'not_found'             => __( 'Not found', '5-stars-rating-funnel' ),
                'not_found_in_trash'    => __( 'Not found in Trash', '5-stars-rating-funnel' ),
                'featured_image'        => __( 'Funnel Logo', '5-stars-rating-funnel' ),
                'set_featured_image'    => __( 'Set funnel logo', '5-stars-rating-funnel' ),
                'remove_featured_image' => __( 'Remove funnel logo', '5-stars-rating-funnel' ),
                'use_featured_image'    => __( 'Use as funnel logo', '5-stars-rating-funnel' ),
                'insert_into_item'      => __( 'Insert into Rating Funnel', '5-stars-rating-funnel' ),
                'uploaded_to_this_item' => __( 'Uploaded to this Rating Funnel', '5-stars-rating-funnel' ),
                'items_list'            => __( 'Rating Funnels list', '5-stars-rating-funnel' ),
                'items_list_navigation' => __( 'Rating Funnels list navigation', '5-stars-rating-funnel' ),
                'filter_items_list'     => __( 'Filter Rating Funnels list', '5-stars-rating-funnel' )
            ];

            $args = [
                'label'               => __( 'Rating Funnel', '5-stars-rating-funnel' ),
                'description'         => __( '', '5-stars-rating-funnel' ),
                'labels'              => $labels,
                'menu_icon'           => 'dashicons-star-filled',
                'supports'            => ['title', 'thumbnail'],
                // 'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'comments', 'trackbacks', 'page-attributes', 'post-formats', 'custom-fields'),
                'taxonomies'          => [],
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 5,
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => false,
                'can_export'          => true,
                'has_archive'         => false,
                'hierarchical'        => false,
                'exclude_from_search' => true,
                'show_in_rest'        => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post'
            ];
            register_post_type( 'rratingg', $args );
        }

        public static function flush_rewrite_rules() {
            $flush_rewrite_rules = get_option( 'rrtngg_flush_rewrite_rules' );

            if ( empty( $flush_rewrite_rules ) ) {
                flush_rewrite_rules();

                update_option( 'rrtngg_flush_rewrite_rules', 1 );
            }
        }

        public static function metaboxes() {
            $metaboxes = self::get_metaboxes();

            foreach ( $metaboxes as $id => $metabox ) {
                $template = RRTNGG_ABS_PATH . 'templates/admin/metaboxes/funnel/' . $metabox['callback_args']['id'] . '.php';

                if ( file_exists( $template ) ) {
                    add_meta_box(
                        $id,
                        $metabox['title'],
                        ['RRTNGG_Funnel_CPT', 'generate_metabox'],
                        'rratingg',
                        $metabox['context'],
                        $metabox['priority'],
                        $metabox['callback_args']
                    );
                }
            }
        }

        public static function generate_metabox( $post, $metabox ) {
            $mb_id  = $metabox['args']['id'];
            $fields = $metabox['args']['fields'];

            include_once RRTNGG_ABS_PATH . 'templates/admin/metaboxes/funnel/' . $mb_id . '.php';
        }

        public static function get_metaboxes() {
            return [
                'footer_settings'   => [
                    'title'         => __( 'Page settings', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'footer_settings',
                        'fields' => self::get_metabox_fields( 'footer_settings' )
                    ]
                ],
                'general_settings'  => [
                    'title'         => __( 'Services Integration Settings', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'general_settings',
                        'fields' => self::get_metabox_fields( 'general_settings' )
                    ]
                ],
                'rating_page'       => [
                    'title'         => __( 'Rating Page', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'rating_page',
                        'fields' => self::get_metabox_fields( 'rating_page' )
                    ]
                ],
                'positive_response' => [
                    'title'         => __( 'Positive Answers', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'positive_answer',
                        'fields' => self::get_metabox_fields( 'positive_answer' )
                    ]
                ],
                'negative_response' => [
                    'title'         => __( 'Negative Answers', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'negative_answer',
                        'fields' => self::get_metabox_fields( 'negative_answer' )
                    ]
                ],
                'feedback_page'     => [
                    'title'         => __( 'Feedback Page', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'feedback_page',
                        'fields' => self::get_metabox_fields( 'feedback_page' )
                    ]
                ],
                'email_reminders'   => [
                    'title'         => __( 'Funnel Emails', '5-stars-rating-funnel' ),
                    'callback'      => 'generate_metabox',
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => [
                        'id'     => 'email_reminders',
                        'fields' => self::get_metabox_fields( 'email_reminders' )
                    ]
                ]
            ];
        }

        public static function get_fields() {
            $fields = [];

            $services        = RRTNGG_Services_Manager::get_services();
            $services_fields = [];
            $post_language =    get_post_meta( get_the_ID(  ), 'rrtngg_funnel_created_language', true ); 
            $current_user_profile_url = get_edit_profile_url( get_current_user_id() );
            $general_settings_url = admin_url( 'options-general.php' );
            $lang_settings = [];
            if(!empty($post_language) && $post_language != '' ){
                $lang_settings = [
                    /* translators: %s is a placeholder for the external link */
                    'label'             =>  __( 'Language', '5-stars-rating-funnel' ),
                    'type'              => 'html',
                    'html'              => sprintf( __( 'Funnel created in <strong> %s language </strong>. Funnel frontend language stays in its language when site or user language is changed. To change the funnel language, switch to the language you like in <a href="2%s" target="_blank">current user</a> or <a href="3%s" target="_blank">general settings</a> and create a new funnel.', '5-stars-rating-funnel' ), get_language_name_from_abbreviation($post_language), $current_user_profile_url , $general_settings_url ),
                ];
            } else{
                $lang_settings = [
                    /* translators: %s is a placeholder for the external link */
                    'label'             =>  '',
                    'type'              => 'html',
                    'html'              => '',
                ];
            }
            if ( ! empty( $services ) ) {
                $i = 1;

                foreach ( $services as $sid => $service ) {
                    $services_fields['rrtngg_service_divider_' . $sid] = [
                        'type' => 'html',
                        'html' => rrtngg_get_table_divider()
                    ];

                    if ( empty( $service['available'] ) ) {
                        $services_fields['rrtngg_services_' . $sid . '_hide_open'] = [
                            'type' => 'html',
                            'html' => rrtngg_get_email_only_pro_description( $service['label'] ) . '</tbody></table><div style="display: none;">'
                        ];
                    } else {
                        ob_start();
                    ?>
					</tbody></table><h3 style="margin: 0;">
					<?php
                        if ( ! empty( $service['label'] ) ) {
                                            ?>
						<h3 style="margin: 0;"><?php echo esc_html( $service['label'] ); ?></h3>
						<?php
                            }

                                                if ( ! empty( $service['description'] ) ) {
                                                    echo wp_kses( $service['description'], RRTNGG_Manager::get_allowed_tags() );
                                                }
                                            ?>
					<table class="form-table"><tbody>
					<?php
                        $html                                            = ob_get_clean();
                                            $services_fields['rrtngg_service_label_' . $sid] = [
                                                'type' => 'html',
                                                'html' => $html
                                            ];
                                        }
                                        $services_fields['rrtngg_services_' . $sid . '_enabled'] = [
                                            'label'   => __( 'Enable / Disable', '5-stars-rating-funnel' ),
                                            'type'    => 'select',
                                            'options' => [
                                                'enabled'  => __( 'Enabled', '5-stars-rating-funnel' ),
                                                'disabled' => __( 'Disabled', '5-stars-rating-funnel' )
                                            ],
                                            'default' => 'disabled',
                                            'classes' => 'rrtngg_visible_control',
                                            'data'    => [
                                                'control' => 'rrtngg_services_' . $sid . '_visible'
                                            ]
                                        ];

                                        if ( $service['type'] === 'button' ) {
                                            $services_fields['rrtngg_services_' . $sid . '_btn_color'] = [
                                                'label'             => __( 'Button Color', '5-stars-rating-funnel' ),
                                                'type'              => 'text',
                                                'default'           => '#0d6efd',
                                                'classes'           => 'rrtngg-color-picker',
                                                'container_classes' => 'rrtngg_services_' . $sid . '_visible rrtngg_services_' . $sid . '_visible_enabled'
                                            ];

                                            $services_fields['rrtngg_services_' . $sid . '_btn_text'] = [
                                                'label'             => __( 'Button Text', '5-stars-rating-funnel' ),
                                                'type'              => 'text',
                                                'default'           => ! empty( $service['defaults']['btn_text'] ) ? esc_html( $service['defaults']['btn_text'] ) : '',
                                                'container_classes' => 'rrtngg_services_' . $sid . '_visible rrtngg_services_' . $sid . '_visible_enabled'
                                            ];

                                            $services_fields['rrtngg_services_' . $sid . '_btn_text_color'] = [
                                                'label'             => __( 'Button Text Color', '5-stars-rating-funnel' ),
                                                'type'              => 'text',
                                                'default'           => '#ffffff',
                                                'classes'           => 'rrtngg-color-picker',
                                                'container_classes' => 'rrtngg_services_' . $sid . '_visible rrtngg_services_' . $sid . '_visible_enabled'
                                            ];

                                            $services_fields['rrtngg_services_' . $sid . '_below_btn_text'] = [
                                                'label'             => __( 'Below Button Text', '5-stars-rating-funnel' ),
                                                'type'              => 'textarea',
                                                'default'           => ! empty( $service['defaults']['below_btn_text'] ) ? $service['defaults']['below_btn_text'] : '',
                                                'allow_empty'       => true,
                                                'container_classes' => 'rrtngg_services_' . $sid . '_visible rrtngg_services_' . $sid . '_visible_enabled'
                                            ];

                                            // $services_fields['rrtngg_services_'.$sid.'_link_text'] = [
                                            // 'label' => __( 'Link Text', '5-stars-rating-funnel' ),
                                            // 'type' => 'text',
                                            // 'container_classes' => 'rrtngg_services_'.$sid.'_visible rrtngg_services_'.$sid.'_visible_enabled',
                                            // ];
                                        }

                                        if ( ! empty( $service['fields'] ) ) {
                                            foreach ( $service['fields'] as $sfid => $field ) {
                                                $field['container_classes']                                     = 'rrtngg_services_' . $sid . '_visible rrtngg_services_' . $sid . '_visible_enabled';
                                                $services_fields['rrtngg_services_' . $sid . '_field_' . $sfid] = $field;
                                            }
                                        }

                                        if ( empty( $service['available'] ) ) {
                                            $services_fields['rrtngg_services_' . $sid . '_hide_close'] = [
                                                'type' => 'html',
                                                'html' => '</div><table class="form-table"><tbody>'
                                            ];
                                        }

                                        ++$i;
                                    }
                                }
                                $services_fields['rrtngg_services_custom_link_enabled']['default'] = 'enabled';
                                $fields['general_settings']                                        = $services_fields;

                                $fields['rating_page']                                  = [];
                                $fields['rating_page']['rrtngg_rating_require_confirm'] = [
                                    'label' => __( 'Require rating confirmation', '5-stars-rating-funnel' ),
                                    'type'  => 'checkbox'
                                    // 'default_state' => 1,
                                ];
                                $fields['rating_page']['rrtngg_rating_view'] = [
                                    'label'   => __( 'Rating View', '5-stars-rating-funnel' ),
                                    'type'    => 'select',
                                    'options' => [
                                        'like_unlike'  => __( 'Like / Unlike', '5-stars-rating-funnel' ),
                                        'stars_rating' => __( 'Stars Rating', '5-stars-rating-funnel' )
                                    ],
                                    'default' => 'stars_rating',
                                    'classes' => 'rrtngg_visible_control',
                                    'data'    => [
                                        'control' => 'rrtngg_template_visible'
                                    ]
                                ];
                                $fields['rating_page']['rrtngg_like_unlike_btn_style'] = [
                                    'label'             => __( 'Like / Unlike Button Style', '5-stars-rating-funnel' ),
                                    'type'              => 'select',
                                    'options'           => rrtngg_get_like_unlike_btn_style_options(),
                                    'default'           => 'thumbs_1',
                                    'classes'           => 'rrtngg_visible_control',
                                    'data'              => [
                                        'control' => 'rrtngg_like_unlike_btn_style_prev'
                                    ],
                                    'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_like_unlike',
                                    'description'       => rrtngg_get_like_unlike_btn_style_description()
                                ];
                                $fields['rating_page']['rrtngg_like_btn_color'] = [
                                    'label'             => __( 'Like Btn Color', '5-stars-rating-funnel' ),
                                    'type'              => 'text',
                                    'default'           => '#65e565',
                                    'classes'           => 'rrtngg-color-picker',
                                    'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_like_unlike'
                                ];
                                $fields['rating_page']['rrtngg_unlike_btn_color'] = [
                                    'label'             => __( 'Unlike Btn Color', '5-stars-rating-funnel' ),
                                    'type'              => 'text',
                                    'default'           => '#e56565',
                                    'classes'           => 'rrtngg-color-picker',
                                    'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_like_unlike'
                                ];
                                if ( ! RRTNGG_License_Manager::is_premium() ) {
                                    ob_start();
                                ?>
			<p>
				<?php echo __( 'You can choose would it be 4 & 5 stars or only 5 stars to open positive rating page.', '5-stars-rating-funnel' ); ?>
			</p>
			<?php
                $pre_html = ob_get_clean();

                            ob_start();
                        ?>
			<p>
				<?php echo __( 'In free version it would be 4 & 5 stars.', '5-stars-rating-funnel' ); ?>
			</p>
			<?php
                $post_html = ob_get_clean();

                            $fields['rating_page']['rrtngg_stars_rating_like_unlike_only_pro_description'] = [
                                'type' => 'html',
                                'html' => rrtngg_get_email_only_pro_description( __( 'Good review', '5-stars-rating-funnel' ), ' class="rrtngg_template_visible rrtngg_template_visible_stars_rating"', $pre_html, $post_html )
                            ];
                            $fields['rating_page']['rrtngg_stars_rating_like_unlike_hide_open'] = [
                                'type' => 'html',
                                'html' => '</tbody></table><div style="display: none;">'
                            ];
                        }
                        $fields['rating_page']['rrtngg_stars_rating_like_unlike'] = [
                            'label'             => __( 'Good review', '5-stars-rating-funnel' ),
                            'type'              => 'select',
                            'options'           => [
                                'five_stars_only' => __( '5 stars only', '5-stars-rating-funnel' ),
                                'five_four_stars' => __( '5 and 4 stars', '5-stars-rating-funnel' )
                            ],
                            'default'           => 'five_four_stars',
                            'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_stars_rating'
                        ];
                        if ( ! RRTNGG_License_Manager::is_premium() ) {
                            $fields['rating_page']['rrtngg_stars_rating_like_unlike_hide_close'] = [
                                'type' => 'html',
                                'html' => '</div><table class="form-table"><tbody>'
                            ];
                        }
                        $fields['rating_page']['rrtngg_stars_rating_fill_color'] = [
                            'label'             => __( 'Filled Star Color', '5-stars-rating-funnel' ),
                            'type'              => 'text',
                            'default'           => '#ffcb57',
                            'classes'           => 'rrtngg-color-picker',
                            'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_stars_rating'
                        ];
                        $fields['rating_page']['rrtngg_stars_rating_empty_color'] = [
                            'label'             => __( 'Empty Star Color', '5-stars-rating-funnel' ),
                            'type'              => 'text',
                            'default'           => '#3f3a34',
                            'classes'           => 'rrtngg-color-picker',
                            'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_stars_rating'
                        ];
                        $fields['rating_page']['rrtngg_rating_content'] = [
                            'label'       => __( 'Custom content', '5-stars-rating-funnel' ),
                            'type'        => 'wpeditor',
                            'default'     => rrtngg_get_pages_default_content( 'rating_page' ),
                            'allow_empty' => true
                        ];

                        $fields['positive_answer'] = [
                            'rrtngg_positive_response_content' => [
                                'label'       => __( 'Custom content', '5-stars-rating-funnel' ),
                                'type'        => 'wpeditor',
                                'default'     => rrtngg_get_pages_default_content( 'positive_answer' ),
                                'allow_empty' => true
                            ]
                        ];

                        $fields['negative_answer'] = [];

                        if ( ! RRTNGG_License_Manager::is_premium() ) {
                            $fields['negative_answer']['rrtngg_negative_response_only_pro_description'] = [
                                'type' => 'html',
                                'html' => rrtngg_get_email_only_pro_description( __( 'Disable Rating buttons', '5-stars-rating-funnel' ) )
                            ];
                            $fields['negative_answer']['rrtngg_negative_response_hide_open'] = [
                                'type' => 'html',
                                'html' => '</tbody></table><div style="display: none;">'
                            ];
                        }

                        $fields['negative_answer']['rrtngg_negative_response_disable_rating'] = [
                            'label'             => __( 'Disable Rating buttons', '5-stars-rating-funnel' ),
                            'type'              => 'checkbox',
                            'short_description' => __( 'Not recommended if you use this when asking for Google reviews, to comply with their policy of not selectively asking for reviews only from people with positive feedback.', '5-stars-rating-funnel' )
                        ];

                        if ( ! RRTNGG_License_Manager::is_premium() ) {
                            $fields['negative_answer']['rrtngg_negative_response_close'] = [
                                'type' => 'html',
                                'html' => '</div><table class="form-table"><tbody>'
                            ];
                        }

                        $fields['negative_answer']['rrtngg_negative_response_content'] = [
                            'label'       => __( 'Custom content', '5-stars-rating-funnel' ),
                            'type'        => 'wpeditor',
                            'default'     => rrtngg_get_pages_default_content( 'negative_answer' ),
                            'allow_empty' => true
                        ];

                        $fields['feedback_page'] = [
                            'rrtngg_feedback_admin_email'              => [
                                'label' => __( 'Feedback admin email', '5-stars-rating-funnel' ),
                                'type'  => 'text'
                            ],
                            'rrtngg_feedback_admin_email_subject'      => [
                                'label' => __( 'Feedback admin email subject', '5-stars-rating-funnel' ),
                                'type'  => 'text'
                            ],
                            'rrtngg_feedback_first_name_field_enabled' => [
                                'label' => __( 'Enable First Name Field', '5-stars-rating-funnel' ),
                                'type'  => 'checkbox'
                                // 'default_state' => 1,
                            ],
                            'rrtngg_feedback_last_name_field_enabled'  => [
                                'label' => __( 'Enable Last Name Field', '5-stars-rating-funnel' ),
                                'type'  => 'checkbox'
                                // 'default_state' => 1,
                            ],
                            'rrtngg_feedback_email_field_enabled'      => [
                                'label' => __( 'Enable Email Field', '5-stars-rating-funnel' ),
                                'type'  => 'checkbox'
                                // 'default_state' => 1,
                            ],
                            'rrtngg_html_divider_1'                    => [
                                'type' => 'html',
                                'html' => rrtngg_get_table_divider()
                            ],
                            'rrtngg_feedback_button_fill_color'        => [
                                'label'             => __( 'Button Color', '5-stars-rating-funnel' ),
                                'type'              => 'text',
                                'default'           => '#0d6efd',
                                'classes'           => 'rrtngg-color-picker',
                                'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_stars_rating'
                            ],
                            'rrtngg_feedback_button_fill_hover_color'  => [
                                'label'             => __( 'Hover Color', '5-stars-rating-funnel' ),
                                'type'              => 'text',
                                'default'           => '#0b5ed7',
                                'classes'           => 'rrtngg-color-picker',
                                'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_stars_rating'
                            ],
                            'rrtngg_feedback_button_text_color'        => [
                                'label'             => __( 'Text Color', '5-stars-rating-funnel' ),
                                'type'              => 'text',
                                'default'           => '#ffffff',
                                'classes'           => 'rrtngg-color-picker',
                                'container_classes' => 'rrtngg_template_visible rrtngg_template_visible_stars_rating'
                            ],
                            'rrtngg_feedback_page_content'             => [
                                'label'       => __( 'Custom content', '5-stars-rating-funnel' ),
                                'type'        => 'wpeditor',
                                'default'     => rrtngg_get_pages_default_content( 'feedback_page' ),
                                'allow_empty' => true
                            ]
                        ];
                        // default state are 'on' or 1
                        $external_link_feedback    = 'https://rratingg.tawk.help/en-us/article/public-rating-no-email-invitation-needed';
                        $fields['footer_settings'] = [
                            'rrtngg_public_feedback_language'    => $lang_settings
                            ,
                            'rrtngg_public_feedback'     => [
                                /* translators: %s is a placeholder for the external link */
                                'label'             => sprintf( __( 'Enable the public feedback <a href="%s" target="_blank">New ⓘ</a>', '5-stars-rating-funnel' ), $external_link_feedback ),
                                'type'              => 'checkbox',
                                'short_description' => __( 'Make it possible to open the funnel link without e-mail invitation', '5-stars-rating-funnel' ),
                                'default_state'     => 1,
                                'disable_label_key' => true
                            ],

                            'rrtngg_content_hide_header' => [
                                'label'             => __( 'Show Header', '5-stars-rating-funnel' ),
                                'type'              => 'checkbox',
                                'short_description' => __( 'By Enableing Will show the site header', '5-stars-rating-funnel' )
                            ],
                            'rrtngg_content_hide_footer' => [
                                'label'             => __( 'Show Footer', '5-stars-rating-funnel' ),
                                'type'              => 'checkbox',
                                'short_description' => __( 'By enableing will show the site footer.', '5-stars-rating-funnel' )
                            ],
                            'rrtngg_logo_max_width'      => [
                                'label'             => __( 'Logo maximum width (px)', '5-stars-rating-funnel' ),
                                'type'              => 'number',
                                'short_description' => __( 'Left empty or set 0 to make it maximum width 100%', '5-stars-rating-funnel' )
                            ],
                            'rrtngg_logo_padding_top'    => [
                                'label'             => __( 'Logo padding top (px) - only for desktop and tablet screen size', '5-stars-rating-funnel' ),
                                'type'              => 'number',
                                'default'           => '30',
                                'short_description' => __( 'Left empty or set 0 to make no padding', '5-stars-rating-funnel' )
                            ],
                            'rrtngg_logo_padding_bottom' => [
                                'label'             => __( 'Logo padding bottom (px) - only for desktop and tablet screen size', '5-stars-rating-funnel' ),
                                'type'              => 'number',
                                'default'           => '40',
                                'short_description' => __( 'Left empty or set 0 to make no padding', '5-stars-rating-funnel' )
                            ],
                            'rrtngg_page_style'          => [
                                'label'   => __( 'Page style', '5-stars-rating-funnel' ),
                                'type'    => 'select',
                                'options' => [
                                    'inherited' => __( 'Theme inherited', '5-stars-rating-funnel' ),
                                    'custom'    => __( 'Custom styles', '5-stars-rating-funnel' )
                                ],
                                'default' => 'inherited',
                                'classes' => 'rrtngg_visible_control',
                                'data'    => [
                                    'control' => 'rrtngg_page_style_visible'
                                ]
                            ],
                            'rrtngg_page_bg_color'       => [
                                'label'             => __( 'Page Background Color', '5-stars-rating-funnel' ),
                                'type'              => 'text',
                                // 'default' => '#fff',
                                'classes'           => 'rrtngg-color-picker',
                                'container_classes' => 'rrtngg_page_style_visible rrtngg_page_style_visible_custom'
                            ],
                            'rrtngg_page_txt_color'      => [
                                'label'             => __( 'Page Text Color', '5-stars-rating-funnel' ),
                                'type'              => 'text',
                                // 'default' => '#333333',
                                'classes'           => 'rrtngg-color-picker',
                                'container_classes' => 'rrtngg_page_style_visible rrtngg_page_style_visible_custom'
                            ],
                            'rrtngg_page_link_color'     => [
                                'label'             => __( 'Page Link Color', '5-stars-rating-funnel' ),
                                'type'              => 'text',
                                // 'default' => '#0d6efd',
                                'classes'           => 'rrtngg-color-picker',
                                'container_classes' => 'rrtngg_page_style_visible rrtngg_page_style_visible_custom'
                            ],
                            'rrtngg_footer_content'      => [
                                'label'   => __( 'Footer content', '5-stars-rating-funnel' ),
                                'type'    => 'wpeditor',
                                'default' => '<p style="text-align: center;">' . __( 'Copyright © {current_year} by {current_domain_link}. All Rights Reserved.', '5-stars-rating-funnel' ) . '</p>'
                            ]
                        ];

                        $email_steps = RRTNGG_Manager::get_mail_steps();

                        if ( ! empty( $email_steps ) ) {
                            $email_reminders = [];

                            $email_reminders['rrtngg_email_description_' . $i] = [
                                'type' => 'html',
                                'html' => rrtngg_get_email_settings_description()
                            ];

                            $i = 1;

                            foreach ( $email_steps as $id => $email_step ) {
                                if ( 'no_mail' === $id ) {
                                    continue;
                                }

                                $email_reminders['rrtngg_email_divider_' . $i] = [
                                    'type' => 'html',
                                    'html' => rrtngg_get_table_divider()
                                ];

                                $email_reminders['rrtngg_email_label_' . $id] = [
                                    'type' => 'html',
                                    'html' => '</tbody></table><h3 style="margin: 0;">' . $email_step['label'] . '</h3><table class="form-table"><tbody>'
                                ];

                                if ( empty( $email_step['available'] ) ) {
                                    $email_reminders['rrtngg_email_only_pro_description_' . $i] = [
                                        'type' => 'html',
                                        'html' => rrtngg_get_email_only_pro_description( $email_step['label'] )
                                    ];
                                    $email_reminders['rrtngg_email_hide_open_' . $i] = [
                                        'type' => 'html',
                                        'html' => '</tbody></table><div style="display: none;">'
                                    ];
                                }

                                if ( 'invitation' !== $id ) {
                                    $email_reminders['rrtngg_' . $id . '_enabled'] = [
                                        'label' => __( 'Enable', '5-stars-rating-funnel' ),
                                        'type'  => 'checkbox'
                                        // 'default_state' => 1,
                                    ];
                                }

                                $email_reminders['rrtngg_' . $id . '_delay'] = [
                                    'label'   => __( 'Delay before sending email (days)', '5-stars-rating-funnel' ),
                                    'type'    => 'number',
                                    'step'    => 'any',
                                    'default' => 4
                                ];

                                $email_reminders['rrtngg_' . $id . '_subject'] = [
                                    'label'   => __( 'Subject', '5-stars-rating-funnel' ),
                                    'type'    => 'text',
                                    'default' => $email_step['subject']
                                ];

                                $email_reminders['rrtngg_' . $id . '_content'] = [
                                    'label'   => __( 'Content', '5-stars-rating-funnel' ),
                                    'type'    => 'wpeditor',
                                    'default' => rrtngg_get_mail_steps_default_content( $id )
                                ];

                                if ( empty( $email_step['available'] ) ) {
                                    $email_reminders['rrtngg_email_hide_close_' . $i] = [
                                        'type' => 'html',
                                        'html' => '</div><table class="form-table"><tbody>'
                                    ];
                                }

                                ++$i;
                            }

                            $fields['email_reminders'] = $email_reminders;
                        }

                        return $fields;
                    }

                    public static function get_metabox_fields( $id ) {
                        $fields = self::get_fields();

                        if ( ! empty( $fields[$id] ) ) {
                            return $fields[$id];
                        }

                        return [];
                    }

                    public static function make_active( $post ) {
                        if ( $post->post_type !== 'rratingg' ) {
                            return '';
                        }
                        $is_premium = ! empty( RRTNGG_License_Manager::is_premium() );
                        if ( $is_premium && !self::is_active( $post->ID )) {
                            $is_premium =  RRTNGG_License_Manager::is_premium() ;
                            $active_funnel_list = self::get_active();
                            if($is_premium['is_agency']){
                                if ( !empty(  $active_funnel_list ) && sizeof($active_funnel_list) >=10 ) {
                                    return '';
                                }
                            }elseif($is_premium['is_unlimited']){
            
                            }else{
                                if ( !empty(  $active_funnel_list ) && sizeof($active_funnel_list) >=3 ) {
                                    return '';
                                }
                            }
                            
                            
                        }else{
                            $active_funnel_list = self::get_active();

                            if ( !empty(  $active_funnel_list ) && sizeof($active_funnel_list) >=1 && !self::is_active( $post->ID )) {
                                return '';
                            }
                        }

                        $status_name = __( 'Enable', '5-stars-rating-funnel' );
                        $is_checked  = '';
                        $funnel_status = __( 'Inactive', '5-stars-rating-funnel' );
                        if ( self::is_active( $post->ID ) ) {
                            $status_name = __( 'Disable', '5-stars-rating-funnel' );
                            $is_checked  = 'checked';
                            $funnel_status = __( 'Active', '5-stars-rating-funnel' );

                            // return '';
                        }
                    	?>
							<div class="misc-pub-section misc-pub-post-funnel-is-active">
								<?php _e( 'Funnel Status:', '5-stars-rating-funnel' );?>
								<span id="post-funnel-is-active-display">
									<?php echo esc_html( $funnel_status ); ?>
								</span>

								<a href="#post_funnel_is_active" class="edit-post-funnel-is-active hide-if-no-js" role="button">
									<span aria-hidden="true"><?php _e( 'Edit' );?></span>
									<span class="screen-reader-text"><?php _e( 'Edit status' );?></span>
								</a>

								<div id="post-funnel-is-active-select" class="hide-if-js">
									<input
										type="checkbox"
										name="rrtngg_funnel_active"
										id="rrtngg_funnel_active"
                                        <?php echo $is_checked; ?>
									/>

									<label for="rrtngg_funnel_active">
										<?php echo $status_name ;?>
									</label>

									<a href="#post_funnel_is_active" class="save-post-funnel-is-active hide-if-no-js button">
										<?php _e( 'OK', '5-stars-rating-funnel' );?>
									</a>

									<a href="#post_funnel_is_active" class="cancel-post-funnel-is-active hide-if-no-js button-cancel">
										<?php _e( 'Cancel', '5-stars-rating-funnel' );?>
									</a>
								</div>
							</div>
						<?php
            }

                public static function new_funnel( $post_id, $post, $update ) {
                    if ( $post->post_type !== 'rratingg' || ! empty( $update ) ) {
                        return;
                    }

                    $check_1_2_32_upgrade = get_option( 'rrtngg_1_2_32_upgrade' );
                    if ( empty( $check_1_2_32_upgrade ) ) {
                        include_once RRTNGG_ABS_PATH . 'includes/RRTNGG_Upgrade.php';
                        RRTNGG_Upgrade::set_1_2_32_upgrade();
                    }
                    $current_user_language  =  get_user_locale(get_current_user_id(  ));
                    update_post_meta( $post_id, 'rrtngg_funnel_created_language', $current_user_language );


                    $default_thumb = get_option( 'rrtngg_default_funnel_logo' );

                    if ( ! empty( $default_thumb ) ) {
                        update_post_meta( $post_id, '_thumbnail_id', $default_thumb );
                    }
                }

                public static function save_fields( $post_id ) {
                    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                        return $post_id;
                    }

                    if ( ! empty( $_POST['rrtngg_funnel_active'] ) ) {
                        if ( ! self::is_active( $post_id ) ) {
                            update_post_meta( $post_id, 'rrtngg_funnel_is_active', 1 );
                        }
                    }else{
                        update_post_meta( $post_id, 'rrtngg_funnel_is_active', 0 );

                    }

                    $fields_by_mb    = self::get_fields();
                    $fields          = [];
                    $funnel_settings = empty( get_post_meta( get_the_ID(), 'rrtngg_funnel_settings', true ) ) ? [] : get_post_meta( get_the_ID(), 'rrtngg_funnel_settings', true );

                    foreach ( $fields_by_mb as $mb_fields ) {
                        $fields = array_merge( $fields, $mb_fields );
                    }

                    if ( ! empty( $fields ) ) {
                        foreach ( $fields as $id => $field ) {
                            if ( 'html' === $field['type'] ) {
                                unset( $fields[$id] );
                            }
                        }

                        foreach ( $fields as $id => $field ) {
                            $name = substr( $id, 7 );

                            if ( isset( $_POST[$id] ) ) {
                                switch ( $field['type'] ) {
                                    case 'wpeditor':
                                        $val = wp_kses_post( $_POST[$id] );
                                        break;
                                    case 'textarea':
                                        $val = sanitize_textarea_field( $_POST[$id] );
                                        break;
                                    case 'email':
                                        $val = sanitize_email( $_POST[$id] );
                                        break;
                                    case 'checkbox':
                                        $val = sanitize_title( $_POST[$id] );
                                        break;
                                    case 'text':
                                        $val = sanitize_text_field( $_POST[$id] );
                                        break;
                                    default:
                                        $val = sanitize_text_field( $_POST[$id] );
                                }
                                $funnel_settings[$name] = $val;
                                update_post_meta( $post_id, $id, $val );
                            } elseif ( $field['type'] === 'checkbox' && isset( $funnel_settings[$name] ) && ! isset( $_POST[$id] ) ) {
                                $val                    = '';
                                $funnel_settings[$name] = $val;
                                update_post_meta( $post_id, $id, '' );
                            } elseif ( $field['type'] === 'checkbox' && ! isset( $funnel_settings[$name] ) ) {
                                $val                    = isset( $field['default_state'] ) ? $field['default_state'] : '';
                                $funnel_settings[$name] = $val;
                                update_post_meta( $post_id, $id, $val );
                            }
                        }
                        update_post_meta( $post_id, 'rrtngg_funnel_settings', $funnel_settings );
                    }
                }

                public static function get( $id, $published = true, $preview = false ) {
                    $funnel = get_post( $id );

                    if ( empty( $funnel ) ) {
                        return false;
                    }
                    if ( $funnel->post_type !== 'rratingg' ) {
                        return false;
                    }
                    if ( $published && $funnel->post_status !== 'publish' ) {
                        return false;
                    }
                    $is_premium = ! empty( RRTNGG_License_Manager::is_premium() );
                    if ( ! $preview && ! in_array($id , self::get_active(  )) ) {
                        return false;
                    }

                    $funnel->funnel_settings = get_post_meta( $id, 'rrtngg_funnel_settings', true );

                    return $funnel;
                }

                public static function get_active() {
                    $active_post = false;
                    $is_premium = ! empty(RRTNGG_License_Manager::is_premium());
                    $allowed_active = 1;
                    if($is_premium){
                        $is_premium = RRTNGG_License_Manager::is_premium();

                        if($is_premium['is_agency']){
                            $allowed_active = 10;
                        }elseif($is_premium['is_unlimited']){
                            $allowed_active = 0;
                        }else{
                            $allowed_active = 3;
                        }
                    }
                    
                    global $wpdb;

                    $sql              = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'rrtngg_funnel_is_active'";
                    $results          = $wpdb->get_results( $sql, ARRAY_A );
                    $active_post_list = [];
					
                    if ( ! empty( $results ) ) {
                        foreach ( $results as $result ) {
                            $post_id            = isset( $result['post_id'] ) ? $result['post_id'] : '';
                            $active_post_status = get_post_status( $result['post_id'] );
                            if ( $active_post_status == 'publish' ) {
                                // $wpdb->update( $wpdb->postmeta, ['meta_value' => 1], ['post_id' => $post_id, 'meta_key' => 'rrtngg_funnel_is_active'] );
                                $active_status =  get_post_meta( $result['post_id'], 'rrtngg_funnel_is_active', true );
                                if($active_status != '' && $active_status == 1){
                                    if($allowed_active != 0 && sizeof($active_post_list) >= $allowed_active){
                                        $wpdb->update( $wpdb->postmeta, ['meta_value' => 0], ['post_id' => $post_id, 'meta_key' => 'rrtngg_funnel_is_active'] );
                                    }else{
                                        array_push( $active_post_list, $post_id );

                                    }
                                }
                            } else {
                                $wpdb->update( $wpdb->postmeta, ['meta_value' => 0], ['post_id' => $post_id, 'meta_key' => 'rrtngg_funnel_is_active'] );
                            }
                        }
                    }
                    
                    return $active_post_list;
                }

                public static function is_active( $id ) {
                    $active_funnel_list = self::get_active();

                    if ( empty(  $active_funnel_list ) ) {
                        return false;
                    }

                    if (  in_array((int) $id, (array) $active_funnel_list ) ) {
                        return true;
                    }

                    return false;
                }
        }
