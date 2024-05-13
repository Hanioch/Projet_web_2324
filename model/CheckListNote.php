<?php

require_once "model/Note.php";

class ChecklistNote extends Note
{
    public function __construct(private string $title, private User $owner, private  bool $pinned, private bool $archived, private int $weight, private ?int $id = NULL, private ?string $created_at = NULL, private ?string $edited_at = NULL, private ?array $list_item = NULL)
    {
        parent::__construct($title, $owner, $pinned, $archived, $weight, $id, $created_at, $edited_at);
        $this->fetch_list_item();
    }

    public function get_Title(): string
    {
        return $this->title;
    }

    public function set_Title(string $title): void
    {
        $this->title = $title;
    }

    public function get_Owner(): User
    {
        return $this->owner;
    }

    public function set_Owner(User $owner): void
    {
        $this->owner = $owner;
    }


    public function is_Archived(): bool
    {
        return $this->archived;
    }

    public function set_Archived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function get_Weight(): int
    {
        return $this->weight;
    }

    public function set_Weight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function get_Created_At(): ?string
    {
        return $this->created_at;
    }

    public function set_Created_At(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function get_Edited_At(): ?string
    {
        return $this->edited_at;
    }

    public function set_Edited_At(?string $edited_at): void
    {
        $this->edited_at = $edited_at;
    }


    public function fetch_list_item()
    {
        $query = self::execute("SELECT cni.*, n.title, n.owner, n.pinned, n.archived, n.weight, n.created_at, n.edited_at FROM checklist_note_items cni JOIN notes n ON n.id = cni.checklist_note WHERE checklist_note = :checklist_note ORDER BY cni.checked ASC, cni.id ASC", ["checklist_note" => $this->id]);
        $data = $query->fetchAll();

        $items = [];
        foreach ($data as $row) {
            $items[] = new ChecklistNoteItems($row['content'], $row['checked'], $row['checklist_note'], $row['id']);
        }

        $this->set_list_item($items);
    }

    public static function is_checklist_note(int | null $id_to_check): bool
    {
        if ($id_to_check == NULL) {
            return false;
        } else {
            return true;
        }
    }

    public function set_list_item(array $list)
    {
        $this->list_item = $list;
    }
    public function get_List_Item()
    {
        return $this->list_item;
    }

    public function delete(User $initiator): Note |false
    {
        if ($this->owner === $initiator) {
            self::execute("DELETE FROM checklist_notes WHERE id = :id", ["id" => $this->id]);
            parent::delete($initiator);
            return $this;
        }
        return false;
    }

    public static function get_by_id($id): ChecklistNote | false
    {
        $query = self::execute("SELECT n.*, cn.* FROM notes n JOIN checklist_notes cn ON n.id = cn.id WHERE n.id = :id", ["id" => $id]);
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
                $note = parent::add_note_in_DB();
                self::execute(
                    'INSERT INTO checklist_notes (id) VALUES
                (:id)',
                    ['id' => $note->get_Id()]
                );
                return $this;
            } else {
                parent::modify_note_in_DB();
                return $this;
            }
        }

        return $errors;
    }

    public function get_Items(): array
    {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :checklist_note ORDER BY id", ["checklist_note" => $this->id]);
        $data = $query->fetchAll();

        $items = [];
        foreach ($data as $row) {
            $items[] = new ChecklistNoteItems($row['content'], $row['checked'], $row['checklist_note'], $row['id']);
        }

        return $items;
    }
}
