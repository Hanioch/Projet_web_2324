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
        <div class="card-body text-white">
            <form>
                <div class="mb-3">
                    <label for="noteTitle" class="form-label">Title</label>
                    <input type="text" id="noteTitle" class="form-control border-0 bg-secondary text-white bg-opacity-25"  value="<?= $note->title?>" disabled>
                </div>
                <div class="mb-3 ">
                    <label for="noteText" class="form-label">Text</label>
                    <textarea id="noteText" class="form-control border-0 bg-secondary text-white bg-opacity-25" style="height: calc(50vh - 20px);" disabled><?= $text->content ?></textarea>
                </div>
            </form>
        </div>
</div>
<?php
include('./utils/footer.php'); ?>
