$(document).ready(function() {
    $('input[type="checkbox"]').change(function() {
        var itemId = $(this).closest('.input-group').find('input[name="item_id"]').val();
        var noteId = $(this).closest('.input-group').find('input[name="note_id"]').val();
        var isChecked = $(this).prop('checked');

        if (isChecked) {
            $(this).closest('.input-group').prop('checked', false);
        } else {
            $(this).closest('.input-group').prop('checked', true);
        }


        $.ajax({
            url: 'notes/toggleCheckbox',
            method: 'POST',
            data: { item_id: itemId, note_id: noteId, checked: isChecked },
            success: function(response) {
                // Gérer la réponse du serveur (optionnel)
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Gérer les erreurs (optionnel)
                console.error(error);
            }
        });
    });
});