<?php
$id_form = "form_edit_text_note";
$title_page = "edit_text_note";
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
                    <p>Do you really want to quit without save ?</p>
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
    $action_form = "notes/edit_text_note/" . $note->get_Id();
    if ($is_list_filter_exist) $action_form .= "/" . $list_filter_encoded;
    ?>

    <form id="<?= $id_form ?>" action="<?= $action_form ?>" method="post">
        <div class="card-header text-white mb-2 fst-italic fs-6">
            <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->get_Created_At()) ?> . </span>
            <?php if ($note->get_Edited_At() !== null) : ?>
                <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->get_Edited_At()) ?>.</span>
            <?php endif; ?>
        </div>

        <div class="mb-3 text-white">
            <div class="">
                <label for="titleNote" class="form-label">Title</label>
                <input required type="text" value="<?= $note->get_Title() ?>" name="title" class="form-control bg-secondary text-white bg-opacity-25 mb-2 <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" id="titleNote">
                <div class="invalid-feedback" id="title_error">
                    <?php
                    if (!empty($errors['title'])) {
                    ?>
                        <?= $errors['title'] ?>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="mb-3 text-white">
            <label for="contentNote" class="form-label">Text</label>
            <textarea name="text" class="form-control bg-secondary text-white bg-opacity-25 <?= !empty($errors['content']) ? 'is-invalid' : '' ?>" id="contentNote" cols="30" rows="10"><?= $note->get_Content() ?></textarea>
        </div>
        <div class="invalid-feedback" id="text_error">
            <?php
            if (!empty($errors['content'])) {
            ?>
                <?= $errors['content'] ?>
            <?php
            }
            ?>
        </div>
    </form>

    <div id="noteId" data-note-id="<?= $note->get_Id() ?>"></div>
</div>
<script>
    const pageName = "editChecklistnote";
    const urlToRedirect = "<?= $back_url ?>"
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/scriptModalEdit.js"></script>
<script src="js/scriptValidateTextNote.js"></script>

<?php include('./utils/footer.php'); ?>