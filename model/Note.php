<?php

require_once "model/MyModel.php";
require_once "model/User.php";
require_once "model/Note.php";


class Note extends MyModel
{
    public function __construct(private string $title, private User $owner, private  bool $pinned, private bool $archived, private int $weight, private ?int $id = NULL, private ?string $created_at = NULL, private ?string $edited_at = NULL)
    {
    }
    public function get_Owner(): User
    {
        return $this->owner;
    }
    public function set_Owner(User $owner): void
    {
        $this->owner = $owner;
    }
    public function get_Edited_At(): ?string
    {
        return $this->edited_at;
    }

    public function set_Edited_At(?string $edited_at): void
    {
        $this->edited_at = $edited_at;
    }
    public function get_Created_At(): ?string
    {
        return $this->created_at;
    }

    public function set_Created_At(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function is_Archived(): bool
    {
        return $this->archived;
    }

    public function set_Archived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function is_Pinned(): bool
    {
        return $this->pinned;
    }

    public function set_Pinned(bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    public function get_Title(): string
    {
        return $this->title;
    }

    public function set_Title(string $title): void
    {
        $this->title = $title;
    }

    public function get_Id(): ?int
    {
        return $this->id;
    }

    public function get_Weight(): int
    {
        return $this->weight;
    }

    public function set_Weight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function get_nearest_note(bool $is_more): Note | false
    {
        $operator = $is_more ? '>' : '<';

        $query = self::execute("
            SELECT n.* FROM notes n
            INNER JOIN users u ON u.id = n.owner
            WHERE u.id = :owner AND weight $operator :weight
            AND pinned = :pinned AND archived = false
            ORDER BY ABS(weight - :weight)
            LIMIT 1
            ", ["owner" => $this->owner->get_Id(), "weight" => $this->weight, "pinned" => $this->pinned]);

        $row = $query->fetch();

        if (!$row) {
            return false;
        }

        $owner = User::get_user_by_id($row['owner']);

        return new Note($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
    }

    public function validate(): array
    {
        $errors = [];
        $user = User::get_user_by_id($this->owner->get_Id());
        // TO DO: check si l'id de l'user correspond à l'id de l'user connnecter. 

        // if ($user->id === ) {
        //     $errors[] = "Incorrect owner";
        // }
        $config = parse_ini_file('C:\PRWB2324\projects\prwb_2324_a04\config\dev.ini',true);
        $note_title_min_length = $config['Rules']['note_title_min_length'];
        $note_title_max_length = $config['Rules']['note_title_max_length'];

        if (strlen($this->get_Title()) < $note_title_min_length|| strlen($this->get_Title()) > $note_title_max_length) {
            $errors['title'] = "Title length must be between 3 and 25 ";
        }
        if (!($this->weight > 0 && !$this->is_not_unique_weight())) {
            $errors['weight'] = "Weight must be positive and unique";
        }
        return $errors;
    }

    public function is_not_unique_weight(): bool
    {
        $notesByOwner = $this->owner->get_notes();
        $isNotUnique = false;
        $i = 0;
        $notes = $notesByOwner["pinned"];

        if ($this->pinned == 0) {
            $notes = $notesByOwner["other"];
        }

        while (!$isNotUnique && $i < count($notes)) {
            $note = $notes[$i];
            if ($note->weight == $this->weight && $note->id != $this->id) {
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
            $owner = User::get_user_by_id($row['owner']);
            return new Note($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
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
    public function delete_All(User $initiator): Note|false
    {
        if ($this->owner == $initiator) {
            self::execute('DELETE FROM note_shares WHERE note = :note_id', ['note_id' => $this->id]);

            if (self::is_checklist_note($this->id)) {
                self::execute('DELETE FROM checklist_note_items WHERE checklist_note = :note_id', ['note_id' => $this->id]);
                self::execute('DELETE FROM checklist_notes WHERE id = :note_id', ['note_id' => $this->id]);
            } else {
                self::execute('DELETE FROM text_notes WHERE id = :note_id', ['note_id' => $this->id]);
            }

            self::execute('DELETE FROM notes WHERE id = :note_id', ['note_id' => $this->id]);
            return $this;
        }
        return false;
    }
    public function persist(?Note $second_note = NULL): Note|array
    {
        $errors = $this->validate();
        if (empty($errors)) {
            if ($this->id == NULL) {
                return self::add_note_in_DB();
            } else {
                if ($second_note == NULL) {
                    return self::modify_note_in_DB();
                } else {
                    return $this->move_note_in_DB($second_note);
                }
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
                'owner' => $this->owner->get_Id(),
                'pinned' => $this->pinned ? 1 : 0,
                'archived' => $this->archived ? 1 : 0,
                'weight' => $this->weight
            ]
        );

        $note = self::get_note(self::lastInsertId());
        $this->id = $note->get_Id();
        $this->created_at = $note->get_Created_At();
        $this->edited_at = $note->get_Edited_At();
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
    protected function modify_head_in_DB(): Note
    {
        self::execute('UPDATE notes SET title = :title, edited_at = :edited_at, pinned = :pinned, archived = :archived, weight = :weight WHERE id = :id', [
            'title' => $this->title,
            'edited_at' => $this->edited_at,
            'pinned' => $this->pinned ? 1 : 0,
            'archived' => $this->archived ? 1 : 0,
            'weight' => $this->weight,
            'id' => $this->id
        ]);

        return $this;
    }
    protected function move_note_in_DB(Note $second_note): Note
    {
        $second_weight = $second_note->get_Weight();
        $second_id = $second_note->get_Id();
        self::execute('UPDATE notes n1, notes n2 SET n1.weight = :second_weight, n2.weight= :weight WHERE n1.id= :id AND n2.id=:second_id', [
            'weight' => $this->weight,
            'second_weight' => $second_weight,
            'id' => $this->id,
            'second_id' => $second_id,
        ]);
        return $this;
    }
    public static function is_checklist_note(int $id): bool
    {
        $query = self::execute("SELECT id FROM checklist_notes WHERE id = :id", ["id" => $id]);
        return $query->rowCount() > 0;
    }
    public function toggle_Pin(): static
    {
        $this->pinned = !$this->pinned;
        return $this->modify_head_in_DB();
    }
    public function set_Archive_reverse(): static
    {
        $this->archived = !$this->archived;
        return $this->modify_head_in_DB();
    }

    public static function time_elapsed_string($datetime, $full = false): string
    {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function get_last_insert_id(): int
    {
        return Model::lastInsertId();
    }
}
