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
            $this->redirect("main", "test");
        } else {
            $this->redirect("main", "login");
        }
    }
    public function test(): void {
        echo "<h1>Test !</h1>";
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
        $errors = [
            "mail"=>[],
            "fullname"=>[],
            "password"=>[],
            "password_confirm"=>[]
        ];

        if (isset($_POST['mail']) && isset($_POST['fullname']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $mail = trim($_POST['mail']);
            $fullname = trim($_POST['fullname']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($mail, Tools::my_hash($password), $fullname, Role::USER);
            $errors = User::validate_unicity($mail);
            $errors = array_merge($errors, User::validate_fullname($fullname));
            $errors = array_merge($errors, User::validate_mail($mail));
            $errors = array_merge($errors, User::validate_password($password));
            $errors = array_merge($errors, User::validate_password_confirmation($password,$password_confirm));

            if (count($errors) == 0) {
                $user->persist(); //sauve l'utilisateur
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(["mail" => $mail, "password" => $password,
            "password_confirm" => $password_confirm, "errors" => $errors]);
    }

}