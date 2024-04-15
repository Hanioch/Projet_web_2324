$(function () {
  $("#sortable1, #sortable2")
    .sortable({
      connectWith: ".connectedSortable",
      update: function (event, ui) {
        var itemMoved = ui.item; // Récupère l'élément déplacé
        var replacedItem = ui.item.prev(); // Récupère l'élément qu'il a remplacé
        console.log("Id Element déplacé :", itemMoved[0].id);
        console.log("Element déplacé :", itemMoved[0].children[0]);
        console.log("Id Element remplacé :", replacedItem[0].id);
        console.log("Element remplacé :", replacedItem[0].children[0]);
      },
    })
    .disableSelection();
});
/**
 * TODO ici tu dois faire une requete ajax qui vas envoyer les deux element qu'on veut
 * déplacer et changer toute les modifications de chacune des notes entres les 2.
 * Bien changer tout leurs poids en fonction de pinned et unpinned
 * Vérifie que la note reçu peut aussi être pinned ou unpined via cette requete ajax et le
 * poids doit dans tout les cas être adapté.
 * */
