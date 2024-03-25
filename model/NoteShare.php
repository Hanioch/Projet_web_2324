<?php

require_once "model/MyModel.php";
require_once "model/CheckListNote.php";
require_once "model/CheckListNoteItems.php";
require_once "model/TextNote.php";
require_once "model/User.php";
require_once "model/Note.php";
require_once "model/NoteShare.php";
class NoteShare extends MyModel{


    public function __construct(private int $id, private int $noteId, private int $userId, private int $isEditor) {}

    public function get_Id(): ?int
    {
        return $this->id;
    }

    public function set_Id(int $id): void
    {
        $this->id = $id;
    }

    public function get_Note_Id(): int
    {
        return $this->noteId;
    }

    public function set_Note_Id(int $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function get_User_Id(): int
    {
        return $this->userId;
    }

    public function set_User_Id(int $userId): void
    {
        $this->userId = $userId;
    }

    public function get_Is_Editor(): int
    {
        return $this->isEditor;
    }

    public function set_Is_Editor(int $isEditor): void
    {
        $this->isEditor = $isEditor;
    }

    public static function add_Share($noteId, $userId, $isEditor) {
        return self::execute('INSERT INTO note_shares (note, user, editor) VALUES (:note_id, :user_id, :is_editor)', [
            'note_id' => $noteId,
            'user_id' => $userId,
            'is_editor' => $isEditor ? 1 : 0
        ]);
    }

    public static function remove_Share($noteId, $userId) {
        return self::execute('DELETE FROM note_shares WHERE note = :note_id AND user = :user_id', [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
    }

    public static function change_Permissions($noteId, $userId) {
        $currentStatusQuery = self::execute("SELECT editor FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
        $currentStatusResult = $currentStatusQuery->fetch();

        if (!$currentStatusResult) return false;

        $newEditorStatus = $currentStatusResult['editor'] == 1 ? 0 : 1;

        return self::execute('UPDATE note_shares SET editor = :new_editor WHERE note = :note_id AND user = :user_id', [
            'note_id' => $noteId,
            'user_id' => $userId,
            'new_editor' => $newEditorStatus
        ]);
    }

    public static function get_Shared_By_User($userId) {
        $query = self::execute("SELECT * FROM note_shares WHERE user = :user_id", ['user_id' => $userId]);
        return $query->fetchAll();
    }

    public static function get_Shared_With_User($userId, $noteId) {
        $query = self::execute("SELECT * FROM note_shares ns JOIN notes n ON ns.note = n.id JOIN users u ON ns.user = u.id  WHERE n.owner = :user_id AND ns.note = :note_id ORDER BY full_name", ['user_id' => $userId,'note_id'=>$noteId]);
        return $query->fetchAll();
    }

    public static function is_Note_Shared_With_User($noteId, $userId) {
        $query = self::execute("SELECT COUNT(*) FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
        $count = (int)$query->fetchColumn();
        return $count > 0;
    }
    public static function can_Edit($noteId, $userId) {
        $query = self::execute("SELECT editor FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
        $result = $query->fetch();
        return $result ? $result['editor'] == 1 : false;
    }

}