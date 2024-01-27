<?php

require_once "model/MyModel.php";
require_once "model/User.php";
require_once "model/Note.php";


class Note extends MyModel
{
    public function __construct(private string $title, private User $owner, private  bool $pinned, private bool $archived, private int $weight, private ?int $id = NULL, private ?string $created_at = NULL, private ?string $edited_at = NULL)
    {
    }
    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }
    public function getEditedAt(): ?string
    {
        return $this->edited_at;
    }

    public function setEditedAt(?string $edited_at): void
    {
        $this->edited_at = $edited_at;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function isPinned(): bool
    {
        return $this->pinned;
    }

    public function setPinned(bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
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
            ", ["owner" => $this->owner->getId(), "weight" => $this->weight, "pinned" => $this->pinned]);

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
        $user = User::get_user_by_mail($this->owner->getMail());
        // TO DO: check si l'id de l'user correspond à l'id de l'user connnecter. 

        // if ($user->id === ) {
        //     $errors[] = "Incorrect owner";
        // }

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
                'owner' => $this->owner->getId(),
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

    protected function move_note_in_DB(Note $second_note): Note
    {
        $second_weight = $second_note->getWeight();
        $second_id = $second_note->getId();
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
    public function togglePin(): static
    {
        $this->pinned = !$this->pinned;
        return $this->modify_note_in_DB();
    }
    public function setArchive(): static
    {
        $this->archived = !$this->archived;
        return $this->modify_note_in_DB();
    }
    public static function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $diff->d -= $weeks * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($k === 'w') {
                if ($weeks) {
                    $v = $weeks . ' ' . $v . ($weeks > 1 ? 's' : '');
                }
            } elseif ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
