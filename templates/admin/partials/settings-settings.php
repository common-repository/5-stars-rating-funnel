<?php
/**
 * Provide a admin area view for the plugin settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$general_settings = is_array(get_option( 'rrating_funnel_gen_settings' ))? get_option( 'rrating_funnel_gen_settings' ):array();
?>


<div class="rrating_general_options ">
    <form method="post">
        <h2><?php _e( 'General Settings', '5-stars-rating-funnel' ); ?></h2>
        <table cellpadding="5" cellspacing="5" class="ldlca-gen-tble">
            <tbody>
                <tr>
                    <td width="500px" valign="top" class="qst-ld-certify">
                        <label for="rrating_enable_ip_restriction"><?php _e( 'Restrict Rating on the base of IP:', '5-stars-rating-funnel' ); ?>
                    </td>
                    <td valign="top">
                        <input type="checkbox"  id="rrating_enable_ip_restriction" name="rrating_enable_ip_restriction" value="yes" <?php echo ( array_key_exists('rrating_enable_ip_restriction',$general_settings) && $general_settings['rrating_enable_ip_restriction'] == 'yes') ? 'checked="checked"' : ''; ?> >
                    </td>
                </tr>
                <tr>
                    <td class="qst-ld-certify">
                        <p style="font-size:11px;">
                            <?php _e( 'Issues and why not enabled by default:', '5-stars-rating-funnel' ); ?>
                            <br>
                            <?php _e( 'When use VPN and Starlink internet only you can rate only once for the IP, in the whole network.', '5-stars-rating-funnel' ); ?>
                            <?php _e( 'We tested to use the browser ID but all apple devices offer to hide it.', '5-stars-rating-funnel' ); ?>
                            <?php _e( 'So we did not found a working solution.', '5-stars-rating-funnel' ); ?>
                            <br>
                            <?php _e( 'Cookies are not nice to use in Europe and maybe soon banned.', '5-stars-rating-funnel' ); ?>
                            <?php _e( 'Maybe in the future add fingerprint tracking/blocking like WP tracking plugins do.', '5-stars-rating-funnel' ); ?>
                        </p>
                    </td>
                </tr>
                
            </tbody>
        </table>    
        <table>
            <tr>
                <td style="padding-bottom: 0px;">
                    <input name="save_rrating_funnel_gen_settings" class="cs-ld-certify-btn" type="submit" value="<?php esc_attr_e( 'Update Settings' ); ?>" />
                </td>
            </tr>
        </table>
        <?php wp_nonce_field( 'rrating_funnel_gen_settings', 'rrating_funnel_gen_settings_field' ); ?>
        </form>
</div>