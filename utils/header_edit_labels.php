<?php
include('./utils/head.php');

$back_url = "./notes/open_note/" . $note_id;


function show_back_button($back_url)
{
    ?>
    <a class="navbar-brand" href="<?= $back_url ?>">
        <i class="bi bi-chevron-left"></i>
    </a>
    <?php
}

?>

<body class="bg-dark min-vh-100">
<div class="container mw-80" style="margin-bottom: 15rem;">
    <header class="header-note">
        <nav class="navbar navbar-dark">
            <?php
                show_back_button($back_url);
            ?>
        </nav>
    </header>