<?php
/**
 * @var $is_premium
 */
$leads = RRTNGG_Leads_Model::get();

$funnels           = array();
$lead_statuses     = RRTNGG_Manager::get_lead_statuses();
$mail_steps        = RRTNGG_Manager::get_mail_steps();
$mail_statuses     = RRTNGG_Manager::get_next_mail_statuses();
$mail_steps_ids    = array_keys( $mail_steps );
$mail_statuses_ids = array_keys( $mail_statuses );
$date_format       = get_option( 'date_format' );

if ( empty( $is_premium ) ) {
    $leads_not_invited = RRTNGG_Leads_Model::count( "next_mail = 'invitation' AND next_mail_status = 'scheduled'" );
} else {
    $active_funnel = RRTNGG_Funnel_CPT::get_active();

    if ( ! empty( $active_funnel ) ) {
        $ID = $active_funnel->ID;

        $leads_not_invited = RRTNGG_Leads_Model::count( "ID = '" . $ID . "' AND next_mail = 'invitation' AND next_mail_status = 'scheduled'" );
    } else {
        $leads_not_invited = RRTNGG_Leads_Model::count( "next_mail = 'invitation' AND next_mail_status = 'scheduled'" );
    }
}

$leads_scheduled  = RRTNGG_Leads_Model::count( "next_mail_status = 'scheduled'" );
$is_limit_reached = RRTNGG_License_Manager::is_limit_reached();

// var_dump(current_time( 'timestamp' ));
// var_dump($mail_steps);
?>

<table id="reviews-list" class="wp-list-table widefat fixed striped pages">
    <thead>
    <tr>
        <td class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php echo __( 'Select All' ); ?></label>
            <input id="cb-select-all-1" type="checkbox" />
        </td>
        <th class="column-primary"><?php esc_html_e( 'Email', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Name', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Order ID', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Funnel', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Status', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Next Mail', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Link', '5-stars-rating-funnel' ); ?></th>
        <th></th>
    </tr>
    </thead>
    <?php
    if ( ! empty( $leads ) ) {
        $services = RRTNGG_Services_Manager::get_services();
        
        if ( ! empty( (int) $leads_not_invited ) ) {
            ?>
            <div class="wptl-row">
                <div class="wptl-col-xs-12">
                    <p>
                        <button id="rrtng_invite_all" class="button button-primary" data-confirm="<?php _e( 'Are you sure? Email will be sent only for those leads who was not invited previously. This action could not be undone!', '5-stars-rating-funnel' ); ?>">
                            <?php _e( 'Send invitation Email', '5-stars-rating-funnel' ); ?> (<?php echo esc_html( $leads_not_invited ); ?>)
                        </button>
                    </p>
                </div>
            </div>
            <?php
        }
        ?>
        <tbody id="the-list">
        <?php
        foreach ( $leads as $lead ) {
            $full_name_array = array();
            if ( ! empty( $lead['title'] ) ) {
                $full_name_array[] = $lead['title'];
            }
            if ( ! empty( $lead['first_name'] ) ) {
                $full_name_array[] = $lead['first_name'];
            }
            if ( ! empty( $lead['last_name'] ) ) {
                $full_name_array[] = $lead['last_name'];
            }

            if ( empty( $lead['order_id'] ) ) {
                $now_timestamp    = current_time( 'timestamp' );
                $order_id         = $lead['ID'] . '-' . date( 'dmy' );
                $lead['order_id'] = $order_id;

                RRTNGG_Leads_Model::create( $lead );
            }

            $funnel_id = $lead['funnel_id'];

            if ( ! isset( $funnels[ $funnel_id ] ) ) {
                $funnel    = get_post( $funnel_id );
                $is_active = RRTNGG_Funnel_CPT::is_active( $funnel_id );

                if ( empty( $funnel ) || 'publish' !== $funnel->post_status ) {
                    $funnels[ $funnel_id ]['title'] = __( 'Funnel deleted or not published', '5-stars-rating-funnel' );
                } else {
                    $title                          = $funnel->post_title;
                    $funnels[ $funnel_id ]['title'] = $title . " (ID: {$funnel_id})";
                }

                $funnels[ $funnel_id ]['is_active']       = $is_active;
                $funnels[ $funnel_id ]['funnel_settings'] = get_post_meta( $funnel_id, 'rrtngg_funnel_settings', true );
            } else {
                $is_active = $funnels[ $funnel_id ]['is_active'];
            }
            ?>
            <tr>
                <th class="check-column">
                    <input id="cb-select-<?php echo esc_attr( $lead['ID'] ); ?>" type="checkbox" value="<?php echo esc_html( $lead['ID'] ); ?>">
                </th>

                <td class="column-primary has-row-actions">
                    <strong><?php echo esc_html( $lead['email'] ); ?></strong>

                    <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e( 'Show more details', '5-stars-rating-funnel' ); ?></span></button>
                </td>

                <td data-colname="<?php _e( 'Name', '5-stars-rating-funnel' ); ?>"><?php echo esc_html( implode( ' ', $full_name_array ) ); ?></td>
                <td data-colname="<?php _e( 'Order ID', '5-stars-rating-funnel' ); ?>"><?php echo ! empty( $lead['order_id'] ) ? esc_html( $lead['order_id'] ) : ' - '; ?></td>
                <td data-colname="<?php _e( 'Funnel', '5-stars-rating-funnel' ); ?>">
                    <?php echo esc_html( $funnels[ $funnel_id ]['title'] ); ?>
                    <?php echo empty( $is_active ) ? ' - <strong style="color: red;">' . __( 'inactive', '5-stars-rating-funnel' ) . '</strong>' : ''; ?>
                </td>
                <?php
                if ( ! empty( $is_active ) ) {
                    ?>
                    <td data-colname="<?php _e( 'Status', '5-stars-rating-funnel' ); ?>">
                        <?php
                        $lead_status = $lead['status'];

                        if ( empty( $lead_statuses[ $lead_status ]['label'] ) ) {
                            _e( 'Wrong Lead status', '5-stars-rating-funnel' );
                        } else {
                            echo esc_html( $lead_statuses[ $lead_status ]['label'] );

                            if ( ! empty( $lead['meta'] ) ) {
                                $meta = unserialize( $lead['meta'] );
                                if ( ! empty( $meta['services'] ) ) {
                                    ?>
                                    <ul>
                                        <?php
                                        foreach ( $meta['services'] as $service_id => $data ) {
                                            if ( ! empty( $services[ $service_id ]['label'] ) ) {
                                                ?>
                                                <li><span class="dashicons dashicons-yes"></span> <?php echo esc_html( $services[ $service_id ]['label'] ); ?></li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </td>
                    <td data-colname="<?php _e( 'Next Mail', '5-stars-rating-funnel' ); ?>">
                        <?php
                        $next_mail = empty( $lead['next_mail'] ) || ! in_array( $lead['next_mail'], $mail_steps_ids ) ? 'invitation' : $lead['next_mail'];

                        if ( 'no_mail' !== $next_mail ) {
                            $next_mail_label = $mail_steps[ $next_mail ]['label'];
                            echo esc_html( $next_mail_label );

                            if ( empty( $mail_steps[ $next_mail ]['available'] ) ) {
                                echo '<br>';
                                echo rrtngg_get_only_pro_description();
                            } else {
                                if ( $next_mail === 'invitation' ) {
                                    echo '<br>';
                                    echo rrtngg_get_limit_description();
                                } else {
                                    if ( ! empty( $lead['next_mail_at'] ) && ! empty( $lead['next_mail_status'] ) ) {
                                        echo '<br>';
                                        echo '<strong>';
                                        if ( 'sending' === $lead['next_mail_status'] ) {
                                            _e( 'Sending in background', '5-stars-rating-funnel' );
                                        } elseif ( 'scheduled' === $lead['next_mail_status'] ) {
                                            _e( 'Scheduled', '5-stars-rating-funnel' );
                                        } else {
                                            _e( 'Sent', '5-stars-rating-funnel' );
                                        }
                                        echo '</strong>';

                                        if ( 'sending' !== $lead['next_mail_status'] ) {
                                            $now_timestamp = (int) current_time( 'timestamp' );
                                            echo '<br>' . wp_date( $date_format, $lead['next_mail_at'] );
                                            echo ' ' . __( 'at', '5-stars-rating-funnel' ) . ' ' . date( 'H:i:s', $lead['next_mail_at'] );
                                            // echo '<br>: ' . date( 'H:i:s', $now_timestamp );

                                        }
                                    }

                                    if ( empty( $lead['next_mail_status'] ) || 'sent' !== $lead['next_mail_status'] ) {
                                        if ( 'invitation' === $lead['next_mail'] || ! empty( $funnels[ $funnel_id ]['funnel_settings'][ $lead['next_mail'] . '_enabled' ] ) ) {
                                            ?>
                                            <br>
                                            <button
                                                    class="button button-small rrtng_send_email_now"
                                                    data-lead-id="
                                                    <?php
                                                    echo esc_attr( $lead['ID'] );
                                                    ?>
                                                    "<?php echo 'sending' === $lead['next_mail_status'] ? ' disabled' : ''; ?>
                                            >
                                                <?php esc_html_e( 'Send now', '5-stars-rating-funnel' ); ?>
                                            </button>
                                            <?php
                                        } else {
                                            ?>
                                            <br>
                                            <button class="button button-small disabled" disabled>
                                                <?php esc_html_e( 'Send now', '5-stars-rating-funnel' ); ?>
                                            </button>
                                            <br>
                                            <small><?php esc_html_e( 'To send this email, you need to enable it in funnel settings.', '5-stars-rating-funnel' ); ?></small>
                                            <?php
                                        }
                                    }
                                }
                            }
                        } else {
                            echo '-';
                        }

                        ?>
                    </td>
                    <td data-colname="<?php esc_html_e( 'Link', '5-stars-rating-funnel' ); ?>">
                        <a href="<?php echo esc_url( $lead['link'] ); ?>" target="_blank">
                            <?php esc_html_e( 'Test rating funnel', '5-stars-rating-funnel' ); ?> <span class="dashicons dashicons-external"></span>
                        </a>
                    </td>
                    <?php
                } else {
                    ?>
                    <td data-colname="<?php esc_html_e( 'Status', '5-stars-rating-funnel' ); ?>" colspan="3">
                        <?php esc_html_e( 'Multiple funnels disabled.', '5-stars-rating-funnel' ); ?>
                        <?php echo rrtngg_get_only_pro_description(); ?>
                    </td>
                    <?php
                }
                ?>
                <td>
                    <button class="rrtng_delete_lead button button-small" data-lead-id="<?php echo esc_attr( $lead['ID'] ); ?>" data-confirm="<?php esc_html_e( 'Are you sure you want to delete this lead. This action could not be undone!', '5-stars-rating-funnel' ); ?>">
                        <?php esc_html_e( 'Delete', '5-stars-rating-funnel' ); ?>
                    </button>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <?php
    } else {
        ?>
        <tbody id="the-list"></tbody>
        <?php
    }
    ?>
    <tfoot>
    <tr>
        <td class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All' ); ?></label>
            <input id="cb-select-all-1" type="checkbox" />
        </td>
        <th class="column-primary"><?php esc_html_e( 'Email', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Name', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Order ID', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Funnel', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Status', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Next Mail', '5-stars-rating-funnel' ); ?></th>
        <th><?php esc_html_e( 'Link', '5-stars-rating-funnel' ); ?></th>
        <th></th>
    </tr>
    </tfoot>
</table>
