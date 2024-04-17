document.addEventListener("DOMContentLoaded", function() {
    function addShare(noteId, userId, permission) {
        console.log("Paramètres envoyés : noteId =", noteId, "userId =", userId, "permission =", permission);
        $.ajax({
            type: "POST",
            url: "notes/add_share_ajax",
            data: {
                noteId: noteId,
                userId: userId,
                permission: permission
            },
            success: function (response) {
                console.log("Réponse reçue :", response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur d'envoi de la requête AJAX :", textStatus, errorThrown);
            }
        });
        return false;
    }

    function removeShare(noteId, userId) {
        console.log("Paramètres envoyés : noteId =", noteId, "userId =", userId);
        $.ajax({
            type: "POST",
            url: "notes/remove_share_ajax",
            data: {
                noteId: noteId,
                user: userId
            },
            success: function (response) {
                console.log("Réponse reçue :", response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur d'envoi de la requête AJAX :", textStatus, errorThrown);
            }
        });
        return false;
    }

    function changePermission(noteId, userId) {
        console.log("Paramètres envoyés : noteId =", noteId, "userId =", userId);
        $.ajax({
            type: "POST",
            url: "notes/change_permission_ajax",
            data: {
                noteId: noteId,
                user: userId
            },
            success: function (response) {
                console.log("Réponse reçue :", response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur d'envoi de la requête AJAX :", textStatus, errorThrown);
            }
        });
        return false;
    }

    document.getElementById("addShare").addEventListener("click", function () {
        event.preventDefault();
        let noteId = document.getElementById("noteId").value;
        let userId = document.getElementById("user").value;
        let permission = document.getElementById("permission").value;
        addShare(noteId, userId, permission);
    });
    document.getElementById("removeShare").addEventListener("click", function () {
        event.preventDefault();
        let noteId = document.getElementById("noteId").value;
        let userId = document.getElementById("userRemove").value;
        removeShare(noteId, userId);
    });

    document.getElementById("changePermission").addEventListener("click", function () {
        event.preventDefault();
        let noteId = document.getElementById("noteId").value;
        let userId = document.getElementById("userPermission").value;
        changePermission(noteId, userId);
    });
});