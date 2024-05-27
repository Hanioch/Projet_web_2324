<?php

require_once "model/MyModel.php";
require_once "model/User.php";
require_once "model/Note.php";

class Label extends MyModel {
    public function __construct(private ?int $note_id, private string $label_name, private ?int $id = NULL) {}

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    public function get_note_id(): int
    {
        return $this->note_id;
    }
    public function get_label_name(): string
    {
        return $this->label_name;
    }
    public static function add_label($note_id, $label_name) {
        return self::execute('INSERT INTO note_labels (note, label) VALUES (:note_id, :label_name)', [
            'note_id' => $note_id,
            'label_name' => $label_name,
        ]);
    }
    public static function remove_label($note_id, $label_name) {
        return self::execute('DELETE FROM note_labels WHERE note = :note_id AND label = :label_name', [
            'note_id' => $note_id,
            'label_name' => $label_name
        ]);
    }

    public static function get_labels_by_note_id($note_id) {
        $query = self::execute("SELECT * FROM note_labels WHERE note = :note_id", ['note_id' => $note_id]);
        $data = $query->fetchAll();
        $labels = [];
        foreach ($data as $row) {
            $labels[] = new Label($row['note'], $row['label']);
        }
        return $labels;
    }
    public static function get_label_by_note_id_and_label_name($note_id, $label_name) {
        $query = self::execute("SELECT * FROM note_labels WHERE note = :note_id AND label = :label_name", ['note_id' => $note_id, 'label_name' => $label_name]);
        $row = $query->fetch();

        return new Label($row['note'], $row['label']);
    }


    /*public static function get_labels_by_user_id($userId) {
        $query = self::execute("SELECT FROM note_labels l, notes n WHERE l.note = n.id AND n.owner = :user_id", ['user_id' => $userId]);
        $data = $query->fetchAll();
        $labels = [];
        foreach ($data as $row) {
            $labels[] = new Label($row['note'], $row['label']);
        }
        return $labels;
    }*/
    public static function get_labels_by_user_id($user_id) {
        $query = self::execute("SELECT DISTINCT l.label FROM note_labels l
                            INNER JOIN notes n ON l.note = n.id
                            WHERE n.owner = :user_id",
            ['user_id' => $user_id]);
        $data = $query->fetchAll();
        $labels = [];
        foreach ($data as $row) {
            $labels[] = new Label(null, $row['label']);
        }
        return $labels;
    }
    public static function is_unique_by_note(string $label, $note_id) {
        $labels = self::get_labels_by_note_id($note_id);
        /** @var Label $label */
        foreach ($labels as $l) {
            if(strtoupper($l->get_label_name()) === strtoupper($label)) {
                return false;
            }
        }
        return true;
    }

    public function delete(): Label|false
    {
        try {
            self::execute("DELETE FROM note_labels WHERE note = :note AND label = :label", ["note" => $this->get_note_id(), "label" => $this->label_name]);
            return $this;
        } catch (\Throwable $th) {
            return false;
        }
    }


    public static function validate_label(string $label, $note_id): array
    {
        $errors = [
            "label" => []
        ];

        $label_min_length = Configuration::get("label_min_length");
        $label_max_length = Configuration::get("label_max_length");

        if (mb_strlen($label) < $label_min_length || mb_strlen($label) > $label_max_length) {
            $errors["label"][] = "Label length must be between 2 and 10.";
        }
        if (preg_match("/\s/", $label)) {
            $errors["label"][] = "Label name cannot contain any space.";
        }
        if(!(self::is_unique_by_note($label, $note_id))) {
            $errors["label"][] = "A note cannot contain the same label twice.";
        }
        return $errors;
    }
    public function persist()
    {
        self::execute(
            "INSERT INTO note_labels (note, label) VALUES (:note, :label)",
            ["note" => $this->note_id, "label" => $this->label_name]
        );
    }

    public static function fix_label_format($content) : string {
        if(mb_strlen($content) > 0) {
            $content = ucfirst($content);
        }
        return $content;
    }
}