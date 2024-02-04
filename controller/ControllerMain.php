<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller
{
    public function index(): void
    {
        if ($this->user_logged()) {
            $this->redirect("notes");
        } else {
            $this->redirect("main", "login");
        }
    }

    public function login(): void
    {
        if ($this->user_logged()) {
            $this->redirect("notes");
        }else{
            $mail = '';
            $password = '';
            $errors = [
                "mail" => [],
                "password" => []
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

    }

    public function signup(): void
    {
        if ($this->user_logged()) {
            $this->redirect("notes");
        }else{
            $mail = '';
            $full_name = '';
            $password = '';
            $password_confirm = '';
            $errors = [
                "mail" => [],
                "full_name" => [],
                "password" => [],
                "password_confirm" => []
            ];

            if (isset($_POST['mail']) && isset($_POST['full_name']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
                $mail = trim($_POST['mail']);
                $full_name = trim($_POST['full_name']);
                $password = $_POST['password'];
                $password_confirm = $_POST['password_confirm'];

                $user = new User($mail, Tools::my_hash($password), $full_name, Role::USER);
                $errors = array_merge($errors, User::validate_full_name($full_name));
                $errors = array_merge($errors, User::validate_mail($mail));
                $errors = array_merge($errors, User::validate_password($password));
                $errors = array_merge($errors, User::validate_password_confirmation($password, $password_confirm));

                if (empty($errors["mail"]) && empty($errors["full_name"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                    $user->persist();
                    $this->log_user($user);
                }
            }
            (new View("signup"))->show([
                "mail" => $mail,
                "full_name" => $full_name,
                "password" => $password,
                "password_confirm" => $password_confirm,
                "errors" => $errors
            ]);
        }


    }
}
