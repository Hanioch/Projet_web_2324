<?php
$title_page = "Edit Profil";
include("./utils/header_settings.php");
?>
    <div class="row p-3">
        <form class=" text-white" action="Settings/edit_profile" method="post">
            <label for="button-addon1" class="form-label">Edit your name</label>
            <div class="input-group mb-3">
                <button class="btn btn-success border" type="submit" id="button-addon1"><i class="bi bi-download"></i></button>
                <input type="text" name="full_name" class="form-control<?php if (count($errors["full_name"]) > 0): ?> is-invalid<?php endif; ?> border" placeholder="" id="full_name" aria-label="Example text with button addon" aria-describedby="button-addon1" value="<?= $user->get_Full_Name() ?>">
            </div>
            <?php if (isset($errors) && count($errors["full_name"]) > 0): ?>
                <ul class="list-unstyled" >
                    <?php foreach ($errors["full_name"] as $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <li><?= $error ?></li>
                        </div>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?= $success ?>
                </div>
            <?php endif; ?>
        </form>

    </div>
</div>
<?php
include('./utils/footer.php'); ?>