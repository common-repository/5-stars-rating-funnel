<?php
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />

    <style type="text/css">
        @media only screen and (max-width: 768px) {

        }@media only screen and (max-width: 480px) {
            body, table, td, p, a, li, blockquote {
                -webkit-text-size-adjust: none !important;
            }
        }@media only screen and (max-width: 480px) {
            h1 {
                font-size: 22px !important;
                line-height: 125% !important;
            }
        }@media only screen and (max-width: 480px) {
            td.email_body_content p {
                font-size: 16px !important;
                line-height: 180% !important;
            }
        }</style>

    <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
</head>

<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    <div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
        <table role="presentation" style="width:100%;border:none;border-spacing:0;">
            <tr>
                <td align="center" valign="top">
                    <table role="presentation" style="width:100%;border:none;border-spacing:0;" id="template_container">
                        <tr>
                            <td align="center" valign="top">
                                <!-- Header -->
                                <table id="template_header" role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;">
                                    <tr>
                                        <td id="header_wrapper">
                                            <h1>{{email_heading}}</h1>
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Header -->
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">
                                <!-- Body -->
                                <table id="template_body" role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;">
                                    <tr>
                                        <td valign="top" id="body_content">
                                            <!-- Content -->
                                            <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                <tr>
                                                    <td class="email_body_content" align="left" valign="top"
                                                        style="mso-line-height-rule: exactly; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 10px; padding-left: 20px; word-break: break-word; color: #202020; font-size: 16px; line-height: 180%; text-align: left;">
                                                        <div id="body_content_inner">
                                                            {{email_content}}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- End Content -->
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Body -->
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td align="center" valign="top">
                    <table id="template_footer" role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;">
                        <tr>
                            <td valign="top">
                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                    <tr>
                                        <td colspan="2" valign="middle" id="credit">
                                            {{email_footer}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
