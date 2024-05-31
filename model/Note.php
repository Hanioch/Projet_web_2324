<?php

require_once "model/MyModel.php";
require_once "model/User.php";
require_once "model/Note.php";


abstract class Note extends MyModel implements JsonSerializable
{
    public function __construct(private string $title, private User $owner, private  bool $pinned, private bool $archived, private int $weight, private ?int $id = NULL, private ?string $created_at = NULL, private ?string $edited_at = NULL)
    {
    }
    public function get_owner(): User
    {
        return $this->owner;
    }
    public function set_owner(User $owner): void
    {
        $this->owner = $owner;
    }
    public function get_edited_at(): ?string
    {
        return $this->edited_at;
    }

    public function set_edited_at(?string $edited_at): void
    {
        $this->edited_at = $edited_at;
    }
    public function get_created_at(): ?string
    {
        return $this->created_at;
    }

    public function set_created_at(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function is_archived(): bool
    {
        return $this->archived;
    }

    public function set_archived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function is_pinned(): bool
    {
        return $this->pinned;
    }

    public function set_pinned(bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function set_title(string $title): void
    {
        $this->title = $title;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    protected function set_id($id): void
    {
        $this->id = $id;
    }

    public function get_weight(): int
    {
        return $this->weight;
    }

    public function set_weight(int $weight): void
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
            ", ["owner" => $this->owner->get_id(), "weight" => $this->weight, "pinned" => $this->pinned]);

        $row = $query->fetch();

        if (!$row) {
            return false;
        }

        $owner = User::get_user_by_id($row['owner']);
        $query = self::execute("select * from text_notes where id = :id", ["id" => $row['id']]);
        if ($query->rowCount() == 0) {
            return new ChecklistNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
        } else {
            return new TextNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], null, $row['id'], $row['created_at'], $row['edited_at']);
        }
    }

    public function get_nearest_archived_note(): Note | false
    {
        $query = self::execute("
            SELECT n.* FROM notes n
            INNER JOIN users u ON u.id = n.owner
            WHERE u.id = :owner AND weight < :weight
            AND archived = true
            ORDER BY ABS(weight - :weight)
            LIMIT 1
            ", ["owner" => $this->owner->get_id(), "weight" => $this->weight]);

        $row = $query->fetch();

        if (!$row) {
            return false;
        }

        $owner = User::get_user_by_id($row['owner']);

        $query = self::execute("select * from text_notes where id = :id", ["id" => $row['id']]);
        var_dump($row['id']);
        if ($query->rowCount() == 0) {

            $note_id = filter_var($row['id'], FILTER_VALIDATE_INT);
            return new ChecklistNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $note_id, $row['created_at'], $row['edited_at']);
        } else {
            $note_id = filter_var($row['id'], FILTER_VALIDATE_INT);
            return new TextNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['content'], $note_id, $row['created_at'], $row['edited_at']);
        }
    }

    public function validate(): array
    {
        $errors = [];
        $user = User::get_user_by_id($this->owner->get_id());

        $note_title_min_length = Configuration::get("note_title_min_length");
        $note_title_max_length = Configuration::get("note_title_max_length");

        if (mb_strlen($this->get_title()) < $note_title_min_length || mb_strlen($this->get_title()) > $note_title_max_length) {
            $errors['title'] = "Title length must be between {$note_title_min_length} and {$note_title_max_length} ";
        }
        if (!($this->weight > 0 && !$this->is_not_unique_weight())) {
            $errors['weight'] = "Weight must be positive and unique";
        }
        if (!$this->is_unique_title($this->title)) {
            $errors['title'] = "Title must be unique for the owner.";
        }

        return $errors;
    }
    public function is_unique_title(string $title): bool
    {
        $query = self::execute("SELECT COUNT(*) AS count FROM notes WHERE title = :title AND owner = :owner AND id != :id", [
            'title' => $title,
            'owner' => $this->owner->get_id(),
            'id' => $this->id ?? 0,
        ]);
        $result = $query->fetch();

        return $result['count'] === 0;
    }
    public static function is_unique_title_ajax(string $title, int $owner, int $note_id): bool
    {
        if ($note_id === -1) {

            $query = self::execute("SELECT COUNT(*) AS count FROM notes WHERE title = :title AND owner = :owner ", [
                'title' => $title,
                'owner' => $owner
            ]);
        } else {
            $query = self::execute("SELECT COUNT(*) AS count FROM notes WHERE title = :title AND owner = :owner AND id != :id", [
                'title' => $title,
                'owner' => $owner,
                'id' => $note_id,
            ]);
        }
        $result = $query->fetch();
        return $result['count'] === 0;
    }
    public function is_not_unique_weight(): bool
    {
        $notes_by_owner = $this->owner->get_notes();
        $is_not_unique = false;
        $i = 0;
        $notes = $notes_by_owner["pinned"];

        if ($this->pinned == 0) {
            $notes = $notes_by_owner["other"];
        }

        while (!$is_not_unique && $i < count($notes)) {
            $note = $notes[$i];
            if ($note->weight == $this->weight && $note->id != $this->id) {
                $is_not_unique = true;
            }
            $i++;
        }
        return $is_not_unique;
    }

    public static function get_note(int $id): Note| false
    {
        $query = self::execute("select * from notes n JOIN text_notes t ON t.id = n.id where n.id= :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            $query = self::execute("select * from notes n JOIN checklist_notes c ON c.id = n.id where n.id= :id", ["id" => $id]);
            if ($query->rowCount() == 0) {
                return false;
            } else {
                $row = $query->fetch();
                $owner = User::get_user_by_id($row['owner']);
                return new ChecklistNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
            }
        } else {
            $row = $query->fetch();
            $owner = User::get_user_by_id($row['owner']);
            //$query = self::execute("select * from text_notes where id = :id", ["id" => $row['id']]);

            return new TextNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['content'], $row['id'], $row['created_at'], $row['edited_at']);
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
    public function delete_all(User $initiator): Note|false
    {
        if ($this->owner == $initiator) {
            self::execute('DELETE FROM note_shares WHERE note = :note_id', ['note_id' => $this->id]);

            if (self::is_checklist_note($this->id)) {
                self::execute('DELETE FROM checklist_note_items WHERE checklist_note = :note_id', ['note_id' => $this->id]);
                self::execute('DELETE FROM checklist_notes WHERE id = :note_id', ['note_id' => $this->id]);
            } else {
                self::execute('DELETE FROM text_notes WHERE id = :note_id', ['note_id' => $this->id]);
            }
            self::execute('DELETE FROM note_labels WHERE note = :note_id', ['note_id' => $this->id]);

            self::execute('DELETE FROM notes WHERE id = :note_id', ['note_id' => $this->id]);
            return $this;
        }
        return false;
    }
    public function persist(): Note|array
    {
        $errors = $this->validate();
        if (empty($errors)) {
            if ($this->id == NULL) return self::add_note_in_DB();
            else return self::modify_note_in_DB();
        } else {
            return $errors;
        }
    }
    public function persist_head(): Note|array
    {
        $errors = $this->validate();
        if (empty($errors)) {
            if ($this->id == NULL) return self::add_note_in_DB();
            else return self::modify_head_in_DB();
        } else {
            return $errors;
        }
    }
    protected function add_note_in_DB(): void
    {
        self::execute(
            'INSERT INTO notes (title, owner, created_at, edited_at, pinned, archived, weight) VALUES
         (:title, :owner, NOW(), null, :pinned, :archived, :weight)',
            [
                'title' => $this->title,
                'owner' => $this->owner->get_id(),
                'pinned' => $this->pinned ? 1 : 0,
                'archived' => $this->archived ? 1 : 0,
                'weight' => $this->weight
            ]
        );
        /*
        $note = self::get_note(self::lastInsertId());

        $this->id = $note->get_id();
        $this->created_at = $note->get_created_at();
        $this->edited_at = $note->get_edited_at();
        return $this;
                */
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


    public function is_checklist_note(): bool
    {
        $query = self::execute("SELECT id FROM checklist_notes WHERE id = :id", ["id" => $this->get_id()]);
        return $query->rowCount() > 0;
    }
    public function toggle_pin(): static
    {
        $this->pinned = !$this->pinned;
        return $this->modify_head_in_DB();
    }
    public function set_archive_reverse(): static
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
    public function jsonSerialize(): mixed
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}
