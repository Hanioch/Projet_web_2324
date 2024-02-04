<?php

class TextNote extends Note
{

    public function __construct(private string $title, private User $owner, private  bool $pinned, private bool $archived, private $weight, private ?string $content, private ?int $id = NULL, private ?string $created_at = NULL, private ?string $edited_at = NULL)
    {
        parent::__construct($title, $owner, $pinned, $archived, $weight, $id, $created_at, $edited_at);
    }

    public function get_Content(): ?string
    {
        return $this->content;
    }

    public function set_Content(?string $content): void
    {
        $this->content = $content;
    }

    public static function get_text_note(int $id): TextNote| false
    {
        $query = self::execute("select * from notes n join text_notes tn ON tn.id = n.id where n.id= :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            $owner = User::get_user_by_id($row['owner']);
            return new TextNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['content'], $row['id'], $row['created_at'], $row['edited_at']);
        }
    }

    public function delete(User $initiator): Note|false
    {
        if ($this->owner == $initiator) {
            self::execute('DELETE FROM text_notes WHERE id = :id', ['id' => $this->id]);
            parent::delete($initiator);
            return $this;
        }
        return false;
    }

    public function persist(?Note $second_note = NULL): TextNote|array
    {
        $errors = $this->validate();
        if (empty($errors)) {

            if ($this->id == NULL) {
                $note = parent::add_note_in_DB();
                self::execute(
                    'INSERT INTO text_notes (id,content) VALUES
                 (:id,:content)',
                    [
                        'id' => $note->get_Id(),
                        'content' => $this->get_Content(),
                    ]
                );
                return $this;
            } else {
                self::execute('UPDATE text_notes SET  content = :content WHERE id = :id', [
                    'content' => $this->get_Content(),
                    'id' => $this->get_Id()
                ]);
                parent::modify_note_in_DB();
                return $this;
            }
        } else {
            return $errors;
        }
    }
}
