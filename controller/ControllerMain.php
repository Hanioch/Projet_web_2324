<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller {

    //si l'utilisateur est connecté, redirige vers les notes.
    //sinon, produit la vue de login.
    public function index() : void {
//        var_dump($this->user_logged());
        if ($this->user_logged()) {
            $this->redirect("notes", "index");
        } else {
            $this->redirect("main", "login");
        }
    }

    public function login(): void {
        $mail = '';
        $password = '';
        $errors = [
            "mail"=>[],
            "password"=>[]
        ];
        if (isset($_POST['mail']) && isset($_POST['password'])) {
            $mail = $_POST['mail'];
            $password = $_POST['password'];
            $errors = User::validate_login($mail, $password);
            if (empty($errors["mail"]) && empty($errors["password"])) {
                $this->log_user(User::get_user_by_mail($mail));
            }

        }
        (new View("login"))->show(["mail" => $mail, "password" => $password, "errors" => $errors]);
    }

    public function signup() : void {
        $mail = '';
        $fullname = '';
        $password = '';
        $password_confirm = '';
        $errors = [];

        if (isset($_POST['mail']) && isset($_POST['fullname']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $mail = trim($_POST['mail']);
            $fullname = trim($_POST['fullname']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($mail, Tools::my_hash($password), $fullname, Role::USER);
            $errors = Member::validate_unicity($pseudo);
            $errors = array_merge($errors, $member->validate());
            $errors = array_merge($errors, Member::validate_passwords($password, $password_confirm));

            if (count($errors) == 0) {
                $member->persist(); //sauve l'utilisateur
                $this->log_user($member);
            }
        }
        (new View("signup"))->show(["pseudo" => $pseudo, "password" => $password,
            "password_confirm" => $password_confirm, "errors" => $errors]);
    }

}