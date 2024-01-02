<?php

require_once "Model.php";

class ChecklistNote extends Note
{
    public function __construct(protected string $title, protected User $owner, protected  bool $pinned, protected bool $archived, protected int $weight, public ?int $id = NULL, protected ?string $created_at = NULL, protected ?string $edited_at = NULL)
    {
    }


    public function getItems(): array | false
    {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :checklist_note", ["checklist_note" => $this->id]);
        $data = $query->fetchAll();

        $items = [];
        foreach ($data as $row) {
            $items[] = new ChecklistNoteItems($row('content'), $row('checked', $row('id'), $row('checklist_note')));
        }

        return $items;
    }

    public function delete(User $initiator): Note |false
    {
        if ($this->owner == $initiator) {
            self::execute("DELETE FROM checklist_notes WHERE id = :id", ["id" => $this->id]);
            parent::delete($initiator);
            return $this;
        }
        return false;
    }

    public static function getById($id): ChecklistNote | false
    {
        $query = self::execute("SELECT * FROM checklist_notes WHERE id = :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new ChecklistNote($row['title'], $row['owner'], $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
        }
    }

    public function persist(): ChecklistNote|array
    {
        $errors = $this->validate();
        if (empty($errors)) {
            if ($this->id == NULL) {
                parent::add_note_in_DB();
                self::execute(
                    'INSERT INTO checklist_notes (id) VALUES
                (:id)',
                    ['id' => $this->id,]
                );
                return $this;
            }
        } else {
            return $errors;
        }
    }
}
