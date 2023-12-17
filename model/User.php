<?php

require_once "framework/Model.php";

enum Role: string {
    case USER = "user";
    case ADMIN = "admin";
}

class User extends Model {

    public function __construct(public string $mail, public string $hashed_password, public string $full_name, public Role $role) {

    }

    public function persist() : User {
        if(self::get_user_by_mail($this->mail))
            self::execute("UPDATE users SET hashed_password=:hashed_password, full_name=:full_name, role=:role WHERE mail=:mail ",
                ["hashed_password"=>$this->hashed_password, "full_name"=>$this->full_name, "role"=>$this->role]);
        else
            self::execute("INSERT INTO users (mail,hashed_password,full_name,role) VALUES(:mail,:hashed_password,:full_name,:role)",
                ["mail"=>$this->mail, "hashed_password"=>$this->hashed_password, "full_name"=>$this->full_name, "role"=>$this->role]);
        return $this;
    }

    public static function get_user_by_mail(string $mail) : User|false {
        $query = self::execute("SELECT * FROM users where mail = :mail", ["mail"=>$mail]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            switch($data["role"]){
                case "admin" :
                    $role = Role::ADMIN;
                default :
                    $role = Role::USER;
            }
//            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $role);
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], (Role::ADMIN === $data["role"]) ? Role::ADMIN : Role::USER);
        }
    }

    // à décommenter si full_name doit être unique

//    public static function get_user_by_name(string $full_name) : User|false {
//        $query = self::execute("SELECT * FROM users where full_name = :full_name", ["full_name"=>$full_name]);
//        $data = $query->fetch();
//        if ($query->rowCount() == 0) {
//            return false;
//        } else {
//            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"]);
//        }
//    }

    public static function get_users() : array {
        $query = self::execute("SELECT * FROM users", []);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["mail"], $row["hashed_password"], $row["full_name"], $row["role"]);
        }
        return $results;
    }

    private static function validate_password(string $password) : array {
        $errors = [];
        if (!strlen($password) > 0) {
            $errors[] = "Password is required.";
        } if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    private static function validate_full_name(string $full_name) : array {
        $errors = [];
        if (!strlen($full_name) > 0) {
            $errors[] = "Name is required.";
        } if (strlen($full_name) < 3) {
            $errors[] = "Name must be at least 3 characters long";
        }
        return $errors;
    }

    public static function validate_password_confirmation(string $password, string $password_confirm) : array {
        $errors = self::validate_password($password);
        if (!strlen($password_confirm) > 0) {
            $errors[] = "Password confirmation is required.";
        } if ($password != $password_confirm) {
            $errors[] = "Confirmation must match password above";
        }
        return $errors;
    }
    private static function validate_mail(string $mail) : array {
        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        $errors = [];
        if (!strlen($mail) > 0) {
            $errors[] = "Mail is required.";
        } if (!(preg_match($regex, $mail))) {
            $errors[] = "Must be a valid mail address.";
        }
        return $errors;
    }

    public static function validate_unicity(string $mail) : array {
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }

    private static function check_password(string $clear_password, string $hash) : bool {
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

    public static function validate_login(string $mail, string $password) : array {
        $errors = [
            "mail"=>[],
            "password"=>[]
        ];
        $user = User::get_user_by_mail($mail);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors["password"][] = "Incorrect password.";
            }
        } else {
            if (empty($mail)) {
                $errors["mail"][] = "Mail required.";
            } else {
                $errors["mail"][] = "'$mail' is not registered yet. Please sign up.";
            }
        }
        return $errors;
    }
}