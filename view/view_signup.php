<?php
include("utils/header_login.php");
?>

    <div class="row justify-content-center align-items-center">
        <form class="p-3 border rounded-4 text-white text-center" action="main/signup" method="post">
            <fieldset class="h4">Sign Up</fieldset>
            <hr>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control <?php if (count($errors["mail"]) > 0): ?>is-invalid<?php endif; ?>" id="mail" name="mail" placeholder="Email" value="<?=$mail ?>">
                    <?php if (count($errors["mail"]) > 0): ?>
                        <div class="text-start invalid-feedback">
                            <ul class="list-unstyled">
                                <?php foreach ($errors["mail"] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control <?php if (count($errors["full_name"]) > 0): ?>is-invalid<?php endif; ?>" id="full_name" name="full_name" placeholder="Full Name" value="<?= $full_name ?>">
                    <?php if (count($errors["full_name"]) > 0): ?>
                        <div class="text-start invalid-feedback">
                            <ul class="list-unstyled">
                                <?php foreach ($errors["full_name"] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" class="form-control <?php if (count($errors["password"]) > 0): ?>is-invalid<?php endif; ?>" id="password" name="password" placeholder="Password" value="<?= $password ?>">
                    <?php if (count($errors["password"]) > 0): ?>
                        <div class="text-start invalid-feedback">
                            <ul class="list-unstyled">
                                <?php foreach ($errors["password"] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-key"></i>
                    </span>
                    <input type="password" class="form-control <?php if (count($errors["password_confirm"]) > 0): ?>is-invalid<?php endif; ?>" id="password_confirm" name="password_confirm" placeholder="Confirm your password" value="<?= $password_confirm ?>">
                    <?php if (count($errors["password_confirm"]) > 0): ?>
                        <div class="text-start invalid-feedback">
                            <ul class="list-unstyled">
                                <?php foreach ($errors["password_confirm"] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary col-12">Sign Up</button>
            </div>
            <div class="mb-3">
                <a href="main/login" type="button" class="btn btn-outline-danger col-12">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php
include('./utils/footer.php');
?>
