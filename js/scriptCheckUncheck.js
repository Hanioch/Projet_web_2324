$(() => {
    $('input[type="checkbox"]').change(async function() {

        const itemId = $(this).closest('.input-group').find('input[name="item_id"]').val();
        const noteId = $(this).closest('.input-group').find('input[name="note_id"]').val();
        const isChecked = $(this).prop('checked');

        console.log(itemId);
        console.log(noteId);
        console.log(isChecked);

        $(this).prop('checked', isChecked);

        const response = await $.post('notes/toggleCheckbox', { item_id: itemId, note_id: noteId, checked: isChecked });

    });
});