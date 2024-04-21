/*   const pageName = "editChecklistnote";
    const urlToRedirect = "<?= $back_url ?>"
*/
const idNote = $("#idNote").attr("value");
let isNoteModified = false;
const btnBack = $("#btnBack");
const oldTitleNote = $("#titleNote").val();
const oldContentNote = $("#contentNote").val();
btnBack.css({
  display: "block",
});

const checkIsNoteModified = () => {
  const newTitleNote = $("#titleNote");
  const newContentNote = $("#contentNote");

  if (oldTitleNote !== newTitleNote.val()) {
    return true;
  }

  if (oldContentNote !== undefined) {
    return newContentNote.val() !== oldContentNote;
  }
  return false;
};

const modalConfirmQuit = new bootstrap.Modal($("#modalGoBack"), {
  backdrop: true,
  focus: true,
  keyboard: true,
});

btnBack.on("click", () => {
  if (checkIsNoteModified()) {
    modalConfirmQuit.show();
  } else {
    window.location.href = urlToRedirect;
  }
});
