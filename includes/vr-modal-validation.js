jQuery(function ($) {
    // Validation for the form
    var formSelector = '#post';

    $(formSelector).validate({
        // validation rules and messages
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
                required: ' <strong>Rubrik är obligatoriskt.</strong>'
            },
            'vrm_content': {
                required: ' <strong>Innehåll är obligatoriskt.</strong>'
            },
            'vrm_button_title': {
                required: ' <strong>Länk rubrik är obligatoriskt.</strong>'
            },
            'vrm_button_url': {
                required: ' URL är obligatoriskt.',
                url: ' <strong>Ange en giltig URL - https://exempel.se/</strong>'
            }
        },

        // Error placement
        errorPlacement: function (error, element) {
            // Use error.appendTo to automatically insert in the correct location
            error.appendTo(element.parent());
        }
    });
});
