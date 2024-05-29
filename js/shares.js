document.addEventListener('DOMContentLoaded', function() {
    let buttons = document.querySelectorAll('button');

    buttons.forEach(function(button) {
        button.type = 'button';
    });
});
function addShareOnClick() {
    let noteId = document.getElementById("noteId").value;
    let userId = document.getElementById("user").options[document.getElementById("user").selectedIndex].value;
    let permission = document.getElementById("permission").options[document.getElementById("permission").selectedIndex].value;
    let errorContainer = document.getElementById("errorContainer");

    if (userId == "-User-" || permission == "-Permission-") {
        errorContainer.innerHTML = `
        <div class="alert alert-warning" role="alert">
           Please select a user and a permission to share.
        </div>
    `;
    } else {
        errorContainer.innerHTML = "";
        addShares(noteId, userId, permission);
    }
}

function addShares(noteId, userId, permission) {
    if (document.getElementById("errorContainer").innerHTML.trim() === "") {
        $.ajax({
            type: "POST",
            url: "notes/add_share_service",
            data: {
                noteId: noteId,
                userId: userId,
                permission: permission
            },
            success: function (response) {
                try {
                    let data = JSON.parse(response);
                    refreshPage(data);
                } catch (error) {
                    console.error("Error parsing JSON in addShare:", error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error sending AJAX request in addShare:", textStatus, errorThrown);
            }
        });
        return true;
    }
}

function removeShares(noteId, userId) {
    $.ajax({
        type: "POST",
        url: "notes/remove_share_service",
        data: {
            noteId: noteId,
            userId: userId,
        },
        success: function (response) {
            try {
                let data = JSON.parse(response);
                refreshPage(data);
            } catch (error) {
                console.error("Error parsing JSON in removeShares:", error);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error sending AJAX request in removeShares:", textStatus, errorThrown);
        }
    });
    return false;
}

    function changePermissions(noteId, userId) {
        $.ajax({
            type: "POST",
            url: "notes/change_permission_service",
            data: {
                noteId: noteId,
                userId: userId
            },
            success: function (response) {
                try {
                    let data = JSON.parse(response);
                    refreshPage(data);
                } catch (error) {
                    console.error("Error parsing JSON in changePermission:", error);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error sending AJAX request in changePermission:", textStatus, errorThrown);
            }
        });
        return false;
    }

    function refreshPage(data) {
        createSharesAndAdd(data.usersToShareWith, data.existingShares);
    }

    function createSharesAndAdd(usersToShareWith, existingShares) {
        $('#sharesContainer').empty();

        if (existingShares.length > 0) {
            existingShares.forEach(function(share) {
                var shareHtml = `
                    <div class="input-group mb-3">
                        <input type="text" name="listShares" class="form-control text-white custom-placeholder bg-dark border-secondary fst-italic" placeholder="${share.full_name} (${share.editor ? 'editor' : 'reader'})" aria-label="Recipient's username with two button addons" disabled>
                        <form action="./notes/shares/${share.note}" method="post">
                            <input type="hidden" name="user" value="${share.user}" id="userPermission_${share.user}">
                            <button class="btn btn-primary border-secondary border rounded-0"  name="changePermission" id="changePermission" type="button" onclick="changePermissions('${share.note}', '${share.user}')">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </form>
                        <form action="./notes/shares/${share.note}" method="post">
                            <input type="hidden" name="user" value="${share.user}" id="userRemove_${share.user}">
                            <button class="arrondirbtn btn btn-danger border-secondary" name="removeShare" id="removeShare" type="button" onclick="removeShares('${share.note}', '${share.user}')">
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

        $('#addContainer').empty();

        if (Object.keys(usersToShareWith).length > 0) {
            var noteId = usersToShareWith[Object.keys(usersToShareWith)[0]].note_id;
            let sortedUsers = Object.entries(usersToShareWith).sort((a, b) => a[1].full_name.localeCompare(b[1].full_name));

            var selectHtml = `
            <form action="./notes/shares/${noteId}" method="post">
                <div class="input-group mb-3">
                    <select class="form-select bg-dark text-white border-secondary" name="user" id="user">
                        <option disabled selected>-User-</option>
        `;

            sortedUsers.forEach(function([userId, userDetails]) {
                selectHtml += `<option value="${userId}">${userDetails.full_name}</option>`;
            });

            selectHtml += `
                        </select>
                         <select class="form-select bg-dark text-white border-secondary" name="permission" id="permission">
                            <option disabled selected>-Permission-</option>
                            <option value="1">Editor</option>
                            <option value="0">Reader</option>
                        </select>
                        <input type="hidden" name="noteId" value="${noteId}" id="noteId">
                        <button id="addShare" name="addShare" class="btn btn-primary border-secondary" type="button" onclick="addShareOnClick()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
              </form>
              <div class="" id="errorContainer"></div>
            `;
            $('#addContainer').append(selectHtml);
        } else {
            var addMaxHtml = `
        <div class="alert alert-info" role="alert">
            This note has been shared with all users. There are no more users to share this note with.
        </div>
    `;
            $('#addContainer').append(addMaxHtml);
        }
    }
