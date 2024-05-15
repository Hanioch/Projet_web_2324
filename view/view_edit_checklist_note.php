<?php
$id_form = "form_edit_checklist_note";
$title_page = "edit_checklist_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

<div class="card-body">
    <span id="idNote" value="<?= $note->get_Id() ?>" style="display: none;"></span>
    <!-- Modal -->
    <div id="modalGoBack" class="modal fade" tabindex="-1" aria-labelledby="fullScreenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fullScreenModalLabel">Are you sure ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to quit without saving ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="<?= $back_url ?>">
                        <button type="button" class="btn btn-primary" id="validBtn">Yes!</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- fin modal -->
    <?php
    $action_form = "notes/edit_checklist_note/" . $note->get_Id();
    if ($is_list_filter_exist) $action_form .= "/" . $list_filter_encoded;
    ?>

    <form id="<?= $id_form ?>" class="p-3 text-white " action="<?= $action_form ?>" method="post">
        <div class="mb-3">
            <div class="card-header text-white mb-2 fst-italic fs-6">
                <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->get_Created_At()) ?> . </span>
                <?php if ($note->get_Edited_At() !== null) : ?>
                    <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->get_Edited_At()) ?>.</span>
                <?php endif; ?>
            </div>
            <div class="" id="title_div">
                <label for="titleNote" class="form-label">Title</label>
                <input type="text" value="<?php if (isset($_POST['title'])) {
                                                echo $_POST['title'];
                                            } else {
                                                echo $note->get_Title();
                                            } ?>" name="title" class="form-control  border-0 bg-secondary text-white bg-opacity-25" id="titleNote">
                <?php
                if (!empty($errors['title'])) {
                ?>
                    <span class="error-add-note" id="error_title_span">
                        <?php
                        echo $errors['title'];
                        ?>
                    </span>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="mb-3">
            <div class="">
                <label class="form-label mb-0">Items</label>
                <ul class="list-unstyled" id="list_items_ul">
                    <?php foreach ($items as $item) : ?>
                        <li class="list-unstyled" id="list_items_<?= $item->get_Id() ?>">
                            <div class="input-group pt-3 has-validation">
                                <div class="input-group-text bg-primary  border-secondary ">
                                    <input class="form-check-input border align-middle " type="checkbox" name="checked" value="1" <?= $item->is_Checked() ? 'checked' : '' ?> aria-label="Checkbox for following text input" disabled> <!-- onchange="this.form.submit()" -->
                                </div>
                                <input <?php if (!empty($errors['item' . $item->get_Id()])) { echo 'value="' . ($_POST['item' . $item->get_Id()]) . '"'; } else { echo 'value="' . $item->get_Content() . '"';} ?> type="text" name="item<?php echo $item->get_Id() ?>" class="item-editable form-control bg-secondary text-white bg-opacity-25 border-secondary" id="item<?php echo $item->get_Id() ?>" value="<?php echo isset($_POST['item' . $item->get_Id()]) ? $_POST['item' . $item->get_Id()] : ''; ?>">
                                <button name="remove_button" value="<?= $item->get_Id() ?>" class="btn btn-danger btn-lg rounded-end  border-secondary" type="submit">
                                    <i class="bi bi-x"></i>
                                </button>
                                <input type="hidden" name="item_id" value="<?= $item->get_Id() ?>">
                                <input type="hidden" name="note_id" value="<?= $note->get_Id() ?>">
                            </div>
                            <?php if (isset($errors['item' . $item->get_Id()])) : ?>
                                <div id="error_text_<?= $item->get_Id() ?>" class="error-add-note pt-1">
                                    <?php foreach ($errors['item' . $item->get_Id()] as $error) {
                                        echo $error;
                                    } ?></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <label for="add_item" class="form-label">New Item</label>
        <div class="input-group">
            <input <?php if (!empty($errors['new_item'])) {
                        echo 'value="' . $_POST['new_item'] . '" class="form-control bg-secondary text-white bg-opacity-25 border-secondary is-invalid"';
                    } else {
                        echo 'value=""';
                    } ?> type="text" name="new_item" class="form-control bg-secondary text-white bg-opacity-25 border-secondary" id="add_item">
            <button id="add_button" name="add_button" class="btn btn-primary btn-lg rounded-end  border-secondary" type="submit">
                <i class="bi bi-plus"></i>
            </button>
        </div>
        <div id="new_item_error_div">
            <?php if (isset($item) && isset($errors['new_item'])) : ?>
                <span class="error-add-note" id="new_item_error"><?php echo $errors['new_item'] ?></span>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    const pageName = "editChecklistnote";
    const urlToRedirect = "<?= $back_url ?>"
    const noteId = <?= $note->get_Id() ?>;
</script>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="js/scriptEditChecklistNote.js"></script>
<script src="js/scriptModalEdit.js"></script>

<?php include('./utils/footer.php'); ?>