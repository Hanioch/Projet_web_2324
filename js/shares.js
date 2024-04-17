
function addShare(noteId, userId, permission) {
    console.log("Paramètres envoyés : noteId =", noteId, "userId =", userId, "permission =", permission);
    $.ajax({
        type: "POST",
        url: "js/addshare.php",
        data: {
           // action: "addshare.php",
            noteId: noteId,
            userId: userId,
            permission: permission
        },
        success: function(response) {
            console.log("Réponse reçue :", response);
            if (response.success) {
                console.log("okey")
            } else {
                console.log("pas okey")
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Erreur d'envoi de la requête AJAX :", textStatus, errorThrown);
        }
    });
}

function removeShare(noteId, userId) {
    $.ajax({
        type: "POST",
        url: "notes/remove_share_ajax",
        data: { noteId: noteId, user: userId },
        success: function(response) {
            if (response.success) {
                console.log("okey")
            } else {
                console.log("pas okey")
            }
        }
    });
}

function changePermission(noteId, userId) {
    $.ajax({
        type: "POST",
        url: "notes/change_permission_ajax",
        data: { noteId: noteId, user: userId },
        success: function(response) {
            if (response.success) {
            } else {
            }
        },
        error: function() {
        }
    });
}
document.getElementById("addShare").addEventListener("click", function() {
    let userId = document.getElementById("user").value;
    let permission = document.getElementById("permission").value;
    addShare(21, 2, 0);
});