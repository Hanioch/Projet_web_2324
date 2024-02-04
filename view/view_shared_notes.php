<?php
require_once "tools.php";
//adapter avec un getter
$full_name =  $sender->get_Full_Name();
$id_send= $sender->get_Id();
$title_page = Page::Shared_by->value . " " . $full_name;
include("./utils/header.php");

$editor_note = $notes_shared["editor"];
$reader_note = $notes_shared["reader"];
$base_title_component = "Notes shared to you by " . $full_name . " as ";
if (count($editor_note) > 0) {
    $title_component = $base_title_component . "editor";
    show_note($editor_note, $title_component, $title_page);
}
if (count($reader_note) > 0) {
    $title_component = $base_title_component . "reader";
    show_note($reader_note, $title_component, $title_page);
}
?>
</div>
<?php
include('./utils/footer.php'); ?>