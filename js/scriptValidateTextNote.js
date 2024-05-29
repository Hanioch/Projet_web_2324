$(document).ready(function () {
  var minTitleLength, maxTitleLength, minContentLength, maxContentLength;
  $.ajax({
    url: "notes/get_validation_rules_service",
    type: "GET",
    dataType: "json",
    success: function (data) {
      minTitleLength = data.min_title_length;
      maxTitleLength = data.max_title_length;
      minContentLength = data.min_content_length;
      maxContentLength = data.max_content_length;
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
    var note_id = $("#noteId").length ? $("#noteId").data("note-id") : -1;
    if (title.length === 0) {
      $("#titleNote");
      $("#title_error").text("");
    } else if (title.length < minTitleLength || title.length > maxTitleLength) {
      $("#titleNote").addClass("is-invalid");
      $("#title_error").text(
        "Title must be between " +
          minTitleLength +
          " and " +
          maxTitleLength +
          " characters."
      );
    } else {
      $.ajax({
        url: "notes/check_unique_title_service",
        type: "POST",
        data: {
          title: title,
          note_id: note_id,
        },
        dataType: "json",
        success: function (response) {
          if (!response.unique) {
            $("#titleNote").addClass("is-invalid");
            $("#title_error").text("Title is already used.");
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
        "Content must be between " +
          minContentLength +
          " and " +
          maxContentLength +
          " characters."
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
