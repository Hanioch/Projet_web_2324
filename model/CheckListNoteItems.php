<?php

require_once "Model.php";

class ChecklistNoteItems extends MyModel
{
    public function __construct(private string $content = "", private bool $checked = false, private ?int $id = NULL, private ?int $checklist_note = NULL)
    {
    }

    public function delete(User $initiator): ChecklistNoteItems| false
    {
        //On doit faire un get du checklist_note lié pour verifier l'owner dans un premier temps   et le supprimer aussi.
        if ($this->owner == $initiator) {
            self::execute("DELETE FROM checklist_note_items WHERE id = :id", ["id" => $this->id]);
            //parent::delete($initiator);
            return $this;
        }
        return false;
    }

    public function persist(): ChecklistNoteItems|array
    {
        $errors = $this->validate();
        if (empty($errors)) {
            if ($this->id == NULL) {
                self::execute(
                    'INSERT INTO checklist_note_items (id,checklist_note, content, checked) VALUES
                 (:id,:checklist_note,:content,:checked)',
                    [
                        'id' => $this->id,
                        'checklist_note' => $this->checklist_note,
                        'content' => $this->content,
                        'checked' => $this->checked
                    ]
                );
                return $this;
            } else {
                self::execute('UPDATE hecklist_note_items SET  content = :content, checked = :checked WHERE id = :id', [
                    'content' => $this->content,
                    'checked' => $this->checked,
                    'id' => $this->id
                ]);
                return $this;
            }
        } else {
            return $errors;
        }
    }

    public function validate(): array
    {
        $errors = [];
        $errors = array_merge($errors, $this->validateNoteReference($this->checklist_note));
        $errors = array_merge($errors, $this->validateContent($this->content, $this->checklist_note));
        $errors = array_merge($errors, $this->validateChecked($this->checked));

        return $errors;
    }

    private function validateNoteReference(int $noteId): array
    {
        $errors = [];
        $note = ChecklistNote::getNoteById($noteId);

        if (!$note) {
            $errors[] = "La note n'existe pas.";
        }

        return $errors;
    }

    private function validateContent(string $content, int $noteId): array
    {
        $errors = [];

        if (strlen($content) < 1 || strlen($content) > 60) {
            $errors[] = "Le contenu doit avoir entre 1 et 60 caractères.";
        }

        if ($this->isContentDuplicate($content, $noteId)) {
            $errors[] = "Le contenu doit être unique au sein de la note.";
        }

        return $errors;
    }

    private function validateChecked(bool $checked): array
    {
        $errors = [];

        if (!in_array($checked, [true, false], true)) {
            $errors[] = "La valeur de 'checked' doit être true ou false.";
        }

        return $errors;
    }

    private function isContentDuplicate(string $content, int $noteId): bool
    {
        $query = self::execute("SELECT COUNT(*) FROM checklist_note_items WHERE checklist_note = :noteId AND content = :content", ["noteId" => $noteId, "content" => $content]);
        $count = (int)$query->fetchColumn();
        return $count > 0;
    }
}
