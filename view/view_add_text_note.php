<?php
$id_form = "form_add_text_note";
$title_page = "add_text_note";
$something_to_save = false;
include("./utils/header_add_note.php");
?>

        <div class="card bg-dark text-white border-0">
            <div class="card-header">
                <h5 class="card-title">Add Text Note</h5>
            </div>
            <div class="card-body">
                <form id="<?= $id_form ?>" action="notes/add_text_note" method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input required id="title" type="text" value="<?= $default_title ?>" name="title" class="form-control bg-secondary text-white bg-opacity-25" id="title-add-text-note">
                        <?php if (array_key_exists('title', $errors)): ?>
                            <div class="text-danger">
                                <?= $errors["title"] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="text" class="form-label">Text</label>
                        <textarea id="text" name="text" class="form-control bg-secondary text-white bg-opacity-25" id="text-add-text-note" cols="30" rows="10"><?= $default_text ?></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include('./utils/footer.php'); ?>
