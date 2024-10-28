<?php
/**
 * @var $is_premium
 */
$funnels           = RRTNGG_Manager::get_published_fnnels();
$funnel_id_options = array();
$is_limit_reached  = RRTNGG_License_Manager::is_limit_reached();
$allowed_tags      = array_merge( RRTNGG_Manager::get_allowed_tags(), RRTNGG_Manager::get_fields_allowed_tags() );

foreach ( $funnels as $funnel ) {
    $funnel_id_options[ $funnel->ID ] = $funnel->post_title . " (ID: {$funnel->ID})";
}

$single_import_fields = array(
    'funnel_id'  => array(
        'label'        => __( 'Funnel', '5-stars-rating-funnel' ),
        'type'         => 'select',
        'options'      => $funnel_id_options,
        'row_template' => 'admin_two_col_simple',
    ),
    'email'      => array(
        'label'        => __( 'Email', '5-stars-rating-funnel' ),
        'type'         => 'email',
        'row_template' => 'admin_two_col_simple',
        'is_csv'       => true,
    ),
    'title'      => array(
        'label'        => __( 'Title', '5-stars-rating-funnel' ),
        'type'         => 'text',
        'row_template' => 'admin_two_col_simple',
        'is_csv'       => true,
    ),
    'first_name' => array(
        'label'          => __( 'First Name', '5-stars-rating-funnel' ),
        'type'           => 'text',
        'row_template'   => 'admin_two_col_simple',
        'is_csv'         => true,
        'allowed_fields' => array( 'name', 'firstname' ),
    ),
    'last_name'  => array(
        'label'          => __( 'Last  Name', '5-stars-rating-funnel' ),
        'type'           => 'text',
        'row_template'   => 'admin_two_col_simple',
        'is_csv'         => true,
        'allowed_fields' => array( 'lastname' ),
    ),
    'order_id'   => array(
        'label'          => __( 'Order ID', '5-stars-rating-funnel' ),
        'type'           => 'text',
        'row_template'   => 'admin_two_col_simple',
        'is_csv'         => true,
        'allowed_fields' => array( 'orderid' ),
    ),
);
$current_csv          = get_option( 'rrtngg_current_import' );

if (
        ! empty( $current_csv['status'] )
        && ! empty( $current_csv['file_name'] )
        && ! empty( $current_csv['file_path'] )
        && 'uploaded' === $current_csv['status']
) {
    $csv_fields_val = rrtngg_parse_csv_file( $current_csv['file_path'] );

    if ( ! empty( $csv_fields_val ) ) {
        foreach ( $single_import_fields as $field_id => $single_import_field ) {
            $current_need_mapping = false;
            $current_in_allowed   = false;

            if ( ! empty( $single_import_field['is_csv'] ) ) {
                $allowed_fields = ! empty( $single_import_field['allowed_fields'] ) && is_array( $single_import_field['allowed_fields'] ) ? $single_import_field['allowed_fields'] : array();

                $allowed_fields[] = $field_id;

                $allowed_field = array_unique( $allowed_fields );

                foreach ( $allowed_fields as $allowed_field ) {
                    if ( in_array( $allowed_field, $csv_fields_val['fields'] ) ) {
                        $current_in_allowed = true;
                    }
                }

                if ( empty( $current_in_allowed ) ) {
                    if ( empty( $current_csv['fields_mapping'][ $field_id ] ) ) {
                        $need_mapping         = true;
                        $current_need_mapping = true;
                    } else {
                        $field_csv = $current_csv['fields_mapping'][ $field_id ];

                        if ( ! in_array( $field_csv, $csv_fields_val['fields'] ) ) {
                            $need_mapping         = true;
                            $current_need_mapping = true;
                        }
                    }
                }


                if ( $field_id === 'email' && $current_need_mapping ) {
                    $import_forbidden = true;
                }
            }
        }
    }
}
?>

<div class="wptl-settings-group" style="margin-top: 25px;">
    <div class="wptl-settings-group-header">
        <h3><?php _e( 'CSV bulk import', '5-stars-rating-funnel' ); ?></h3>
    </div>

    <div class="wptl-settings-group-body">
        <?php
        if ( ! $is_premium ) {
            ?>
            <div class="wptl-row">
                <div class="wptl-col-xs-12">
                    <?php echo rrtngg_get_only_pro_description(); ?>
                </div>
            </div>
            <?php
        } else {

            if ( ! empty( $csv_fields_val ) ) {
                ?>
                <div class="wptl-row">
                    <div class="wptl-col-xs-12">
                        <div class="wptl-row">
                            <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                                <p>
                                    <?php _e( 'File for import', '5-stars-rating-funnel' ); ?>
                                </p>
                            </div>
                            <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-8">
                                <p>
                                    <input type="text" value="<?php echo esc_html( $current_csv['file_name'] ); ?>" style="width: 100%" readonly>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="wptl-col-xs-12">
                        <div id="leads_csv_container">
                            <form id="fields_mapping">
                                <div class="wptl-row">
                                    <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                                        <div class="wptl-row">
                                            <div class="wptl-col-xs-12 wptl-col-md-4">
                                                <p>
                                                    <label for="funnel_id"><?php esc_html_e( 'Select Funnel', '5-stars-rating-funnel' ); ?></label>
                                                </p>
                                            </div>

                                            <div class="wptl-col-xs-12 wptl-col-md-8">
                                                <p>
                                                    <select name="funnel_id" id="funnel_id">
                                                        <?php
                                                        foreach ( $funnels as $funnel ) {
                                                            ?>
                                                            <option value="<?php echo esc_html( $funnel->ID ); ?>"><?php echo esc_html( $funnel->post_title ); ?> (ID: <?php echo esc_html( $funnel->ID ); ?>)</option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    foreach ( $single_import_fields as $field_id => $single_import_field ) {
                                        if ( ! empty( $single_import_field['is_csv'] ) ) {
                                            ?>
                                            <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                                                <div class="wptl-row">
                                                    <div class="wptl-col-xs-12 wptl-col-md-4">
                                                        <p>
                                                            <label for="map_<?php echo esc_attr( $field_id ); ?>">
                                                                <?php esc_html_e( 'Assign', '5-stars-rating-funnel' ); ?>
                                                                <strong><?php echo esc_html( $single_import_field['label'] ); ?></strong>
                                                                <?php esc_html_e( 'field', '5-stars-rating-funnel' ); ?>
                                                            </label>
                                                        </p>
                                                    </div>

                                                    <div class="wptl-col-xs-12 wptl-col-md-8">
                                                        <p>
                                                            <select name="map_<?php echo esc_attr( $field_id ); ?>" id="map_<?php echo esc_attr( $field_id ); ?>">
                                                                <option value="">-- <?php esc_html_e( 'Select CSV field', '5-stars-rating-funnel' ); ?> --</option>
                                                                <?php
                                                                $allowed_fields = ! empty( $single_import_field['allowed_fields'] ) && is_array( $single_import_field['allowed_fields'] ) ? $single_import_field['allowed_fields'] : array();
                                                                foreach ( $csv_fields_val['fields'] as $csv_field ) {
                                                                    ?>
                                                                    <option
                                                                            value="<?php echo esc_html( $csv_field ); ?>"
                                                                        <?php echo $csv_field === $field_id || in_array( $csv_field, $allowed_fields ) ? ' selected' : ''; ?>
                                                                    ><?php echo esc_html( $csv_field ); ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="wptl-row">
                <div class="wptl-col-xs-12">
                    <p>
                        <?php
                        if ( ! empty( $csv_fields_val ) ) {
                            ?>
                            <button id="import_leads_btn" class="button" type="button"<?php echo ! empty( $import_forbidden ) ? ' disabled' : ''; ?>>
                                <?php esc_html_e( 'Import Leads', '5-stars-rating-funnel' ); ?>
                            </button>

                            <button id="import_email_leads_btn" class="button" type="button"<?php echo ! empty( $import_forbidden ) ? ' disabled' : ''; ?>>
                                <?php esc_html_e( 'Import Leads & send Invite', '5-stars-rating-funnel' ); ?>
                            </button>
                            <?php
                        }
                        ?>
                        <button id="upload_leads_csv_btn" class="button" type="button"
                            <?php echo ! empty( $current_csv ) ? ' style="display:none;"' : ''; ?>
                        >
                            <?php esc_html_e( 'Select / Upload CSV file', '5-stars-rating-funnel' ); ?>
                        </button>

                        <button id="delete_leads_csv_btn" type="button"  class="button"
                            <?php echo empty( $current_csv ) ? ' style="display:none;"' : ''; ?>
                        >
                            <?php esc_html_e( 'Cancel import', '5-stars-rating-funnel' ); ?>
                        </button>

                        <?php
                        if ( empty( $current_csv ) && file_exists( RRTNGG_ABS_PATH . 'sample-data/sample-leads.csv' ) ) {
                            $csv_url = plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'sample-data/sample-leads.csv'
                            ?>
                            <a href="<?php echo esc_url( $csv_url ); ?>" target="_blank" class="button">
                                <?php esc_html_e( 'Download Sample CSV File', '5-stars-rating-funnel' ); ?>
                            </a>
                            <?php
                        }
                        ?>
                    </p>

                    <?php
                    if ( empty( $current_csv ) ) {
                        ?>
                        <p>
                            <?php
                            echo sprintf( __( 'Make sure your CSV file UTF-8 encoded, and symbol %1$s,%2$s (coma) used as field separator.', '5-stars-rating-funnel' ), '<code style="background-color: #ffff94">', '</code>' );
                            ?>
                        </p>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<div class="wptl-settings-group">
    <div class="wptl-settings-group-header">
        <h3><?php esc_html_e( 'Add single lead', '5-stars-rating-funnel' ); ?></h3>
    </div>

    <div class="wptl-settings-group-body">
        <div class="wptl-row">
            <div class="wptl-col-xs-12">
                <form id="import_single_lead">
                    <div class="wptl-row">
                        <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                            <?php
                            echo wp_kses( RRTNGG_Field_Generator::form_field( 'funnel_id', $single_import_fields['funnel_id'] ), $allowed_tags );
                            ?>
                        </div>
                        <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                            <?php
                            echo wp_kses( RRTNGG_Field_Generator::form_field( 'email', $single_import_fields['email'] ), $allowed_tags );
                            ?>
                        </div>
                        <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                            <?php
                            echo wp_kses( RRTNGG_Field_Generator::form_field( 'title', $single_import_fields['title'] ), $allowed_tags );
                            ?>
                        </div>
                        <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                            <?php
                            echo wp_kses( RRTNGG_Field_Generator::form_field( 'first_name', $single_import_fields['first_name'] ), $allowed_tags );
                            ?>
                        </div>
                        <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                            <?php
                            echo wp_kses( RRTNGG_Field_Generator::form_field( 'last_name', $single_import_fields['last_name'] ), $allowed_tags );
                            ?>
                        </div>
                        <div class="wptl-col-xs-12 wptl-col-md-6 wptl-col-lg-4">
                            <?php
                            echo wp_kses( RRTNGG_Field_Generator::form_field( 'order_id', $single_import_fields['order_id'] ), $allowed_tags );
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="wptl-row">
            <div class="wptl-col-xs-12">
                <p>
                    <button id="add_single_lead_btn" class="button" type="button">
                        <?php esc_html_e( 'Add Lead', '5-stars-rating-funnel' ); ?>
                    </button>

                    <?php
                        ?>
                        <button id="invite_single_lead_btn" class="button" type="button">
                            <?php esc_html_e( 'Add Lead & Send Invite', '5-stars-rating-funnel' ); ?>
                        </button>
                        <?php
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="wptl-settings-group">
    <div class="wptl-settings-group-header">
        <h3><?php _e( 'REST API integration', '5-stars-rating-funnel' ); ?></h3>
    </div>

    <div class="wptl-settings-group-body">
        <div class="wptl-row">
            <div class="wptl-col-xs-12">
                <?php
                if ( $is_premium ) {
                    $keys = get_option( 'rrtngg_api_keys', array() );

                    ?>
                    <div class="wptl-row">
                        <div class="wptl-col-xs-12 wptl-col-md-6">
                            <?php
                            echo wp_kses(
                                RRTNGG_Field_Generator::form_field(
                                    'api_key_title',
                                    array(
                                        'label'        => __( 'Short description', '5-stars-rating-funnel' ),
                                        'type'         => 'text',
                                        'row_template' => 'admin_two_col_simple',
                                    )
                                ),
                                $allowed_tags
                            );
                            ?>
                        </div>

                        <div class="wptl-col-xs-12 wptl-col-md-6">
                            <p>
                                <button id="generate_rrtngg_api_key" class="button button-primary" type="button">
                                    <?php echo __( 'Generate API Key', '5-stars-rating-funnel' ); ?>
                                </button>
                            </p>
                        </div>
                    </div>
                    <?php

                    if ( ! empty( $keys ) ) {
                        foreach ( $keys as $key => $description ) {
                            ?>
                            <div style="margin-bottom: 5px; padding: 5px; border: 1px solid #ddd;">
                                <div class="wptl-row">
                                    <div class="wptl-col-xs-12 wptl-col-md-12 wptl-col-lg-3">
                                        <h4 style="margin: 10px 0">
                                            <?php echo esc_html( $description ); ?>
                                        </h4>
                                    </div>
                                    <div class="wptl-col-xs-12 wptl-col-md-8 wptl-col-lg-7">
                                        <p>
                                            <input type="text" readonly value="<?php echo esc_html( $key ); ?>">
                                        </p>
                                    </div>
                                    <div class="wptl-col-xs-12 wptl-col-md-2 wptl-col-lg-2">
                                        <p>
                                            <button
                                                    class="button button-primary delete_rrtngg_api_key"
                                                    type="button"
                                                    data-key="<?php echo esc_attr( $key ); ?>"
                                                    data-confirm="<?php echo __( 'Are you sure you want to delete this API Key, this could not be undone. All apps which are using this key could not use your API any more.', '5-stars-rating-funnel' ); ?>"
                                            >
                                                <?php echo __( 'Delete', '5-stars-rating-funnel' ); ?>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="wptl-row">
                        <div class="wptl-col-xs-12">
                            <h3><?php _e( 'How to use REST API.', '5-stars-rating-funnel' ); ?></h3>

                            <h4>
                                <?php _e( 'Authorization', '5-stars-rating-funnel' ); ?>:
                            </h4>
                            <p>
                                <?php _e( 'Add header key', '5-stars-rating-funnel' ); ?> <code>Authorization</code> <?php _e( 'with one of generated API keys', '5-stars-rating-funnel' ); ?>
                            </p>

                            <p>
                                <?php _e( 'If your service do not allow to set up custom headers use', '5-stars-rating-funnel' ); ?> <code>token</code> <?php _e( 'query parameter (see description below)', '5-stars-rating-funnel' ); ?>
                            </p>

                            <h4>
                                <?php _e( 'Import leads', '5-stars-rating-funnel' ); ?>:
                            </h4>

                            <p>
                                <?php _e( 'Endpoint URL', '5-stars-rating-funnel' ); ?>:
                                <code>
                                    <?php echo site_url( '/wp-json/rrtngg/v1/import' ); ?>
                                </code>
                            </p>

                            <p>
                                <?php _e( 'Method', '5-stars-rating-funnel' ); ?>:
                                <code>GET</code> <?php _e( 'or', '5-stars-rating-funnel' ); ?> <code>POST</code>
                            </p>

                            <p>
                                <?php _e( 'Required parameters', '5-stars-rating-funnel' ); ?>:
                            </p>

                            <ul>
                                <li>
                                    <code>funnel_id</code> -
                                    <?php _e( 'Funnel ID which this lead would be connected', '5-stars-rating-funnel' ); ?>
                                </li>
                                <li>
                                    <code>email</code> -
                                    <?php _e( 'Lead email address', '5-stars-rating-funnel' ); ?>
                                </li>
                                <li>
                                    <code>token</code> (<?php _e( 'required only if', '5-stars-rating-funnel' ); ?>
                                    "Authorization"
                                    <?php _e( 'header not set up', '5-stars-rating-funnel' ); ?>) -
                                    <?php _e( 'One of generated API keys', '5-stars-rating-funnel' ); ?>
                                </li>
                            </ul>

                            <p>
                                <?php _e( 'Optional parameters', '5-stars-rating-funnel' ); ?>:
                            </p>

                            <ul>
                                <li>
                                    <code>just_import</code> -
                                    <?php _e( 'If you just want to add this lead and not send any email invitaion, just add this parameter with value 1', '5-stars-rating-funnel' ); ?>
                                </li>
                                <li>
                                    <code>title</code> -
                                    <?php _e( 'Mr., Mrs. Dr etc.', '5-stars-rating-funnel' ); ?>
                                </li>
                                <li>
                                    <code>first_name</code> -
                                    <?php _e( 'Lead first name', '5-stars-rating-funnel' ); ?>
                                </li>
                                <li>
                                    <code>last_name</code> -
                                    <?php _e( 'Lead last name', '5-stars-rating-funnel' ); ?>
                                </li>
                                <li>
                                    <code>order_id</code> -
                                    <?php _e( 'Order ID', '5-stars-rating-funnel' ); ?>
                                </li>
                            </ul>

                            <h4><?php _e( 'F.e.', '5-stars-rating-funnel' ); ?>:</h4>

                            <p style="margin-bottom: 15px;">
                                 <strong><?php _e( 'Method', '5-stars-rating-funnel' ); ?> <code>GET</code></strong>:<br>
                                <code>
                                    <?php echo site_url( '/wp-json/rrtngg/v1/import' ); ?>?email=myemail@<?php echo rrtngg_get_current_domain_name(); ?>&funnel_id=42&title=mr.&first_name=<?php _e( 'John', '5-stars-rating-funnel' ); ?>&last_name=<?php _e( 'Doe', '5-stars-rating-funnel' ); ?>&order_id=z123-2001&just_import=1
                                </code>
                            </p>

                            <p>
                                <strong><?php _e( 'Method', '5-stars-rating-funnel' ); ?> <code>POST</code></strong>:<br>
                                <?php _e( 'Form fields', '5-stars-rating-funnel' ); ?> (<?php _e( 'request body', '5-stars-rating-funnel' ); ?>)
<pre style="background: rgba(0,0,0,.07);padding: 5px;">
{
    "email":"myemail@rratingg.loc",
    "funnel_id":"42",
    "title":"mr.",
    "first_name":"<?php _e( 'John', '5-stars-rating-funnel' ); ?>",
    "last_name":"<?php _e( 'Doe', '5-stars-rating-funnel' ); ?>",
    "order_id":"z123-2001",
    "just_import":"1"
}
</pre>
                            </p>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <?php _e( 'REST API integration disabled.', '5-stars-rating-funnel' ); ?>
                    <?php echo wp_kses( rrtngg_get_only_pro_description(), $allowed_tags ); ?>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
