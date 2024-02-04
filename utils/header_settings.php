<?php
include('./utils/head.php')
?>

<body class="bg-dark min-vh-100">
<div class="container mw-80" style="margin-bottom: 15rem;">
    <header class="header-note">

        <nav class="navbar navbar-dark">
            <?php
            if ($title_page === "Shares") {
                $chevronLink = "./notes/open_note/" . $noteId;
            } else {
                $chevronLink = ($title_page === "Settings") ? "./notes" : "./settings";
            }
            echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <i class="bi bi-chevron-left"></i>
                    </a>';
            ?>
        </nav>

        <h2 class="title"><?= $title_page ?></h2>

    </header>