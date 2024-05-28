<?php

require_once "model/MyModel.php";
require_once "model/CheckListNote.php";
require_once "model/CheckListNoteItem.php";
require_once "model/TextNote.php";
require_once "model/User.php";
require_once "model/Note.php";
require_once "model/NoteShare.php";
class NoteShare extends MyModel implements JsonSerializable {


    public function __construct(private int $id, private int $note_id, private int $user_id, private int $is_editor) {}

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    public function get_note_id(): int
    {
        return $this->note_id;
    }

    public function set_note_id(int $note_id): void
    {
        $this->note_id = $note_id;
    }

    public function get_user_id(): int
    {
        return $this->user_id;
    }

    public function set_user_id(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function get_is_editor(): int
    {
        return $this->is_editor;
    }

    public function set_is_editor(int $is_editor): void
    {
        $this->is_editor = $is_editor;
    }

    public static function add_share($note_id, $user_id, $is_editor) {
        return self::execute('INSERT INTO note_shares (note, user, editor) VALUES (:note_id, :user_id, :is_editor)', [
            'note_id' => $note_id,
            'user_id' => $user_id,
            'is_editor' => $is_editor ? 1 : 0
        ]);
    }

    public static function remove_share($note_id, $user_id) {
        return self::execute('DELETE FROM note_shares WHERE note = :note_id AND user = :user_id', [
            'note_id' => $note_id,
            'user_id' => $user_id
        ]);
    }

    public static function change_permissions($note_id, $user_id) {
        $current_status_query = self::execute("SELECT editor FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $note_id,
            'user_id' => $user_id
        ]);
        $current_status_result = $current_status_query->fetch();

        if (!$current_status_result) return false;

        $new_editor_status = $current_status_result['editor'] == 1 ? 0 : 1;

        return self::execute('UPDATE note_shares SET editor = :new_editor WHERE note = :note_id AND user = :user_id', [
            'note_id' => $note_id,
            'user_id' => $user_id,
            'new_editor' => $new_editor_status
        ]);
    }

    public static function get_shared_by_user($user_id) {
        $query = self::execute("SELECT * FROM note_shares WHERE user = :user_id", ['user_id' => $user_id]);
        return $query->fetchAll();
    }

    public static function get_shared_with_user($user_id, $note_id) {
        $query = self::execute("SELECT * FROM note_shares ns JOIN notes n ON ns.note = n.id JOIN users u ON ns.user = u.id  WHERE n.owner = :user_id AND ns.note = :note_id ORDER BY full_name", ['user_id' => $user_id,'note_id'=>$note_id]);
        return $query->fetchAll();
    }

    public static function is_note_shared_with_user($note_id, $user_id) {
        $query = self::execute("SELECT COUNT(*) FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $note_id,
            'user_id' => $user_id
        ]);
        $count = (int)$query->fetchColumn();
        return $count > 0;
    }
    public static function can_edit($note_id, $user_id) {
        $query = self::execute("SELECT editor FROM note_shares WHERE note = :note_id AND user = :user_id", [
            'note_id' => $note_id,
            'user_id' => $user_id
        ]);
        $result = $query->fetch();
        return $result ? $result['editor'] == 1 : false;
    }
    public function jsonSerialize(): mixed
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}