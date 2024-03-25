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
        $user = $this->get_user_or_redirect();
        $notes = $user->get_notes();
        $users_shared_notes = $user->get_users_shared_note();
        (new View("notes"))->show(["notes" => $notes, "users_shared_notes" => $users_shared_notes]);
    }

    public function move_note(): void
    {
        if (isset($_GET['param1']) && isset($_GET['param2'])) {
            $note_id = $_GET['param1'];
            $action = $_GET['param2'];

            if ($action === 'increment') {
                $this->modif_weight(true, $note_id);
            } elseif ($action === 'decrement') {
                $this->modif_weight(false, $note_id);
            }
        } else {
            $this->note_list();
        }
    }

    private function modif_weight(bool $is_more, int $note_id)
    {
        $current_note = Note::get_note($note_id);
        if ($current_note instanceof Note) {
            $other_notes = $current_note->get_nearest_note($is_more);
            if ($other_notes instanceof Note) {
                $current_note->persist($other_notes);
            }
        }
        $this->note_list();
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
            if ($sender instanceof User) {
                $notes_shared = $user->get_notes_shared_by($id_sender);
                $users_shared_notes = $user->get_users_shared_note();

                (new View("shared_notes"))->show(["notes_shared" => $notes_shared, "users_shared_notes" => $users_shared_notes, "sender" => $sender]);
            } else {
                (new View("error"))->show(["error" => "Page doesn't exist."]);
            }
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
                (new View("add_text_note"))->show(["result" => $result, "default_title" => $default_title, "default_text" => $default_text]);
            } else {
                $result["success"] = "The note has been added successfully.";
                $this->open_note($note->get_Id());
            }
        } else {
            (new View("add_text_note"))->show(["result" => $result, "default_title" => $default_title, "default_text" => $default_text]);
        }
    }

    public function add_checklist_note(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $items = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            if (empty($title)) {
                $errors['title'] = "Title must be defined.";
            }
            $weight = $user->get_heaviest_note() + 1;
            $note = new ChecklistNote($title, $user, false, false, $weight);
            if (!empty($valid = $note->validate())) {
                $errors['title'] = $valid;
            }
            $itemsInput = [];
            for ($i = 1; $i <= 5; $i++) {
                $itemContent = trim($_POST['item' . $i] ?? '');
                if (!empty($itemContent)) {
                    if (!array_key_exists($itemContent, $itemsInput)) {
                        $itemsInput[$itemContent] = [];
                    }
                    $itemsInput[$itemContent][] = $i;
                }
            }

            foreach ($itemsInput as $content => $indexes) {
                if (count($indexes) > 1) {
                    foreach ($indexes as $index) {
                        $errors['item' . $index] = "Item '$content' is duplicated.";
                    }
                }
            }

            if (empty($errors)) {
                $weight = $user->get_heaviest_note() + 1;
                $note = new ChecklistNote($title, $user, false, false, $weight);
                if ($note->persist()) {
                    foreach ($itemsInput as $content => $indexes) {
                        $checklistItem = new ChecklistNoteItems($content, false);
                        $checklistItem->set_checklist_note($note->get_Id());
                        $checklistItem->persist();
                    }
                    $this->redirect("notes", "open_note", $note->get_Id());
                } else {
                    $errors['note'] = "Error while creating checklist note.";
                }
            }
        }

        (new View("add_checklist_note"))->show([
            "user" => $user,
            "errors" => $errors,
            "default_title" => $title ?? '',
            "items" => $items,
        ]);
    }

    private function validateUniqueItem(ChecklistNote $checklistNote, string $content): bool
    {
        $existingItems = $checklistNote->get_Items();
        foreach ($existingItems as $item) {
            if ($item->get_Content() === $content && $item->get_Content() !== '') {
                return false;
            }
        }
        return true;
    }

    public function edit_checklist_note(): void
    {
        $errors = [];
        $shared_note_id = NULL;
        $note_id = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_Id();
        $note = ChecklistNote::get_note($note_id);
        $items = ChecklistNoteItems::get_items_by_checklist_note_id($note_id);

        //On verifie les erreurs. 
        if (!($note instanceof Note)) {
            //la checklist note n'existe pas.
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        foreach ($items as $item) {
            if (!($item instanceof ChecklistNoteItems)) {
                // un item n'existe pas.
                (new View("error"))->show(["error" => "Page doesn't exist."]);
                return;
            }
        }

        $is_shared_note = NoteShare::is_Note_Shared_With_User($note_id, $user_id);
        $can_edit = $is_shared_note ? NoteShare::can_Edit($note_id, $user_id)  : true;

        if ($is_shared_note) {
            $shared_note_id = $note->get_Owner()->get_Id();
        }

        $canAccess = ($note->get_Owner()->get_Id() === $user_id) || ($is_shared_note && $can_edit);
        if (!$canAccess) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $checklist_note = new ChecklistNote($note->get_Title(), $note->get_Owner(), $note->is_Pinned(), $note->is_Archived(), $note->get_Weight(), $note->get_Id());
            if (isset($_POST['save_button'])) {
                $errors = $this->edit_title($note, $errors);
            } else if (isset($_POST['add_button'])) {
                $errors = $this->add_item($checklist_note, $errors);
                if (empty($errors)) {
                    $items = ChecklistNoteItems::get_items_by_checklist_note_id($note_id);
                    $errors = array_merge($errors, $this->edit_title($note, $errors));
                }
            } else if (isset($_POST['remove_button'])) {
                $item = ChecklistNoteItems::get_checklist_note_item_by_id($_POST['remove_button']);
                $item->delete();
                $items = ChecklistNoteItems::get_items_by_checklist_note_id($note_id);
                $errors = $this->edit_title($note, $errors);
            }
            if (empty($errors) && isset($_POST['save_button'])) {
                $this->redirect("notes", "open_note", $note->get_Id());
            }
        }
        (new View("edit_checklist_note"))->show([
            'note' => $note,
            'items' => $items,
            'shared_note_id' => $shared_note_id,
            'errors' => $errors
        ]);
    }

    public function edit_title(Note $note, array $errors): array
    {
        if (isset($_POST['title'])) {
            $title = trim($_POST['title']);
            $note->set_Title($title);
            if (!($test = $note->persist()) instanceof Note) {
                $errors = $test;
            }
        }
        return $errors;
    }

    public function add_item(ChecklistNote $note, array $errors): array
    {
        $items = $note->get_Items();
        $string_items = [];
        foreach ($items as $i) {
            $string_items[] = $i->get_content();
        }

        if (isset($_POST['new_item'])) {
            if (trim($_POST['new_item']) == '') {
                $errors['new_item'] = "Item cannot be empty.";
            } else {
                $item = trim($_POST['new_item']);
                if (!($this->item_exists($string_items, $item))) {
                    $new_item = new ChecklistNoteItems($item, false, $note->get_Id());
                    $new_item->persist();
                } else {
                    $errors['new_item'] = "Item already exists.";
                }


                if (!($test = $note->persist()) instanceof Note) {
                    $errors = array_merge($errors, $test);
                }
            }
        }
        return $errors;
    }

    private function item_exists(array $items, string $item_content): bool
    {
        foreach ($items as $i) {
            if (strtoupper($i) === strtoupper($item_content)) {
                return true;
            }
        }
        return false;
    }

    public function edit_text_note(): void
    {
        $errors = [];
        $shared_note_id = NULL;

        if (!isset($_GET['param1'])) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        $note_id = $_GET['param1'];
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_Id();
        $note = TextNote::get_text_note($note_id);

        if (!($note instanceof TextNote)) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        $is_shared_note = NoteShare::is_Note_Shared_With_User($note_id, $user_id);
        $can_edit = $is_shared_note ? NoteShare::can_Edit($note_id, $user_id)  : true;

        if ($is_shared_note) {
            $shared_note_id = $note->get_Owner()->get_Id();
        }

        $canAccess = ($note->get_Owner()->get_Id() === $user_id) || ($is_shared_note && $can_edit);
        if (!$canAccess) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'])) {
                $title = trim($_POST['title']);
                $content = isset($_POST['text']) ? $_POST['text'] : "";
                if ($title == $note->get_Title() && $content == $note->get_Content()) {
                    $errors[] = "aucune modification apportée";
                } else {
                    $note->set_Title($title);
                    $note->set_Content($content);
                    $result = $note->persist();
                    if (!($result instanceof Note)) {
                        $errors = $result;
                    } else {
                        $this->redirect("notes", "open_note", $note->get_Id());
                    }
                }
            } else {
                "Les paramètres ne sont pas définis.";
            }
        }
        (new View("edit_text_note"))->show([
            'note' => $note,
            'shared_note_id' => $shared_note_id,
            'errors' => $errors
        ]);
    }


    public function open_note(int $id = -1): void
    {
        $noteId = $id !== -1 ? $id : filter_var($_GET['param1'], FILTER_VALIDATE_INT);
        $user = $this->get_user_or_redirect();
        $userId = $user->get_Id();
        $error = "";

        if ($noteId === false) {
            $error = "Identifiant de note invalide.";
        } else {
            $note = Note::get_note($noteId);
            if (!($note instanceof Note)) {
                $error = "Note introuvable.";
            } else {
                $isSharedNote = NoteShare::is_Note_Shared_With_User($noteId, $userId);
                $canEdit = True;
                if ($isSharedNote) {
                    $canEdit = NoteShare::can_Edit($noteId, $userId);
                }

                $canAccess = ($note->get_Owner()->get_Id() === $userId) || $isSharedNote;
                if (!$canAccess) {
                    $error = "Accès non autorisé.";
                } else {
                    $id_sender = $note->get_Owner()->get_Id();
                    $isChecklistNote = Note::is_checklist_note($noteId);
                    if ($isChecklistNote) {
                        $checklistItems = ChecklistNoteItems::get_items_by_checklist_note_id($noteId);
                    } else {
                        $text = TextNote::get_text_note($noteId);
                    }
                    $headerType = 'notes';
                    if ($isSharedNote) {
                        $headerType = 'shared_by';
                    } elseif ($note->is_Archived()) {
                        $headerType = 'archives';
                    }
                }
            }
        }
        (new View("open_note"))->show([
            'error' => $error,
            'note' => $note ?? null,
            'headerType' => $headerType ?? null,
            'canEdit' => $canEdit ?? false,
            'text' => $text ?? null,
            'id_sender' => $id_sender ?? null,
            'checklistItems' => $checklistItems ?? null,
            'isChecklistNote' => $isChecklistNote ?? false
        ]);
    }
    public function shares(): void
    {
        $noteId = filter_var($_GET['param1'], FILTER_VALIDATE_INT);
        $currentUser = $this->get_user_or_redirect();
        $currentUserId = $currentUser->get_Id();
        $error = "";
        $errorAdd = "";

        if ($noteId === false) {
            $error = "Identifiant de note invalide.";
        } else {
            $note = Note::get_note($noteId);
            if (isset($_POST['addShare'])) {
                $noteId = $_POST['noteId'];
                $userId = $_POST['user'] ?? null;
                $permission = $_POST['permission'] ?? null;
                if (!empty($userId) && $permission !== null) {
                    NoteShare::add_Share($noteId, $userId, $permission);
                    $this->refresh();
                    exit();
                } else {
                    $errorAdd = "Please select a user and a permission to share.";
                }
            }
            if (isset($_POST['changePermission'])) {
                $user = $_POST['user'];
                NoteShare::change_Permissions($noteId, $user);
                $this->refresh();
                exit();
            }
            if (isset($_POST['removeShare'])) {
                $user = $_POST['user'];
                NoteShare::remove_Share($noteId, $user);

                $this->refresh();
                exit();
            }
            if (!($note instanceof Note)) {
                $error = "Note introuvable.";
            } else {
                $canAccess = ($note->get_Owner()->get_Id() === $currentUserId);
                if (!$canAccess) {
                    $error = "Accès non autorisé.";
                } else {
                    $existingShares = NoteShare::get_Shared_With_User($currentUserId, $noteId);
                    $sharedUserIds = [];
                    foreach ($existingShares as $share) {
                        $sharedUserIds[] = $share['id'];
                    }

                    $allUsers = User::get_users();
                    $usersToShareWith = [];
                    foreach ($allUsers as $user) {
                        if ($user->get_Id() !== $currentUserId && !in_array($user->get_Id(), $sharedUserIds)) {
                            $usersToShareWith[] = $user;
                        }
                    }
                }
            }
        }

        (new View("shares"))->show([
            'usersToShareWith' => $usersToShareWith ?? null,
            'existingShares' => $existingShares ?? null,
            'noteId' => $noteId,
            'note' => $note ?? null,
            'currentUser' => $currentUser ?? null,
            'error' => $error,
            'errorAdd' => $errorAdd
        ]);
    }

    public function toggle_Pin()
    {
        $noteId = $_POST['note_id'];
        $note = Note::get_note($noteId);
        $note->toggle_Pin();
        $this->refresh();
    }
    public function toggle_Checkbox()
    {
        $itemId = $_POST['item_id'];
        $item = ChecklistNoteItems::get_checklist_note_item_by_id($itemId);
        $item->toggle_Checkbox();
        $this->refresh();
    }
    public function set_Archive()
    {
        $noteId = $_POST['note_id'];
        $note = Note::get_note($noteId);
        $note->set_Archive_reverse();
        $this->refresh();
    }
    public function delete()
    {
        $user = $this->get_user_or_redirect();

        if (isset($_POST['note_id'])) {
            $noteId = $_POST['note_id'];
            $note = Note::get_note($noteId);

            if ($note && $note->delete_All($user)) {
                $this->refresh("./archives");
            } else {

                $this->refresh("./index");
            }
        }
    }
    public function confirm_delete()
    {
        $noteId = $_POST['note_id'];
        $note = Note::get_note($noteId);

        (new View("confirm_delete"))->show(array("note" => $note));
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
