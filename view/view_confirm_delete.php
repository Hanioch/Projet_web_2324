<?php
if (isset($header_type) && empty($error)) {
    include("utils/header_{$header_type}.php");
} else {
    include("utils/header_error.php");
}
?>
<?php if (isset($error) && !empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
<?php else: ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-white text-black border-secondary">
                    <div class="card-header border">
                        <h5 class="card-title">Delete Note Confirmation</h5>
                    </div>
                    <div class="card-body border">
                        <p>Are you sure you want to delete this note?</p>
                    </div>
                    <div class="card-footer text-end border">
                        <a href="./notes/open_note/<?= $note->get_id() ?>" class="btn btn-secondary">Cancel</a>
                        <form action="notes/delete" method="POST" style="display: inline;">
                            <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>
<?php include('./utils/footer.php'); ?>