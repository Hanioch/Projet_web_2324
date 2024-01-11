<?php
include('/Applications/MAMP/htdocs/prwb_2324_a04/utils/head.php')
?>

<body class="bg-dark min-vh-100">
    <div class="container mw-80" style="margin-bottom: 15rem;">
        <header class="header-note">
            <button class="btn btn-nav" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list nav-icon"></i>
            </button>

            <h2 class="title"> <?= $title_page ?></h2>

            <div class="offcanvas  offcanvas-start text-bg-dark" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title title-nav" id="offcanvasWithBothOptionsLabel">NoteApp</h5>
                    <button type="button" class="btn-close btn-close-white " data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <?php
                    $nav_list = [];
                    $nav_list[] = array(
                        "title" => "My notes",
                        "url" => "https://mynotes.be"
                    );
                    $nav_list[] = array(
                        "title" => "My archives",
                        "url" => "https://myarchives.be"
                    );

                    foreach ($users_shared_notes as $u) {
                        $nav_list[] = array(
                            "title" => "Shared by " . $u,
                            "url" => "https://sharedby" . $u
                        );
                    }
                    $nav_list[] = array(
                        "title" => "Settings",
                        "url" => "https://settings.be"
                    );

                    foreach ($nav_list as $elem) {
                        $url = $elem["url"];
                        $title = $elem["title"];
                    ?>
                        <a class="nav-link <?= $title === $title_page ?  "selected" : "" ?> " href=<?= $url ?>> <?= $title ?></a>
                        <br>

                    <?php
                    }
                    ?>
                </div>
            </div>
        </header>