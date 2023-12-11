<?php

require_once "Model.php";

class ChecklistNoteItem extends MyModel {

    private int $id;
    private int $checklist_note;
    private string $content;
    private bool $checked;

    public function __construct(int $id, int $checklist_note, string $content, bool $checked) {
        $this->id = $id;
        $this->checklist_note = $checklist_note;
        $this->content = $content;
        $this->checked = $checked;
    }

    public static function create(int $checklist_note, string $content) : ChecklistNoteItem {
        $id = self::execute("INSERT INTO checklist_note_items (checklist_note, content) VALUES (:checklist_note, :content)", ["checklist_note" => $checklist_note, "content" => $content])->lastInsertId();
        return new self($id, $checklist_note, $content, false); 
    }

    public function delete() : void {
        self::execute("DELETE FROM checklist_note_items WHERE id = :id", ["id" => $this->id]);
    }

    public function update(array $data) : void {
        $updateColumns = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $sql = "UPDATE checklist_note_items SET $updateColumns WHERE id = :id";
        self::execute($sql, ["id" => $this->id] + $data);
    }

    public function check() : void {
        self::execute("UPDATE checklist_note_items SET checked = true WHERE id = :id", ["id" => $this->id]);
    }

    public function uncheck() : void {
        self::execute("UPDATE checklist_note_items SET checked = false WHERE id = :id", ["id" => $this->id]);
    }

    public function validate() : array {
        $errors = [];
        $errors = array_merge($errors, $this->validateNoteReference($this->checklist_note));
        $errors = array_merge($errors, $this->validateContent($this->content, $this->checklist_note));
        $errors = array_merge($errors, $this->validateChecked($this->checked));

        return $errors;
    }

    private function validateNoteReference(int $noteId) : array {
        $errors = [];
        $note = ChecklistNote::getNoteById($noteId);

        if (!$note) {
            $errors[] = "La note n'existe pas.";
        }

        return $errors;
    }

    private function validateContent(string $content, int $noteId) : array {
        $errors = [];

        if (strlen($content) < 1 || strlen($content) > 60) {
            $errors[] = "Le contenu doit avoir entre 1 et 60 caractères.";
        }

        if ($this->isContentDuplicate($content, $noteId)) {
            $errors[] = "Le contenu doit être unique au sein de la note.";
        }

        return $errors;
    }

    private function validateChecked(bool $checked) : array {
        $errors = [];

        if (!in_array($checked, [true, false], true)) {
            $errors[] = "La valeur de 'checked' doit être true ou false.";
        }

        return $errors;
    }

    private function isContentDuplicate(string $content, int $noteId) : bool {
        $query = self::execute("SELECT COUNT(*) FROM checklist_note_items WHERE checklist_note = :noteId AND content = :content", ["noteId" => $noteId, "content" => $content]);
        $count = (int)$query->fetchColumn();
        return $count > 0;
    }
}
