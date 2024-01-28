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
                <input required type="text" value='<?= $default_title ?>' name="title" class="form-control" id="title_add_checklist_note">
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
                <label for="item" class="form-label">Items</label>
                <ul>
                    <?php for ($i = 0 ; $i < 5 ; $i++){ ?>
                    <li class="mb-2">
                        <input type="text" name="item<?php echo $i ?>" class="form-control" id="item<?php echo $i ?>">
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        </form>
    </div>

<?php include('./utils/footer.php'); ?>