<?php
include('./utils/head.php')
?>

<body class="bg-dark min-vh-100">
<div class="container mw-80" style="margin-bottom: 15rem;">
    <header class="header-note">

        <nav class="navbar navbar-dark">
            <?php
            if (isset($id_send)) {
                $chevronLink = "./notes/shared_by/$id_send";
            } else {
                $chevronLink = "./notes";
            }
            echo '<a class="navbar-brand" href="' . $chevronLink . '">
                        <i class="bi bi-chevron-left"></i>
                        
                    </a>';
            var_dump($id_send);

            ?>

        </nav>
        <nav class="navbar navbar-dark ">
             <div class="">
            <?php
            $chevronLink = "./notes";
            echo '<a class="navbar-brand" href="' . $chevronLink . '">
                       <i class="bi bi-pencil"></i>
                    </a>';
            ?>
             </div>
        </nav>


    </header>