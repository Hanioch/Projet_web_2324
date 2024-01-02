<?php


class Note extends MyModel
{

    public function __construct(protected string $title, protected User $owner, protected  bool $pinned, protected bool $archived, protected int $weight, public ?int $id = NULL, protected ?string $created_at = NULL, protected ?string $edited_at = NULL)
    {
    }

    public function validate(): array
    {
        $errors = [];

        if (User::get_user_by_mail($this->owner->mail)) {
            $errors[] = "Incorrect owner";
        }
        if (!(strlen($this->title) > 3 && strlen($this->title) < 25)) {
            $errors[] = "Title must be filled";
        }
        if (!($this->weight > 0 && !$this->is_not_unique_weight())) {
            $errors[] = "Weight must be positif and unique";
        }

        return $errors;
    }

    public function is_not_unique_weight(): bool
    {
        $notesByOwner = $this->owner->get_notes();
        $isNotUnique = false;
        $i = 0;

        while (!$isNotUnique && $i < count($notesByOwner)) {
            $note = $notesByOwner[$i];
            if ($note->weight == $this->weight) {
                $isNotUnique = true;
            }
            $i++;
        }
        return $isNotUnique;
    }



    public static function get_note(int $id): Note| false
    {
        $query = self::execute("select * from notes where id= :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            // $owner = new User();
            return new Note($row['title'], $row['owner'], $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
        }
    }

    public function delete(User $initiator): Note|false
    {
        if ($this->owner == $initiator) {
            self::execute('DELETE FROM notes WHERE id = :id', ['id' => $this->id]);
            return $this;
        }
        return false;
    }

    public function persist(): Note|array
    {
        $errors = $this->validate();
        if (empty($errors)) {
            if ($this->id == NULL) {
                return self::add_note_in_DB();
            } else {
                return self::modify_note_in_DB();
            }
        } else {
            return $errors;
        }
    }

    protected function add_note_in_DB(): Note
    {
        self::execute(
            'INSERT INTO notes (title, owner, created_at, edited_at, pinned, archived, weight) VALUES
         (:title, :owner, NOW(), null, :pinned, :archived, :weight)',
            [
                'title' => $this->title,
                'owner' => $this->owner->id,
                'pinned' => $this->pinned ? 1 : 0,
                'archived' => $this->archived ? 1 : 0,
                'weight' => $this->weight
            ]
        );
        $note = self::get_note(self::lastInsertId());
        $this->id = $note->id;
        $this->created_at = $note->created_at;
        $this->edited_at = $note->edited_at;
        return $this;
    }

    protected function modify_note_in_DB(): Note
    {
        self::execute('UPDATE notes SET title = :title, edited_at = NOW(), pinned = :pinned, archived = :archived, weight = :weight WHERE id = :id', [
            'title' => $this->title,
            'pinned' => $this->pinned ? 1 : 0,
            'archived' => $this->archived ? 1 : 0,
            'weight' => $this->weight,
            'id' => $this->id
        ]);

        return $this;
    }
}
