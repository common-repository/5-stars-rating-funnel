<?php
function rrtngg_get_current_domain_name() {
    $domain = get_site_url();
    $domain = preg_replace( '/^http:\/\//i', '', $domain );
    $domain = preg_replace( '/^https:\/\//i', '', $domain );
    $domain = preg_replace( '/^www./i', '', $domain );

    return $domain;
}
function rrtngg_get_service_mybusiness_link_description() {
    ob_start();
    ?>
    <p>
        <?php
        echo sprintf( __( 'Find your Place ID %1$shere%2$s', '5-stars-rating-funnel' ), '<a target="_blank" href="https://developers.google.com/maps/documentation/places/web-service/place-id">', '</a>' )
        ?>
    </p>
    <?php
    return ob_get_clean();
}

function rrtngg_get_service_goggle_review_merchant_id_description() {
    ob_start();
    ?>
    <p>
        <?php
        echo __( 'Your Merchant Center ID. You can get this value from the Google Merchant Center.', '5-stars-rating-funnel' )
        ?>
    </p>
    <?php
    return ob_get_clean();
}

function rrtngg_get_service_goggle_review_delivery_country_description() {
    ob_start();
    ?>
    <p>
        <?php
        echo sprintf( __( "The two-letter country code identifies where the customer's order will be delivered. This value must be in %s format. Do not use “ZZ” for this field. For example, \"US\".", '5-stars-rating-funnel' ), '<a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2">ISO 3166-1 alpha-2</a>' )
        ?>
    </p>
    <?php
    return ob_get_clean();
}

function rrtngg_format_metabox_rows( $label, $input ) {
    return '<div style="margin-top: 10px;"><strong>' . $label . '</strong></div><div>' . $input . '</div>';
}

function rrtngg_get_like_unlike_btn_style_options() {
    return array(
        'thumbs_1'  => __( 'Thumbs up/down 1', '5-stars-rating-funnel' ),
        'thumbs_2'  => __( 'Thumbs up/down 2', '5-stars-rating-funnel' ),
        'face_1'    => __( 'Face 1', '5-stars-rating-funnel' ),
        'face_2'    => __( 'Face 2', '5-stars-rating-funnel' ),
        'face_3'    => __( 'Face 3', '5-stars-rating-funnel' ),
        'heart_1'   => __( 'Heart 1', '5-stars-rating-funnel' ),
        'heart_2'   => __( 'Heart 2', '5-stars-rating-funnel' ),
        'weather_1' => __( 'Weather', '5-stars-rating-funnel' ),
        'check_1'   => __( 'Check / Uncheck', '5-stars-rating-funnel' ),
    );
}

function rrtngg_get_mail_steps_default_content( $step ) {
    $template = RRTNGG_ABS_PATH . 'templates/emails/default/' . $step . '.php';

    if ( ! file_exists( $template ) ) {
        $step = 'invitation';
    }

    $template = RRTNGG_ABS_PATH . 'templates/emails/default/' . $step . '.php';

    if ( ! file_exists( $template ) ) {
        return '';
    }

    ob_start();
    include $template;
    return ob_get_clean();
}

function get_language_name_from_abbreviation( $abbreviation ) {
    // Array mapping language abbreviations to their names
    $language_names = array(
        'en_US' => 'English (United States)',
        'es_ES' => 'Spanish (Spain)',
        'fr_FR' => 'French (France)',
        'de_DE' => 'German (Germany)',
        'it_IT' => 'Italian (Italy)',
        'pt_PT' => 'Portuguese (Portugal)',
        'pt_BR' => 'Portuguese (Brazil)',
        'nl_NL' => 'Dutch (Netherlands)',
        'ru_RU' => 'Russian (Russia)',
        'ja'    => 'Japanese',
        'zh_CN' => 'Chinese (Simplified)',
        'zh_TW' => 'Chinese (Traditional)',
        // Add more language mappings as needed
    );

    // Return the language name if it exists in the mapping array, otherwise return the abbreviation itself
    return isset( $language_names[ $abbreviation ] ) ? $language_names[ $abbreviation ] : $abbreviation;
}

function rrtngg_get_pages_default_content( $step ) {
    $template = RRTNGG_ABS_PATH . 'templates/funnel/default/' . $step . '.php';

    if ( ! file_exists( $template ) ) {
        return '';
    }

    ob_start();
    include $template;
    return ob_get_clean();
}

function rrtngg_get_like_unlike_btn_style_description() {
    $like_unlike_btn_styles = rrtngg_get_like_unlike_btn_style_options();

    ob_start();
    ?>
    <div style="display: inline-block; width: 100px;">
    <?php
    foreach ( $like_unlike_btn_styles as $option => $data ) {
        ?>
        <p class="rrtngg_like_unlike_btn_style_prev rrtngg_like_unlike_btn_style_prev_<?php echo esc_attr( $option ); ?>" style="text-align: center;">
            <i class="rrtngg_like_unlike_btn rrtngg_like_btn icon-rrtngg_like_unlike_<?php echo esc_attr( $option ); ?>_like"></i>
            <i class="rrtngg_like_unlike_btn rrtngg_unlike_btn icon-rrtngg_like_unlike_<?php echo esc_attr( $option ); ?>_unlike"></i>
        </p>
        <?php
    }
    ?>
    </div>
    <?php
    return ob_get_clean();
}

function rrtngg_get_email_only_pro_description( $label, $attr = '', $pre_html = '', $post_html = '' ) {
    ob_start();
    ?>
    <tr<?php echo wp_kses( $attr, RRTNGG_Manager::get_allowed_tags() ); ?>>
        <th scope="row" style="padding: 0 10px 0 0"><label style="display:inline-block; margin: 4px 0;"><?php echo wp_kses( $label, RRTNGG_Manager::get_allowed_tags() ); ?></label></th>
        <td style="padding: 0 10px">
            <?php
            if ( ! empty( $pre_html ) ) {
                echo wp_kses( $pre_html, RRTNGG_Manager::get_allowed_tags() );}
            ?>
            <p style="margin: 4px 0;">
                <?php echo wp_kses( rrtngg_get_only_pro_description(), RRTNGG_Manager::get_allowed_tags() ); ?>
            </p>
            <?php
            if ( ! empty( $post_html ) ) {
                echo wp_kses( $post_html, RRTNGG_Manager::get_allowed_tags() );}
            ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

function rrtngg_get_limit_description() {
    $upgrade_link   = RRTNGG_License_Manager::get_upgrade_link();
    $trial_link     = RRTNGG_License_Manager::get_trial_link();
    $trial_link_btn = '';

    if ( ! empty( $upgrade_link ) ) {
        $upgrade_link = sprintf( __( '%1$sUpgrade now%2$s', '5-stars-rating-funnel' ), '<a class="button button-small button-primary" href="' . esc_url( $upgrade_link ) . '" target="_blank">', '</a>' );
    }

    if ( ! empty( $trial_link ) ) {
        if ( ! empty( $upgrade_link ) ) {
            $trial_link_btn .= __( 'or', '5-stars-rating-funnel' ) . ' ';
        }
        $trial_link_btn .= sprintf( __( '%1$sTry trial%2$s', '5-stars-rating-funnel' ), '<a class="button button-small" href="' . esc_url( $trial_link ) . '" target="_blank">', '</a>' );
    }

    ob_start();
    ?>
    <?php echo __( 'You can not send any more invitations!', '5-stars-rating-funnel' ); ?>
    <br>
    <?php echo wp_kses( $upgrade_link, RRTNGG_Manager::get_allowed_tags() ); ?>
    <?php echo wp_kses( $trial_link_btn, RRTNGG_Manager::get_allowed_tags() ); ?>
    <?php
    return ob_get_clean();
}

function rrtngg_get_only_pro_description() {
    $upgrade_link   = RRTNGG_License_Manager::get_upgrade_link();
    $trial_link     = RRTNGG_License_Manager::get_trial_link();
    $trial_link_btn = '';

    if ( ! empty( $upgrade_link ) ) {
        $upgrade_link = sprintf( __( '%1$sUpgrade now%2$s', '5-stars-rating-funnel' ), '<a class="button button-small button-primary" href="' . esc_url( $upgrade_link ) . '" target="_blank">', '</a>' );
    }

    if ( ! empty( $trial_link ) ) {
        if ( ! empty( $upgrade_link ) ) {
            $trial_link_btn .= __( 'or', '5-stars-rating-funnel' ) . ' ';
        }
        $trial_link_btn .= sprintf( __( '%1$sTry 7 days trial%2$s', '5-stars-rating-funnel' ), '<a class="button button-small" href="' . esc_url( $trial_link ) . '" target="_blank">', '</a>' );
    }

    ob_start();
    ?>
    <?php echo __( 'Available only in pro version!', '5-stars-rating-funnel' ); ?>
    <?php echo wp_kses( $upgrade_link, RRTNGG_Manager::get_allowed_tags() ); ?>
    <?php echo wp_kses( $trial_link_btn, RRTNGG_Manager::get_allowed_tags() ); ?>
    <?php
    return ob_get_clean();
}

function rrtngg_get_email_settings_description() {
    $placeholders = RRTNGG_Manager::get_placeholders();
    ob_start();
    ?>
    </tbody></table>
    <h3 style="margin: 0;"><?php echo __( 'Email templates settings', '5-stars-rating-funnel' ); ?></h3>
    <table class="form-table"><tbody>

    <tr>
        <th scope="row">
            <label style="display:inline-block; margin: 4px 0;" for="rrtngg_feedback_first_name_field_enabled">
                <?php echo __( 'Available placeholders', '5-stars-rating-funnel' ); ?>
            </label>
        </th>
        <td>
            <?php
            foreach ( $placeholders as $placeholder_id => $placeholder ) {
                if ( ! empty( $placeholder['hide'] ) ) {
                    continue;
                }
                ?>
                <p>
                    <code>{<?php echo esc_html( $placeholder_id ); ?>}</code> - <?php echo esc_html( $placeholder['label'] ); ?>
                </p>
                <?php
            }
            ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

function rrtngg_get_table_divider() {
    ob_start();
    ?>
    </tbody></table>
    <hr>
    <table class="form-table"><tbody>
    <?php
    return ob_get_clean();
}

function rrtngg_parse_csv_file( $path, $atts = array() ) {
    $default_atts = array(
        'sep' => ',',
    );

    $atts = array_merge( $default_atts, $atts );

    $row = 0;

    $fields = array();
    $values = array();

    if ( ( $handle = fopen( $path, 'r' ) ) !== false ) {
        while ( ( $data = fgetcsv( $handle, 10000, $atts['sep'] ) ) !== false ) {
            $num = count( $data );
            // echo "<p> $num fields in line $row: <br /></p>\n";

            if ( 0 === $row ) {
                for ( $c = 0; $c < $num; $c++ ) {
                    $string = $data[ $c ];
                    if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
                        $string = substr( $string, 3 );
                    }
                    $fields[] = preg_replace( '/[\x{200B}-\x{200D}]/u', '', strtolower( trim( $string ) ) );
                }
            } else {
                for ( $f = 0; $f < $num; $f++ ) {
                    $string = $data[ $f ];
                    if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
                        $string = substr( $string, 3 );
                    }
                    $values[ $row ][ $fields[ $f ] ] = $string;
                }
            }
            $row++;
        }
        fclose( $handle );

        return array(
            'fields' => $fields,
            'values' => $values,
        );
    } else {
        return false;
    }
}

function rrtngg_get_btn_colors( $hex_color ) {
    $hexCode = ltrim( $hex_color, '#' );
    if ( strlen( $hexCode ) == 3 ) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    if ( strlen( $hexCode ) == 3 ) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hoverCode = array_map( 'hexdec', str_split( $hexCode, 2 ) );

    $adjustPercent = -0.05;
    foreach ( $hoverCode as & $color ) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount    = ceil( $adjustableLimit * $adjustPercent );

        $color = str_pad( dechex( $color + $adjustAmount ), 2, '0', STR_PAD_LEFT );
    }

    $hover_color = '#' . implode( $hoverCode );

    $r          = hexdec( substr( $hex_color, 1, 2 ) );
    $g          = hexdec( substr( $hex_color, 3, 2 ) );
    $b          = hexdec( substr( $hex_color, 5, 2 ) );
    $yiq        = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;
    $text_color = ( $yiq >= 198 ) ? 'black' : 'white';

    return array(
        'hover_color' => $hover_color,
        'text_color'  => $text_color,
    );
}

function rrtngg_show_below_button_text( $service_id, $funnel_settings ) {
    if ( ! empty( $funnel_settings[ 'services_' . $service_id . '_below_btn_text' ] ) ) {
        ?>
        <div id="<?php echo esc_attr( $service_id ); ?>_below_btn_text">
            <?php echo wp_kses( wpautop( $funnel_settings[ 'services_' . esc_attr( $service_id ) . '_below_btn_text' ] ), RRTNGG_Manager::get_allowed_tags() ); ?>
        </div>
        <?php
    }
}

function rrtngg_show_buttons_styles( $service_id, $funnel_settings ) {
    $btn_color  = ! empty( $funnel_settings[ 'services_' . $service_id . '_btn_color' ] ) ? $funnel_settings[ 'services_' . $service_id . '_btn_color' ] : '#0d6efd';
    $btn_colors = rrtngg_get_btn_colors( $btn_color );

    if ( ! empty( $funnel_settings[ 'services_' . $service_id . '_btn_text_color' ] ) ) {
        $btn_colors['text_color'] = $funnel_settings[ 'services_' . $service_id . '_btn_text_color' ];
    }

    ?>
    <style>
        #rrtngg_services_<?php echo esc_attr( $service_id ); ?>_btn {
            background-color: <?php echo esc_attr( $btn_color ); ?>;
            border-color: <?php echo esc_attr( $btn_color ); ?>;
            color: <?php echo esc_attr( $btn_colors['text_color'] ); ?>;
        }
        #rrtngg_services_<?php echo esc_attr( $service_id ); ?>_btn:hover {
            background-color: <?php echo esc_attr( $btn_colors['hover_color'] ); ?>;
            border-color: <?php echo esc_attr( $btn_colors['hover_color'] ); ?>;
        }
    </style>
    <?php
}
