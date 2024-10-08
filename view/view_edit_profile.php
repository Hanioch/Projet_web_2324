<?php
$title_page = "Edit Profile";
include("utils/header_settings.php");
?>
<div class="row p-3">
    <form class="text-white" action="Settings/edit_profile" method="post">
        <div class="mb-3">
            <label for="full_name" class="form-label">Edit your name</label>
            <input type="text" name="full_name" class="form-control<?php if (isset($errors) && count($errors["full_name"]) > 0): ?> is-invalid<?php endif; ?>" id="full_name" value="<?= $user->get_full_name() ?>">
            <?php if (isset($errors) && count($errors["full_name"]) > 0): ?>
            <div class="invalid-feedback" id="text_error">
                    <ul class="list-unstyled">
                        <?php foreach ($errors["full_name"] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="mail" class="form-label">Edit your Email</label>
            <input type="email" name="mail" class="form-control<?php if (isset($errors) && count($errors["mail"]) > 0): ?> is-invalid<?php endif; ?>" id="mail" value="<?= $user->get_mail() ?>">
            <?php if (isset($errors) && count($errors["mail"]) > 0): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="list-unstyled">
                        <?php foreach ($errors["mail"] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <button class="btn btn-success" type="submit">Submit Changes</button>
    </form>
</div>
</div>
<?php include('utils/footer.php'); ?>
