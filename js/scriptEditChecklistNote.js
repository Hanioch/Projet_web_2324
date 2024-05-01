$(() => {
  handleKeyPress();
  handleAddKeyPress();
  handleClick();

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
          $("#add_button").disable();
        } else {
          $("#new_item_error").remove();
          $("#add_item").removeClass("is-invalid");
          $("#add_item").addClass("is-valid");
          $("#add_button").enable();
        }
      });
    });
  }

  function handleAddClick() {
    $("#add_button").on("click", (e) => {
      e.preventDefault();

    });
  }

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


const refresh = () => {};
