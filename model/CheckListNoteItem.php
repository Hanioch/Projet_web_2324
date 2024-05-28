<?php

require_once "model/MyModel.php";

class ChecklistNoteItem extends MyModel implements JsonSerializable
{
    public function __construct(private string $content = "", private bool $checked = false, private ?int $checklist_note = NULL, private ?int $id = NULL)
    {
    }

    public function get_content(): string
    {
        return $this->content;
    }

    public function set_content(string $content): void
    {
        $this->content = $content;
    }

    public function is_checked(): bool
    {
        return $this->checked;
    }

    public function set_checked(bool $checked): void
    {
        $this->checked = $checked;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_id(?int $id): void
    {
        $this->id = $id;
    }

    public function get_checklist_note(): ?int
    {
        return $this->checklist_note;
    }

    public function set_checklist_note(?int $checklist_note): void
    {
        $this->checklist_note = $checklist_note;
    }

    public function delete(): ChecklistNoteItem|false
    {
        try {
            self::execute("DELETE FROM checklist_note_items WHERE id = :id", ["id" => $this->id]);
            return $this;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function persist(): ChecklistNoteItem|array
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
                    'checked' => $this->checked ? 1 : 0,
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
        $errors = array_merge($errors, $this->validate_content($this->content, $this->checklist_note));
        $errors = array_merge($errors, $this->validate_checked($this->checked));

        return $errors;
    }

    private function validate_note_reference(int $note_id): array
    {
        $errors = [];
        $note = ChecklistNote::get_by_id($note_id);

        if (!$note) {
            $errors[] = "Note does not exist.";
        }

        return $errors;
    }

    private function validate_content(string $content): array
    {
        $errors = [];

        $item_min_length = Configuration::get("item_min_length");
        $item_max_length = Configuration::get("item_max_length");

        if (mb_strlen($content) > 0 && (mb_strlen($content) < $item_min_length || mb_strlen($content) > $item_max_length)) {
            $errors[] = "Item must be between {$item_min_length} and {$item_max_length} characters long.";
        }

        return $errors;
    }

    private function validate_checked(bool $checked): array
    {
        $errors = [];

        if (!in_array($checked, [true, false], true)) {
            $errors[] = "'Checked' value must be true or false.";
        }

        return $errors;
    }

    public static function get_checklist_note_item_by_id(int $id): ChecklistNoteItem |false
    {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE id = :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new ChecklistNoteItem($row['content'], $row['checked'], $row['checklist_note'], $row['id']);
        }
    }
    protected function modify_item_in_DB(): ChecklistNoteItem
    {
        self::execute('UPDATE checklist_note_items SET content = :content, checked = :checked, checklist_note = :checklist_note WHERE id = :id', [
            'content' => $this->content,
            'checked' => $this->checked ? 1 : 0,
            'checklist_note' => $this->checklist_note,
            'id' => $this->id
        ]);

        return $this;
    }

    public function toggle_checkbox(): ChecklistNoteItem
    {
        $this->checked = !$this->checked;
        $this->modify_item_in_DB();
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}
