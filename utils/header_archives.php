<?php
include('./utils/head.php')
?>

<body class="bg-dark min-vh-100">
    <div class="container mw-80" style="margin-bottom: 15rem;">
        <header class="header-note">
            <nav class="navbar navbar-dark">
                <?php
                $chevronLink = "./notes/archives";
                echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <i class="bi bi-chevron-left"></i>
                    </a>';
                ?>
            </nav>
            <nav class="navbar navbar-dark ">
                <div class="">
                    <form action="notes/confirm_delete/<?= $note->get_Id() ?>" method="POST" class="navbar-brand">
                        <input type="hidden" name="note_id" value="<?= $note->get_Id() ?>">
                        <button id="button_trash" type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi bi-trash text-danger"></i>
                        </button>
                    </form>
                </div>
                <div class="">
                    <form action="notes/set_Archive" method="POST" class="navbar-brand">
                        <input type="hidden" name="note_id" value="<?= $note->get_Id() ?>">
                        <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi bi-box-arrow-up"></i>
                        </button>
                    </form>
                </div>
            </nav>
        </header>