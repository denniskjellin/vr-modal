jQuery(function ($) {
    // Ensure that the form ID matches the ID of your meta box form
    var formSelector = '#post';

    $(formSelector).validate({
        // Define your validation rules and messages
        rules: {
            'vrm_title': {
                required: true
            },
            'vrm_content': {
                required: true
            },
            'vrm_button_title': {
                required: true
            },
            'vrm_button_url': {
                required: true,
                url: true
            }
        },
        messages: {
            'vrm_title': {
                required: 'Fältet är obligatoriskt.'
            },
            'vrm_content': {
                required: 'Fältet är obligatoriskt.'
            },
            'vrm_button_title': {
                required: 'Fältet är obligatoriskt.'
            },
            'vrm_button_url': {
                required: 'Fältet är obligatoriskt.',
                url: 'Ange en giltig URL'
            }
        },
        // Specify where to display the error messages
        errorPlacement: function (error, element) {
            error.appendTo(element.parent());

            //console.log(element);

            $('.wrap').prepend('<div class="notice notice-error is-dismissible"><p>asdfsafafsd' + error[0].innerText + '</p></div>');
        }
    });

});
