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
        //TODOOOO le faire fonctionner avec le persist.
        // $modif = $current_note->persist($other_notes);
        $current_note->move_note_in_DB($other_notes);
    }

    public function archives(): void
    {
        $user = $this->get_user_or_redirect();
        //        var_dump($this->get_user_or_redirect());
        $notes_archives = $user->get_notes_archives();
        $users_shared_notes = $user->get_users_shared_note();

        //        var_dump($notes);
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

    public function signup(): void
    {
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
        (new View("signup"))->show([
            "mail" => $mail, "password" => $password,
            "password_confirm" => $password_confirm, "errors" => $errors
        ]);
    }
}
