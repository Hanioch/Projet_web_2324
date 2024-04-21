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
        itemMoved = ui.item[0]; // Récupère l'élément déplacé
        let replacedItem = ui.item.prev(); // Récupère l'élément qu'il a remplacé
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
    "notes/move_note_js",
    {
      idNoteMoved,
      idReplacedNote,
      switchedColumn,
    },
    (res) => {
      console.log(res);
    }
  );
};
