<?php
$id_form = "form_add_checklist_note";
$title_page = "add_checklist_note";
$something_to_save = false;
include("./utils/header_add_note.php");

?>

<div class="card bg-dark text-white border-0">
    <div class="card-header">
        <h5 class="card-title">Add Checklist Note</h5>
    </div>
    <div class="card-body">
        <form id="<?= $id_form ?>" action="notes/add_checklist_note" method="post">
            <div class="mb-3">
                <label for="title_add_checklist_note" class="form-label">Title</label>
                <input type="text" name="title" value="<?= isset($_POST['title']) ?$_POST['title'] : ''; ?>" class="form-control bg-secondary text-white bg-opacity-25 <?= !empty($errors['title']) ? 'is-invalid' : '' ?>" id="title_add_checklist_note">
                <?php if (!empty($errors['title'])): ?>
                    <div class="invalid-feedback">
                        <?php
                        foreach($errors['title'] as $error) {
                            echo $error;
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Items</label>
                <ul class="list-unstyled custom-list ">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <li>
                            <input type="text" name="item<?= $i ?>" value="<?= isset($_POST['item' . $i]) ? $_POST['item' . $i] : ''; ?>" class="form-control bg-secondary text-white bg-opacity-25 mt-2 <?= !empty($errors['item' . $i]) ? 'is-invalid' : (isset($_POST['item' . $i]) && !empty($_POST['item' . $i]) ? 'is-valid' : '') ?>" id="item<?= $i ?>">
                            <?php if (isset($errors['item' . $i])): ?>
                                <div class="invalid-feedback mb-1">
                                    <?= $errors['item' . $i] ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        </form>
    </div>
</div>

<?php include('./utils/footer.php'); ?>
