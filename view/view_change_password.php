<?php
$title_page = "Change Password";
include("utils/header_settings.php");
?>
    <div class="row p-3 h-100 justify-content-center ">
        <form class=" text-white" action="Settings/change_password" method="post">
            <div class="form-group m-3">
                <label for="old_password" class="form-label">Old password</label>
                <div class="input-group">
                    <input type="password" class="form-control <?php if (count($errors["old_password"]) > 0): ?>is-invalid<?php endif; ?>" id="old_password" name="old_password" placeholder="Old" autocomplete="off" value="<?= $old_password?>">
                </div>
                <?php if (count($errors["old_password"]) > 0): ?>
                    <div class="text-left invalid-feedback">
                        <ul class="list-unstyled">
                            <?php foreach ($errors["old_password"] as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group m-3">
                <label for="password" class="form-label">New password</label>
                <div class="input-group">
                    <input type="password" class="form-control <?php if (count($errors["password"]) > 0): ?>is-invalid<?php endif; ?>" id="password" name="password" placeholder="New" value="<?= $password ?>">
                </div>
                <?php if (count($errors["password"]) > 0): ?>
                    <div class="text-left invalid-feedback">
                        <ul class="list-unstyled">
                            <?php foreach ($errors["password"] as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group m-3">
                <label for="password_confirm" class="form-label">Confirm password</label>
                <div class="input-group">
                    <input type="password" class="form-control <?php if (count($errors["password_confirm"]) > 0): ?>is-invalid<?php endif; ?>" id="password_confirm" name="password_confirm" placeholder="Confirm" value="<?= $password_confirm ?>">
                </div>
                <?php if (count($errors["password_confirm"]) > 0): ?>
                    <div class="text-left invalid-feedback">
                        <ul class="list-unstyled">
                            <?php foreach ($errors["password_confirm"] as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group mt-5 m-4 ">
                <button type="submit" class="btn btn-primary col-12 ">Save</button>
            </div>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success m-4" role="alert">
                    <?= $success ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php
include('utils/footer.php'); ?>