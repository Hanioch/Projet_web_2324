<?php

class TextNote extends Note
{

    public function __construct(private string $content)
    {
    }

    /*public function validate(): array
    {
        $errors = parent::validate();
        return $errors;
    }
    */


    public static function get_text_note(int $id): Note| false
    {
        $query = self::execute("select * from notes n join text_notes tn ON tn.id = n.id where id= :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new TextNote($row('id'), $row['title'], $row['owner'], $row['created_at'], $row['edited_at'], $row['pinned'], $row['archived'], $row['weight'], $row['content']);
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

    public function persist(): TextNote|array
    {
        // TODOOO 
        // il faut tout revoir ici
        $errors = $this->validate();
        if (empty($errors)) {

            if ($this->id == NULL) {

                self::execute(
                    'INSERT INTO text_notes (id,content) VALUES
                 (:id,:content)',
                    [
                        'id' => $this->id,
                        'content' => $this->content,
                    ]
                );
                parent::add_note_in_DB();
                return $this;
            } else {
                self::execute('UPDATE text_notes SET  content = :content WHERE id = :id', [
                    'content' => $this->content,
                    'id' => $this->id
                ]);
                parent::modify_note_in_DB();
                return $this;
            }
        } else {
            return $errors;
        }
    }
}
