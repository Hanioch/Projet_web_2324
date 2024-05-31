const button = $("#button_trash");
button.attr("type", "button");
button.attr("data-bs-toggle", "modal");
button.attr("data-bs-target", "#fullScreenModal");
let isDeleted = false;

const validBtn = $("#validBtn");
const note_id = $("#note_id").attr("value");

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
  if (isDeleted) {
    const url = window.location.href;
    const newUrl = url.replace(/(Notes\/).*/, "$1archives");
    window.location.href = newUrl;
    return;
  }

  $.post("notes/delete_service", { note_id })
    .done((res) => {
      isDeleted = true;
      modalConfirmDelete.hide();
      modalSuccessDelete.show();
    })
    .fail((xhr, status, error) => {
      if (xhr.status === 400) {
      }
      console.error("Error ", xhr, " : ", error);
    });
};

button.on("click", function () {
  modalConfirmDelete.show();
});
validBtn.on("click", validDelete);
