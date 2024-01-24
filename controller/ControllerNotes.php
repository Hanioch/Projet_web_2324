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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && isset($_POST['id'])) {
                $action = $_POST['action'];
                $note_id = $_POST['id'];

                if ($action === 'increment') {
                    $this->modif_weight(true, $note_id);
                } elseif ($action === 'decrement') {
                    $this->modif_weight(false, $note_id);
                }
            }
        }

        $user = $this->get_user_or_redirect();
        $notes = $user->get_notes();
        $users_shared_notes = $user->get_users_shared_note();
        (new View("notes"))->show(["notes" => $notes, "users_shared_notes" => $users_shared_notes]);
    }

    private function modif_weight(bool $is_more, int $note_id)
    {
        $current_note = Note::get_note($note_id);
        $other_notes = $current_note->get_nearest_note($is_more);
        $current_note->persist($other_notes);
    }

    public function archives(): void
    {
        $user = $this->get_user_or_redirect();
        $notes_archives = $user->get_notes_archives();
        $users_shared_notes = $user->get_users_shared_note();
        (new View("archives"))->show(["notes_archives" => $notes_archives, "users_shared_notes" => $users_shared_notes]);
    }

    public function shared_by(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1'])) {
            $id_sender = $_GET['param1'];
            $sender = User::get_user_by_id($id_sender);
            $notes_shared = $user->get_notes_shared_by($id_sender);
            $users_shared_notes = $user->get_users_shared_note();

            (new View("shared_notes"))->show(["notes_shared" => $notes_shared, "users_shared_notes" => $users_shared_notes, "sender" => $sender]);
        } else {
            echo "Les paramètres ne sont pas définis.";
            print_r($_GET);
        }
    }

    public function open_note(){
        $noteId = $_GET['param1'];
        $noteType = $_GET['param2'];
        $note = Note::get_note($noteId);
        if (!$note) {
            die("Note not found");
        }
        $isChecklistNote = Note::is_checklist_note($noteId);

        if ($isChecklistNote) {
            (new View("open_checklist_note"))->show(['note' => $note, 'noteType' => $noteType]);
        } else {

            (new View("open_text_note"))->show(['note' => $note, 'noteType' => $noteType]);
        }
    }
    public function togglePin() {
        $noteId = $_POST['note_id'];
        if ($noteId === null) {
        }

        $note = Note::get_note($noteId);
        if (!$note) {

        }

        $note->togglePin();

        $this->refresh();
    }
    public function setArchive() {
        $noteId = $_POST['note_id'];
        if ($noteId === null) {
        }

        $note = Note::get_note($noteId);
        if (!$note) {

        }

        $note->setArchive();

        if($note->archived) {
            $this->refresh("./open_note/$noteId/archives");
        }else{
            $this->refresh("./open_note/$noteId/notes");
        }


    }
    public function delete() {
        $user = $this->get_user_or_redirect();

        if (isset($_POST['note_id'])) {
            $noteId = $_POST['note_id'];
            $note = Note::get_note($noteId);

            if ($note && $note->delete($user)) {
                $this->refresh("./archives");
            } else {

                $this->refresh("./index");
            }
        }
    }
    function refresh($url = null) {
        if ($url) {
            header('Location: ' . $url);
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        exit;
    }

}
