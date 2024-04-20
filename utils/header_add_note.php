<?php
include('./utils/head.php');

$back_url = "./notes";

if (isset($shared_note_id) && $shared_note_id !== NULL) {
    $back_url = "./notes/shared_by/" . $shared_note_id;
}

?>

<body class="bg-dark min-vh-100">
    <div class="container mw-80" style="margin-bottom: 15rem;">
        <header class="header-note">
            <nav class="navbar navbar-dark">
                <button class="navbar-brand" style="display: none; background-color: transparent; border: 0;" id="btnBack" data-bs-toggle="modal" data-bs-target="#modalGoBack">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <noscript>
                    <a class="navbar-brand" href="<?= $back_url ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </noscript>
            </nav>
            <button id="save_button" name="save_button" class="button-add-text-note" type="submit" form=<?= $id_form ?>>
                <span class="material-icons" id="icon-save">
                    save
                </span>
            </button>
        </header>