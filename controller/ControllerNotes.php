<?php

require_once 'model/User.php';
require_once 'model/Label.php';
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

    public function move_note_js(): void
    {
        $user = $this->get_user_or_redirect();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo " erreur: methode non valide ";
            return;
        }

        if (!(isset($_POST["idNoteMoved"])
            && isset($_POST["idReplacedNote"])
            && isset($_POST["switchedColumn"]))) {
            echo "erreur: attributs non valide";
            return;
        }
        //si noteMoved > alors on prend tout ce qui est plus petit jusqua replacedNote
        //si note Moved< alors on prend tout ce qui est plus grand 
        $id_note_moved = intval($_POST["idNoteMoved"]);
        $id_replaced_note = intval($_POST["idReplacedNote"]);
        $is_switched_column = filter_var($_POST["switchedColumn"], FILTER_VALIDATE_BOOLEAN);

        $note_moved = $user->get_note_by_id($id_note_moved);
        $replaced_note = NULL;
        $is_note_moved_pinned = $note_moved->is_Pinned();

        if ($is_switched_column) {
            $note_moved->set_Pinned(!$is_note_moved_pinned);
            $note_moved->persist();
            $is_note_moved_pinned = $note_moved->is_Pinned();
        }

        $weight_replaced_note = "";
        if ($id_replaced_note === 0) {
            // on recupere la note la plus élévée+1 pour que, plus bas, il prenne en compte
            // tout notes comprises entre la note qu'on déplace et toute les notes plus hautes.
            $weight_replaced_note = $user->get_heaviest_note($is_note_moved_pinned) + 1;
        } else {
            $replaced_note = $user->get_note_by_id($id_replaced_note);
            $weight_replaced_note = $replaced_note->get_Weight();
        }

        $this->move_all_note_between($note_moved, $weight_replaced_note);
    }

    public function move_all_note_between(Note $note_moved, int $weight_replaced_note)
    {
        $user = $this->get_user_or_redirect();
        $weight_moved_note =  $note_moved->get_Weight();
        $is_note_moved_pinned = $note_moved->is_Pinned();

        $notes_to_move = $user->get_notes_with_weight_between(
            $weight_moved_note,
            $weight_replaced_note,
            $is_note_moved_pinned
        );

        if (empty($notes_to_move)) {
            return;
        }

        $first_is_biggest = $weight_moved_note > $weight_replaced_note;

        foreach ($notes_to_move as $note) {
            $this->modif_weight($first_is_biggest, $note["id"]);
        }
    }
    public function move_all_archived_note_between(Note $note_moved, int $weight_replaced_note)
    {
        $user = $this->get_user_or_redirect();
        $weight_moved_note =  $note_moved->get_Weight();

        $notes_to_move = $user->get_notes_archived_with_weight_between(
            $weight_moved_note,
            $weight_replaced_note
        );

        if (empty($notes_to_move)) {
            return;
        }

        foreach ($notes_to_move as $note) {
            $this->modif_archived_weight($note["id"]);
        }
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

            $this->note_list();
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
                $weight_current = $current_note->get_Weight();
                $weight_other = $other_notes->get_Weight();
                $user = $this->get_user_or_redirect();

                $other_notes->set_Weight($user->get_heaviest_note() + 1);
                $other_notes->persist();
                $current_note->set_Weight($weight_other);
                $current_note->persist();
                $other_notes->set_Weight($weight_current);
                $other_notes->persist();
            }
        }
    }

    private function modif_archived_weight(int $note_id)
    {
        $current_note = Note::get_note($note_id);
        if ($current_note instanceof Note) {
            $other_notes = $current_note->get_nearest_archived_note();
            if ($other_notes instanceof Note) {
                $weight_current = $current_note->get_Weight();
                $weight_other = $other_notes->get_Weight();
                $user = $this->get_user_or_redirect();

                $other_notes->set_Weight($user->get_heaviest_note() + 1);
                $other_notes->persist();
                $current_note->set_Weight($weight_other);
                $current_note->persist();
                $other_notes->set_Weight($weight_current);
                $other_notes->persist();
            }
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
        $error = [];

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
                (new View("add_text_note"))->show(["result" => $result, "default_title" => $default_title, "default_text" => $default_text, 'errors' => $errors]);
            } else {
                $result["success"] = "The note has been added successfully.";
                $this->redirect("notes", "open_note", $note->get_Id());
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
        $is_javascript_request = isset($_POST["noteId"]);
        $errors = [];
        $shared_note_id = NULL;
        $note_id = $is_javascript_request ? intval($_POST["noteId"]) : $_GET['param1'];
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
                $errors = array_merge($errors, $this->edit_items($note, $errors));
            } else if (isset($_POST['add_button'])) {
                $errors = $this->add_item($checklist_note, $errors);
                if (empty($errors)) {
                    $items = ChecklistNoteItems::get_items_by_checklist_note_id($note_id);
                    $errors = array_merge($errors, $this->edit_title($note, $errors));
                    $this->redirect("notes", "edit_checklist_note", $note->get_Id());
                }
            } else if (isset($_POST['remove_button'])) {
                $item = ChecklistNoteItems::get_checklist_note_item_by_id($_POST['remove_button']);
                $item->delete();
                $items = ChecklistNoteItems::get_items_by_checklist_note_id($note_id);
                $errors = $this->edit_title($note, $errors);
                $this->redirect("notes", "edit_checklist_note", $note->get_Id());
            }
            $note = ChecklistNote::get_note($note_id);
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

    public function edit_items(Note $note, array $errors): array
    {
        $checklist_note = new ChecklistNote($note->get_Title(), $note->get_Owner(), $note->is_Pinned(), $note->is_Archived(), $note->get_Weight(), $note->get_Id());
        $currentItems = $checklist_note->get_Items();
        $newNote = clone $checklist_note;
        $newItems = $newNote->get_Items();
        $stringNewItems = [];

        /** @var $i ChecklistNoteItems*/
        foreach ($newItems as $i) {
            $id = $i->get_Id();
            if (isset($_POST['item' . $id])) {
                $i->set_Content($_POST['item' . $id]);
                $stringNewItems[] = $i->get_content();

                if (trim($_POST['item' . $id]) == '') {
                    $errors['item' . $id][] = "Item cannot be empty.";
                } else {
                    $item = trim($_POST['item' . $id]);
                    if (true !== ($duplicates = $this->is_unique($i, $newItems))) {
                        foreach ($duplicates as $dup) {
                            if (empty($errors['item' . $dup])) {
                                $errors['item' . $dup][] = "Item already exists.";
                            }
                        }
                    } else {
                        $i->persist();
                    }


                    if (!($test = $note->persist()) instanceof Note) {
                        $errors = array_merge($errors, $test);
                    }
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

    private function is_unique(ChecklistNoteItems $i, array $items): bool | array
    {
        $count = 0;
        $res = [];
        /** @var ChecklistNoteItems $item */
        foreach ($items as $item) {
            if ($item->get_content() === $i->get_content()) {
                $count++;
                $res[] = $item->get_Id();
            }
        }
        if ($count < 2) {
            return true;
        } else {
            return $res;
        }
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
                    $errors['title'] = "aucune modification apportée";
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
                    $this->redirect("notes", "shares/$noteId");
                    exit();
                } else {
                    $errorAdd = "Please select a user and a permission to share.";
                }
            }
            if (isset($_POST['changePermission'])) {
                $user = $_POST['user'];
                NoteShare::change_Permissions($noteId, $user);
                $this->redirect("notes", "shares/$noteId");
                exit();
            }
            if (isset($_POST['removeShare'])) {
                $user = $_POST['user'];
                NoteShare::remove_Share($noteId, $user);

                $this->redirect("notes", "shares/$noteId");
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
    public function refresh_share_ajax(int $noteId): void
    {
        $currentUser = $this->get_user_or_redirect();
        $currentUserId = $currentUser->get_Id();

        $existingShares = NoteShare::get_Shared_With_User($currentUserId, $noteId);
        $sharedUserIds = [];
        foreach ($existingShares as $share) {
            $sharedUserIds[] = $share['id'];
        }

        $allUsers = User::get_users();
        $usersToShareWith = [];
        foreach ($allUsers as $user) {
            if ($user->get_Id() !== $currentUserId && !in_array($user->get_Id(), $sharedUserIds)) {
                $usersToShareWith[$user->get_Id()] = [
                    'full_name' => $user->get_Full_Name(),
                    'note_id' => $noteId
                ];
            }
        }
        $responseData = [
            'existingShares' => $existingShares,
            'usersToShareWith' => $usersToShareWith
        ];

        echo json_encode($responseData);
    }
    public function add_share_ajax(): void
    {
        $noteId = $_POST['noteId'] ?? null;
        $userId = $_POST['userId'] ?? null;
        $permission = $_POST['permission'] ?? null;

        if (isset($noteId) && isset($userId) && isset($permission)) {
            if (NoteShare::add_Share($noteId, $userId, $permission)) {
                $this->refresh_share_ajax($noteId);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to add share"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
        }
    }
    public function remove_share_ajax(): void
    {
        $noteId = $_POST['noteId'] ?? null;
        $userId = $_POST['userId'] ?? null;

        if (isset($noteId) && isset($userId)) {
            if (NoteShare::remove_Share($noteId, $userId)) {
                $this->refresh_share_ajax($noteId);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to remove share"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
        }
    }

    public function change_permission_ajax(): void
    {
        $noteId = $_POST['noteId'] ?? null;
        $userId = $_POST['userId'] ?? null;

        if (isset($noteId) && isset($userId)) {
            if (NoteShare::change_Permissions($noteId, $userId)) {
                $this->refresh_share_ajax($noteId);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to remove share"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
        }
    }

    public function toggle_Pin()
    {
        $user = $this->get_user_or_redirect();
        $noteId = $_POST['note_id'];
        $note = Note::get_note($noteId);
        $note->toggle_Pin();

        $new_weight = $user->get_heaviest_note($note->is_Pinned()) + 1;

        $this->move_all_note_between($note, $new_weight);

        $this->redirect("notes", "open_note/$noteId");
    }
    public function toggle_Checkbox()
    {
        $noteId = $_POST['note_id'];
        $itemId = $_POST['item_id'];
        $item = ChecklistNoteItems::get_checklist_note_item_by_id($itemId);

        $item->toggle_Checkbox();
        $this->redirect("notes", "open_note/$noteId");
    }

    public function toggle_checkbox_service()
    {
        $noteId = $_POST['note_id'];
        $itemId = $_POST['item_id'];
        $item = ChecklistNoteItems::get_checklist_note_item_by_id($itemId);
        $item->toggle_Checkbox();
        $items = ChecklistNoteItems::get_items_by_checklist_note_id($noteId);
        $table = [];
        /** @var CheckListNoteItems $i */
        foreach ($items as $i) {
            $row = [];
            $row["content"] = $i->get_content();
            $row["checked"] = $i->is_Checked();
            $row["checklist_note"] = $i->get_ChecklistNote();
            $row["id"] = $i->get_Id();
            $table[] = $row;
        }

        echo json_encode($table);
    }

    public function edit_item_service()
    {
        $noteId = $_POST['note_id'];
        $itemId = $_POST['item_id'];
        $note = ChecklistNote::get_note($noteId);

        $errors = $this->edit_items($note, []);
        $item = ChecklistNoteItems::get_checklist_note_item_by_id($itemId);

        $row = [];
        $row["content"] = $item->get_content();
        $row["checked"] = $item->is_Checked();
        $row["checklist_note"] = $item->get_ChecklistNote();
        $row["id"] = $item->get_Id();
        $row["errors"] = $errors;


        echo json_encode($row);
    }

    public function check_new_item_service()
    {
        $content = $_POST['content'];
        $noteId = $_POST['note_id'];
        $newItem = new ChecklistNoteItems($content, false, $noteId);
        $newItemId = $newItem->get_Id();
        $note = ChecklistNote::get_by_id($noteId);
        $errors = [];
        $items = $note->get_Items();

        /** @var $i ChecklistNoteItems*/
        foreach ($items as $i) {
            if(strtoupper($i->get_content()) === strtoupper($content)) {
                $errors['new_item'] = "Item already exists.";
            } else if (trim($content) === "") {
                $errors['new_item'] = "Item cannot be empty.";
            }
        }

        echo json_encode($errors);
    }

    public function remove_item_service()
    {
        $itemId = $_POST['item_id'];
        $noteId = $_POST['note_id'];
        $note = ChecklistNote::get_note($noteId);
        $item = ChecklistNoteItems::get_checklist_note_item_by_id($itemId);

        $item ->delete();
    }

    public function add_item_service()
    {
        $noteId = $_POST['note_id'];
        $note = ChecklistNote::get_note($noteId);
        $checklist_note = new ChecklistNote($note->get_Title(), $note->get_Owner(), $note->is_Pinned(), $note->is_Archived(), $note->get_Weight(), $note->get_Id());
        $errors = [];
        $this->add_item($checklist_note, $errors);

        $items = ChecklistNoteItems::get_items_by_checklist_note_id($noteId);
        $table = [];
        /** @var CheckListNoteItems $i */
        foreach ($items as $i) {
            $row = [];
            $row["content"] = $i->get_content();
            $row["checked"] = $i->is_Checked();
            $row["checklist_note"] = $i->get_ChecklistNote();
            $row["id"] = $i->get_Id();
            $table[] = $row;
        }

        echo json_encode($table);
    }

    public function edit_title_service()
    {
        $noteId = $_POST['note_id'];
        $newContent = $_POST['title'];
        $note = ChecklistNote::get_note($noteId);

        $errors = [];
        $errors = $this->edit_title($note, $errors);

        $row = [];
        $row["errors"] = $errors;


        echo json_encode($row);
    }

    public function set_Archive()
    {
        $user = $this->get_user_or_redirect();
        $noteId = $_POST['note_id'];
        $note = Note::get_note($noteId);
        $note->set_Archive_reverse();

        if ($note->is_Pinned()) {
            $note->toggle_Pin();
        }

        if ($note->is_Archived()) {
            $new_weight = $user->get_heaviest_note(NULL, $note->is_Archived() + 1);
            $this->move_all_archived_note_between($note, $new_weight);
        } else {
            $new_weight =  $user->get_heaviest_note(false) + 1;
            $this->move_all_note_between($note, $new_weight);
        }

        $this->redirect("notes", "open_note/$noteId");
    }
    public function delete()
    {
        $user = $this->get_user_or_redirect();

        if (isset($_POST['note_id'])) {
            $noteId = $_POST['note_id'];
            $note = Note::get_note($noteId);

            if ($note && $note->delete_All($user)) {
                $this->redirect("notes", "archives");
            } else {
                $this->redirect("notes");
            }
        }
    }
    public function confirm_delete(): void
    {
        $noteId = filter_var($_GET['param1'], FILTER_VALIDATE_INT);
        $user = $this->get_user_or_redirect();
        $userId = $user->get_Id();
        $note = null;
        $error = "";

        if ($noteId === false) {
            $error = "Identifiant de note invalide.";
        } else {
            $note = Note::get_note($noteId);
            if (!($note instanceof Note)) {
                $error = "Note introuvable.";
            } else {
                $canAccess = ($note->get_Owner()->get_Id() === $userId);
                if (!$canAccess) {
                    $error = "Accès non autorisé.";
                } elseif (!$note->is_Archived()) {
                    $error = "Note non archivée.";
                }
            }
        }
        $headerType = 'login';
        (new View("confirm_delete"))->show([
            "error" => $error,
            "note" => $note,
            "canAccess" => $canAccess,
            "headerType" => $headerType
        ]);
    }

    public function delete_using_js(): void
    {
        $note_id = filter_var($_POST['idNote'], FILTER_VALIDATE_INT);
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_Id();
        $note = null;
        $error = "";

        if ($note_id === false) {
            $this->error_delete(400, "Identifiant de note invalide.");
        } else {
            $note = Note::get_note($note_id);
            if (!($note instanceof Note)) {
                $this->error_delete(400, "Note introuvable");
            } else {
                $canAccess = ($note->get_Owner()->get_Id() === $user_id);
                if (!$canAccess) {
                    $this->error_delete(400, "Accès non autorisé.");
                } elseif (!$note->is_Archived()) {
                    $this->error_delete(400, "Note non archivée.");
                }
            }
        }

        $this->delete($note);
        $success = $note->delete_All($user);
        if (!$success) $this->error_delete(500, "erreur lors de la suppression");
        else echo "la note à bien été supprimée";
    }

    private function error_delete(int $status, string $message)
    {
        http_response_code($status); // Code d'erreur HTTP approprié
        exit($message);
    }
    public function getValidationRules(): void
    {
        $config = parse_ini_file('config/dev.ini', true);

        $minTitleLength = $config['Rules']['note_title_min_length'];
        $maxTitleLength = $config['Rules']['note_title_max_length'];
        $minContentLength = $config['Rules']['note_min_length'];
        $maxContentLength = $config['Rules']['note_max_length'];

        $validationRules = [
            'minTitleLength' => $minTitleLength,
            'maxTitleLength' => $maxTitleLength,
            'minContentLength' => $minContentLength,
            'maxContentLength' => $maxContentLength
        ];

        header('Content-Type: application/json');
        echo json_encode($validationRules);
    }
    public function checkUniqueTitle(): void
    {
        $title = $_POST['title'];
        $noteId = $_POST['noteId'];
        $user = $this->get_user_or_redirect();
        $userId = $user->get_Id();

        $isUnique =  Note::is_unique_title_ajax($title, $userId, $noteId);
        header('Content-Type: application/json');
        echo json_encode(['unique' => $isUnique]);
    }
    private static function get_labels_to_suggest($labelsByUser, $labels): array {
        $res = [];
        foreach($labelsByUser as $label){
            $mustDisplay = true;
            foreach($labels as $l) {
                if($label->get_label_name() === $l->get_label_name()) {
                    $mustDisplay = false;
                    break;
                }
            }
            if($mustDisplay) {
                $res[] = $label->get_label_name();
            }
        }
        sort($res);
        return $res;
    }

    public function edit_labels(): void
    {
        $user = $this->get_user_or_redirect();
        $noteId = filter_var($_GET['param1'], FILTER_VALIDATE_INT);
        $note = ChecklistNote::get_note($noteId);
        $labels = Label::get_labels_by_note_id($noteId);
        $labelsByUser = Label::get_labels_by_user_id($user->get_Id());
        $labelsToSuggest = $this->get_labels_to_suggest($labelsByUser, $labels);
        $errors = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['remove_button'])) {
                $labelName = ($_POST['remove_button']);
                $labelToDelete = Label::get_label_by_note_id_and_label_name($note->get_Id(), $labelName);
                $labelToDelete->delete();
                $labels = Label::get_labels_by_note_id($note->get_Id());
                $labelsByUser = Label::get_labels_by_user_id($user->get_Id());
                $labelsToSuggest = $this->get_labels_to_suggest($labelsByUser, $labels);
                $this->redirect("notes", "edit_labels", $noteId);
            } else if (isset($_POST['add_button']) && (trim(($_POST['new_label']) > 0) || ($_POST['new_label']) === "")) {
                $labelName = ($_POST['new_label']);
                $errors = Label::validate_label($labelName, $noteId);

                if (empty($errors['label'])) {
                    $newLabel = new Label($noteId, $labelName);
                    $newLabel->persist();
                    $this->redirect("notes", "edit_labels", $noteId);
                }
            }
        }

        (new View("edit_labels"))->show([
            'note' => $note,
            'user' => $user,
            'note_id' => $noteId,
            'labels' => $labels,
            'labels_to_suggest' => $labelsToSuggest,
            'errors' => $errors
        ]);
    }

    public function remove_label_service(): void {
        $user = $this->get_user_or_redirect();
        $noteId = $_POST['note_id'];
        $labelName = $_POST['label_name'];
        $labelToDelete = Label::get_label_by_note_id_and_label_name($noteId, $labelName);
        $labelToDelete->delete();

        $labels = Label::get_labels_by_note_id($noteId);
        $labelsByUser = Label::get_labels_by_user_id($user->get_Id());
        $labelsToSuggest = $this->get_labels_to_suggest($labelsByUser, $labels);

        $table = [];
        /** @var Label $l */
        foreach ($labels as $l) {
            $table["labels"][] = $l->get_label_name();
        }
        foreach ($labelsToSuggest as $l) {
            $table["suggestions"][] = $l;
        }

        echo json_encode($table);
    }

    public function add_label_service()
    {
        $user = $this->get_user_or_redirect();
        $noteId = $_POST['note_id'];
        $newLabelName = $_POST['new_label'];
        $label = new Label($noteId, $newLabelName);
        $label->persist();

        $labelsToDisplay = Label::get_labels_by_note_id($noteId);
        $labelsByUser = Label::get_labels_by_user_id($user->get_Id());
        $labelsToSuggest = $this->get_labels_to_suggest($labelsByUser, $labelsToDisplay);

        $table = [];
        /** @var Label $l */
        foreach ($labelsToDisplay as $l) {
            $table["labels"][] = $l->get_label_name();
        }
        foreach ($labelsToSuggest as $l) {
            $table["suggestions"][] = $l;
        }

        echo json_encode($table);
    }

}
