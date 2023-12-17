<?php

require "framework/Model.php";

class Note extends Model
{

    public function __construct(public ?int $id, protected string $title, protected User $owner, protected ?string $created_at, protected ?string $edited_at, protected  bool $pinned, protected bool $archived, protected int $weight)
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
        if (!($this->weight > 0)) {
            //TODOOO ajouter une condition pour verifier qu'il est unique.

            $errors[] = "Weight must be positif and unique";
        }

        return $errors;
    }



    public static function get_note(int $id): Note| false
    {
        $query = self::execute("select * from notes where id= :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Note($row('id'), $row['title'], $row['owner'], $row['created_at'], $row['edited_at'], $row['pinned'], $row['archived'], $row['weight']);
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
                self::execute(
                    'INSERT INTO notes (title, owner, created_at, edited_at, pinned, archived, weight) VALUES
                 (:title, :owner, NOW(), null, :pinned, :archived, :weight)',
                    [
                        'title' => $this->title,
                        //TO DOO ici il faut mettre  l'id de l'owner donc le rajouter dans le modèle.
                        'owner' => $this->owner,
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
            } else {
                self::execute('UPDATE notes SET title = :title, edited_at = NOW(), pinned = :pinned, archived = :archived, weight = :weight WHERE id = :id', [
                    'title' => $this->title,
                    'pinned' => $this->pinned ? 1 : 0,
                    'archived' => $this->archived ? 1 : 0,
                    'weight' => $this->weight,
                    'id' => $this->id
                ]);

                return $this;
            }
        } else {
            return $errors;
        }
    }
}
