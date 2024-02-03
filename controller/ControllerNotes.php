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
        if ($other_notes instanceof Note) {
            $current_note->persist($other_notes);
        }
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
        $result = [];
        $result["success"] = NULL;
        $result["errors"] = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'])) {
                $title = trim($_POST['title']);
                $text = isset($_POST['text']) ? trim($_POST['text']) : "";
                $weight = $user->get_heaviest_note() + 1;
                $new_text_note = new TextNote($title, $user, false, false, $weight, $text);
                $note = $new_text_note->persist();

                if (!($note instanceof TextNote)) {
                    $result["errors"] = $note;
                    $default_title = $title;
                    $default_text = $text;
                } else {
                    $result["success"] = "The note has been added successfully.";
                }
            } else {
                "Les parametre ne sont pas définis.";
            }
        }

        (new View("add_text_note"))->show(["result" => $result, "default_title" => $default_title, "default_text" => $default_text]);
    }

    public function add_checklist_note(): void {

        $user = $this->get_user_or_redirect();
        $default_title = "";
        $default_text = "";
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validated_items = [];
            $items_list = [];
            for ($i = 1; $i < 6; $i++) {
                if (isset($_POST['item' . $i])) {
                    $item_content = trim($_POST['item' . $i]);
                    if ($item_content !== '' && in_array($item_content, $validated_items)) {
                        $errors['item' . $i][] = "Items must be unique.";
                        $prevItemKey = array_search($item_content, $validated_items) + 1;
                        $errors['item' . $prevItemKey][] = "Items must be unique.";
                        unset($validated_items[$prevItemKey-1]);
                    } else {
                        if($item_content !== '') {
                            $validated_items[] = $item_content;
                        }
                        $new_checklist_item = new ChecklistNoteItems($item_content, false);
                        if($new_checklist_item->getContent() !== '') {
                            $items_list[] = $new_checklist_item;
                        }
                        if(!empty($valid = $new_checklist_item->validate())) {
                            $errors['item' . $i] = $valid;
                        }
                    }
                }
            }
            if (isset($_POST['title'])) {
                $title = trim($_POST['title']);

                $weight = $user->get_heaviest_note() + 1;
                $note = new ChecklistNote($title, $user, false, false, $weight);
                if(!empty($valid = $note->validate())) {
                    $errors['title'] = $valid;
                }
                if (empty($errors)) {
                    if (!($note->persist() instanceof ChecklistNote)) {
                        $errors['note'][] = "Error while creating checklist_note.";
                    }
                    if(!empty($validated_items)) {
                        if (empty($errors['note'])) {
                            foreach ($items_list as $item) {
                                $item->set_checklist_note($note->getId());
                                $item->persist();
                            }
                        }
                    }
                    $this->redirect("notes", "open_note", $note->getId());
                }
            } else {
                $errors['title'][] = "Title must be defined.";
            }
        }
        (new View("add_checklist_note"))->show(["errors" => $errors, "default_title" => $default_title, "default_text" => $default_text]);
    }

    private function validateUniqueItem(ChecklistNote $checklistNote, string $content): bool {
        $existingItems = $checklistNote->getItems();
        foreach ($existingItems as $item) {
            if ($item->getContent() === $content && $item->getContent() !== '') {
                return false;
            }
        }
        return true;
    }

    public function edit_checklist_note (): void{
        $noteId = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $note = Note::get_note($noteId);
        $items = ChecklistNoteItems::get_items_by_checklist_note_id($noteId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        }
        (new View("edit_checklist_note"))->show([
            'note' => $note,
            'items' => $items,
        ]);
    }


    public function open_note(): void
    {
        $noteId = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $userId = $user->getId();
        $error = "";

        $note = Note::get_note($noteId);
        if (!$note) {
            $error = "Note introuvable.";
        } else {
            $isSharedNote = NoteShare::isNoteSharedWithUser($noteId, $userId);
            $canEdit = True;
            if ($isSharedNote) {
                $canEdit = NoteShare::canEdit($noteId, $userId);
            }

            $canAccess = ($note->getOwner()->getId() === $userId) || $isSharedNote;
            if (!$canAccess) {
                $error = "Accès non autorisé.";
            } else {
                $id_sender = $note->getOwner()->getId();
                $isChecklistNote = Note::is_checklist_note($noteId);
                if ($isChecklistNote) {
                    $checklistItems = ChecklistNoteItems::get_items_by_checklist_note_id($noteId);
                } else {
                    $text = TextNote::get_text_note($noteId);
                }
                $headerType = 'notes';
                if ($isSharedNote) {
                    $headerType = 'shared_by';
                } elseif ($note->isArchived()) {
                    $headerType = 'archives';
                }
            }
        }
        (new View("open_note"))->show([
            'error' => $error,
            'note' => $note,
            'headerType' => $headerType ?? null,
            'canEdit' => $canEdit ?? false,
            'text' => $text ?? null,
            'id_sender' => $id_sender ?? null,
            'checklistItems' => $checklistItems ?? null,
            'isChecklistNote' => $isChecklistNote ?? false
        ]);
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

        $this->refresh();
    }
    public function delete()
    {
        $user = $this->get_user_or_redirect();

        if (isset($_POST['note_id'])) {
            $noteId = $_POST['note_id'];
            $note = Note::get_note($noteId);

            if ($note && $note->deleteAll($user)) {
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
