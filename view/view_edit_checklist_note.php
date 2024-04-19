<?php
$id_form = "form_edit_checklist_note";
$title_page = "edit_checklist_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

<div class="card-body">
    <form id="<?= $id_form ?>" class="p-3 text-white " action="notes/edit_checklist_note/<?=$note->get_Id()?>" method="post">
        <div class="mb-3">
            <div class="card-header text-white mb-2 fst-italic fs-6">
                <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->get_Created_At()) ?> . </span>
                <?php if ($note->get_Edited_At() !== null) : ?>
                    <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->get_Edited_At()) ?>.</span>
                <?php endif; ?>
            </div>
            <div class="">
                <label for="title_add_checklist_note" class="form-label">Title</label>
                <input type="text" value="<?php if(isset($_POST['title']) ){ echo $_POST['title'];}else{ echo $note->get_Title();} ?>" name="title" class="form-control  border-0 bg-secondary text-white bg-opacity-25" id="title_add_checklist_note">
                <?php
                if (!empty($errors['title'])) {
                    ?>
                    <span class="error-add-note">
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
                <label class="form-label">Items</label>
                <ul class="list-unstyled">
                    <?php foreach ($items as $item): ?>
                        <li class="list-unstyled">
                            <div class="input-group mb-3">
                                <div class="input-group-text bg-primary  border-secondary ">
                                    <input class="form-check-input  border align-middle"  type="checkbox" name="checked" value="1" <?= $item->is_Checked() ? 'checked' : '' ?> aria-label="Checkbox for following text input" disabled> <!-- onchange="this.form.submit()" -->
                                </div>
                                <input value="<?= $item->get_Content() ?>" type="text" name="item<?php echo $item->get_Id() ?>" class="form-control bg-secondary text-white bg-opacity-25 border-secondary" id="item<?php echo $item->get_Id() ?>"  value="<?php echo isset($_POST['item' . $item->get_Id()]) ? htmlspecialchars($_POST['item' . $item->get_Id()]) : ''; ?>">
                                <button name="remove_button" value="<?= $item->get_Id() ?>" class="btn btn-danger btn-lg rounded-end  border-secondary" type="submit">
                                    <i class="bi bi-x"></i>
                                </button>
                                <?php if (isset($errors['item' . $item->get_Id()])): ?>
                                    <span class="error-add-note"><?php foreach($errors['item' . $item->get_Id()] as $error){echo $error;} ?></span>
                                <?php endif; ?>
                                <input type="hidden" name="item_id" value="1">
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <label for="add_item" class="form-label">New Item</label>
        <div class="input-group">
            <input <?php if(!empty($errors['new_item'])){echo 'value="' . $_POST['new_item'] . '"';} else{echo 'value=""';}?> type="text" name="new_item" class="form-control bg-secondary text-white bg-opacity-25 border-secondary" id="add_item">
                <button name="add_button" class="btn btn-primary btn-lg rounded-end  border-secondary" type="submit">
                    <i class="bi bi-plus"></i>
                </button>
            <input type="hidden" name="item_id" value="1">
        </div>
        <?php if (isset($item) && isset($errors['new_item'])): ?>
            <span class="error-add-note"><?php echo $errors['new_item'] ?></span>
        <?php endif; ?>
    </form>
</div>

<?php include('./utils/footer.php'); ?>