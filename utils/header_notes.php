<?php
include('./utils/head.php')
?>

<body class="bg-dark min-vh-100">
<div class="container mw-80" style="margin-bottom: 15rem;">
    <header class="header-note">

        <nav class="navbar navbar-dark">
            <?php
            $chevronLink = "./notes";
            echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <i class="bi bi-chevron-left"></i>
                    </a>';
            ?>
        </nav>
        <nav class="navbar navbar-dark ">
            <div class="">
            <?php
            $chevronLink = "./notes";
            echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <i class="bi bi-share"></i>
                    </a>';
            ?>
            </div>
            <div class="">
                <?php
                $pinIcon = $note->isPinned() ? "bi-pin-fill" : "bi-pin";
                ?>
                <form action="notes/togglePin" method="POST" class="navbar-brand"  >
                    <input type="hidden" name="note_id" value="<?= $note->getId() ?>">
                        <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi <?= $pinIcon ?>"></i>
                        </button>
                </form>
            </div>
            <div class="">
                <form action="notes/setArchive" method="POST" class="navbar-brand"  >
                    <input type="hidden" name="note_id" value="<?= $note->getId() ?>">
                    <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                        <i class="bi bi-arrow-down-square"></i>
                    </button>
                </form>
            </div>
             <div class="">
            <?php
            $chevronLink = "./notes/edit_checklist_note/" . $note->getId();
            echo '<a class="navbar-brand" href="' . $chevronLink . '">
                       <i class="bi bi-pencil"></i>
                    </a>';
            ?>
             </div>
        </nav>


    </header>