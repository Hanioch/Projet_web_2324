<?php

require_once 'model/User.php';
require_once 'model/Label.php';
require_once "model/NoteShare.php";
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once "framework/Utils.php";
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

    public function back_note_list(): void
    {
        $this->redirect("notes");
    }

    public function move_note_service(): void
    {
        $user = $this->get_user_or_redirect();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            echo "Error : invalid method.";
            return;
        }

        if (!(isset($_POST["idNoteMoved"])
            && isset($_POST["idReplacedNote"])
            && isset($_POST["switchedColumn"]))) {
            echo "Error : invalid arguments.";
            return;
        }
        //si noteMoved > alors on prend tout ce qui est plus petit jusqua replacedNote
        //si note Moved< alors on prend tout ce qui est plus grand 
        $id_note_moved = intval($_POST["idNoteMoved"]);
        $id_replaced_note = intval($_POST["idReplacedNote"]);
        $is_switched_column = filter_var($_POST["switchedColumn"], FILTER_VALIDATE_BOOLEAN);

        $note_moved = $user->get_note_by_id($id_note_moved);
        $replaced_note = NULL;
        $is_note_moved_pinned = $note_moved->is_pinned();

        if ($is_switched_column) {
            $note_moved->set_pinned(!$is_note_moved_pinned);
            $note_moved->persist_head();
            $is_note_moved_pinned = $note_moved->is_pinned();
        }

        $weight_replaced_note = "";
        if ($id_replaced_note === 0) {
            // on recupere la note la plus élévée+1 pour que, plus bas, il prenne en compte
            // tout notes comprises entre la note qu'on déplace et toute les notes plus hautes.
            $weight_replaced_note = $user->get_heaviest_note($is_note_moved_pinned) + 1;
        } else {
            $replaced_note = $user->get_note_by_id($id_replaced_note);
            $weight_replaced_note = $replaced_note->get_weight();
        }

        $this->move_all_notes_between($note_moved, $weight_replaced_note);
    }

    public function move_all_notes_between(Note $note_moved, int $weight_replaced_note)
    {
        $user = $this->get_user_or_redirect();
        $weight_moved_note =  $note_moved->get_weight();
        $is_note_moved_pinned = $note_moved->is_pinned();

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
    public function move_all_archived_notes_between(Note $note_moved, int $weight_replaced_note)
    {
        $user = $this->get_user_or_redirect();
        $weight_moved_note =  $note_moved->get_weight();

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note_id = $_POST['id'];
            $action = $_POST['action'];

            if ($action === 'increment') {
                $this->modif_weight(true, $note_id);
            } elseif ($action === 'decrement') {
                $this->modif_weight(false, $note_id);
            }
        }
        $this->redirect("notes");
    }

    private function modif_weight(bool $is_more, int $note_id)
    {
        $current_note = Note::get_note($note_id);
        if ($current_note instanceof Note) {
            $other_note = $current_note->get_nearest_note($is_more);
            if ($other_note instanceof Note) {
                $weight_current = $current_note->get_weight();
                $weight_other = $other_note->get_weight();
                $user = $this->get_user_or_redirect();

                $other_note->set_weight($user->get_heaviest_note() + 1);
                $other_note->persist_head();
                $current_note->set_weight($weight_other);
                $current_note->persist_head();
                $other_note->set_weight($weight_current);
                $other_note->persist_head();
            }
        }
    }

    private function modif_archived_weight(int $note_id)
    {
        $current_note = Note::get_note($note_id);
        if ($current_note instanceof Note) {
            $other_note = $current_note->get_nearest_archived_note();
            if ($other_note instanceof Note) {
                $weight_current = $current_note->get_weight();
                $weight_other = $other_note->get_weight();
                $user = $this->get_user_or_redirect();

                $other_note->set_weight($user->get_heaviest_note() + 1);
                $other_note->persist_head();
                $current_note->set_weight($weight_other);
                $current_note->persist_head();
                $other_note->set_weight($weight_current);
                $other_note->persist_head();
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

    public function search(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $list_filter = [];

            foreach ($_POST as $filter => $value) {
                if ($value === "on") {
                    $list_filter[] = $filter;
                }
            }

            if (count($list_filter) > 0) {
                $list_filter_encoded = Utils::url_safe_encode($list_filter);
                $this->redirect("notes", "search", $list_filter_encoded);
            } else
                $this->redirect("notes", "search");
        } else {
            $list_filter = isset($_GET['param1']) ? Utils::url_safe_decode($_GET['param1']) : [];
            if ($list_filter === NULL) $list_filter = [];

            $user = $this->get_user_or_redirect();
            $notes_searched["personal"] = count($list_filter) === 0 ? [] : $user->get_notes_searched($list_filter);
            $users_shared_notes = $user->get_users_shared_note();

            $list_label = $user->get_filter_list();
            $new_list_label = [];
            foreach ($list_label as $label) {
                $checked = false;
                foreach ($list_filter as $filter) {
                    if ($filter === $label) {
                        $checked = true;
                    }
                }
                $new_list_label[$label] = $checked;
            }

            $notes_searched["shared"] = [];

            foreach ($users_shared_notes as $u) {
                $note_by_someone = $user->get_notes_with_label_shared_by($u->get_id(), $list_filter);
                if (count($note_by_someone) > 0) {
                    $notes_searched["shared"][$u->get_full_name()] = $note_by_someone;
                }
            }
            (new View("search"))->show(["notes_searched" => $notes_searched, "users_shared_notes" => $users_shared_notes, "list_label" => $new_list_label]);
        }
    }
    public function search_service(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $list_filter = [];
            foreach ($_POST as $filter => $value) {
                if ($value === "on") {
                    $list_filter[] = $filter;
                }
            }

            $data_to_update = $this->get_data_to_update($list_filter);
            echo json_encode($data_to_update);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Method Not Allowed"]);
        }
    }
    public function get_labels_service(): void
    {
        $note_id = intval($_POST['note_id'] ?? 0);
        if ($note_id !== 0) {
            $labels = Label::get_labels_by_note_id($note_id);
            echo json_encode($labels);
        }
    }
    private function get_data_to_update(array $list_filter): array
    {
        if (count($list_filter) > 0) {
            $list_filter_encoded = Utils::url_safe_encode($list_filter);
        }
        $user = $this->get_user_or_redirect();
        $notes_searched["personal"] = $user->get_notes_searched($list_filter);
        $users_shared_notes = $user->get_users_shared_note();

        $list_label = $user->get_filter_list();
        $new_list_label = [];
        foreach ($list_label as $label) {
            $checked = false;
            foreach ($list_filter as $filter) {
                if ($filter === $label) {
                    $checked = true;
                }
            }
            $new_list_label[$label] = $checked;
        }

        $notes_searched["shared"] = [];

        foreach ($users_shared_notes as $u) {
            $note_by_someone = $user->get_notes_with_label_shared_by($u->get_id(), $list_filter);
            if (count($note_by_someone) > 0) {
                $notes_searched["shared"][$u->get_full_name()] = $note_by_someone;
            }
        }
        $data_to_update = [
            "notes_searched" => $notes_searched,
            "users_shared_notes" => $users_shared_notes,
            "list_label" => $new_list_label,
            "list_filter_encoded" => $list_filter_encoded ?? null,
        ];

        return $data_to_update;
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
            echo "Parameters are not defined.";
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
                $this->redirect("notes", "open_note", $note->get_id());
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
            $items_input = [];
            for ($i = 1; $i <= 5; $i++) {
                $item_content = trim($_POST['item' . $i] ?? '');
                if (!empty($item_content)) {
                    $test = new ChecklistNoteItem($item_content, false);
                    if (!empty($test->validate())) {
                        $errors['item' . $i] = ($test->validate())[0];
                    }
                    if (!array_key_exists($item_content, $items_input)) {
                        $items_input[$item_content] = [];
                    }
                    $items_input[$item_content][] = $i;
                }
            }

            foreach ($items_input as $content => $indexes) {
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
                    foreach ($items_input as $content => $indexes) {
                        $checklist_item = new ChecklistNoteItem($content, false);
                        $checklist_item->set_checklist_note($note->get_id());
                        $checklist_item->persist();
                    }
                    $this->redirect("notes", "open_note", $note->get_id());
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

    private function validate_unique_item(ChecklistNote $checklist_note, string $content): bool
    {
        $existing_items = $checklist_note->get_list_item();
        foreach ($existing_items as $item) {
            if ($item->get_content() === $content && $item->get_content() !== '') {
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
        $user_id = $user->get_id();
        $note = ChecklistNote::get_note($note_id);
        $items = $note->get_list_item();

        //On verifie les erreurs. 
        if (!($note instanceof Note)) {
            //la checklist note n'existe pas.
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        foreach ($items as $item) {
            if (!($item instanceof ChecklistNoteItem)) {
                // un item n'existe pas.
                (new View("error"))->show(["error" => "Page doesn't exist."]);
                return;
            }
        }

        $is_shared_note = NoteShare::is_note_shared_with_user($note_id, $user_id);
        $can_edit = $is_shared_note ? NoteShare::can_edit($note_id, $user_id)  : true;

        if ($is_shared_note) {
            $shared_note_id = $note->get_owner()->get_id();
        }

        $can_access = ($note->get_owner()->get_id() === $user_id) || ($is_shared_note && $can_edit);
        if (!$can_access) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $checklist_note = new ChecklistNote($note->get_title(), $note->get_owner(), $note->is_pinned(), $note->is_archived(), $note->get_weight(), $note->get_id());
            if (isset($_POST['save_button'])) {
                $errors = $this->edit_title($note, $errors);
                $errors = array_merge($errors, $this->edit_items($note, $errors));
            } else if (isset($_POST['add_button'])) {
                $errors = $this->add_item($checklist_note, $errors);
                if (empty($errors)) {
                    $items = $checklist_note->get_list_item();
                    $errors = array_merge($errors, $this->edit_title($note, $errors));

                    $is_list_filter_exist = isset($_GET["param2"]);
                    $list_filter_encoded = $is_list_filter_exist ? $_GET["param2"] : "";

                    if ($is_list_filter_exist)
                        $this->redirect("notes", "edit_checklist_note", $note->get_id(), $list_filter_encoded);
                    else
                        $this->redirect("notes", "edit_checklist_note", $note->get_id());
                }
            } else if (isset($_POST['remove_button'])) {
                $item = ChecklistNoteItem::get_checklist_note_item_by_id($_POST['remove_button']);
                $item->delete();
                $items = $checklist_note->get_list_item();
                $errors = $this->edit_title($note, $errors);

                $is_list_filter_exist = isset($_GET["param2"]);
                $list_filter_encoded = $is_list_filter_exist ? $_GET["param2"] : "";

                if ($is_list_filter_exist)
                    $this->redirect("notes", "edit_checklist_note", $note->get_id(), $list_filter_encoded);
                else
                    $this->redirect("notes", "edit_checklist_note", $note->get_id());
            }
            $note = ChecklistNote::get_note($note_id);

            if (empty($errors) && isset($_POST['save_button'])) {

                $is_list_filter_exist = isset($_GET["param2"]);
                $list_filter_encoded = $is_list_filter_exist ? $_GET["param2"] : "";

                if ($is_list_filter_exist)
                    $this->redirect("notes", "open_note", $note->get_id(), $list_filter_encoded);
                else
                    $this->redirect("notes", "open_note", $note->get_id());
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
            $note->set_title($title);
            $note_copy = $note;
            if (!($test = $note_copy->persist()) instanceof Note) {
                $errors = $test;
            }
        }
        return $errors;
    }

    public function add_item(ChecklistNote $note, array $errors): array
    {
        $items = $note->get_list_item();
        $string_items = [];
        foreach ($items as $i) {
            $string_items[] = $i->get_content();
        }

        if (isset($_POST['new_item'])) {
            if (trim($_POST['new_item']) == '') {
                $errors['new_item'] = "Item cannot be empty.";
            } else {
                $item = trim($_POST['new_item']);
                $new_item = new ChecklistNoteItem($item, false, $note->get_id());
                if (!empty($test = $new_item->validate())) {
                    $errors['new_item'] = $test[0];
                }
                if (!($this->item_exists($string_items, $item))) {
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
        $checklist_note = new ChecklistNote($note->get_title(), $note->get_owner(), $note->is_pinned(), $note->is_archived(), $note->get_weight(), $note->get_id());
        $current_items = $checklist_note->get_list_item();
        $new_note = clone $checklist_note;
        $new_items = $new_note->get_list_item();
        $string_new_items = [];
        /** @var  ChecklistNoteItem $i*/
        foreach ($new_items as $i) {
            $id = $i->get_id();
            if (isset($_POST['item' . $id])) {
                $i->set_content($_POST['item' . $id]);
                $string_new_items[] = $i->get_content();

                if (trim($_POST['item' . $id]) == '') {
                    $errors['item' . $id][] = "Item cannot be empty.";
                } else {
                    $item = trim($_POST['item' . $id]);
                    if (!empty($test = $i->validate())) {
                        $errors['item' . $id][] = $test[0];
                    }
                    if (true !== ($duplicates = $this->is_unique($i, $new_items))) {
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

    private function is_unique(ChecklistNoteItem $i, array $items): bool | array
    {
        $count = 0;
        $res = [];
        /** @var ChecklistNoteItem $item */
        foreach ($items as $item) {
            if ($item->get_content() === $i->get_content()) {
                $count++;
                $res[] = $item->get_id();
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
        $user_id = $user->get_id();
        $note = TextNote::get_text_note($note_id);

        if (!($note instanceof TextNote)) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        $is_shared_note = NoteShare::is_note_shared_with_user($note_id, $user_id);
        $can_edit = $is_shared_note ? NoteShare::can_edit($note_id, $user_id)  : true;

        if ($is_shared_note) {
            $shared_note_id = $note->get_owner()->get_id();
        }

        $can_access = ($note->get_owner()->get_id() === $user_id) || ($is_shared_note && $can_edit);
        if (!$can_access) {
            (new View("error"))->show(["error" => "Page doesn't exist."]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['title'])) {
                $title = trim($_POST['title']);
                $content = isset($_POST['text']) ? $_POST['text'] : "";
                if ($title == $note->get_title() && $content == $note->get_content()) {
                    $errors['title'] = "No modification to save.";
                } else {
                    $note->set_title($title);
                    $note->set_content($content);
                    $result = $note->persist();
                    if (!($result instanceof Note)) {
                        $errors = $result;
                    } else {
                        $is_list_filter_exist = isset($_GET["param2"]);
                        $list_filter_encoded = $is_list_filter_exist ? $_GET["param2"] : "";

                        if ($is_list_filter_exist)
                            $this->redirect("notes", "open_note", $note->get_Id(), $list_filter_encoded);
                        else
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
        $note_id = isset($_GET['param1']) ? filter_var($_GET['param1'], FILTER_VALIDATE_INT) : false;
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_id();
        $error = "";
        $note = Note::get_note($note_id);

        if ($note === false) $this->redirect("notes", "archives");

        if ($note_id === false) {
            $error = "Cannot find Note ID";
        } else {
            $is_checklist_note = $note->is_checklist_note();
            $note = null;
            if ($is_checklist_note) {
                $note = ChecklistNote::get_note($note_id);
            } else {
                $note = TextNote::get_note($note_id);
            }

            if (!($note instanceof Note)) {
                $error = "Cannot find this Note.";
            } else {
                $is_shared_note = NoteShare::is_note_shared_with_user($note_id, $user_id);
                $can_edit = True;
                if ($is_shared_note) {
                    $can_edit = NoteShare::can_edit($note_id, $user_id);
                }

                $can_access = ($note->get_owner()->get_id() === $user_id) || $is_shared_note;
                if (!$can_access) {
                    $error = "Unauthorized access.";
                } else {
                    $id_sender = $note->get_owner()->get_id();
                    if ($is_checklist_note) {
                        $note->fetch_list_item();
                        $checklist_items = $note->get_list_item();
                    } else {
                        $text = TextNote::get_text_note($note_id);
                    }
                    $header_type = 'notes';
                    if ($is_shared_note) {
                        $header_type = 'shared_by';
                    } elseif ($note->is_archived()) {
                        $header_type = 'archives';
                    }
                }
            }
        }
        (new View("open_note"))->show([
            'error' => $error,
            'note' => $note ?? null,
            'header_type' => $header_type ?? null,
            'can_edit' => $can_edit ?? false,
            'text' => $text ?? null,
            'id_sender' => $id_sender ?? null,
            'checklist_items' => $checklist_items ?? null,
            'is_checklist_note' => $is_checklist_note ?? false
        ]);
    }
    public function shares(): void
    {
        $note_id = $_GET['param1'];
        $current_user = $this->get_user_or_redirect();
        $current_user_id = $current_user->get_id();
        $error = "";
        $error_add = "";

        if ($note_id === false) {
            $error = "Cannot find Note ID";
        } else {
            $note = Note::get_note($note_id);
            if (isset($_POST['addShare'])) {
                $note_id = $_POST['note_id'];
                $user_id = $_POST['user'] ?? null;
                $permission = $_POST['permission'] ?? null;
                if (!empty($user_id) && $permission !== null) {
                    NoteShare::add_Share($note_id, $user_id, $permission);
                    $this->redirect("notes", "shares/$note_id");
                    exit();
                } else {
                    $error_add = "Please select a user and a permission to share.";
                }
            }
            if (isset($_POST['changePermission'])) {
                $user = $_POST['user'];
                NoteShare::change_permissions($note_id, $user);
                $this->redirect("notes", "shares/$note_id");
                exit();
            }
            if (isset($_POST['removeShare'])) {
                $user = $_POST['user'];
                NoteShare::remove_share($note_id, $user);

                $this->redirect("notes", "shares/$note_id");
                exit();
            }
            if (!($note instanceof Note)) {
                $error = "Cannot find this Note";
            } else {
                $can_access = ($note->get_Owner()->get_Id() === $current_user_id);
                if (!$can_access) {
                    $error = "Unauthorized access.";
                } else {
                    $existing_shares = NoteShare::get_shared_with_user($current_user_id, $note_id);
                    $shared_user_ids = [];
                    foreach ($existing_shares as $share) {
                        $shared_user_ids[] = $share['id'];
                    }

                    $all_users = User::get_users();
                    $users_to_share_with = [];
                    foreach ($all_users as $user) {
                        if ($user->get_id() !== $current_user_id && !in_array($user->get_id(), $shared_user_ids)) {
                            $users_to_share_with[] = $user;
                        }
                    }
                }
            }
        }

        (new View("shares"))->show([
            'users_to_share_with' => $users_to_share_with ?? null,
            'existing_shares' => $existing_shares ?? null,
            'note_id' => $note_id,
            'note' => $note ?? null,
            'current_user' => $current_user ?? null,
            'error' => $error,
            'error_add' => $error_add
        ]);
    }
    public function refresh_share_service(int $note_id): void
    {
        $current_user = $this->get_user_or_redirect();
        $current_user_id = $current_user->get_id();

        $existing_shares = NoteShare::get_shared_with_user($current_user_id, $note_id);
        $shared_user_ids = [];
        foreach ($existing_shares as $share) {
            $shared_user_ids[] = $share['id'];
        }

        $all_users = User::get_users();
        $users_to_share_with = [];
        foreach ($all_users as $user) {
            if ($user->get_id() !== $current_user_id && !in_array($user->get_id(), $shared_user_ids)) {
                $users_to_share_with[$user->get_id()] = [
                    'full_name' => $user->get_full_name(),
                    'note_id' => $note_id
                ];
            }
        }
        $response_data = [
            'existingShares' => $existing_shares,
            'usersToShareWith' => $users_to_share_with
        ];

        echo json_encode($response_data);
    }
    public function add_share_service(): void
    {
        $note_id = $_POST['noteId'] ?? null;
        $user_id = $_POST['userId'] ?? null;
        $permission = $_POST['permission'] ?? null;

        if (isset($note_id) && isset($user_id) && isset($permission)) {
            if (NoteShare::add_share($note_id, $user_id, $permission)) {
                $this->refresh_share_service($note_id);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to add share"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
        }
    }
    public function remove_share_service(): void
    {
        $note_id = $_POST['noteId'] ?? null;
        $user_id = $_POST['userId'] ?? null;

        if (isset($note_id) && isset($user_id)) {
            if (NoteShare::remove_share($note_id, $user_id)) {
                $this->refresh_share_service($note_id);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to remove share"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
        }
    }

    public function change_permission_service(): void
    {
        $note_id = $_POST['noteId'] ?? null;
        $user_id = $_POST['userId'] ?? null;

        if (isset($note_id) && isset($user_id)) {
            if (NoteShare::change_permissions($note_id, $user_id)) {
                $this->refresh_share_service($note_id);
            } else {
                echo json_encode(["success" => false, "error" => "Failed to remove share"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
        }
    }

    public function toggle_pin()
    {
        $user = $this->get_user_or_redirect();
        $note_id = $_POST['note_id'];
        $note = Note::get_note($note_id);
        $note->toggle_pin();

        $new_weight = $user->get_heaviest_note($note->is_pinned()) + 1;

        $this->move_all_notes_between($note, $new_weight);

        $this->redirect("notes", "open_note/$note_id");
    }
    public function toggle_checkbox()
    {
        $note_id = $_POST['note_id'];
        $item_id = $_POST['item_id'];
        $item = ChecklistNoteItem::get_checklist_note_item_by_id($item_id);

        $item->toggle_checkbox();
        $this->redirect("notes", "open_note/$note_id");
    }

    public function toggle_checkbox_service()
    {
        $note_id = $_POST['note_id'];
        $item_id = $_POST['item_id'];
        $cln = ChecklistNote::get_note($note_id);
        $item = ChecklistNoteItem::get_checklist_note_item_by_id($item_id);
        $item->toggle_checkbox();
        $cln->fetch_list_item();
        $items = $cln->get_list_item();
        $table = [];
        /** @var CheckListNoteItem $i */
        foreach ($items as $i) {
            $row = [];
            $row["content"] = $i->get_content();
            $row["checked"] = $i->is_checked();
            $row["checklist_note"] = $i->get_checklist_note();
            $row["id"] = $i->get_id();
            $row["success"] = true;
            $table[] = $row;
        }

        echo json_encode($table);
    }

    public function edit_item_service()
    {
        $note_id = $_POST['note_id'];
        $item_id = $_POST['item_id'];
        $note = ChecklistNote::get_note($note_id);

        $errors = $this->edit_items($note, []);
        $item = ChecklistNoteItem::get_checklist_note_item_by_id($item_id);

        $row = [];
        $row["content"] = $item->get_content();
        $row["checked"] = $item->is_checked();
        $row["checklist_note"] = $item->get_checklist_note();
        $row["id"] = $item->get_id();
        $row["errors"] = $errors;

        echo json_encode($row);
    }

    public function check_new_item_service()
    {
        $content = $_POST['content'];
        $note_id = $_POST['note_id'];
        $new_item = new ChecklistNoteItem($content, false, $note_id);
        $new_item_id = $new_item->get_id();
        $note = ChecklistNote::get_by_id($note_id);
        $errors = [];
        $note->fetch_list_item();
        $items = $note->get_list_item();

        if (!empty($test = $new_item->validate())) {
            $errors['new_item'] = $test[0];
        } else {
            /** @var $i ChecklistNoteItem */
            foreach ($items as $i) {
                if (strtoupper($i->get_content()) === strtoupper($content)) {
                    $errors['new_item'] = "Item already exists.";
                } else if (trim($content) === "") {
                    $errors['new_item'] = "Item cannot be empty.";
                }
            }
        }

        echo json_encode($errors);
    }

    public function remove_item_service()
    {
        $item_id = $_POST['item_id'];
        $note_id = $_POST['note_id'];
        $note = ChecklistNote::get_note($note_id);
        $item = ChecklistNoteItem::get_checklist_note_item_by_id($item_id);
        $note->persist();
        $item->delete();
    }

    public function add_item_service()
    {
        $note_id = $_POST['note_id'];
        $note = ChecklistNote::get_note($note_id);
        $checklist_note = new ChecklistNote($note->get_title(), $note->get_owner(), $note->is_pinned(), $note->is_archived(), $note->get_weight(), $note->get_id());
        $errors = [];
        $this->add_item($checklist_note, $errors);
        $note->fetch_list_item();
        $items = $note->get_list_item();
        $table = [];
        /** @var CheckListNoteItem $i */
        foreach ($items as $i) {
            $row = [];
            $row["content"] = $i->get_content();
            $row["checked"] = $i->is_checked();
            $row["checklist_note"] = $i->get_checklist_note();
            $row["id"] = $i->get_id();
            $table[] = $row;
        }

        echo json_encode($table);
    }

    public function edit_title_service()
    {
        $note_id = $_POST['note_id'];
        $new_content = $_POST['title'];
        $note = ChecklistNote::get_note($note_id);
        $note->set_title($new_content);

        $errors = [];
        $errors = $note->validate();
        $row = [];
        $row["errors"] = $errors;

        echo json_encode($row);
    }

    public function set_archive()
    {
        $user = $this->get_user_or_redirect();
        $note_id = $_POST['note_id'];
        $note = Note::get_note($note_id);
        if (!$note) $this->redirect("notes", "archives");
        else $note->set_archive_reverse();

        if ($note->is_pinned()) {
            $note->toggle_pin();
        }

        if ($note->is_archived()) {
            $new_weight = $user->get_heaviest_note(NULL, $note->is_archived() + 1);
            $this->move_all_archived_notes_between($note, $new_weight);
        } else {
            $new_weight =  $user->get_heaviest_note(false) + 1;
            $this->move_all_notes_between($note, $new_weight);
        }

        $this->redirect("notes", "open_note/$note_id");
    }
    public function delete(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_POST['note_id'])) {
            $note_id = $_POST['note_id'];
            $note = Note::get_note($note_id);
            if ($note && $note->delete_all($user)) {
                $this->redirect("notes", "archives");
            } else {
                $this->redirect("notes");
            }
        }
    }
    public function confirm_delete(): void
    {
        $note_id = filter_var($_GET['param1'], FILTER_VALIDATE_INT);
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_id();
        $note = null;
        $error = "";
        if ($note_id === false) {
            $error = "Cannot find Note ID.";
        } else {
            $note = Note::get_note($note_id);
            if (!($note instanceof Note)) {
                $error = "Cannot find this Note.";
            } else {
                $can_access = ($note->get_owner()->get_id() === $user_id);
                if (!$can_access) {
                    $error = "Unauthorized access.";
                } elseif (!$note->is_archived()) {
                    $error = "Note must be archived before being deleted.";
                }
            }
        }
        $header_type = 'login';
        (new View("confirm_delete"))->show([
            "error" => $error,
            "note" => $note,
            "can_access" => $can_access,
            "header_type" => $header_type
        ]);
    }

    public function delete_service(): void
    {
        $note_id = filter_var($_POST['note_id'], FILTER_VALIDATE_INT);
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_id();
        $note = null;
        $error = "";

        if ($note_id === false) {
            $this->error_delete(400, "Cannot find Note ID.");
        } else {
            $note = Note::get_note($note_id);
            if (!($note instanceof Note)) {
                $this->error_delete(400, "Cannot find Note.");
            } else {
                $can_access = ($note->get_owner()->get_id() === $user_id);
                if (!$can_access) {
                    $this->error_delete(400, "Unauthorized access.");
                } elseif (!$note->is_archived()) {
                    $this->error_delete(400, "Note is not archived.");
                }
            }
        }

        $this->delete($note);
        $success = $note->delete_all($user);
        if (!$success) $this->error_delete(500, "Error while deleting Note.");
        else echo "Note has been deleted successfully.";
    }

    private function error_delete(int $status, string $message)
    {
        http_response_code($status);
        exit($message);
    }
    public function get_validation_rules_checklist_note_service(): void
    {
        $min_title_length = Configuration::get("note_title_min_length");
        $max_title_length = Configuration::get("note_title_max_length");
        $item_min_length = Configuration::get("item_min_length");
        $item_max_length = Configuration::get("item_max_length");


        $validation_rules = [
            'minTitleLength' => $min_title_length,
            'maxTitleLength' => $max_title_length,
            'itemMinLength' => $item_min_length,
            'itemMaxLength' => $item_max_length
        ];

        header('Content-Type: application/json');
        echo json_encode($validation_rules);
    }

    public function get_validation_rules_service(): void
    {
        $min_title_length = Configuration::get("note_title_min_length");
        $max_title_length = Configuration::get("note_title_max_length");
        $min_content_length = Configuration::get("note_min_length");
        $max_content_length = Configuration::get("note_max_length");

        $validation_rules = [
            'min_title_length' => $min_title_length,
            'max_title_length' => $max_title_length,
            'min_content_length' => $min_content_length,
            'max_content_length' => $max_content_length
        ];

        header('Content-Type: application/json');
        echo json_encode($validation_rules);
    }

    public function check_unique_title_service(): void
    {
        $title = $_POST['title'];
        $note_id = $_POST['note_id'];
        $user = $this->get_user_or_redirect();
        $user_id = $user->get_id();

        $is_unique =  Note::is_unique_title_ajax($title, $user_id, $note_id);
        header('Content-Type: application/json');
        echo json_encode(['unique' => $is_unique]);
    }
    private static function get_labels_to_suggest($labels_by_user, $labels): array
    {
        $res = [];
        foreach ($labels_by_user as $label) {
            $must_display = true;
            foreach ($labels as $l) {
                if ($label->get_label_name() === $l->get_label_name()) {
                    $must_display = false;
                    break;
                }
            }
            if ($must_display) {
                $res[] = $label->get_label_name();
            }
        }
        sort($res);
        return $res;
    }

    public function edit_labels(): void
    {
        $user = $this->get_user_or_redirect();
        $note_id = isset($_GET['param1']) ? filter_var($_GET['param1'], FILTER_VALIDATE_INT) : false;

        $errors = [];
        $error = "";
        if ($note_id === false) {
            $error = "Note ID invalid.";
        } else {
            $note = Note::get_note($note_id);
            $user_id = $user->get_id();
            $labels = Label::get_labels_by_note_id($note_id);
            $labels_by_user = Label::get_labels_by_user_id($user_id);
            $labels_to_suggest = $this->get_labels_to_suggest($labels_by_user, $labels);
            if (!($note instanceof Note)) {
                $error = "Cannot find Note.";
            } else {
                $is_shared_note = NoteShare::is_note_shared_with_user($note_id, $user_id);
                $can_edit = true;
                if ($is_shared_note) {
                    $can_edit = NoteShare::can_edit($note_id, $user_id);
                }
                $can_access = ($note->get_owner()->get_id() === $user_id) || $is_shared_note;
                if (!$can_access || !$can_edit) {
                    $error = "Unauthorized access.";
                } else {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (isset($_POST['remove_button'])) {
                            $label_name = ($_POST['remove_button']);
                            $label_to_delete = Label::get_label_by_note_id_and_label_name($note->get_id(), $label_name);
                            $label_to_delete->delete();
                            $labels = Label::get_labels_by_note_id($note->get_id());
                            $labels_by_user = Label::get_labels_by_user_id($user_id);
                            $labels_to_suggest = $this->get_labels_to_suggest($labels_by_user, $labels);
                            $this->redirect("notes", "edit_labels", $note_id);
                        } else if (isset($_POST['add_button']) && (trim(($_POST['new_label']) > 0) || ($_POST['new_label']) === "")) {
                            $label_name = Label::fix_label_format($_POST['new_label']);
                            $errors = Label::validate_label($label_name, $note_id);

                            if (empty($errors['label'])) {
                                $new_label = new Label($note_id, $label_name);
                                $new_label->persist();
                                $this->redirect("notes", "edit_labels", $note_id);
                            }
                        }
                    }
                }
            }
        }
        (new View("edit_labels"))->show([
            'note' => $note ?? null,
            'user' => $user,
            'note_id' => $note_id,
            'labels' => $labels ?? null,
            'labels_to_suggest' => $labels_to_suggest ?? null,
            'errors' => $errors,
            'error' => $error
        ]);
    }

    public function remove_label_service(): void
    {
        $user = $this->get_user_or_redirect();
        $note_id = $_POST['note_id'];
        $label_name = $_POST['label_name'];
        $label_to_delete = Label::get_label_by_note_id_and_label_name($note_id, $label_name);
        $label_to_delete->delete();

        $labels = Label::get_labels_by_note_id($note_id);
        $labels_by_user = Label::get_labels_by_user_id($user->get_id());
        $labels_to_suggest = $this->get_labels_to_suggest($labels_by_user, $labels);

        $table = [];
        /** @var Label $l */
        foreach ($labels as $l) {
            $table["labels"][] = $l->get_label_name();
        }
        foreach ($labels_to_suggest as $l) {
            $table["suggestions"][] = $l;
        }

        echo json_encode($table);
    }

    public function add_label_service()
    {
        $user = $this->get_user_or_redirect();
        $note_id = $_POST['note_id'];
        $new_label_name = Label::fix_label_format($_POST['new_label']);
        $label = new Label($note_id, $new_label_name);
        $errors = Label::validate_label($new_label_name, $note_id);
        if (empty($errors["label"])) {
            $label->persist();
        }

        $labels_to_display = Label::get_labels_by_note_id($note_id);
        $labels_by_user = Label::get_labels_by_user_id($user->get_id());
        $labels_to_suggest = $this->get_labels_to_suggest($labels_by_user, $labels_to_display);

        $table = [];
        /** @var Label $l */
        foreach ($labels_to_display as $l) {
            $table["labels"][] = $l->get_label_name();
        }
        foreach ($labels_to_suggest as $l) {
            $table["suggestions"][] = $l;
        }

        echo json_encode($table);
    }

    public function check_new_label_service()
    {
        $content = Label::fix_label_format($_POST['content']);
        $note_id = $_POST['note_id'];
        $errors = Label::validate_label($content, $note_id);

        echo json_encode($errors);
    }
}
