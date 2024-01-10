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
            $this->redirect("main", "edit_profile");
        } else {
            $this->redirect("main", "login");
        }
    }
    public function edit_profile(): void {
        $user = $this->get_user_or_redirect();
        $full_name ="";
        $errors = [
            "full_name"=>[]
        ];
        $success = isset($_GET['param1']) ? "Votre profil a été mis à jour avec succès." : '';
        if (isset($_POST['full_name'])) {
            $full_name = trim($_POST['full_name']);
            $errors = User::validate_full_name($full_name);

            if($full_name != $user->full_name){

                if(empty($errors["full_name"])){
                    $user->full_name = $full_name;
                    $user->persist();
                }

                if (count($_POST) > 0 && empty($errors["full_name"])){
                    $this->redirect("main", "edit_profile", "ok");
                    echo "Redirecting to main/test?ok";
                }

                if (isset($_POST['param1'])){
                    $success = "Your profile has been successfully updated.";

                }

            }

        }


        (new View("edit_profile"))->show(["user" => $user, "success" => $success,"full_name"=> $full_name,'errors'=> $errors]);

    }
    public function test() : void {
        echo "<h1>Hello !</h1>";
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
        $full_name = '';
        $password = '';
        $password_confirm = '';
        $errors = [
            "mail"=>[],
            "full_name"=>[],
            "password"=>[],
            "password_confirm"=>[]
        ];

        if (isset($_POST['mail']) && isset($_POST['full_name']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $mail = trim($_POST['mail']);
            $full_name = trim($_POST['full_name']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($mail, Tools::my_hash($password), $full_name, Role::USER);
            //$errors = array_merge($errors, User::validate_unicity($mail));
            $errors = array_merge($errors, User::validate_full_name($full_name));
            $errors = array_merge($errors, User::validate_mail($mail));
            $errors = array_merge($errors, User::validate_password($password));
            $errors = array_merge($errors, User::validate_password_confirmation($password,$password_confirm));

            if (empty($errors["mail"]) && empty($errors["full_name"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                $user->persist(); //sauve l'utilisateur
                $this->log_user($user);
            }
        }
        (new View("signup"))->show([
            "mail" => $mail,
            "full_name" => $full_name,
            "password" => $password,
            "password_confirm" => $password_confirm,
            "errors" => $errors]);
    }

}