<?php
if (isset($noteType)) {
    if ($noteType == 'archives') {
        include('utils/header_archives.php');
    } elseif ($noteType == 'shared_by') {
        include('utils/header_shared_by.php');
    } else {
        include('utils/header_notes.php');
    }
} else {
    include("utils/header_default.php");
}
?>
    <div class="card-header text-white mb-2 fst-italic fs-6">
        <span style="font-size: 0.8em;">Created <?= Note::time_elapsed_string($note->created_at) ?> . </span>
        <?php if ($note->edited_at !== null): ?>
            <span style="font-size: 0.8em;">Edited <?= Note::time_elapsed_string($note->edited_at) ?>.</span>
        <?php endif; ?>
    </div>

        <div class="mb-3 text-white">
            <label for="noteTitle" class="form-label">Title</label>
            <input type="text" id="noteTitle" class="form-control border-0 bg-secondary text-white bg-opacity-25" value="<?= htmlspecialchars($note->title) ?>" disabled>
        </div>
    <div class="card-body text-white">
        <label for="noteText" class="form-label">Items</label>
        <?php foreach ($checklistItems as $item): ?>
            <form action="notes/toggleCheckbox" method="POST">
                <div class="input-group mb-3">
                    <div class="input-group-text bg-primary ">
                        <input class="form-check-input border" type="checkbox" name="checked" value="1" <?= $item->checked ? 'checked' : '' ?> aria-label="Checkbox for following text input" onchange="this.form.submit()" <?= $canEdit ? '' : 'disabled' ?> >
                    </div>
                    <input type="text" class="form-control bg-secondary text-white bg-opacity-25 border-0 <?= $item->checked ? 'text-decoration-line-through' : '' ?>" value="<?= htmlspecialchars($item->content) ?>" aria-label="Text input with checkbox" disabled>
                    <input type="hidden" name="item_id" value="<?= $item->id ?>">
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</div>
<?php
include('./utils/footer.php'); ?>
