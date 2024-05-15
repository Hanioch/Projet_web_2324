<?php
include('./utils/head.php');
$is_list_filter_exist = isset($_GET["param2"]);
$list_filter_encoded = $is_list_filter_exist ? $_GET["param2"] : "";
?>

<body class="bg-dark min-vh-100">
    <div class="container mw-80" style="margin-bottom: 15rem;">
        <header class="header-note">

            <nav class="navbar navbar-dark">
                <?php
                if (isset($id_sender)) {
                    $chevronLink = "./notes/shared_by/$id_sender";
                } else {
                    $chevronLink = "./notes";
                }

                if ($is_list_filter_exist) $chevronLink = "./notes/search/" . $list_filter_encoded;

                echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <i class="bi bi-chevron-left"></i>
                        
                    </a>';
                ?>

            </nav>
            <nav class="navbar navbar-dark ">
                <div class="">
                    <?php if ($canEdit) : ?>
                        <?php

                        if ($isChecklistNote) {
                            $chevronLink = "./notes/edit_checklist_note/" . $note->get_Id();
                        } else {
                            $chevronLink = "./notes/edit_text_note/" . $note->get_Id();
                        }
                        if ($is_list_filter_exist) $chevronLink .= "/" . $list_filter_encoded;

                        echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <div class="d-inline-block">
                            <form action="notes/edit_labels/' . $note->get_Id() . '" method="POST" class="navbar-brand">
                                <input type="hidden" name="note_id" value="' . $note->get_Id() . '">
                                <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                                    <i class="bi bi-tag"></i>
                                </button>
                            </form>
                        </div>
                       <i class="bi bi-pencil"></i>
                    </a>';
                        ?>
                    <?php endif; ?>
                </div>
            </nav>

        </header>