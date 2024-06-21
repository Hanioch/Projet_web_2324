<?php

require_once 'model/User.php';
require_once 'model/Label.php';
require_once "model/NoteShare.php";
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once "framework/Utils.php";
class ControllerSession1 extends Controller
{
    public function index(): void
    {
        $user_id = isset($_POST['selected_user']) ? (int)$_POST['selected_user'] : null;

        if ($user_id) {
            $this->redirect("session1", "index",$user_id);
        } else {
            $this->session1();
        }
    }

    private function session1( ): void
    {
        $user_id = isset($_GET['param1']) ? (int)$_GET['param1'] : null;
        $current_user = $this->get_user_or_redirect();
        $users = User::get_users();
        $notes = [];

        if ($user_id !== null) {
            $user = User::get_user_by_id($user_id);
            if ($user) {
                $notes = $user->get_notes();
            }
        }

        (new View("session1"))->show([
            "notes" => $notes,
            "users" => $users
        ]);
    }
}
?>