$(() => {
    $('.btn-submit').click(function(event) {
        event.preventDefault();

        console.log("coucou");

        let itemId = $(this).closest('.input-group').find('input[name="item_id"]').val();
        let noteId = $(this).closest('.input-group').find('input[name="note_id"]').val();
        let isChecked = $(this).closest('.input-group').find('input[type="checkbox"]').prop('checked');

        console.log(itemId);
        console.log(noteId);
        console.log(isChecked);

        $(this).closest('.input-group').find('input[type="checkbox"]').attr('checked', isChecked ? 'checked' : null);

        $.ajax({
            url: 'notes/toggle_checkbox_service',
            method: 'POST',
            data: { item_id: itemId, note_id: noteId, checked: isChecked }
        });

    });
});