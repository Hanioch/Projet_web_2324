<?php

require_once "model/MyModel.php";
require_once "model/CheckListNote.php";
require_once "model/CheckListNoteItems.php";
require_once "model/TextNote.php";


enum Role: string
{
    case USER = "user";
    case ADMIN = "admin";
}

class User extends MyModel
{
    public function __construct(private string $mail, private string $hashed_password, private string $full_name, private Role $role, private ?int $id = NULL)
    {
    }
    // Getters
    public function get_Mail(): string {
        return $this->mail;
    }

    public function get_Hashed_Password(): string {
        return $this->hashed_password;
    }

    public function get_Full_Name(): string {
        return $this->full_name;
    }

    public function get_Role(): Role {
        return $this->role;
    }

    public function get_Id(): ?int {
        return $this->id;
    }

    // Setters
    public function set_Mail(string $mail): void {
        $this->mail = $mail;
    }

    public function set_Hashed_Password(string $hashed_password): void {
        $this->hashed_password = $hashed_password;
    }

    public function set_Full_Name(string $full_name): void {
        $this->full_name = $full_name;
    }

    public function set_Role(Role $role): void {
        $this->role = $role;
    }
    public function persist(): User
    {
        if (self::get_user_by_mail($this->mail))
            self::execute(
                "UPDATE users SET hashed_password=:hashed_password, full_name=:full_name, role=:role WHERE mail=:mail ",
                ["mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role->value]
            );
        else
            self::execute(
                "INSERT INTO users (mail,hashed_password,full_name,role) VALUES(:mail,:hashed_password,:full_name,:role)",
                ["mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role->value]
            );
        return $this;
    }
    public static function get_user_by_mail(string $mail): User|false
    {
        $query = self::execute("SELECT * FROM users where mail = :mail", ["mail" => $mail]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], Role::USER, $data["id"]);
        }
    }
    public function delete(): void {
        if ($this->id != NULL) {
            self::execute("DELETE FROM users WHERE id = :id", ['id' => $this->id]);
        }
    }

    public static function get_users(): array
    {
        $query = self::execute("SELECT * FROM users", []);
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
        if (strlen($password) === 0) {
            $errors["password"][] = "Password is required.";
        }
        if (strlen($password) < 8) {
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
        if (!strlen($full_name) > 0) {
            $errors["full_name"][] = "Name is required.";
        }
        if (strlen($full_name) < 3) {
            $errors["full_name"][] = "Name must be at least 3 characters long";
        }
        return $errors;
    }

    public static function validate_password_confirmation(string $password, string $password_confirm): array
    {
        $errors = [
            "password_confirm" => []
        ];
        if (!strlen($password_confirm) > 0) {
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

            if (!strlen($mail) > 0) {
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

    // redondant avec la nouvelle méthode validate_full_name ?

    //    public function validate() : array {
    //        $errors = [];
    //        if (!strlen($this->pseudo) > 0) {
    //            $errors[] = "Pseudo is required.";
    //        } if (!(strlen($this->pseudo) >= 3 && strlen($this->pseudo) <= 16)) {
    //            $errors[] = "Pseudo length must be between 3 and 16.";
    //        } if (!(preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $this->pseudo))) {
    //            $errors[] = "Pseudo must start by a letter and must contain only letters and numbers.";
    //        }
    //        return $errors;
    //    }

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

        if (ChecklistNote::is_checklist_note($row['checklist_id'])) {
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

    public function get_users_shared_note(): array
    {
        $query = self::execute("SELECT DISTINCT u.*
        FROM users u
        INNER JOIN notes n ON u.id = n.owner
        INNER JOIN note_shares ns ON n.id = ns.note
        WHERE ns.user = :owner;
        
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
    
   public function get_heaviest_note(): int
    {
        $query = self::execute("
        SELECT weight FROM notes
        WHERE owner = :owner
        ORDER BY weight DESC
        LIMIT 1;    
        ", ["owner" => $this->id]);
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            $row = $query->fetch();
            return $row['weight'];
        }
    }

    public static function get_user_by_id($id): User | false
    {
        $query = self::execute("select * from users where id = :id", ["id" => $id]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            $new_role = User::get_role_format($row["role"]);
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
        if (!(Tools::my_hash($old_password) === $user->get_Hashed_Password())) {
            $errors['old_password'][] = "Incorrect old password.";
        }

        return $errors;
    }
}
