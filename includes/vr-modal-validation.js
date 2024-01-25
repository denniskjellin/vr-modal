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
                required: ' <strong class="error">Rubrik är obligatoriskt.</strong>'
            },
            'vrm_content': {
                required: ' <strong class="error">Innehåll är obligatoriskt.</strong>'
            },
            'vrm_button_title': {
                required: ' <strong class="error">Länk rubrik är obligatoriskt.</strong>'
            },
            'vrm_button_url': {
                required: ' <strong class="error">URL är obligatoriskt.</strong>',
            }
        },

        // Error placement
        errorPlacement: function (error, element) {
            // Use error.appendTo to automatically insert in the correct location
            error.appendTo(element.parent());
        }
    });
});
