<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerSettings extends Controller
{


    public function index(): void
    {
        $this->redirect("Settings", "Settings");

    }
    public function settings(): void
    {
        $user = $this->get_user_or_redirect();

        (new View("settings"))->show(["user" => $user]);

    }

    public function logout_user():void{
        $this->logout();
    }
    public function test(): void
    {
        echo "<h1>Hello !</h1>";
    }
    public function edit_profile(): void
    {
        $user = $this->get_user_or_redirect();
        $full_name = "";
        $email = "";
        $errors = [
            "full_name" => [],
            "email" => []
        ];
        $success = (isset($_GET['param1']) && $_GET['param1'] == "ok") ? "Votre profil a été mis à jour avec succès." : '';
        $updateAttempted = false;

        if (isset($_POST['full_name'])) {
            $full_name = trim($_POST['full_name']);
            $errors = User::validate_full_name($full_name);
            $updateAttempted = true;

            if ($full_name != $user->get_Full_Name() && empty($errors["full_name"])) {
                $user->set_Full_Name($full_name);
                $user->persist();
                $success = "Votre profil a été mis à jour avec succès.";
            }
        }

        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
            $errors = User::validate_mail($email);
            $updateAttempted = true;

            if ($email != $user->get_Mail() && empty($errors["email"])) {
                $user->set_Mail($email);
                $user->persist();
                $success = "Votre profil a été mis à jour avec succès.";
            }
        }

        // Redirection après la mise à jour réussie
        if ($updateAttempted && empty($errors["full_name"]) && empty($errors["email"])) {
            $this->redirect("Settings", "edit_profile", "ok");
        }

        (new View("edit_profile"))->show(["user" => $user, "success" => $success, "full_name" => $full_name, "email" => $email, 'errors' => $errors]);
    }
    public function edit_profile1(): void
    {
        $user = $this->get_user_or_redirect();
        $full_name = "";
        $email = "";
        $errors = [
            "full_name" => [],
            "email" => []
        ];
        $success = (isset($_GET['param1']) && $_GET['param1'] == "ok") ? "Votre profil a été mis à jour avec succès." : '';
        if (isset($_POST['full_name'])) {
            $full_name = trim($_POST['full_name']);
            $errors = User::validate_full_name($full_name);

            if ($full_name != $user->get_Full_Name()) {

                if (empty($errors["full_name"])) {
                    $user->set_Full_Name($full_name);
                    $user->persist();
                }

                if (count($_POST) > 0 && empty($errors["full_name"])) {
                    $this->redirect("Settings", "edit_profile", "ok");
                }

            }

        }

        (new View("edit_profile"))->show(["user" => $user, "success" => $success, "full_name" => $full_name, 'errors' => $errors, 'email'=>$email]);

    }
    public function change_password(): void{
        $user = $this->get_user_or_redirect();
        $old_password = '';
        $password = '';
        $password_confirm = '';
        $errors = [
            "old_password" => [],
            "password" => [],
            "password_confirm" => []
        ];

        $success = (isset($_GET['param1']) && $_GET['param1'] == "ok") ? "Votre mot de passe a été mis à jour avec succès." : '';
        if (isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $old_password = $_POST['old_password'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $errors = array_merge($errors, User::change_password($old_password,$user));
            $errors = array_merge($errors, User::validate_password($password));
            $errors = array_merge($errors, User::validate_password_confirmation($password,$password_confirm));
            if ($old_password === $password) {
                $errors['password'][] = "Le nouveau mot de passe ne peut pas être identique à l'ancien mot de passe.";
            }

            if (empty($errors["old_password"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                $user->set_Hashed_Password(Tools::my_hash($password));
                $user->persist();
                $success = "Password updated successfully.";
            }

            if (count($_POST) > 0 && empty($errors["old_password"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                $this->redirect("Settings", "change_password", "ok");
            }

        }

        (new View("change_password"))->show([
            "user" => $user,
            "errors" => $errors,
            "success" => $success,
            "old_password" => $old_password,
            "password"=> $password,
            "password_confirm"=> $password_confirm
        ]);
    }

}