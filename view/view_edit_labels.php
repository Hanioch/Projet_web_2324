<?php

$id_form = "form_edit_labels";
include("./utils/header_edit_labels.php");

?>

    <div class="card-body">
        <span id="idNote" value="<?= $note->get_Id() ?>" style="display: none;"></span>

        <form id="<?= $id_form ?>" class="p-3 text-white " action="notes/edit_labels/<?= $note->get_Id() ?>" method="post">

            <div class="mb-3">
                <div class="">
                    <label class="form-label mb-0 fs-5">Labels : </label>
                    <ul class="list-unstyled" id="list_labels">
                        <?php
                            if(empty($labels)) {
                        ?> <span class="fst-italic">This note does not yet have a label.</span>
                            <?php }
                        ?>
                        <?php foreach ($labels as $label) : ?>
                            <li class="list-unstyled" id="list_labels_<?= $label->get_id() ?>">
                                <div class="input-group pt-3 has-validation">
                                    <input readonly value="<?= $label->get_label_name() ?>" type="text" name="label<?php echo $label->get_id() ?>" class="form-control bg-secondary text-white bg-opacity-25 border-secondary" id="label<?php echo $label->get_id() ?>" >
                                    <button name="remove_button" value="<?= $label->get_id() ?>" class="btn btn-danger btn-lg rounded-end  border-secondary" type="submit">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <label for="add_label" class="form-label">Add a new label : </label>
            <div class="input-group">
                <input type="text" name="new_label" class="form-control bg-secondary text-white bg-opacity-25 border-secondary" id="add_label" placeholder="Type to search or create...">
                <button id="add_button" name="add_button" class="btn btn-primary btn-lg rounded-end  border-secondary" type="submit">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div id="new_label_error_div">
                <!-- gestion des erreurs à faire -->
            </div>
        </form>
    </div>

    <script>
        const pageName = "editChecklistnote";
        const urlToRedirect = "<?= $back_url ?>"
        const noteId = <?= $note->get_Id() ?>
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="js/scriptModalEdit.js"></script>
    <script src="js/scriptEditChecklistNote.js"></script>

<?php include('./utils/footer.php'); ?>