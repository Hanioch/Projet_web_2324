<?php
$id_form = "form_edit_text_note";
$title_page = "edit_text_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

<div class="card-body">
    <form id="<?= $id_form ?>" action="notes/edit_text_note/<?= $note->get_Id() ?>" method="post">
        <div class="card-header text-white mb-2 fst-italic fs-6">
            <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->get_Created_At()) ?> . </span>
            <?php if ($note->get_Edited_At() !== null) : ?>
                <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->get_Edited_At()) ?>.</span>
            <?php endif; ?>
        </div>

        <div class="mb-3 text-white">
            <div class="">
                <label for="title_add_text_note" class="form-label">Title</label>
                <input required type="text" value="<?= $note->get_Title() ?>" name="title" class="form-control bg-secondary text-white bg-opacity-25 mb-2 <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" id="title_add_text_note">
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
            <label for="text_add_text_note" class="form-label">Text</label>
            <textarea name="text" class="form-control bg-secondary text-white bg-opacity-25 <?= !empty($errors['content']) ? 'is-invalid' : '' ?>" id="text_add_text_note" cols="30" rows="10"><?= $note->get_Content() ?></textarea>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/scriptValidateTextNote.js"></script>
<?php include('./utils/footer.php'); ?>