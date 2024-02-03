<?php

require_once "model/MyModel.php";

class ChecklistNoteItems extends MyModel
{
    public function __construct(private string $content = "", private bool $checked = false, private ?int $checklist_note = NULL, private ?int $id = NULL)
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): void
    {
        $this->checked = $checked;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getChecklistNote(): ?int
    {
        return $this->checklist_note;
    }

    public function setChecklistNote(?int $checklist_note): void
    {
        $this->checklist_note = $checklist_note;
    }

    public function delete(User $initiator): ChecklistNoteItems|false
    {
        $checklistNote = ChecklistNote::get_by_id($this->checklist_note);
        if ($checklistNote->getOwner()->getId() == $initiator->getId()) {
            self::execute("DELETE FROM checklist_note_items WHERE id = :id", ["id" => $this->id]);
            $checklistNote->delete($initiator);
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
                    'INSERT INTO checklist_note_items (checklist_note, content, checked) VALUES
                 (:checklist_note,:content,:checked)',
                    [
                        'checklist_note' => $this->checklist_note,
                        'content' => $this->content,
                        'checked' => $this->checked ? 1 : 0
                    ]
                );
                return $this;
            } else {
                self::execute('UPDATE checklist_note_items SET  content = :content, checked = :checked WHERE id = :id', [
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
        //        $errors = array_merge($errors, $this->validate_note_reference($this->checklist_note));
        $errors = array_merge($errors, $this->validate_content($this->content, $this->checklist_note));
        $errors = array_merge($errors, $this->validate_checked($this->checked));

        return $errors;
    }

    private function validate_note_reference(int $noteId): array
    {
        $errors = [];
        $note = ChecklistNote::get_by_id($noteId);

        if (!$note) {
            $errors[] = "La note n'existe pas.";
        }

        return $errors;
    }

    private function validate_content(string $content): array
    {
        $errors = [];

        if (strlen($content) > 0 && (strlen($content) < 1 || strlen($content) > 60)) {
            $errors[] = "Le contenu doit avoir entre 1 et 60 caractères.";
        }

        return $errors;
    }

    private function validate_checked(bool $checked): array
    {
        $errors = [];

        if (!in_array($checked, [true, false], true)) {
            $errors[] = "La valeur de 'checked' doit être true ou false.";
        }

        return $errors;
    }

    public static function get_checklist_note_item_by_id(int $id): ChecklistNoteItems |false
    {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE id = :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new ChecklistNoteItems($row['content'], $row['checked'], $row['checklist_note'], $row['id']);
        }
    }
    public static function get_items_by_checklist_note_id(int $checklistNoteId): array
    {
        $query = self::execute(
            "SELECT cni.*, n.title, n.owner, n.pinned, n.archived, n.weight, n.created_at, n.edited_at FROM checklist_note_items cni JOIN notes n ON n.id = cni.checklist_note WHERE checklist_note = :checklist_note ORDER BY cni.checked ASC, n.created_at ASC",
            ["checklist_note" => $checklistNoteId]
        );
        $data = $query->fetchAll();
        $items = [];
        foreach ($data as $row) {
            $items[] = new ChecklistNoteItems($row['content'], $row['checked'], $row['checklist_note'], $row['id']);
        }
        return $items;
    }
    protected function modify_item_in_DB(): ChecklistNoteItems
    {
        self::execute('UPDATE checklist_note_items SET content = :content, checked = :checked, checklist_note = :checklist_note WHERE id = :id', [
            'content' => $this->content,
            'checked' => $this->checked ? 1 : 0,
            'checklist_note' => $this->checklist_note,
            'id' => $this->id
        ]);

        return $this;
    }

    public function toggleCheckbox(): ChecklistNoteItems
    {
        $this->checked = !$this->checked;
        $this->modify_item_in_DB();
        return $this;
    }


    public function set_checklist_note(int $id): ChecklistNoteItems
    {
        $this->checklist_note = $id;
        return $this;
    }
}
