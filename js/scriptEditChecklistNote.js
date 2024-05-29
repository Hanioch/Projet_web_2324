let minTitleLength, maxTitleLength, itemMinLength, itemMaxLength;
$(() => {
  $.ajax({
    url: "notes/get_validation_rules_checklist_note_service",
    type: "GET",
    dataType: "json",
    success: (data) => {
      minTitleLength = data.minTitleLength;
      maxTitleLength = data.maxTitleLength;
      itemMinLength = data.itemMinLength;
      itemMaxLength = data.itemMaxLength;
    },
    error: (xhr, status, error) => {
      console.error(error);
    },
  });

  handleKeyPress();
  handleClick();
  $("#add_button").prop("disabled", true);
  $("#save_button").prop("disabled", true).css("opacity", "0.2");

  function handleKeyPress() {
    handleItemKeyPress();
    handleAddKeyPress();
    handleTitleKeyPress();
  }

  function handleClick() {
    handleRemoveClick();
    handleAddClick();
    handleSaveClick();
  }
});

function handleItemKeyPress() {
  $('[id^="item"]').keyup(function (event) {
    let itemId = $(this)
      .closest(".input-group")
      .find('input[name="item_id"]')
      .val();

    let itemElem = $(this);
    let item = itemElem.val();
    let response = {
      success: true,
      id: itemId,
    };
    let errors = [];

    if (item.length === 0) {
      errors.push("Item cannot be empty.");
    }

    if (item.length < itemMinLength || item.length > itemMaxLength) {
      errors.push("Item must be between 1 and 60 characters long.");
    }

    if (hasDuplicateItem({ value: item, id: "item" + itemId })) {
      errors.push("Item already exists.");
    }

    if (errors.length > 0) {
      response = {
        ...response,
        success: false,
        errors: {
          ["item" + itemId]: errors,
        },
      };
    }

    displayItem(response, itemElem);
  });
}

function hasDuplicateItem(item) {
  const { value, id } = item;
  const items = $('[id^="item"]')
    .map(function () {
      return {
        valueItem: $(this).val(),
        idItem: this.id,
      };
    })
    .get();

  let isTrue = false;
  let i = 0;

  while (!isTrue && i < items.length) {
    const { valueItem, idItem } = items[i];
    if (value.toUpperCase() === valueItem.toUpperCase() && id !== idItem) {
      isTrue = true;
    }
    i++;
  }
  return isTrue;
}

// fonction pour gérer la frappe clavier dans le champ new_item
function handleAddKeyPress() {
  $("#add_item").keyup(function () {
    let content = $(this).val();

    let errors = [];

    if (content.length === 0) {
      $("#new_item_error").remove();
      $("#add_item").removeClass("is-invalid");
      $("#add_item").removeClass("is-valid");
      $("#add_button").prop("disabled", true);
      return;
    }

    if (content.length > itemMaxLength) {
      errors.push("Item must be between 1 and 60 characters long.");
    }

    if (hasDuplicateItem({ value: content, id: "item" })) {
      errors.push("Item already exists.");
    }
    if (errors.length > 0) {
      let html = '<span class="error-add-note" id="new_item_error">';
      errors.forEach((err) => {
        html += err;
      });
      html += "</span>";
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
}

// fonction pour gérer la frappe clavier dans le champ titre
function handleTitleKeyPress() {
  $("#titleNote").keyup(function (event) {
    let newContent = $("#titleNote").val();
    if (
      newContent.length < minTitleLength ||
      newContent.length > maxTitleLength
    ) {
      $("#save_button").prop("disabled", true).css("opacity", "0.3");
      $("#titleNote").removeClass("is-valid");
      $("#titleNote").addClass("is-invalid");
      let html = '<span class="error-add-note" id="error_title_span">';
      html += "Title length must be between 3 and 25";
      html += "</span>";
      $("#error_title_span").remove();
      $("#title_div").append(html);
    } else {
      $("#save_button").prop("disabled", false).css("opacity", "1");
      $("#titleNote").removeClass("is-invalid");
      $("#titleNote").addClass("is-valid");
      $("#error_title_span").remove();
    }
  });
}

// fonction pour gérer le clic sur les boutons remove_item
function handleRemoveClick() {
  $("button[name='remove_button']").each(function () {
    $(this).click(function (e) {
      e.preventDefault();
      let itemId = $(this).val();

      $.ajax({
        url: "notes/remove_item_service",
        method: "POST",
        data: { item_id: itemId, note_id: noteId },
      }).done(function () {
        $("#list_items_" + itemId).remove();
        $("#save_button").prop("disabled", false).css("opacity", "1");
      });
    });
  });
}

// fonction pour gérer le clic sur le bouton add_item
function handleAddClick() {
  $("#add_button").click(function (e) {
    e.preventDefault();

    let newItem = $("#add_item").val();

    $.ajax({
      url: "notes/add_item_service",
      method: "POST",
      data: { note_id: noteId, new_item: newItem },
    }).done(function (response) {
      let jsonResponse = JSON.parse(response);

      let itemList = displayItems(jsonResponse);

      $("#list_items_ul").html(itemList);
      //remettre le handle click ne fonctionne pas ?? le premier remove après un add fait
      //toujours un refresh de la page
      handleRemoveClick();

      $("#add_item").val("");
      $("#add_item").removeClass("is-valid");
      $("#save_button").prop("disabled", false).css("opacity", "1");
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

    $.ajax({
      url: "notes/toggle_checkbox_service",
      method: "POST",
      data: { item_id: itemId, note_id: noteId },
    }).done(function (response) {
      let jsonResponse = JSON.parse(response);

      let itemList = displayItem(jsonResponse);

      $("#itemsDiv").html(itemList);

      handleRemoveClick();
      handleAddClick();
    });
  });
}

function displayItem(itemJson, itemElem) {
  // ajout d'un attribut success qui nous donne l'état de la requete.
  if (itemJson.success) {
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

      for (let error of itemJson.errors["item" + itemJson.id]) {
        html +=
          "<div id='" +
          "error_text_" +
          itemJson.id +
          "' class='error-add-note pt-1'>" +
          error +
          "</div>";
      }

      $("#error_text_" + itemJson.id).remove();
      $("#list_items_" + itemJson.id).append(html);
    }
  }
}

function displayItems(itemsJson) {
  let html = '<ul class="list-unstyled" id="list_items_ul">';
  for (let i of itemsJson) {
    html += '<li class="list-unstyled" id="list_items_' + i.id + '">';
    html += '<div class="input-group pt-3 has-validation">';
    html += '<div class="input-group-text bg-primary  border-secondary ">';
    html +=
      '<input class="form-check-input border align-middle " type="checkbox" name="checked" value="1"' +
      (i.checked ? "checked" : "") +
      ' aria-label="Checkbox for following text input" disabled>';
    html += "</div>";
    html +=
      '<input defaultValue="' +
      i.content +
      '"value="' +
      i.content +
      '" type="text" name="item' +
      i.id +
      '" class="item-editable form-control bg-secondary text-white bg-opacity-25 border-secondary" id="item' +
      i.id +
      '" >';
    // attribut supprimé à l'input au-dessus
    // value="<?php echo isset($_POST['item' . $item->get_Id()]) ? htmlspecialchars($_POST['item' . $item->get_Id()]) : ''; ?>"
    html +=
      '<button name="remove_button" value="' +
      i.id +
      '" class="btn btn-danger btn-lg rounded-end  border-secondary" type="submit">';
    html += '<i class="bi bi-x"></i>';
    html += "</button>";
    html += '<input type="hidden" name="item_id" value="' + i.id + '">';
    html +=
      '<input type="hidden" name="note_id" value="' + i.checklist_note + '">';
    html += "</div>";
    html += "</li>";
  }
  return html;
}
