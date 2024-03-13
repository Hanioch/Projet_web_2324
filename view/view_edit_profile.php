<?php
$title_page = "Edit Profil";
include("./utils/header_settings.php");
?>
<div class="row p-3">
    <form class="text-white" action="Settings/edit_profile" method="post">
        <div class="mb-3">
            <label for="full_name" class="form-label">Edit your name</label>
            <input type="text" name="full_name" class="form-control<?php if (isset($errors) && count($errors["full_name"]) > 0): ?> is-invalid<?php endif; ?>" id="full_name" value="<?= $user->get_Full_Name() ?>">
            <?php if (isset($errors) && count($errors["full_name"]) > 0): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="list-unstyled">
                        <?php foreach ($errors["full_name"] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Edit your Email</label>
            <input type="email" name="email" class="form-control<?php if (isset($errors) && count($errors["email"]) > 0): ?> is-invalid<?php endif; ?>" id="email" value="<?= $user->get_Mail() ?>">
            <?php if (isset($errors) && count($errors["email"]) > 0): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="list-unstyled">
                        <?php foreach ($errors["email"] as $error): ?>
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
<?php include('./utils/footer.php'); ?>
