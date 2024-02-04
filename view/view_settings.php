<?php
$title_page = "Settings";
include("./utils/header_settings.php");
?>
    <div class="row p-1">
        <div class="row-col-md-6">
            <h5 class="text-white bold-text">Hey <?= $user->get_Full_Name() ?>!</h5>
        </div>
        <div class="col-md-6 p-3">
            <a href="Settings/edit_profile" class="text-white link-with-icon">
                <i class="bi bi-person"></i>  Edit Profile</a><br>
            <a href="Settings/change_password" class="text-white link-with-icon">
                <i class="bi bi-lock"></i>  Change Password</a><br>
            <a href="Settings/logout_user" class="text-danger link-with-icon">
                <i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <?php
    include('./utils/footer.php'); ?>