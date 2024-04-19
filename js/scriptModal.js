const button = $("#button_trash");
button.attr("type", "button");
button.attr("data-bs-toggle", "modal");
button.attr("data-bs-target", "#fullScreenModal");

const validBtn = $("#validBtn");
const idNote = $("#idNote").attr("value");

const modalConfirmDelete = new bootstrap.Modal($("#fullScreenModal"), {
  backdrop: true,
  focus: true,
  keyboard: false,
});
const modalSuccessDelete = new bootstrap.Modal($("#modalSuccessDelete"), {
  backdrop: true,
  focus: true,
  keyboard: false,
});

const validDelete = () => {
  $.post("notes/delete_using_js", { idNote })
    .done((res) => {
      modalConfirmDelete.hide();
      modalSuccessDelete.show();
    })
    .fail((xhr, status, error) => {
      console.error("Error ", status, " : ", error);
    });
};

const redirect = () => {};

button.on("click", function () {
  modalConfirmDelete.show();
});
validBtn.on("click", validDelete);
