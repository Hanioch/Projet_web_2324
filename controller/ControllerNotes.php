<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once "model/NoteShare.php";
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

    public function add_text_note(): void
    {
        $user = $this->get_user_or_redirect();
        $default_title = "";
        $default_text = "";
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'])) {
                $title = trim($_POST['title']);
                $text = isset($_POST['text']) ? trim($_POST['text']) : "";
                $weight = $user->get_heaviest_note() + 1;
                $new_text_note = new TextNote($title, $user, false, false, $weight, $text);
                $note = $new_text_note->persist();

                if (!($note instanceof TextNote)) {
                    $errors = $note;
                    $default_title = $title;
                    $default_text = $text;
                }
            } else {
                "Les parametre ne sont pas définis.";
            }
        }

        (new View("add_text_note"))->show(["errors" => $errors, "default_title" => $default_title, "default_text" => $default_text]);
    }

    public function add_checklist_note(): void
    {
        $user = $this->get_user_or_redirect();
        $default_title = "";
        $default_text = "";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'])) {
                $title = trim($_POST['title']);

                $weight = $user->get_heaviest_note() + 1;
                $new_checklist_note = new ChecklistNote($title, $user, false, false, $weight);
                $note = $new_checklist_note->persist();

                // Vérifiez si la note a été correctement ajoutée avant d'ajouter les éléments
                if ($note instanceof ChecklistNote) {
                    // Parcourez les éléments de la liste et créez un enregistrement dans la table checklist_note_items pour chacun
                    for ($i = 0; $i < 5; $i++) {
                        if (isset($_POST['item' . $i])) {
                            $item_content = trim($_POST['item' . $i]);
                            $new_checklist_item = new ChecklistNoteItems($item_content, false, $note->id);
                            $item = $new_checklist_item->persist();
                            if (!$item instanceof ChecklistNoteItems) {
                                // Gérez les erreurs si la création de l'élément échoue
                                $errors[] = "Erreur lors de la création de l'élément de la liste de contrôle.";
                            }
                        }
                    }
                } else {
                    // Gérez les erreurs si la création de la note échoue
                    $errors[] = "Erreur lors de la création de la note de liste de contrôle.";
                }
            } else {
                // Gérez les erreurs si le titre n'est pas défini
                $errors[] = "Le titre de la note de liste de contrôle n'est pas défini.";
            }
        }

        (new View("add_checklist_note"))->show(["errors" => $errors, "default_title" => $default_title, "default_text" => $default_text]);
    }


    public function open_note()
    {
        $noteId = $_GET['param1'];
        $noteType = $_GET['param2'];
        $userId = $this->get_user_or_redirect()->id;
        $note = Note::get_note($noteId);
        $text = TextNote::get_text_note($noteId);
        $id_sender = $note->owner->id;
        $checklistItems = ChecklistNoteItems::get_items_by_checklist_note_id($noteId);
        if ($noteType == 'shared_by') {
            $canEdit = NoteShare::canEdit($noteId, $userId);
        } else {
            $canEdit = 1;
        }
        if (!$note) {
            die("Note not found");
        }
        $isChecklistNote = Note::is_checklist_note($noteId);

        if ($isChecklistNote) {
            (new View("open_checklist_note"))->show(['note' => $note, 'noteType' => $noteType, 'canEdit' => $canEdit, 'text' => $text, 'id_sender' => $id_sender, 'checklistItems' => $checklistItems]);
        } else {

            (new View("open_text_note"))->show(['note' => $note, 'noteType' => $noteType, 'canEdit' => $canEdit, 'text' => $text, 'id_sender' => $id_sender]);
        }
    }
    public function togglePin()
    {
        $noteId = $_POST['note_id'];
        if ($noteId === null) {
        }

        $note = Note::get_note($noteId);
        if (!$note) {
        }

        $note->togglePin();

        $this->refresh();
    }
    public function toggleCheckbox()
    {
        $itemId = $_POST['item_id'];

        $item = ChecklistNoteItems::get_checklist_note_item_by_id($itemId);

        $item->toggleCheckbox();

        $this->refresh();
    }
    public function setArchive()
    {
        $noteId = $_POST['note_id'];
        if ($noteId === null) {
        }

        $note = Note::get_note($noteId);
        if (!$note) {
        }

        $note->setArchive();

        if ($note->archived) {
            $this->refresh("./open_note/$noteId/archives");
        } else {
            $this->refresh("./open_note/$noteId/notes");
        }
    }
    public function delete()
    {
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

    function refresh($url = null)
    {
        if ($url) {
            header('Location: ' . $url);
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
        exit;
    }
}
