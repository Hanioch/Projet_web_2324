<?php
if (isset($header_type) && empty($error)) {
    include("utils/header_{$header_type}.php");
} else {
    include("utils/header_error.php");
}
?>
<!-- Full screen modal -->
<div id="fullScreenModal" class="modal fade" tabindex="-1" aria-labelledby="fullScreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullScreenModalLabel">Are you sure ?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you really want to delete this note "<?php echo htmlspecialchars($note->get_title()); ?>"and all of its dependencies ?</p>
                <p>This process cannot be undone</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="validBtn">Yes, delete it!</button>
            </div>
        </div>
    </div>
</div>
<div id="modalSuccessDelete" class="modal fade" tabindex="-1" aria-labelledby="fullScreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSuccessDeleteLabel">deleted</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This note has been deleted</p>
            </div>
            <div class="modal-footer">
                <a id="monBouton" href="notes">
                    <button type="button" class="btn btn-secondary">Close</button>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- fin -->
<?php if ($note !== null) : ?>
    <span id="idNote" style="display: none;"></span>
<?php endif; ?>
<?php if (isset($error) && !empty($error)) : ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
<?php else : ?>
    <div class="card-header text-white mb-2 fst-italic fs-6">
        <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->get_created_at()) ?> . </span>
        <?php if ($note->get_edited_at() !== null) : ?>
            <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->get_edited_at()) ?>.</span>
        <?php endif; ?>
    </div>

    <div class="mb-3 text-white">
        <label for="noteTitle" class="form-label">Title</label>
        <input type="text" id="noteTitle" class="form-control border-0 bg-secondary text-white bg-opacity-25" value="<?= $note->get_title() ?>" disabled>
    </div>
    <div class="card-body text-white" id="itemsDiv">
        <?php if ($is_checklist_note) : ?>
            <label class="form-label">Items</label>
            <?php foreach ($checklist_items as $item) : ?>
                <form action="notes/toggle_Checkbox" method="POST">
                    <div class="input-group mb-3">
                        <div class="input-group-text bg-primary ">
                            <button class="btn btn-submit" <?= $can_edit ? '' : 'disabled' ?>>
                                <input class="form-check-input border opacity-100" id="checkbox_<?= $item->get_id() ?>" type="checkbox" name="checked" value="1" <?= $item->is_checked() ? 'checked' : '' ?> aria-label="Checkbox for following text input" disabled>
                            </button>
                        </div>
                        <input type="text" class="form-control bg-secondary text-white bg-opacity-25 border-0 <?= $item->is_checked() ? 'text-decoration-line-through' : '' ?>" value="<?= $item->get_content() ?>" aria-label="Text input with checkbox" disabled>
                        <input type="hidden" name="item_id" value="<?= $item->get_id() ?>">
                        <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                    </div>
                </form>
            <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="mb-3 ">
        <label for="noteText" class="form-label">Text</label>
        <textarea id="noteText" class="form-control border-0 bg-secondary text-white bg-opacity-25" style="height: calc(50vh - 20px);" disabled><?= $text->get_content() ?></textarea>
    </div>
    </div>
<?php endif; ?>
<?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="js/scriptCheckUncheck.js"></script>
<script src="js/scriptModal.js"></script>

<?php include('./utils/footer.php'); ?>