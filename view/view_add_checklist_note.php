<?php
$id_form = "form_add_checklist_note";
$title_page = "add_checklist_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

    <div class="row justify-content-center align-items-center">

        <form id=<?= $id_form ?> class="p-3 border rounded-4 text-white text-center" action="notes/add_checklist_note" method="post">
        <div class="mb-3">
            <div class="">
                <label for="title_add_checklist_note" class="form-label">Title</label>
                <input required type="text" value='<?= $default_title ?>' name="title" class="form-control" id="title-add-text-note">
                <?php
                if (array_key_exists('title', $errors)) {
                    ?>
                    <span class="error-add-note">
                        <?= $errors["title"] ?>
                    </span>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="mb-3">
            <div class="">
                <label for="text_add_text_note" class="form-label">Text</label>
                <textarea name="text" class="form-control" id="text-add-text-note" cols="30" rows="10"><?= $default_text ?></textarea>
            </div>
        </div>
        </form>



    </div>
<?php
include('./utils/footer.php'); ?>