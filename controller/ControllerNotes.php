<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerNotes extends Controller {

    public function index() : void {
        $this->note_list();
    }

    private function note_list() : void {
        $user = $this->get_user_or_redirect();
        $notes = $user->get_notes();
        var_dump($user);
        (new View("notes"))->show(["notes" => $notes]);
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
            $errors = User::validate_unicity($mail);
            $errors = array_merge($errors, User::validate_mail($mail));
            $errors = array_merge($errors, User::validate_password($password));

            if (count($errors) == 0) {
                $user->persist(); //sauve l'utilisateur
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(["mail" => $mail, "password" => $password,
            "password_confirm" => $password_confirm, "errors" => $errors]);
    }

}