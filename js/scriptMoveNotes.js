$(function () {
  let prevParent = null;
  let itemMoved = null;
  let idReplacedItem = null;

  $("#sortable1, #sortable2")
    .sortable({
      connectWith: ".connectedSortable",
      start: (event, ui) => {
        prevParent = ui.item.parent()[0].id;
      },
      update: (event, ui) => {
        itemMoved = ui.item[0];
        let replacedItem = ui.item.prev();
        idReplacedItem = replacedItem[0] ? replacedItem[0].id : 0;
      },
      stop: (event, ui) => {
        let newParent = ui.item.parent()[0].id;
        let isSwitched = prevParent !== newParent;

        if (
          prevParent !== null &&
          itemMoved !== null &&
          idReplacedItem !== null
        ) {
          sendIdMovable(itemMoved.id, idReplacedItem, isSwitched);
        }
      },
    })
    .disableSelection();
});

const sendIdMovable = (idNoteMoved, idReplacedNote, switchedColumn) => {
  $.post(
    "notes/move_note_service",
    {
      idNoteMoved,
      idReplacedNote,
      switchedColumn,
    },
    (res) => {
      console.log(res);
      checkDivAreEmpty();
    }
  );
};

const checkDivAreEmpty = () => {
  const pinned = document.getElementById("sortable1");
  const other = document.getElementById("sortable2");
  const pinnedChild = pinned ? pinned.childElementCount : false;
  const otherChild = other ? other.childElementCount : false;

  if (pinned && pinnedChild === 0) {
    const parent = pinned.parentNode;
    const title = document.getElementById("Pinned");
    parent.removeChild(pinned);
    parent.removeChild(title);
  }

  if (other && otherChild === 0) {
    const parent = other.parentNode;
    const title = document.getElementById("Others");

    parent.removeChild(other);
    parent.removeChild(title);
  }
};
