<?php

require_once "model/MyModel.php";
require_once "model/CheckListNote.php";
require_once "model/CheckListNoteItem.php";
require_once "model/TextNote.php";


enum Role: string
{
    case USER = "user";
    case ADMIN = "admin";
}

class User extends MyModel implements JsonSerializable
{
    private array $config;
    public function __construct(private string $mail, private string $hashed_password, private string $full_name, private Role $role, private ?int $id = NULL)
    {
    }
    // Getters
    public function get_mail(): string
    {
        return $this->mail;
    }

    public function get_hashed_password(): string
    {
        return $this->hashed_password;
    }

    public function get_full_name(): string
    {
        return $this->full_name;
    }

    public function get_role(): Role
    {
        return $this->role;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    // Setters
    public function set_mail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function set_hashed_password(string $hashed_password): void
    {
        $this->hashed_password = $hashed_password;
    }

    public function set_full_name(string $full_name): void
    {
        $this->full_name = $full_name;
    }

    public function set_role(Role $role): void
    {
        $this->role = $role;
    }
    public function persist(): User
    {
        if ($this->id !== null && self::get_user_by_id($this->id)) {
            self::execute(
                "UPDATE users SET hashed_password=:hashed_password, full_name=:full_name, role=:role, mail=:mail WHERE id=:id",
                ["id" => $this->id, "mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role->value]
            );
        } elseif ($this->mail !== null && self::get_user_by_mail($this->mail)) {
            self::execute(
                "UPDATE users SET hashed_password=:hashed_password, full_name=:full_name, role=:role WHERE mail=:mail",
                ["mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role->value]
            );
        } else {
            self::execute(
                "INSERT INTO users (mail, hashed_password, full_name, role) VALUES (:mail, :hashed_password, :full_name, :role)",
                ["mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role->value]
            );
        }

        return $this;
    }
    public static function get_user_by_mail(string $mail): User|false
    {
        $query = self::execute("SELECT * FROM users where mail = :mail", ["mail" => $mail]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $new_role = Role::tryFrom($data["role"]) ?: Role::USER;
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $new_role, $data["id"]);
        }
    }
    public function is_admin(): bool
    {
        return $this->role === Role::ADMIN;
    }
    public function delete(): void
    {
        if ($this->id != NULL) {
            self::execute("DELETE FROM users WHERE id = :id", ['id' => $this->id]);
        }
    }

    public static function get_users(): array
    {
        $query = self::execute("SELECT * FROM users ORDER BY full_name ASC", []);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $role = Role::tryFrom($row["role"]) ?: Role::USER;
            $results[] = new User($row["mail"], $row["hashed_password"], $row["full_name"], $role, $row["id"]);
        }
        return $results;
    }

    public static function validate_password(string $password): array
    {
        $errors = [
            "password" => []
        ];

        $password_min_length = Configuration::get("password_min_length");


        if (mb_strlen($password) === 0) {
            $errors["password"][] = "Password is required.";
        }
        if (mb_strlen($password) < $password_min_length) {
            $errors["password"][] = "Password must be at least 8 characters long";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors["password"][] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validate_full_name(string $full_name): array
    {
        $errors = [
            "full_name" => []
        ];

        $fullname_min_length = Configuration::get("fullname_min_length");

        if (!mb_strlen($full_name) > 0) {
            $errors["full_name"][] = "Name is required.";
        } else if (mb_strlen($full_name) < $fullname_min_length) {
            $errors["full_name"][] = "Name must be at least 3 characters long";
        }
        return $errors;
    }

    public static function validate_password_confirmation(string $password, string $password_confirm): array
    {
        $errors = [
            "password_confirm" => []
        ];
        if (!mb_strlen($password_confirm) > 0) {
            $errors["password_confirm"][] = "Password confirmation is required.";
        }
        if ($password != $password_confirm) {
            $errors["password_confirm"][] = "Confirmation must match password above";
        }
        return $errors;
    }
    public static function validate_mail(string $mail): array
    {
        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $errors = [
            "mail" => []
        ];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors["mail"][] = "This user already exists.";
        } else {

            if (!mb_strlen($mail) > 0) {
                $errors["mail"][] = "Mail is required.";
            }
            if (!(preg_match($regex, $mail))) {
                $errors["mail"][] = "Must be a valid mail address.";
            }
        }
        return $errors;
    }

    public static function validate_unicity(string $mail): array
    {
        $errors = [
            "mail" => []
        ];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors["mail"][] = "This user already exists.";
        }
        return $errors;
    }

    private static function check_password(string $clear_password, string $hash): bool
    {
        return $hash === Tools::my_hash($clear_password);
    }

    public static function validate_login(string $mail, string $password): array
    {
        $errors = [
            "mail" => [],
            "password" => []
        ];
        $user = User::get_user_by_mail($mail);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors["password"][] = "Incorrect password.";
            }
        } else {
            $errors["mail"][] = "'$mail' is not registered yet. Please sign up.";
        }
        return $errors;
    }

    private function get_text_note_or_checklist_note(array $row): Note
    {
        $owner = User::get_user_by_id($row['owner']);
        $note = Note::get_note($row['id']);
        if ($note instanceof Note && $note->is_checklist_note()) {
            return  new ChecklistNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['id'], $row['created_at'], $row['edited_at']);
        } else {
            return  new TextNote($row['title'], $owner, $row['pinned'], $row['archived'], $row['weight'], $row['text_content'], $row['id'], $row['created_at'], $row['edited_at']);
        }
    }

    public function get_notes(): array
    {
        $query = self::execute("SELECT
        n.*,
        tn.content AS text_content,
        cn.id AS checklist_id,
        GROUP_CONCAT(cni.id) AS checklist_items
        FROM
        notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        LEFT JOIN checklist_note_items cni ON cn.id = cni.checklist_note  
        where owner = :owner AND n.archived =0 AND   n.id NOT IN
        (SELECT note FROM note_shares ns INNER JOIN notes n2 On n2.id = ns.note WHERE n2.owner != n.owner )
        GROUP BY n.id, n.title, n.pinned, n.archived, n.weight, tn.content, cn.id, n.owner, n.created_at, n.edited_at order by  pinned DESC, weight DESC
       ", ["owner" => $this->id]);
        $data = $query->fetchAll();
        $notes = [];
        $notes["pinned"] = [];
        $notes["other"] = [];
        foreach ($data as $row) {
            $note = $this->get_text_note_or_checklist_note($row);

            if ($row['pinned'] === 1) {
                $notes["pinned"][] = $note;
            } else {
                $notes["other"][] = $note;
            }
        }
        return $notes;
    }

    public function get_notes_with_weight_between(int $weight_first_note, int $weight_second_note, bool $pinned)
    {
        $first_bigger = $weight_first_note > $weight_second_note;
        $condition = $first_bigger ?
            "n.weight < :first_weight AND n.weight >= :second_weight" :
            "n.weight > :first_weight AND n.weight < :second_weight";
        $order_by = $first_bigger ? "DESC" : "ASC";

        $query = self::execute("SELECT  n.*,
        tn.content AS text_content,
        cn.id AS checklist_id
        FROM
        notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        where owner = :owner AND n.pinned= :pinned AND $condition AND n.archived =0  AND n.id NOT IN
        (SELECT note FROM note_shares ns INNER JOIN notes n2 On n2.id = ns.note WHERE n2.owner != n.owner )
        GROUP BY n.id, n.title, n.pinned, n.archived, n.weight, tn.content, cn.id, n.owner, n.created_at, n.edited_at
        ORDER BY weight $order_by
       ", ["owner" => $this->id, "pinned" => $pinned, "first_weight" => $weight_first_note, "second_weight" => $weight_second_note]);
        $data = $query->fetchAll();
        $notes = [];
        foreach ($data as $row) {
            $notes[] = $row;
        }
        return $notes;
    }
    public function get_notes_archived_with_weight_between(int $weight_first_note, int $weight_second_note)
    {
        $query = self::execute("SELECT  n.*,
        tn.content AS text_content,
        cn.id AS checklist_id
        FROM
        notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        where owner = :owner AND n.weight > :first_weight AND n.archived =1
        AND n.id NOT IN
        (SELECT note FROM note_shares ns INNER JOIN notes n2 On n2.id = ns.note WHERE n2.owner != n.owner )
        GROUP BY n.id, n.title, n.pinned, n.archived, n.weight, tn.content, cn.id, n.owner, n.created_at,n.edited_at
        ORDER BY weight ASC
       ", ["owner" => $this->id, "first_weight" => $weight_first_note]);
        $data = $query->fetchAll();
        $notes = [];
        foreach ($data as $row) {
            $notes[] = $row;
        }
        return $notes;
    }

    public function get_users_shared_note(): array
    {
        $query = self::execute("SELECT DISTINCT u.*
        FROM users u
        INNER JOIN notes n ON u.id = n.owner
        INNER JOIN note_shares ns ON n.id = ns.note
        WHERE ns.user = :owner
        ORDER BY u.full_name;
        
         ", ["owner" => $this->id]);

        $data = $query->fetchAll();
        $user_shared = [];
        foreach ($data as $row) {
            $role = User::get_role_format($row["role"]);
            $user_shared[] = new User($row["mail"], $row["hashed_password"], $row["full_name"], $role, $row["id"]);
        }

        return $user_shared;
    }
    public function get_notes_archives(): array
    {
        $query = self::execute("SELECT n.*,
        tn.content AS text_content,
        cn.id AS checklist_id,
        GROUP_CONCAT(cni.id) AS checklist_items
        FROM
        notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        LEFT JOIN checklist_note_items cni ON cn.id = cni.checklist_note  
        where owner = :owner AND n.archived =1 
        GROUP BY n.id, n.title, n.pinned, n.archived, n.weight, tn.content, cn.id, n.owner, n.created_at, n.edited_at
         order by  pinned DESC, weight DESC", ["owner" => $this->id]);

        $data = $query->fetchAll();
        $archives_notes = [];
        foreach ($data as $row) {
            $archives_notes[] = $this->get_text_note_or_checklist_note($row);
        }

        return $archives_notes;
    }

    public function get_notes_shared_by($sender_id): array
    {
        $query = self::execute("SELECT
        n.*,
        tn.content AS text_content,
        cn.id AS checklist_id,
        ns.editor,
        GROUP_CONCAT(cni.id) AS checklist_items
        FROM notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        LEFT JOIN checklist_note_items cni ON cn.id = cni.checklist_note
        LEFT JOIN note_shares ns ON n.id = ns.note
        WHERE ns.user = :owner AND n.owner = :sender
        GROUP BY n.id, n.title, n.owner, n.created_at, n.edited_at, n.pinned, n.archived, n.weight,tn.content,cn.id,ns.editor
        ORDER BY pinned DESC, weight DESC;", ["owner" => $this->id, "sender" => $sender_id]);

        $data = $query->fetchAll();
        $shared_notes = [];
        $shared_notes["editor"] = [];
        $shared_notes["reader"] = [];

        foreach ($data as $row) {
            $note = $this->get_text_note_or_checklist_note($row);

            if ($row["editor"] === 1) {
                $shared_notes["editor"][] = $note;
            } else {
                $shared_notes["reader"][] = $note;
            }
        }


        return $shared_notes;
    }
    public function get_notes_with_label_shared_by($sender_id, $list_filter): array
    {
        $params = ["owner" => $this->id, "sender" => $sender_id];

        if (empty($list_filter)) {
            return [];
        } else {
            $filters_string = implode(',', array_map(function ($i) use ($list_filter) {
                return ":filter$i";
            }, range(0, count($list_filter) - 1)));

            foreach ($list_filter as $index => $filter) {
                $params[":filter$index"] = $filter;
            }

            $labels_condition = 'AND n.id IN (SELECT note FROM note_labels WHERE label IN (' . $filters_string . ')
                              GROUP BY note HAVING COUNT(DISTINCT label) = ' . count($list_filter) . ')';

            $query = self::execute(
                "SELECT
        n.*,
        tn.content AS text_content,
        cn.id AS checklist_id,
        ns.editor,
        GROUP_CONCAT(cni.id) AS checklist_items,
        GROUP_CONCAT(nl.label) AS labels
        FROM notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        LEFT JOIN checklist_note_items cni ON cn.id = cni.checklist_note
        LEFT JOIN note_shares ns ON n.id = ns.note
        LEFT JOIN note_labels nl ON n.id = nl.note
        WHERE ns.user = :owner AND n.owner = :sender $labels_condition
        GROUP BY n.id, n.title, n.owner, n.created_at, n.edited_at, n.pinned, n.archived, n.weight,tn.content,cn.id,ns.editor
        ORDER BY weight DESC;",
                $params
            );

            $data = $query->fetchAll();
            $shared_notes = [];

            foreach ($data as $row) {
                if (empty($list_filter) || count(array_intersect($list_filter, explode(',', $row['labels']))) == count($list_filter)) {
                    $note = $this->get_text_note_or_checklist_note($row);
                    $shared_notes[] = $note;
                }
            }

            return $shared_notes;
        }
    }

    public function get_notes_searched($list_filter): array
    {
        $params = ["owner" => $this->id];

        if (empty($list_filter)) {
            return [];
        } else {
            $filters_string = implode(',', array_map(function ($i) use ($list_filter) {
                return ":filter$i";
            }, range(0, count($list_filter) - 1)));

            foreach ($list_filter as $index => $filter) {
                $params[":filter$index"] = $filter;
            }

            $labels_condition = 'AND n.id IN (SELECT note FROM note_labels WHERE label IN (' . $filters_string . ')
                              GROUP BY note HAVING COUNT(DISTINCT label) = ' . count($list_filter) . ')';

            $query = self::execute("SELECT
            n.*,
            tn.content AS text_content,
            cn.id AS checklist_id,
            GROUP_CONCAT(cni.id) AS checklist_items,
            GROUP_CONCAT(nl.label) AS labels
            FROM notes n
            LEFT JOIN text_notes tn ON n.id = tn.id
            LEFT JOIN checklist_notes cn ON n.id = cn.id
            LEFT JOIN checklist_note_items cni ON cn.id = cni.checklist_note
            LEFT JOIN note_labels nl ON n.id = nl.note
            WHERE n.owner = :owner $labels_condition
            GROUP BY n.id, n.title, n.owner, n.created_at, n.edited_at, n.pinned, n.archived, n.weight, tn.content, cn.id
            ORDER BY weight DESC;", $params);

            $data = $query->fetchAll();
            $search_notes = [];

            foreach ($data as $row) {
                if (empty($list_filter) || count(array_intersect($list_filter, explode(',', $row['labels']))) == count($list_filter)) {
                    $note = $this->get_text_note_or_checklist_note($row);
                    $search_notes[] = $note;
                }
            }
        }

        return $search_notes;
    }
    public function get_filter_list(): array
    {
        $query = self::execute(
            "SELECT DISTINCT
             nl.label
            FROM note_labels nl 
            LEFT JOIN notes n ON n.id = nl.note
            LEFT JOIN note_shares ns ON ns.note = n.id
            WHERE n.owner = :owner OR ns.user = :owner 
            ORDER BY LOWER(nl.label) ASC;",
            ["owner" => $this->id]
        );

        $data = $query->fetchAll();
        $filter_list = [];
        foreach ($data as $row) {
            if ($row["label"]) {
                $filter_list[] = $row["label"];
            }
        }

        return $filter_list;
    }

    public function get_heaviest_note($pinned = NULL, $archived = NULL): int
    {
        $query = "";
        if ($archived !== NULL) {
            $query = self::execute("
            SELECT weight FROM notes
            WHERE owner = :owner AND archived = :archived AND pinned = 0
            ORDER BY weight DESC
            LIMIT 1;    
            ", ["owner" => $this->id, "archived" => $archived ? 1 : 0]);
        } else if ($pinned !== NULL) {
            $query = self::execute("
            SELECT weight FROM notes
            WHERE owner = :owner AND pinned = :pinned
            ORDER BY weight DESC
            LIMIT 1;    
            ", ["owner" => $this->id, "pinned" => $pinned ? 1 : 0]);
        } else {
            $query = self::execute("
                SELECT weight FROM notes
                WHERE owner = :owner
                ORDER BY weight DESC
                LIMIT 1;    
                ", ["owner" => $this->id]);
        }

        if ($query->rowCount() == 0) {
            return 0;
        } else {
            $row = $query->fetch();
            return $row['weight'];
        }
    }

    public function get_note_by_id(int $id): Note | false

    {
        $query = self::execute("SELECT n.*,
        tn.content AS text_content,
        cn.id AS checklist_id,
        GROUP_CONCAT(cni.id) AS checklist_items
        FROM
        notes n
        LEFT JOIN text_notes tn ON n.id = tn.id
        LEFT JOIN checklist_notes cn ON n.id = cn.id
        LEFT JOIN checklist_note_items cni ON cn.id = cni.checklist_note  
        where owner = :owner AND n.id = :id
        GROUP BY n.id, n.title, n.pinned, n.archived, n.weight, tn.content, cn.id, n.owner, n.created_at, n.edited_at
         ", ["owner" => $this->id, "id" => $id]);

        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return $this->get_text_note_or_checklist_note($row);
        }
    }

    public static function get_user_by_id($id): User | false
    {
        $query = self::execute("select * from users where id = :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            $new_role = Role::tryFrom($row["role"]) ?: Role::USER;
            return new User($row['mail'], $row['hashed_password'], $row['full_name'], $new_role, $row['id']);
        }
    }

    public static  function get_role_format(string $role): Role
    {
        $new_role = Role::USER;
        if ($role == Role::ADMIN) {
            $new_role = Role::ADMIN;
        }
        return $new_role;
    }

    public static function change_password(string $old_password, User $user): array
    {
        $errors = [
            "old_password" => []
        ];
        if (!(Tools::my_hash($old_password) === $user->get_hashed_password())) {
            $errors['old_password'][] = "Incorrect old password.";
        }

        return $errors;
    }

    public function jsonSerialize(): mixed
    {
        $vars = get_object_vars($this);

        return $vars;
    }
}
