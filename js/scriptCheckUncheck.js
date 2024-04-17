$(() => {
    $('.btn-submit').click(function(event) {
        event.preventDefault();


        let itemId = $(this).closest('.input-group').find('input[name="item_id"]').val();
        let noteId = $(this).closest('.input-group').find('input[name="note_id"]').val();

        //$(this).closest('.input-group').find('input[type="checkbox"]').attr('checked', isChecked ? 'checked' : '');

        console.log(itemId);
        console.log(noteId);


        $.ajax({
            url: 'notes/toggle_checkbox_service',
            method: 'POST',
            data: { item_id: itemId, note_id: noteId }
        }).done(function(response) {
            //$var = $($.parseHTML(response)).find("#checkbox_"+itemId);
            console.log(response);
            //$("#checkbox_"+itemId).html($var);
        });

    });
});