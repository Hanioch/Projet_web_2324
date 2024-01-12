<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerNotes extends Controller
{

    public function index(): void
    {
        $this->note_list();
    }

    private function note_list(): void
    {
        $user = $this->get_user_or_redirect();
        //        var_dump($this->get_user_or_redirect());
        $notes = $user->get_notes();
        $users_shared_notes = $user->get_users_shared_note();
        //        var_dump($notes);
        (new View("notes"))->show(["notes" => $notes, "users_shared_notes" => $users_shared_notes]);
    }

}
