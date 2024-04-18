$(() => {
    handleClick();
    function handleClick() {

        $('.btn-submit').click(function (event) {
            event.preventDefault();

            let itemId = $(this).closest('.input-group').find('input[name="item_id"]').val();
            let noteId = $(this).closest('.input-group').find('input[name="note_id"]').val();

            $.ajax({
                url: 'notes/toggle_checkbox_service',
                method: 'POST',
                data: {item_id: itemId, note_id: noteId}
            }).done(function (response) {
                let jsonResponse = JSON.parse(response);

                let itemList = displayItems(jsonResponse);

                $('#itemsDiv').html(itemList);

                handleClick();
            });
        });
    }
});

function displayItems(itemsJson) {
    let html= "<label class='form-label'>Items</label>";
    for (let i of itemsJson) {

        html += "<form action='notes/toggle_Checkbox' method='POST'>";
        html += "<div class='input-group mb-3'>";
        html += "<div class='input-group-text bg-primary '>";
        html += "<button class='btn btn-submit' >";
        html += "<input class='form-check-input border' id='checkbox_" + i.id + "' type='checkbox' name='checked' value='1' " + (i.checked ? 'checked' : '') + " aria-label='Checkbox for following text input' >";
        html += "</button>";
        html += "</div>";
        html += "<input type='text' class='form-control bg-secondary text-white bg-opacity-25 border-0 " + (i.checked ? 'text-decoration-line-through' : '') + "' value='" + i.content + "' aria-label='Text input with checkbox' disabled>";
        html += "<input type='hidden' name='item_id' value='" + i.id + "'>";
        html += "<input type='hidden' name='note_id' value='" + i.checklist_note + "'>";
        html += "</div>";
        html += "</form>";
    }
    return html;
}