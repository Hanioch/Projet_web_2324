$(document).ready(function () {
  var minTitleLength, maxTitleLength, minContentLength, maxContentLength;
  $.ajax({
    url: "notes/getValidationRules",
    type: "GET",
    dataType: "json",
    success: function (data) {
      minTitleLength = data.minTitleLength;
      maxTitleLength = data.maxTitleLength;
      minContentLength = data.minContentLength;
      maxContentLength = data.maxContentLength;
    },
    error: function (xhr, status, error) {
      console.error(error);
    },
  });
  $("#titleNote").on("input", function () {
    validateTitle();
  });

  $("#contentNote").on("input", function () {
    validateText();
  });

  function validateTitle() {
    var title = $("#titleNote").val().trim();
    var noteId = $("#noteId").length ? $("#noteId").data("note-id") : -1;
    if (title.length === 0) {
      $("#titleNote");
      $("#title_error").text("");
    } else if (title.length < minTitleLength || title.length > maxTitleLength) {
      $("#titleNote").addClass("is-invalid");
      $("#title_error").text(
        "Le titre doit avoir entre " +
          minTitleLength +
          " et " +
          maxTitleLength +
          " caractères."
      );
    } else {
      $.ajax({
        url: "notes/checkUniqueTitle",
        type: "POST",
        data: {
          title: title,
          noteId: noteId,
        },
        dataType: "json",
        success: function (response) {
          if (!response.unique) {
            $("#titleNote").addClass("is-invalid");
            $("#title_error").text("Ce titre est déjà utilisé.");
          } else {
            $("#titleNote")
              .removeClass("is-invalid")
              .addClass("is-valid");
            $("#title_error").text("");
          }
          enableSaveButtonIfValid();
        },
        error: function (xhr, status, error) {
          console.error(error);
        },
      });
    }
    enableSaveButtonIfValid();
  }

  function validateText() {
    var text = $("#contentNote").val().trim();
    if (text.length === 0 || text === null) {
      $("#contentNote").removeClass("is-invalid");
      $("#contentNote").removeClass("is-valid");
      $("#text_error").text("");
    } else if (
      text.length < minContentLength ||
      text.length > maxContentLength
    ) {
      $("#contentNote").addClass("is-invalid");
      $("#text_error").text(
        "Le texte doit avoir entre " +
          minContentLength +
          " et " +
          maxContentLength +
          " caractères."
      );
    } else {
      $("#contentNote").removeClass("is-invalid").addClass("is-valid");
      $("#text_error").text("");
    }
    enableSaveButtonIfValid();
  }

  function enableSaveButtonIfValid() {
    var titleIsValid = !$("#titleNote").hasClass("is-invalid");
    var textIsValid = !$("#contentNote").hasClass("is-invalid");
    var titleLength = $("#titleNote").val().trim().length;

    if (titleIsValid && textIsValid && titleLength > 0) {
      $("#save_button").prop("disabled", false).removeClass("disabled-button");
    } else {
      $("#save_button").prop("disabled", true).addClass("disabled-button");
    }
  }
  enableSaveButtonIfValid();
});
