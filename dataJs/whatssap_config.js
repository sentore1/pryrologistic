"use strict";

$(document).ready(function () {

    // Show/hide sections based on selected provider
    function switchProvider(provider) {
        $('#section_ultramsg, #section_twilio, #section_meta').hide();
        $('#section_' + provider).show();

        // Update card active state
        $('.provider-card').removeClass('active');
        $('#card_' + provider).addClass('active');
    }

    // Init on page load
    var initialProvider = $('input[name="whatsapp_provider"]:checked').val() || 'ultramsg';
    switchProvider(initialProvider);

    // On provider change
    $('input[name="whatsapp_provider"]').on('change', function () {
        switchProvider($(this).val());
    });

    // Highlight on input
    $('.ultramsg-field, .twilio-field, .meta-field').on('input', function () {
        $(this).toggleClass('highlight', $(this).val() === '');
    });

    // Form submit
    $("#save_data").submit(function (event) {
        event.preventDefault();

        var provider = $('input[name="whatsapp_provider"]:checked').val();
        var emptyFields = [];

        if (provider === 'ultramsg') {
            if ($('#api_ws_url').val() === '')   emptyFields.push('api_ws_url');
            if ($('#api_ws_token').val() === '')  emptyFields.push('api_ws_token');
        } else if (provider === 'twilio') {
            if ($('#twilio_wa_sid').val() === '')    emptyFields.push('twilio_wa_sid');
            if ($('#twilio_wa_token').val() === '')  emptyFields.push('twilio_wa_token');
            if ($('#twilio_wa_number').val() === '') emptyFields.push('twilio_wa_number');
        } else if (provider === 'meta') {
            if ($('#meta_wa_token').val() === '')    emptyFields.push('meta_wa_token');
            if ($('#meta_wa_phone_id').val() === '') emptyFields.push('meta_wa_phone_id');
        }

        if (emptyFields.length > 0) {
            Swal.fire({
                type: 'error',
                title: message_error_form21,
                text: message_error_form22,
                confirmButtonColor: '#336aea',
                showConfirmButton: true,
            });
            emptyFields.forEach(function (id) {
                $('#' + id).addClass('highlight');
            });
            return;
        }

        var data = new FormData($("#save_data")[0]);

        $.ajax({
            url: "./ajax/tools/api_whatsapp_config_ajax.php",
            type: 'POST',
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                Swal.fire({
                    title: message_error_form6,
                    text: message_error_form14,
                    type: 'info',
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    onBeforeOpen: function () { Swal.showLoading(); },
                });
            },
            success: function (response) {
                Swal.close();
                if (response.status === 'success') {
                    Swal.fire({
                        type: 'success',
                        title: message_error_form15,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                    }).then(function () {
                        window.location.href = 'config_whatsapp.php';
                    });
                } else {
                    Swal.fire({
                        type: 'error',
                        title: message_error_form15,
                        text: response.message || message_error_form17,
                        confirmButtonColor: '#336aea',
                        showConfirmButton: true,
                    });
                }
            },
            error: function () {
                Swal.close();
                Swal.fire({
                    type: 'error',
                    title: message_error_form18,
                    text: message_error_form19,
                    confirmButtonColor: '#336aea',
                    showConfirmButton: true,
                });
            }
        });
    });
});
