$(() => {
    handleKeyPress();
    handleClick();
    $("#add_button").prop("disabled", false);
});


function handleClick() {
    handleRemoveClick();
    handleAddClick();
}

// fonction pour gérer le clic sur les boutons remove_label
function handleRemoveClick() {
    $("button[name='remove_button']").each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            let labelName = $(this).val();

            $.ajax({
                url: "notes/remove_label_service",
                method: "POST",
                data: { label_name: labelName, note_id: noteId },
            }).done(function (response) {
                let jsonResponse = JSON.parse(response);
                $("#list_labels_" + labelName).remove();
                if(jsonResponse.length === 0) {
                    let html = "<span class=\"fst-italic\">This note does not yet have a label.</span>";
                    $("#list_labels").html(html);
                }
            });
        });
    });
}


// fonction pour gérer le clic sur le bouton add_label
function handleAddClick() {
    $("#add_button").click(function (e) {
        e.preventDefault();
        let newLabel = $("#add_label").val();

        $.ajax({
            url: "notes/add_label_service",
            method: "POST",
            data: { note_id: noteId, new_label: newLabel },
        }).done(function (response) {
            let jsonResponse = JSON.parse(response);
            let html = displayLabels(jsonResponse);
            $("#list_labels").html(html);
        });














        /*$.ajax({
            url: "notes/add_item_service",
            method: "POST",
            data: { note_id: noteId, new_item: newItem },
        }).done(function (response) {
            let jsonResponse = JSON.parse(response);

            let itemList = displayItems(jsonResponse);

            $("#list_items_ul").html(itemList);

            $("#add_item").val("");
            $("#add_item").removeClass("is-valid");
            $("#save_button").prop("disabled", false).css("opacity", "1");
        });*/
    });
}


// fonction pour gérer la frappe clavier dans le champ new_label
function handleKeyPress() {









    $("#add_item").keyup(function () {
        let content = $(this).val();
        $.ajax({
            url: "notes/check_new_item_service",
            method: "POST",
            data: { note_id: noteId, content: content },
        }).done(function (response) {
            let jsonResponse = JSON.parse(response);
            if ("new_item" in jsonResponse) {
                if ($("#add_item").val() === "") {
                    $("#new_item_error").remove();
                    $("#add_item").removeClass("is-invalid");
                    $("#add_item").removeClass("is-valid");
                    $("#add_button").prop("disabled", true);
                } else {
                    let html = '<span class="error-add-note" id="new_item_error">';
                    html += jsonResponse.new_item;
                    html += "</span>";
                    $("#new_item_error_div").html(html);
                    $("#add_item").removeClass("is-valid");
                    $("#add_item").addClass("is-invalid");
                    $("#add_button").prop("disabled", true);
                }
            } else {
                $("#new_item_error").remove();
                $("#add_item").removeClass("is-invalid");
                $("#add_item").addClass("is-valid");
                $("#add_button").prop("disabled", false);
            }
        });
    });
}





function displayItem(itemJson, itemElem) {
    // si c'est une array il n'y a pas d'erreur, si c'est un json il y a des erreurs
    if (Array.isArray(itemJson.errors)) {
        $("#save_button").prop("disabled", false).css("opacity", "1");

        itemElem.removeClass("is-invalid");
        itemElem.addClass("is-valid");
        $("#error_text_" + itemJson.id).remove();
    } else {
        $("#save_button").prop("disabled", true).css("opacity", "0.3");
        if (itemJson.errors.hasOwnProperty("item" + itemJson.id)) {
            itemElem.removeClass("is-valid");
            itemElem.addClass("is-invalid");
            let html = "";
            /*
            for (let itemId in itemJson.errors) {
              for (let error of itemJson.errors[itemId]) {
                html +=
                    "<div id='error_text_" +
                    itemId +
                    "' class='error-add-note pt-1'>" +
                    error +
                    "</div>";
                // $("#" + itemId).append(html);
                console.log("#list_items_" + itemId);
                $("#error_text_" + itemId).remove();
                $("#list_items_" + itemId).append(html);
              }
            }
            */

            for (let error of itemJson.errors["item" + itemJson.id]) {
                html +=
                    "<div id='" +
                    "error_text_" +
                    itemJson.id +
                    "' class='error-add-note pt-1'>" +
                    itemJson.errors["item" + itemJson.id] +
                    "</div>";
            }

            $("#error_text_" + itemJson.id).remove();
            $("#list_items_" + itemJson.id).append(html);
        }
    }
}

function displayLabels(labelsJson) {
    let html = "";
    let i = 0;
    for (let label of labelsJson) {
        i++;
        html += "<li class=\"list-unstyled\" id=\"list_labels_" + label + "\">";
        html += "<div class=\"input-group pt-3 has-validation\">";
        html += "<input readonly value=\"" + label + "\" type=\"text\" name=\"label" + i + "\" class=\"form-control bg-secondary text-white bg-opacity-25 border-secondary\" id=\"label" + i + "\" >";
        html += "<button name=\"remove_button\" value=\"" + label + "\" class=\"btn btn-danger btn-lg rounded-end  border-secondary\" type=\"submit\">";
        html += "<i class=\"bi bi-x\"></i>";
        html += "</button>";
        html += "</div>";
        html += "</li>";
    }
    return html;
}