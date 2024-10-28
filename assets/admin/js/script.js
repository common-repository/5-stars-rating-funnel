(function ( $ ) {
    $( document.body ).on(
        'change',
        '#fields_mapping #map_email',
        function () {
            var val                    = $( this ).val();
            var import_leads_btn       = $( '#import_leads_btn' );
            var import_email_leads_btn = $( '#import_email_leads_btn' );

            if (val.trim() === '') {
                import_leads_btn.attr( 'disabled', true );
                import_email_leads_btn.attr( 'disabled', true );
            } else {
                import_leads_btn.attr( 'disabled', false );
                import_email_leads_btn.attr( 'disabled', false );
            }
        }
    );

    $( document.body ).on(
        'click',
        '.edit-post-funnel-is-active',
        function (e) {
            e.preventDefault();

            console.log( 'click .edit-post-funnel-is-active' );

            $( this ).hide();
            $( '#post-funnel-is-active-select' ).show();
        }
    );

    $( document.body ).on(
        'click',
        '.save-post-funnel-is-active, .cancel-post-funnel-is-active',
        function (e) {
            e.preventDefault();
            $( '#post-funnel-is-active-select' ).hide();
            $( '.edit-post-funnel-is-active' ).show();
        }
    );

    $( document.body ).on(
        'click',
        '#generate_rrtngg_api_key',
        function () {
            var title = $( '#api_key_title' ).val();

            var data = {
                'action': 'rrtng_generate_api_key',
                'nonce': rratinggJsObj.nonce,
                title: title};

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '.delete_rrtngg_api_key',
        function () {
            var btn             = $( this );
            var confirm_message = btn.data( 'confirm' );
            var confirmed       = confirm( confirm_message );

            if (confirmed) {
                var key = btn.data( 'key' );

                var data = {
                    'action': 'rrtng_delete_api_key',
                    'nonce': rratinggJsObj.nonce,
                    key: key};

                ajaxRequest( data, function () {}, function () {} );
            }
        }
    );

    $( document.body ).on(
        'click',
        '#rrtng_invite_all',
        function () {
            var btn = $( this );

            var data = {
                'action': 'rrtng_invite_all',
                'nonce': rratinggJsObj.nonce,
            };

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '.rrtng_send_email_now',
        function () {
            var btn     = $( this );
            var lead_id = btn.data( 'lead-id' );

            var data = {
                'action': 'rrtngg_send_email',
                'nonce': rratinggJsObj.nonce,
                'lead_id': lead_id};

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '.rrtng_delete_lead',
        function () {
            var btn             = $( this );
            var confirm_message = btn.data( 'confirm' );
            var confirmed       = confirm( confirm_message );

            if (confirmed) {
                var lead_id = btn.data( 'lead-id' );
                var data    = {
                                'action': 'rrtngg_delete_leads',
                                'nonce': rratinggJsObj.nonce,
                                'lead_ids': [lead_id]
                            };

                ajaxRequest( data, function () {}, function () {} );
            }
        }
    );

    $( document.body ).on(
        'click',
        '.rrtng_delete_feedback',
        function () {
            var btn             = $( this );
            var confirm_message = btn.data( 'confirm' );
            var confirmed       = confirm( confirm_message );

            if (confirmed) {
                var feedback_id = btn.data( 'feedback-id' );
                var data        = {
                    'action': 'rrtng_delete_feedbacks',
                    'nonce': rratinggJsObj.nonce,
                    'feedback_ids': [feedback_id]};

                ajaxRequest( data, function () {}, function () {} );
            }
        }
    );

    $( document.body ).on(
        'click',
        '#import_leads_btn',
        function () {
            var formData = $( "#fields_mapping" ).serializeArray();
            var data     = {
                'action': 'rrtngg_import_leads',
                'nonce': rratinggJsObj.nonce,
                'formData': formData
            };

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '#import_email_leads_btn',
        function () {
            var formData = $( "#fields_mapping" ).serializeArray();
            var data     = {
                'action': 'rrtngg_import_leads',
                'nonce': rratinggJsObj.nonce,
                'formData': formData,
                'send_invitation': true
            };

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '#add_single_lead_btn',
        function () {
            var formData = $( "#import_single_lead" ).serializeArray();
            var data     = {
                'action': 'rrtngg_add_single_lead',
                'nonce': rratinggJsObj.nonce,
                'formData': formData};

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '#invite_single_lead_btn',
        function () {
            var formData = $( "#import_single_lead" ).serializeArray();
            var data     = {
                'action': 'rrtngg_add_single_lead',
                'nonce': rratinggJsObj.nonce,
                'formData': formData,
                'send_invitation': true
            };

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '#delete_leads_csv_btn',
        function () {
            var data = {
                'action': 'rrtngg_delete_csv',
                'nonce': rratinggJsObj.nonce,
            };

            ajaxRequest( data, function () {}, function () {} );
        }
    );

    $( document.body ).on(
        'click',
        '#upload_leads_csv_btn',
        function () {
            var btn = $( this );

            var custom_uploader = wp.media(
                {
                    title: 'Select CSV file',
                    library : {
                        // uncomment the next line if you want to attach image to the current post
                        // uploadedTo : wp.media.view.settings.post.id,
                        type : 'text/csv'
                    },
                    button: {
                        text: 'Select this csv' // button label text
                    },
                    multiple: false // for multiple image selection set to true
                }
            ).on(
                'select',
                function () {
                    var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
                    var url        = attachment.url;
                    var id         = attachment.id;

                    var data = {
                        'action': 'rrtngg_upload_csv',
                        'nonce': rratinggJsObj.nonce,
                        'id': id,
                        'url': url
                    };

                    ajaxRequest( data, function () {}, function () {} );

                    // input.val(url);
                    // delete_btn.show();
                }
            ).open();
        }
    );

    $( document.body ).on(
        'change',
        '.rrtngg_visible_control',
        function () {
            var control = $( this );
            set_visible_containers( control );
        }
    );

    $( document ).ready(
        function () {
            set_visible_containers_by_option();
            $( '.rrtngg-color-picker' ).wpColorPicker();
        }
    );

    $( window ).on( 'resize', function () {} );

    $( window ).on( 'load', function (event) {} );

    $( window ).on( 'scroll', function (event) {} );

    function set_visible_containers(control)
    {
        var control_selector = control.data( 'control' );
        var control_val      = control.val();

        if (control_selector) {
            var items = $( '.' + control_selector );

            if (items.length) {
                items.each(
                    function (index) {
                        var item = $( this );
                        item.hide();
                    }
                );
            }
        }

        if (control_val) {
            var items_show = $( '.' + control_selector + '_' + control_val );

            if (items_show.length) {
                items_show.each(
                    function (index) {
                        var item = $( this );
                        item.show();
                    }
                );
            }
        }
    }

    function set_visible_containers_by_option()
    {
        var controls = $( '.rrtngg_visible_control' );

        if (controls.length) {
            controls.each(
                function (index) {
                    var control = $( this );

                    set_visible_containers( control )
                }
            );
        }
    }

    function ajaxRequest(data, cb, cbError)
    {
        $.ajax(
            {
                type: 'post',
                url: rratinggJsObj.ajaxurl,
                data: data,
                success: function (response) {
                    var decoded;

                    try {
                        decoded = $.parseJSON( response );
                    } catch (err) {
                        console.log( err );
                        decoded = false;
                    }

                    if (decoded) {
                        if (decoded.consoleLog) {
                            console.log( decoded.consoleLog );
                        }

                        if (decoded.message) {
                            alert( decoded.message );
                        }

                        if (decoded.fragments) {
                            updateFragments( decoded.fragments );
                        }

                        if (decoded.success) {
                            if (typeof cb === 'function') {
                                cb( decoded );
                            }
                        } else {
                            if (typeof cbError === 'function') {
                                cbError( decoded );
                            }
                        }

                        setTimeout(
                            function () {
                                if (decoded.url) {
                                    window.location.replace( decoded.url );
                                } else if (decoded.reload) {
                                    window.location.reload();
                                }
                            },
                            100
                        );
                    } else {
                        alert( 'Something went wrong' );
                    }
                }
            }
        );
    }

    function updateFragments(fragments)
    {
        if (fragments ) {
            $.each(
                fragments,
                function (key) {
                    $( key )
                    .addClass( 'updating' )
                    .fadeTo( '400', '0.6' )
                    .block(
                        {
                            message: null,
                            overlayCSS: {
                                opacity: 0.6
                            }
                        }
                    );
                }
            );

            $.each(
                fragments,
                function ( key, value ) {
                    $( key ).replaceWith( value );
                    $( key ).stop( true ).css( 'opacity', '1' ).unblock();
                }
            );
        }
    }

})( jQuery );
