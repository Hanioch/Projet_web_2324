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
        $mail = "";
        $errors = [
            "full_name" => [],
            "mail" => []
        ];
        $changes_made = false;
        $success = (isset($_GET['param1']) && $_GET['param1'] == "ok") ? "Votre profil a été mis à jour avec succès." : '';

        if (isset($_POST['full_name'])) {
            $full_name = trim($_POST['full_name']);
            if($full_name != $user->get_full_name()){
                $errors = array_merge($errors, User::validate_full_name($full_name));
                if ( empty( $errors['full_name']) ){
                    $user->set_full_name($full_name);
                    $user->persist();
                    $changes_made = true;
                }
            }

        }

        if (isset($_POST['mail'])) {
            $mail = trim($_POST['mail']);
            if($mail != $user->get_mail()){
                $errors = array_merge($errors, User::validate_mail($mail));
                if (empty($errors['mail'])) {
                    $user->set_mail($mail);
                    $user->persist();
                    $changes_made = true;
                }
            }
        }

        if ($changes_made && empty($errors["full_name"]) && empty($errors["mail"])) {
            $this->redirect("Settings", "edit_profile", "ok");
        }

        (new View("edit_profile"))->show(["user" => $user, "success" => $success, "full_name" => $full_name, "mail" => $mail, 'errors' => $errors]);
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

        $success = (isset($_GET['param1']) && $_GET['param1'] == "ok") ? "Password has been updated successfully." : '';
        if (isset($_POST['old_password']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $old_password = $_POST['old_password'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $errors = array_merge($errors, User::change_password($old_password,$user));
            $errors = array_merge($errors, User::validate_password($password));
            $errors = array_merge($errors, User::validate_password_confirmation($password,$password_confirm));
            if ($old_password === $password) {
                $errors['password'][] = "New password cannot be identical to the old password";
            }

            if (empty($errors["old_password"]) && empty($errors["password"]) && empty($errors["password_confirm"])) {
                $user->set_hashed_password(Tools::my_hash($password));
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