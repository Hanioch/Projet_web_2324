<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller {

    //si l'utilisateur est connecté, redirige vers les notes.
    //sinon, produit la vue de login.
    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("notes", "index");
        } else {
            (new View("signin"))->show();
        }
    }

    //gestion de la connexion d'un utilisateur
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