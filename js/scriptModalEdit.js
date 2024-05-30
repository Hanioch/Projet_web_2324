const idNote = $("#idNote").attr("value");
let isNoteModified = false;
const btnBack = $("#btnBack");
const oldTitleNote = $("#titleNote").val();
const oldContentNote = $("#contentNote").val();

btnBack.css({
  display: "block",
});

const isItemsUpdated = () => {
  return $(".item-editable")
    .toArray()
    .some((e) => {
      let defaultValue = e.defaultValue;
      let value = $(e).val();
      return defaultValue !== value;
    });
};

const checkIsNoteModified = () => {
  const newTitleNote = $("#titleNote");
  const newContentNote = $("#contentNote");
  const addItem = $("#add_item");

  if (oldTitleNote !== newTitleNote.val()) {
    return true;
  }

  if (oldContentNote !== undefined) {
    return newContentNote.val() !== oldContentNote;
  }

  if (addItem.val() !== "") {
    return true;
  }

  return isItemsUpdated();
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
