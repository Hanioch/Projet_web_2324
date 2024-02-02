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
                <input required type="text" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" name="title" class="form-control" id="title_add_checklist_note">
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
                    <?php for ($i = 1 ; $i < 6 ; $i++){ ?>
                    <li class="mb-2">
                        <input type="text" name="item<?php echo $i ?>" class="form-control" id="item<?php echo $i ?>" value="<?php echo isset($_POST['item' . $i]) ? htmlspecialchars($_POST['item' . $i]) : ''; ?>">
                        <?php if (isset($errors['item' . $i])): ?>
                            <span class="error-add-note"><?php foreach($errors['item' . $i] as $error){echo $error;} ?></span>
                        <?php endif; ?>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        </form>
    </div>

<?php include('./utils/footer.php'); ?>