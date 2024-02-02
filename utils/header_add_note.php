<?php
include('./utils/head.php');
?>
<body class="bg-dark min-vh-100">
    <div class="container mw-80" style="margin-bottom: 15rem;">
        <header class="header-note">
            <nav class="navbar navbar-dark">
                <a class="navbar-brand" href="./notes">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </nav>
            <button class="button-add-text-note" type="submit" form=<?= $id_form ?>>
                <span class="material-icons" id="icon-save">
                    save
                </span>
            </button>
        </header>