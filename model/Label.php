<?php

require_once "model/MyModel.php";
require_once "model/User.php";
require_once "model/Note.php";

class Label extends MyModel {
    public function __construct(private int $noteId, private string $labelName, private ?int $id = NULL) {}

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
        return $this->noteId;
    }
    public function get_label_name(): string
    {
        return $this->labelName;
    }
    public static function add_label($noteId, $labelName) {
        return self::execute('INSERT INTO note_labels (note, label) VALUES (:note_id, :label_name)', [
            'note_id' => $noteId,
            'label_name' => $labelName,
        ]);
    }
    public static function remove_label($noteId, $labelName) {
        return self::execute('DELETE FROM note_labels WHERE note = :note_id AND label = :label_name', [
            'note_id' => $noteId,
            'label_name' => $labelName
        ]);
    }

    public static function get_labels_by_note_id($noteId) {
        $query = self::execute("SELECT * FROM note_labels WHERE note = :note_id", ['note_id' => $noteId]);
        $data = $query->fetchAll();
        $labels = [];
        foreach ($data as $row) {
            $labels[] = new Label($row['note'], $row['label']);
        }
        return $labels;
    }
}