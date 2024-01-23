<?php
if (isset($noteType)) {
    include("utils/header_{$noteType}.php");
} else {
    include("utils/header_default.php");
}
?>
<?php
include('./utils/footer.php'); ?>
