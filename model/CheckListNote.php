<?php

require_once "Model.php";

class ChecklistNote extends Note
{
    public function __construct(protected string $title, public User $owner, protected  bool $pinned, protected bool $archived, protected int $weight, public ?int $id = NULL, protected ?string $created_at = NULL, protected ?string $edited_at = NULL, protected ?array $list_item = NULL)
    {
        parent::__construct($title, $owner, $pinned, $archived, $weight, $id, $created_at, $edited_at);
        $this->fetch_list_item();
    }


    public function fetch_list_item()
    {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :checklist_note", ["checklist_note" => $this->id]);
        $data = $query->fetchAll();

        $items = [];
        foreach ($data as $row) {
            $items[] = new ChecklistNoteItems($row('content'), $row('checked', $row('id'), $row('checklist_note')));
        }

        $this->set_list_item($items);
    }

    public function set_list_item(array $list)
    {
        $this->list_item = $list;
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

    public static function get_by_id($id): ChecklistNote | false
    {
        $query = self::execute("SELECT * FROM checklist_notes WHERE id = :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            $owner = User::get_user_by_id($row['owner']);
            return new ChecklistNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
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
                    ['id' => self::lastInsertId(),]
                );
                return $this;
            }
        } else {
            return $errors;
        }
    }
}
