<?php
$title_page = "Settings";
include("./utils/header_settings.php");
?>
    <div class="row p-1">
        <div class="row-col-md-6">
            <h5 class="text-white bold-text">Hey <?= $user->full_name ?>!</h5>
        </div>
        <div class="col-md-6 p-3">
            <a href="Settings/edit_profile" class="text-white link-with-icon"><i class="bi bi-person"></i>  Edit Profile</a><br>
            <a href="Settings/change_password" class="text-white link-with-icon"><i class="bi bi-lock"></i>  Change Password</a><br>
            <button class="btn btn-link text-danger p-0 text-decoration-none " data-bs-toggle="modal" data-bs-target="#logoutConfirmation">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </div>
    </div>
    <div class="modal fade" id="logoutConfirmation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Logout Confirmation</h5>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to logout habibi?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="Settings/logout_user" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php
    include('./utils/footer.php'); ?>