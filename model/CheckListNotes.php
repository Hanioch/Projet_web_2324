<?php

require_once "Model.php";

class ChecklistNote extends MyModel {
    public function __construct(public int $id) {}

    public static function create(array $data) : ChecklistNote {
     
        $id = self::execute("INSERT INTO checklist_notes () VALUES ()", [])->lastInsertId();
        return new self($id);
    }

    public function getItems() : array {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :checklist_note", ["checklist_note" => $this->id]);
        $data = $query->fetchAll();

        $items = [];
        foreach ($data as $row) {
            $items[] = new ChecklistNoteItem($row["id"], $row["checklist_note"], $row["content"], $row["checked"]);
        }

        return $items;
    }

    public function delete() : void {
        self::execute("DELETE FROM checklist_notes WHERE id = :id", ["id" => $this->id]);
    }

    public function update(array $data) : void {
        self::execute("UPDATE checklist_notes SET ... WHERE id = :id", ["id" => $this->id] + $data);
    }

}