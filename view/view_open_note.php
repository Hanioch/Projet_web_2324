<?php
if (isset($headerType) && empty($error)) {
    include("utils/header_{$headerType}.php");
} else {
    include("utils/header_error.php");
}
?>
<?php if (isset($error) && !empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
    </div>
<?php else: ?>
    <div class="card-header text-white mb-2 fst-italic fs-6">
        <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->get_Created_At()) ?> . </span>
        <?php if ($note->get_Edited_At() !== null): ?>
            <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->get_Edited_At()) ?>.</span>
        <?php endif; ?>
    </div>

    <div class="mb-3 text-white">
        <label for="noteTitle" class="form-label">Title</label>
        <input type="text" id="noteTitle" class="form-control border-0 bg-secondary text-white bg-opacity-25" value="<?= htmlspecialchars($note->get_Title()) ?>" disabled>
    </div>
    <div class="card-body text-white" id="itemsDiv">
        <?php if ($isChecklistNote): ?>
                <label class="form-label">Items</label>
                <?php foreach ($checklistItems as $item): ?>
                    <form action="notes/toggle_Checkbox" method="POST">
                        <div class="input-group mb-3">
                            <div class="input-group-text bg-primary ">
                                <button class="btn btn-submit" <?= $canEdit ? '' : 'disabled' ?>>
                                    <input class="form-check-input border" id="checkbox_<?= $item->get_Id()?>" type="checkbox" name="checked" value="1" <?= $item->is_Checked() ? 'checked' : '' ?> aria-label="Checkbox for following text input" <?= $canEdit ? '' : 'disabled' ?>>
                                </button>
                            </div>
                            <input type="text" class="form-control bg-secondary text-white bg-opacity-25 border-0 <?= $item->is_Checked() ? 'text-decoration-line-through' : '' ?>" value="<?= htmlspecialchars($item->get_Content()) ?>" aria-label="Text input with checkbox" disabled>
                            <input type="hidden" name="item_id" value="<?= $item->get_Id() ?>">
                            <input type="hidden" name="note_id" value="<?= $note->get_Id() ?>">
                        </div>
                    </form>
                <?php endforeach; ?>
    </div>
        <?php else: ?>
                    <div class="mb-3 ">
                        <label for="noteText" class="form-label">Text</label>
                        <textarea id="noteText" class="form-control border-0 bg-secondary text-white bg-opacity-25" style="height: calc(50vh - 20px);" disabled><?= $text->get_Content() ?></textarea>
                    </div>
    </div>
        <?php endif; ?>
<?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="js/scriptCheckUncheck.js"></script>

<?php include('./utils/footer.php'); ?>