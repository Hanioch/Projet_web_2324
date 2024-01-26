<?php
require_once "tools.php";

$title_page = Page::Notes->value;
include("./utils/header.php");

$pinned_notes = $notes['pinned'];
$other_notes = $notes['other'];
if (count($pinned_notes) > 0) {
    show_note($pinned_notes, "Pinned", $title_page);
}
if (count($other_notes) > 0) {
    show_note($other_notes, "Others", $title_page);
}

if (count($pinned_notes) == 0 && count($other_notes) == 0) {
?>
    <h4 class="title-note">Your notes are empty.</h4>
<?php
}
?>
</div>
<footer class="footer-note">
    <a href="http://ajoute_un_text_note"><i class="bi bi-file-earmark"></i></a>
    <a href="main/add_checklist_note"><i class="bi bi-ui-checks"></i></a>
</footer>

<?php
include('./utils/footer.php'); ?>