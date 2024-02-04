<?php
$id_form = "form_edit_text_note";
$title_page = "edit_text_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

<div class="card-body">
    <form id="<?= $id_form ?>" action="notes/edit_text_note/<?= $note->get_Id() ?>" method="post">
        <div class="mb-3 text-white">
            <div class="">
                <label for="title_add_text_note" class="form-label">Title</label>
                <input required type="text" value="<?= $note->get_Title() ?>" name="title" class="form-control bg-secondary text-white bg-opacity-25 mb-2" id="title_add_text_note">
                <?php
                if (!empty($errors)) {
                ?>
                    <?php
                    foreach ($errors as $error) {
                    ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="mb-3 text-white">
            <label for="text_add_text_note" class="form-label">Text</label>
            <textarea name="text" class="form-control bg-secondary text-white bg-opacity-25" id="text_add_text_note" cols="30" rows="10"><?= $note->get_Content() ?></textarea>
        </div>
    </form>


</div>

<?php include('./utils/footer.php'); ?>