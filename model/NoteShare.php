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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNoteId(): int
    {
        return $this->noteId;
    }

    public function setNoteId(int $noteId): void
    {
        $this->noteId = $noteId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getIsEditor(): int
    {
        return $this->isEditor;
    }

    public function setIsEditor(int $isEditor): void
    {
        $this->isEditor = $isEditor;
    }

    public static function addShare($noteId, $userId, $isEditor) {
        return self::execute('INSERT INTO note_shares (note, user, editor) VALUES (:note_id, :user_id, :is_editor)', [
            'note_id' => $noteId,
            'user_id' => $userId,
            'is_editor' => $isEditor ? 1 : 0
        ]);
    }

    public static function removeShare($noteId, $userId) {
        return self::execute('DELETE FROM note_shares WHERE note = :note_id AND user = :user_id', [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
    }

    public static function changePermissions($noteId, $userId, $isEditor) {
        return self::execute('UPDATE note_shares SET editor = :is_editor WHERE note = :note_id AND user = :user_id', [
            'note_id' => $noteId,
            'user_id' => $userId,
            'is_editor' => $isEditor ? 1 : 0
        ]);
    }

    public static function getSharedByUser($userId) {
        $query = self::execute("SELECT * FROM note_shares WHERE user = :user_id", ['user_id' => $userId]);
        return $query->fetchAll();
    }

    public static function getSharedWithUser($userId, $noteId) {
        $query = self::execute("SELECT * FROM note_shares ns JOIN notes n ON ns.note = n.id JOIN users u ON ns.user = u.id  WHERE n.owner = :user_id AND ns.note = :note_id", ['user_id' => $userId,'note_id'=>$noteId]);
        return $query->fetchAll();
    }

    public static function isNoteSharedWithUser($noteId, $userId) {
        $query = self::execute("SELECT COUNT(*) FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
        $count = (int)$query->fetchColumn();
        return $count > 0;
    }
    public static function canEdit($noteId, $userId) {
        $query = self::execute("SELECT editor FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $noteId,
            'user_id' => $userId
        ]);
        $result = $query->fetch();
        return $result ? $result['editor'] == 1 : false;
    }

}