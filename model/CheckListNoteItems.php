<?php

require_once "model/MyModel.php";

class ChecklistNoteItems extends MyModel
{
    public function __construct(private string $content = "", private bool $checked = false, private ?int $checklist_note = NULL, private ?int $id = NULL)
    {
    }

    public function get_content(): string
    {
        return $this->content;
    }

    public function set_Content(string $content): void
    {
        $this->content = $content;
    }

    public function is_Checked(): bool
    {
        return $this->checked;
    }

    public function set_Checked(bool $checked): void
    {
        $this->checked = $checked;
    }

    public function get_Id(): ?int
    {
        return $this->id;
    }

    public function set_Id(?int $id): void
    {
        $this->id = $id;
    }

    public function get_ChecklistNote(): ?int
    {
        return $this->checklist_note;
    }

    public function set_ChecklistNote(?int $checklist_note): void
    {
        $this->checklist_note = $checklist_note;
    }

    public function delete(): ChecklistNoteItems|false
    {
        try {
            self::execute("DELETE FROM checklist_note_items WHERE id = :id", ["id" => $this->id]);
            return $this;
        } catch (\Throwable $th) {
            return false;
        }
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
        $config = parse_ini_file('C:\PRWB2324\projects\prwb_2324_a04\config\dev.ini',true);
        $item_min_length = $config['Rules']['item_min_length'];
        $item_max_length = $config['Rules']['item_max_length'];

        if (strlen($content) > 0 && (strlen($content) < $item_min_length || strlen($content) > $item_max_length)) {
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
            "SELECT cni.*, n.title, n.owner, n.pinned, n.archived, n.weight, n.created_at, n.edited_at FROM checklist_note_items cni JOIN notes n ON n.id = cni.checklist_note WHERE checklist_note = :checklist_note ORDER BY cni.checked ASC, cni.id ASC",
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

    public function toggle_Checkbox(): ChecklistNoteItems
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
