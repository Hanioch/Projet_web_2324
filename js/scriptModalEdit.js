const idNote = $("#idNote").attr("value");
let isNoteModified = false;
const btnBack = $("#btnBack");
btnBack.css({
  display: "block",
});

const modifDone = () => {
  if (!isNoteModified) {
    isNoteModified = true;
  }
};

const modalConfirmQuit = new bootstrap.Modal($("#modalGoBack"), {
  backdrop: true,
  focus: true,
  keyboard: false,
});

btnBack.on("click", () => {
  if (isNoteModified) {
    modalConfirmQuit.show();
  } else {
    window.location.href = "./notes";
  }
});
