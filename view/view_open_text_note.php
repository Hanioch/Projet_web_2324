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
    <div class="created">
        <small>Created <?=$note->created_at ?></small>
        <small>Edited <?= $note->edited_at ?></small>
    </div>
</div>
<?php
include('./utils/footer.php'); ?>
