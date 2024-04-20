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
        <!-- TODO transformer les message success en popup temporaire. -->
        <?php if ($result["success"] != NULL) : ?>
            <div class="alert alert-success" role="alert">
                <?= $result["success"] ?>
            </div>
        <?php endif; ?>
        <div class="card-body">
            <form id="<?= $id_form ?>" action="notes/add_text_note" method="post">
                <div class="mb-3">
                    <label for="title_add_text_note" class="form-label">Title</label>
                    <input required type="text" value="<?= $default_title ?>" name="title" class="form-control bg-secondary text-white bg-opacity-25 mb-2 <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" id="title_add_text_note">
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
                <div class="mb-3">
                    <label for="text_add_text_note" class="form-label">Text</label>
                    <textarea name="text" class="form-control bg-secondary text-white bg-opacity-25 <?= !empty($errors['content']) ? 'is-invalid' : '' ?>" id="text_add_text_note" cols="30" rows="10"><?= $default_text ?></textarea>
                    <div class="invalid-feedback" id="text_error">
                    <?php
                    if (!empty($errors['content'])) {
                        ?>
                            <?= $errors['content'] ?>
                        <?php
                    }
                    ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/scriptModalEdit.js"></script>
    <script src="js/scriptValidateTextNote.js"></script>
<?php include('./utils/footer.php'); ?>