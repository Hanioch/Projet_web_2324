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
            validateTitle();
            validateText();
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
        var noteId = $('#noteId').length ? $('#noteId').data('note-id') : -1;
        console.log(noteId)
        if (title.length === 0) {
            $('#title_add_text_note');
            $('#title_error').text('');
        } else if (title.length < minTitleLength || title.length > maxTitleLength) {
            $('#title_add_text_note').addClass('is-invalid');
            $('#title_error').text('Le titre doit avoir entre ' + minTitleLength + ' et ' + maxTitleLength + ' caractères.');
        } else {
            $.ajax({
                url: 'notes/checkUniqueTitle',
                type: 'POST',
                data: {
                    title: title,
                    noteId: noteId
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.unique) {
                        $('#title_add_text_note').addClass('is-invalid');
                        $('#title_error').text('Ce titre est déjà utilisé.');
                    } else {
                        $('#title_add_text_note').removeClass('is-invalid').addClass('is-valid');
                        $('#title_error').text('');
                    }
                    enableSaveButtonIfValid();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        enableSaveButtonIfValid();
    }

    function validateText() {
        var text = $('#text_add_text_note').val().trim();
        if (text.length === 0 || text === null) {
            $('#text_add_text_note').removeClass('is-invalid');
            $('#text_add_text_note').removeClass('is-valid');
            $('#text_error').text('');
        } else if (text.length < minContentLength || text.length > maxContentLength) {
            $('#text_add_text_note').addClass('is-invalid');
            $('#text_error').text('Le texte doit avoir entre ' + minContentLength + ' et ' + maxContentLength + ' caractères.');
        } else {
            $('#text_add_text_note').removeClass('is-invalid').addClass('is-valid');
            $('#text_error').text('');
        }
        enableSaveButtonIfValid();
    }

    function enableSaveButtonIfValid() {
        var titleIsValid = !$('#title_add_text_note').hasClass('is-invalid');
        var textIsValid = !$('#text_add_text_note').hasClass('is-invalid');
        var titleLength = $('#title_add_text_note').val().trim().length;

        if (titleIsValid && textIsValid && titleLength > 0) {
            $('#save_button').prop('disabled', false).removeClass('disabled-button');
        } else {
            $('#save_button').prop('disabled', true).addClass('disabled-button');
        }
    }
});