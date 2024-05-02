$(() => {
  handleKeyPress();
  handleAddKeyPress();
  handleClick();
  $("#add_button").prop("disabled", true);

  // fonction pour gérer la frappe clavier dans les champs edit_item
  function handleKeyPress() {
    $('[id^="item"]').keyup(function (event) {
      let itemId = $(this)
        .closest(".input-group")
        .find('input[name="item_id"]')
        .val();
      let noteId = $(this)
        .closest(".input-group")
        .find('input[name="note_id"]')
        .val();
      let itemElem = $(this);
      let item = itemElem.val();

      $.ajax({
        url: "notes/edit_item_service",
        method: "POST",
        data: { item_id: itemId, note_id: noteId, ["item" + itemId]: item },
      }).done(function (response) {
        let jsonResponse = JSON.parse(response);

        let item = displayItem(jsonResponse, itemElem);
      });
    });
  }

  function handleClick() {
    handleRemoveClick();
    handleAddClick();
    handleSaveClick();
  }

  // fonction pour gérer le clic sur les boutons remove_item
  function handleRemoveClick() {
    $("button[name='remove_button']").each(function () {
      $(this).click(function (e) {
        e.preventDefault();
        let itemId = $(this).val();

        let noteId = $(this)
            .closest(".input-group")
            .find('input[name="note_id"]')
            .val();

        $.ajax({
          url: "notes/remove_item_service",
          method: "POST",
          data: { item_id: itemId, note_id: noteId },
        }).done(function () {
          $("#list_items_" + itemId).remove();
        });
      });
    });
  }

  // fonction pour gérer la frappe clavier dans le champ new_item
  function handleAddKeyPress(){
    $("#add_item").keyup(function() {
      let noteId = $(this)
          .closest(".input-group")
          .find('input[name="note_id"]')
          .val();
      let content = $(this).val();
      $.ajax({
        url: "notes/check_new_item_service",
        method: "POST",
        data: { note_id: noteId, content: content },
      }).done(function (response) {
        let jsonResponse = JSON.parse(response);
        if ("new_item" in jsonResponse) {
          let html = "<span class=\"error-add-note\" id=\"new_item_error\">"
          html += jsonResponse.new_item;
          html += "</span>"
          console.log(html);
          console.log($(this))
          $("#new_item_error_div").html(html);
          $("#add_item").removeClass("is-valid");
          $("#add_item").addClass("is-invalid");
          $("#add_button").prop("disabled", true);
        } else {
          $("#new_item_error").remove();
          $("#add_item").removeClass("is-invalid");
          $("#add_item").addClass("is-valid");
          $("#add_button").prop("disabled", false);
        }
      });
    });
  }

  // fonction pour gérer le clic sur le bouton add_item
  function handleAddClick() {
    $("#add_button").click(function (e)  {
      e.preventDefault();

      let noteId = $(this)
          .closest(".input-group")
          .find('input[name="note_id"]')
          .val();

      let newItem = $("#add_item").val();

      $.ajax({
        url: "notes/add_item_service",
        method: "POST",
        data: { note_id: noteId, new_item: newItem },
      }).done(function (response) {
        let jsonResponse = JSON.parse(response);


        let itemList = displayItems(jsonResponse);

        $('#list_items_ul').html(itemList);

        $("#add_item").val('');
        $("#add_item").removeClass("is-valid");
        $("#add_item").removeClass("is-invalid");
      });

    });
  }

  //fonction pour gérer le clic sur le bouton save_checklistnote
  function handleSaveClick() {
    $(".btn-submit").click(function (event) {
      event.preventDefault();

      let itemId = $(this)
          .closest(".input-group")
          .find('input[name="item_id"]')
          .val();
      let noteId = $(this)
          .closest(".input-group")
          .find('input[name="note_id"]')
          .val();

      $.ajax({
        url: "notes/toggle_checkbox_service",
        method: "POST",
        data: { item_id: itemId, note_id: noteId },
      }).done(function (response) {
        let jsonResponse = JSON.parse(response);

        let itemList = displayItem(jsonResponse);

        $("#itemsDiv").html(itemList);

        handleClick();
      });
    });
  }
});

function displayItem(itemJson, itemElem) {

  // si c'est une array il n'y a pas d'erreur, si c'est un json il y a des erreurs
  if (Array.isArray(itemJson.errors)) {
    itemElem.removeClass("is-invalid");
    itemElem.addClass("is-valid");
    $("#error_text_" + itemJson.id).remove();
  } else {
    if (itemJson.errors.hasOwnProperty("item" + itemJson.id)) {
      itemElem.removeClass("is-valid");
      itemElem.addClass("is-invalid");
      let html = "";
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

function displayItems(itemsJson) {
  let html= "<label class=\"form-label mb-0\">Items</label>";
  html += "<ul class=\"list-unstyled\">";
  for (let i of itemsJson) {

    html += "<li class=\"list-unstyled\" id=\"list_items_" + i.id + "\">";
    html += "<div class=\"input-group pt-3 has-validation\">";
    html += "<div class=\"input-group-text bg-primary  border-secondary \">";
    html += "<input class=\"form-check-input border align-middle \" type=\"checkbox\" name=\"checked\" value=\"1\"" + (i.checked ? 'checked' : '') + " aria-label=\"Checkbox for following text input\" disabled>";
    html += "</div>";
    html += "<input value=\"" + i.content + "\" type=\"text\" name=\"item" + i.id + "\" class=\"form-control bg-secondary text-white bg-opacity-25 border-secondary\" id=\"item" + i.id + "\" >";
    // attribut supprimé à l'input au-dessus
    // value="<?php echo isset($_POST['item' . $item->get_Id()]) ? htmlspecialchars($_POST['item' . $item->get_Id()]) : ''; ?>"
    html += "<button name=\"remove_button\" value=\"" + i.id + "\" class=\"btn btn-danger btn-lg rounded-end  border-secondary\" type=\"submit\">";
    html += "<i class=\"bi bi-x\"></i>";
    html += "</button>";
    html += "<input type=\"hidden\" name=\"item_id\" value=\"" + i.id + "\">";
    html += "<input type=\"hidden\" name=\"note_id\" value=\"" + i.checklist_note + "\">";
    html += "</div>";
    html += "</li>";
  }
  return html;
}


const refresh = () => {};
