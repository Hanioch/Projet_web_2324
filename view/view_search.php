<?php
require_once "tools.php";

$title_page = Page::Search->value . " my notes";
include("utils/header.php");

$personal_notes = $notes_searched['personal'];
$shared_notes = $notes_searched['shared'];

?>
<?php
if (count($list_label) > 0) {
    ksort($list_label);
?>
    <h3 class="title-note">Search notes by tags :</h3>
    <form action="notes/search" method="post">
        <ul id="list-label">

            <?php
            foreach ($list_label as $label => $checked) { ?>
                <li>
                    <input type="checkbox" <?= $checked ? "checked" : "" ?> id="<?= $label ?>Label" name="<?= $label ?>">
                    <label for="<?= $label ?>" style="color: #ddd;"><?= $label ?></label>
                </li>
            <?php }

            ?>
        </ul>
        <noscript>
            <button class="btn btn-secondary" type="submit">Search</button>
        </noscript>
    </form>
    <hr>
<?php } ?>
    <div class="notes_personal">
        <?php
        if (count($personal_notes) > 0) {
            show_note($personal_notes, "Your notes : ", $title_page);
        }
        ?>
    <div class="notes_shared">
        <?php
        foreach ($shared_notes as $user_shared => $ns) {
            show_note($ns, "Notes shared by " . $user_shared . " :", $title_page);
        }
        ?>
    </div>
    <div class="notes_no">
        <?php
        if (count($personal_notes) === 0 && count($shared_notes) === 0) {
        ?>
            <h4 class="title-note">
                No note matches.
            </h4>
        <?php
        }
        ?>
    </div>
</div>
    <script>
        var titlePage = "<?php echo $title_page; ?>";
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/scriptSearch.js"></script>
<?php
include('./utils/footer.php'); ?>