<?php

require_once "Model.php";

class ChecklistNote extends Model {
    private int $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function validate() : array {
        $errors = [];
        $errors = array_merge($errors, $this->validateNoteReference($this->id));

        return $errors;
    }

    public function persist() : ChecklistNote {
        $errors = $this->validate();

        if (empty($errors)) {
            if ($this->id) {
                // Mise à jour de la note existante
                self::execute("UPDATE checklist_notes SET title=:title, owner=:owner, edited_at=:edited_at, pinned=:pinned, archived=:archived, weight=:weight WHERE id=:id",
                    ["title" => $this->title, "owner" => $this->owner, "edited_at" => $this->edited_at, "pinned" => $this->pinned, "archived" => $this->archived, "weight" => $this->weight, "id" => $this->id]);
            } else {
                // Insertion d'une nouvelle note
                $this->id = self::lastInsertId();
                self::execute("INSERT INTO checklist_notes (id, title, owner, created_at, edited_at, pinned, archived, weight) VALUES (:id, :title, :owner, :created_at, :edited_at, :pinned, :archived, :weight)",
                    ["id" => $this->id, "title" => $this->title, "owner" => $this->owner, "created_at" => $this->created_at, "edited_at" => $this->edited_at, "pinned" => $this->pinned, "archived" => $this->archived, "weight" => $this->weight]);
            }
        }

        return $this;
    }

    public function delete() : void {
        self::execute("DELETE FROM checklist_notes WHERE id = :id", ["id" => $this->id]);
    }

    public function getChecklistNoteItems() : array {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :noteId", ["noteId" => $this->id]);
        $data = $query->fetchAll();
    
        $checklistNoteItems = [];
        foreach ($data as $row) {
            $checklistNoteItems[] = new ChecklistNoteItem($row["id"], $row["checklist_note"], $row["content"], $row["checked"]);
        }
    
        return $checklistNoteItems;
    }

    private function validateNoteReference(int $noteId) : array {
        $errors = [];
        $note = self::getNoteById($noteId);

        if (!$note) {
            $errors[] = "La note n'existe pas.";
        }

        return $errors;
    }

    private static function getNoteById(int $noteId) : ?array {
        $query = self::execute("SELECT * FROM checklist_notes WHERE id = :id", ["id" => $noteId]);
        return $query->fetch();
    }

}