<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerSignIn extends Controller
{

    public function index(): void
    {
        if ($this->user_logged()) {
            (new View("notes"))->show();
        } else {
            (new View("index"))->show();
        }
    }

    public function login(): void {
        $mail = '';
        $hashed_password = '';
        $errors = [];
        if (isset($_POST['mail']) && isset($_POST['hashed_password'])) {
            $mail = $_POST['mail'];
            $hashed_password = $_POST['hashed_password'];

            $errors = User::validate_login($mail, $hashed_password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_mail($mail));
            }
        }
        (new View("login"))->show(["mail" => $mail, "hashed_password" => $hashed_password, "errors" => $errors]);
    }
}