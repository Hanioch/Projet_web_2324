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
                console.log(response)
                try {
                    var data = JSON.parse(response);
                    console.log(data)
                    createSharesElements(data);
                } catch (error) {
                    console.error("Erreur de parsing JSON :", error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur d'envoi de la requête AJAX :", textStatus, errorThrown);
            }
        });
        return false;
    }
    function createSharesElements(response) {
        $('#sharesContainer').empty();

        if (response.length > 0) {
            response.forEach(function(share) {
                var shareHtml = `
                <div class="input-group mb-3">
                    <input type="text" name="listShares" class="form-control text-white custom-placeholder bg-dark border-secondary fst-italic" placeholder="${share.full_name} (${share.editor ? 'editor' : 'reader'})" aria-label="Recipient's username with two button addons" disabled>
                    <form action="./notes/shares/${share.note}" method="post">
                        <input type="hidden" name="user" value="${share.user}" id="userPermission">
                        <button class="btn btn-primary border-secondary border rounded-0" type="submit" name="changePermission" id="changePermission">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </form>
                    <form action="./notes/shares/${share.note}" method="post">
                        <input type="hidden" name="user" value="${share.user}" id="userRemove">
                        <button class="arrondirbtn btn btn-danger border-secondary" type="submit" name="removeShare" id="removeShare">
                            <i class="bi bi-x"></i>
                        </button>
                    </form>
                </div>
            `;
                $('#sharesContainer').append(shareHtml);
            });
        } else {
            $('#sharesContainer').append("<p>This note is not shared yet.</p>");
        }
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
                updateExistingShares(response.existingShares);
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
                updateExistingShares(response.existingShares);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur d'envoi de la requête AJAX :", textStatus, errorThrown);
            }
        });
        return false;
    }

    var addShareButton = document.getElementById("addShare");
    if (addShareButton) {
        addShareButton.addEventListener("click", function(event) {
            event.preventDefault();
            let noteId = document.getElementById("noteId").value;
            let userId = document.getElementById("user").value;
            let permission = document.getElementById("permission").value;
            addShare(noteId,userId, permission);
        });
    }
    var removeShareButton = document.getElementById("removeShare");
    if (removeShareButton) {
        removeShareButton.addEventListener("click", function(event) {
            event.preventDefault();
            let noteId = document.getElementById("noteId").value;
            let userId = document.getElementById("userRemove").value;
            removeShare(noteId, userId);
        });
    }

    var changePermissionButton = document.getElementById("changePermission");
    if (changePermissionButton) {
        changePermissionButton.addEventListener("click", function(event) {
            event.preventDefault();
            let noteId = document.getElementById("noteId").value;
            let userId = document.getElementById("userPermission").value;
            changePermission(noteId, userId);
        });
    }
});