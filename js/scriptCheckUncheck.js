$(() => {
    $('.btn-submit').click(function(event) {
        event.preventDefault();

        let itemId = $(this).closest('.input-group').find('input[name="item_id"]').val();
        let noteId = $(this).closest('.input-group').find('input[name="note_id"]').val();

        console.log(itemId);
        console.log(noteId);


        $.ajax({
            url: 'notes/toggle_checkbox_service',
            method: 'POST',
            data: { item_id: itemId, note_id: noteId }
        }).done(function(response) {

            console.log(response);

            var jsonResponse = JSON.parse(response);

            var checked = jsonResponse['\u0000ChecklistNoteItems\u0000checked'];

            console.log("Checked:",checked);

            var checkbox = $("#checkbox_" + itemId);

            checkbox.prop('checked', checked);

        });

    });
});