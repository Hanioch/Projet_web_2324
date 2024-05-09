$(() => {
    handleKeyPress();
    handleClick();
    $("#add_button").prop("disabled", true);

    function handleClick() {
        handleRemoveClick();
        handleAddClick();
    }
});

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
                if(!("labels" in jsonResponse)) {
                    let html = "<span class=\"fst-italic\">This note does not yet have a label.</span>";
                    $("#list_labels").html(html);
                }
                labelsToSuggest = [];
                if("suggestions" in jsonResponse) {
                    labelsToSuggest = jsonResponse.suggestions;
                }
                refreshDataList(labelsToSuggest);
                handleKeyPress();
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
            $("#add_label").val("");
            handleRemoveClick();
            handleKeyPress();
            $("#add_button").prop("disabled", true);

            labelsToSuggest = [];
            if("suggestions" in jsonResponse) {
                labelsToSuggest = jsonResponse.suggestions;
            }
            refreshDataList(labelsToSuggest);
        });
    });
}


// fonction pour gérer la frappe clavier dans le champ new_label
function handleKeyPress() {
    $("#add_label").keyup(function () {
        let content = $(this).val();
        $.ajax({
            url: "notes/check_new_label_service",
            method: "POST",
            data: { note_id: noteId, content: content },
        }).done(function (response) {
            let jsonResponse = JSON.parse(response);
            html = "";
            if(jsonResponse.label.length !== 0) {
                if($("#add_label").val() === "") {
                    $("#add_button").prop("disabled", true);
                    $("#add_label").removeClass("is-valid");
                    $("#add_label").removeClass("is-invalid");
                    $("#error_div").html("");
                } else {
                    $("#add_button").prop("disabled", true);
                    $("#add_label").addClass("is-invalid");
                    $("#add_label").removeClass("is-valid");
                    for (let error of jsonResponse.label) {
                        html += "<div id=\"new_label_error_div\" class=\"error-add-note pt-1\">";
                        html += error;
                        html += "</div>";
                    }
                }
                $("#error_div").html(html);
            } else {
                $("#add_button").prop("disabled", false);
                $("#add_label").addClass("is-valid");
                $("#add_label").removeClass("is-invalid");
                $("#error_div").html("");
            }





            /*if ("new_item" in jsonResponse) {
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
            }*/
        });
    });
}



function refreshDataList(labelsToSuggest) {
    if(labelsToSuggest.length === 0) {
        $("#label_data_list").html("");
    } else {
        let html = "";
        for (let label of labelsToSuggest) {
            html += "<option value=\"" + label + "\"></option>";
        }
        $("#label_data_list").html(html);
    }
}

function displayLabels(labelsJson) {
    let html = "";
    let i = 0;
    if("labels" in labelsJson) {
        for (let label of labelsJson.labels) {
            i++;
            html += "<li class=\"list-unstyled\" id=\"list_labels_" + label + "\">";
            html += "<div class=\"input-group pt-3 has-validation\">";
            html += "<input readonly value=\"" + label + "\" type=\"text\" name=\"label\" class=\"form-control bg-secondary text-white bg-opacity-25 border-secondary\" id=\"label\" >";
            html += "<button name=\"remove_button\" value=\"" + label + "\" class=\"btn btn-danger btn-lg rounded-end  border-secondary\" type=\"submit\">";
            html += "<i class=\"bi bi-x\"></i>";
            html += "</button>";
            html += "</div>";
            html += "</li>";
        }
    }
    return html;
}