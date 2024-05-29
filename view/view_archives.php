<?php
require_once "tools.php";

$title_page = Page::Archives->value;
include("utils/header.php");

if (count($notes_archives) > 0) {
    show_note($notes_archives, "Archives", $title_page);
} else {
?>
    <h4 class="title-note">Your archives are empty.</h4>
<?php
}
?>
</div>
<?php
include('./utils/footer.php'); ?>