<?php
require_once "tools.php";

$title_page = Page::Search->value . " my notes";
include("./utils/header.php");

//$editor_note = $notes_shared["editor"];
//$reader_note = $notes_shared["reader"];
//$base_title_component = "Notes shared by " . $full_name . " as ";

$personal_notes = $notes_searched['personal'];
$shared_notes = $notes_searched['shared'];

?>
<?php
if (count($list_label) > 0) {
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
            //recuperer les element deja selectionné et la liste des differents label existant 

            ?>
        </ul>
        <noscript>
            <button type="submit">Search</button>
        </noscript>
    </form>
<?php
}

if (count($personal_notes) > 0) {
    show_note($personal_notes, "Your notes : ", $title_page);
}

foreach ($shared_notes as $user_shared => $ns) {
    show_note($ns, "Notes shared by " . $user_shared . " :", $title_page);
}

if (count($personal_notes) === 0 && count($shared_notes) === 0) {
?>
    <h3 class="title-note">
        Pas de notes ici..
    </h3>
<?php
}

?>
</div>
<?php
include('./utils/footer.php'); ?>