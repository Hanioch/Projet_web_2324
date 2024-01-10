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

}