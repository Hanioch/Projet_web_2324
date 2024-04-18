$(document).ready(function() {
    var minTitleLength, maxTitleLength, minContentLength, maxContentLength;
    $.ajax({
        url: 'notes/getValidationRules',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log(data)
            minTitleLength = data.minTitleLength;
            maxTitleLength = data.maxTitleLength;
            minContentLength = data.minContentLength;
            maxContentLength = data.maxContentLength;
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
    $('#title_add_text_note').on('input', function() {
        validateTitle();
    });

    $('#text_add_text_note').on('input', function() {
        validateText();
    });

    function validateTitle() {
        var title = $('#title_add_text_note').val().trim();
        if (title.length < minTitleLength || title.length > maxTitleLength) {
            $('#title_add_text_note').addClass('is-invalid');
            $('#title_error').text('Le titre doit avoir entre ' + minTitleLength + ' et ' + maxTitleLength + ' caractères.');
            $('#save_button').prop('disabled', true);
        } else {
            $('#title_add_text_note').removeClass('is-invalid').addClass('is-valid');
            $('#title_error').text('');
            enableSaveButtonIfValid();
        }
    }

    function validateText() {
        var text = $('#text_add_text_note').val().trim();
        if (text.length < minContentLength || text.length > maxContentLength) {
            $('#text_add_text_note').addClass('is-invalid');
            $('#text_error').text('Le texte doit avoir entre ' + minContentLength + ' et ' + maxContentLength + ' caractères.');
            $('#save_button').prop('disabled', true);
        } else {
            $('#text_add_text_note').removeClass('is-invalid').addClass('is-valid');
            $('#text_error').text('');
            enableSaveButtonIfValid();
        }
    }

    function enableSaveButtonIfValid() {
        if ($('#title_add_text_note').hasClass('is-valid') && $('#text_add_text_note').hasClass('is-valid')) {
            $('#save_button').prop('disabled', false);
        } else {
            $('#save_button').prop('disabled', true);
        }
    }
});