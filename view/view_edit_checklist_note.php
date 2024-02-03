<?php
$id_form = "form_edit_checklist_note";
$title_page = "edit_checklist_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

    <div class="row justify-content-center align-items-center">

        <form id=<?= $id_form ?> class="p-3 border rounded-4 text-white text-center" action="notes/add_checklist_note" method="post">
        <div class="mb-3">
            <div class="">
                <label for="title_add_checklist_note" class="form-label">Title</label>
                <input required type="text" value="<?= $note->getTitle() ?>" name="title" class="form-control" id="title_add_checklist_note">
                <?php
                if (!empty($errors['title'])) {
                    ?>
                    <span class="error-add-note">
                    <?php
                    foreach($errors['title'] as $error) {
                        echo $error;
                    }
                    ?>
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

                    <?php foreach ($items as $item): ?>

                        <li class="">
                            <div class="input-group mb-3">
                                <div class="input-group-text bg-primary ">
                                    <input class="form-check-input border align-middle" type="checkbox" name="checked" value="1" <?= $item->isChecked() ? 'checked' : '' ?> aria-label="Checkbox for following text input"> <!-- onchange="this.form.submit()" -->
                                </div>

                                <input value="<?= $item->getContent() ?>"type="text" name="item<?php echo $item->getId() ?>" class="form-control" id="item<?php echo $item->getId() ?>" value="<?php echo isset($_POST['item' . $item->getId()]) ? htmlspecialchars($_POST['item' . $item->getId()]) : ''; ?>">

                                <button class="btn btn-danger btn-lg rounded-end border">-</button>

                                <?php if (isset($errors['item' . $item->getId()])): ?>
                                    <span class="error-add-note"><?php foreach($errors['item' . $item->getId()] as $error){echo $error;} ?></span>
                                <?php endif; ?>

                                <input type="hidden" name="item_id" value="1">

                            </div>

                        </li>

                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
        </form>
    </div>

<?php include('./utils/footer.php'); ?>