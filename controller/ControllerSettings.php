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

    public function test(): void
    {
        echo "<h1>Hello !</h1>";
    }
    public function edit_profile(): void
    {
        $user = $this->get_user_or_redirect();
        $full_name = "";
        $errors = [
            "full_name" => []
        ];
        $success = isset($_GET['param1']) ? "Votre profil a été mis à jour avec succès." : '';
        if (isset($_POST['full_name'])) {
            $full_name = trim($_POST['full_name']);
            $errors = User::validate_full_name($full_name);

            if ($full_name != $user->full_name) {

                if (empty($errors["full_name"])) {
                    $user->full_name = $full_name;
                    $user->persist();
                }

                if (count($_POST) > 0 && empty($errors["full_name"])) {
                    $this->redirect("Settings", "edit_profile", "ok");
                }

                if (isset($_POST['param1'])) {
                    $success = "Your profile has been successfully updated.";

                }

            }

        }


        (new View("edit_profile"))->show(["user" => $user, "success" => $success, "full_name" => $full_name, 'errors' => $errors]);

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
        $success = isset($_GET['param1']) ? "Votre mot de passe a été mis à jour avec succès." : '';

        if (isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $old_password = $_POST['old_password'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $errors = array_merge($errors, User::change_password($old_password,$user));
            $errors = array_merge($errors, User::validate_password($password));
            $errors = array_merge($errors, User::validate_password_confirmation($password,$password_confirm));


            if (empty($errors["old_password"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                $user->setPassword(Tools::my_hash($password));
                $user->persist();
                $success = "Password updated successfully.";
            }

            if (count($_POST) > 0 && empty($errors["old_password"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                $this->redirect("Settings", "change_password", "ok");
            }

            if (isset($_POST['param1'])) {
                $success = "Your profile has been successfully updated.";
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