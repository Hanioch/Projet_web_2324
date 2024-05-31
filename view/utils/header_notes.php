<?php
include('head.php');
$is_list_filter_exist = isset($_GET["param2"]);
$list_filter_encoded = $is_list_filter_exist ? $_GET["param2"] : "";
?>

<body class="bg-dark min-vh-100">
    <div class="container mw-80" style="margin-bottom: 15rem;">
        <header class="header-note">

            <nav class="navbar navbar-dark">
                <?php
                $chevron_link = "./notes/back_note_list";
                $method = "POST";
                if ($is_list_filter_exist) {
                    $chevron_link = "./notes/search/" . $list_filter_encoded;
                    $method = "GET";
                }
                ?>
                <form action="<?= $chevron_link ?>" method="<?= $method ?>" class="navbar-brand" style="margin: 0;">
                    <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit;">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </form>
            </nav>
            <nav class="navbar navbar-dark ">
                <div class="">
                    <form action="notes/shares/<?= $note->get_id() ?>" method="POST" class="navbar-brand">
                        <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                        <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi bi-share"></i>
                        </button>
                    </form>
                </div>
                <div class="">
                    <?php
                    $pin_icon = $note->is_pinned() ? "bi-pin-fill" : "bi-pin";
                    ?>
                    <form action="notes/toggle_Pin" method="POST" class="navbar-brand">
                        <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                        <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi <?= $pin_icon ?>"></i>
                        </button>
                    </form>
                </div>
                <div class="">
                    <form action="notes/set_Archive" method="POST" class="navbar-brand">
                        <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                        <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi bi-arrow-down-square"></i>
                        </button>
                    </form>
                </div>
                <div class="">
                    <form action="notes/edit_labels/<?= $note->get_id() ?>" method="POST" class="navbar-brand">
                        <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                        <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                            <i class="bi bi-tag"></i>
                        </button>
                    </form>
                </div>
                <div class="">
                    <?php
                    if ($is_checklist_note) {
                        $chevron_link = "./notes/edit_checklist_note/" . $note->get_id();
                    } else {
                        $chevron_link = "./notes/edit_text_note/" . $note->get_id();
                    }

                    if ($is_list_filter_exist) $chevron_link .= "/" . $list_filter_encoded;
                    echo '<a class="navbar-brand" href="' . $chevron_link . '">
                       <i class="bi bi-pencil"></i>
                    </a>';
                    ?>
                </div>
            </nav>


        </header>