<?php

    $noteId = $_POST['noteId'] ?? null;
    $userId = $_POST['userId'] ?? null;
    $permission = $_POST['permission'] ?? null;

    if (isset($noteId) && isset($userId) && isset($permission)) {
        if(NoteShare::add_Share($noteId, $userId, $permission)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to add share"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
    }