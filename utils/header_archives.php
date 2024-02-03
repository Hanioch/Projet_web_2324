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
            <div class="navbar-brand">
                    <button class="btn-icon" style="background: none; border: none; color: inherit;" data-bs-toggle="modal" data-bs-target="#deleteNoteConfirmation">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
            </div>
            <div class="">
                <form action="notes/setArchive" method="POST" class="navbar-brand"  >
                    <input type="hidden" name="note_id" value="<?= $note->getId() ?>">
                    <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                        <i class="bi bi-box-arrow-up"></i>
                    </button>
                </form>
            </div>
        </nav>
        <div class="modal fade" id="deleteNoteConfirmation" tabindex="-1" aria-labelledby="deleteNoteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteNoteLabel">Delete Note Confirmation</h5>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this note?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="notes/delete" method="POST">
                            <input type="hidden" name="note_id" id="modalNoteId" value="<?= $note->getId() ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
