document.addEventListener("DOMContentLoaded", function() {
function addShare(noteId, userId, permission) {
    $.ajax({
        type: "POST",
        url: "controller_notes/add_share_ajax",
        data: { noteId: noteId, user: userId, permission: permission },
        success: function(response) {
            if (response.success) {
            } else {
            }
        },
        error: function() {
        }
    });
}

function removeShare(noteId, userId) {
    $.ajax({
        type: "POST",
        url: "controller_notes/remove_share_ajax",
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

function changePermission(noteId, userId) {
    $.ajax({
        type: "POST",
        url: "controller_notes/change_permission_ajax",
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

let shareList = document.getElementById("shareList");

function addToShareList(userId, permission) {
    let listItem = document.createElement("li");
    listItem.textContent = `User: ${userId}, Permission: ${permission}`;
    shareList.appendChild(listItem);
}
function removeFromShareList(userId) {
    let items = shareList.getElementsByTagName("li");
    for (let i = 0; i < items.length; i++) {
        if (items[i].textContent.includes(userId)) {
            shareList.removeChild(items[i]);
            break;
        }
    }
}

function updatePermissionInList(userId, newPermission) {
    let items = shareList.getElementsByTagName("li");
    for (let i = 0; i < items.length; i++) {
        if (items[i].textContent.includes(userId)) {
            items[i].textContent = items[i].textContent.replace(/Permission: \w+/, `Permission: ${newPermission}`);
            break;
        }
    }
}


    document.getElementById("addShare").addEventListener("click", function() {
        console.log("Clicked on addShare button");
        let userId = document.getElementById("user").value;
        let permission = document.getElementById("permission").value;
        addShare(23, userId, permission);
    });

    document.getElementById("removeShare").addEventListener("click", function() {
        console.log("Clicked on removeShare button");
        let userId = document.getElementById("userRemove").value;
        removeShare(23, userId);
    });

    document.getElementById("changePermission").addEventListener("click", function() {
        console.log("Clicked on changePermission button");
        let userId = document.getElementById("userPermission").value;
        changePermission(23, userId);
    });
});