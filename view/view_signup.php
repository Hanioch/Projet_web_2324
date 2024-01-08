<!DOCTYPE html>
<html lang="fr" class="h-100">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?= $web_root ?>"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
              integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <title>Sign Up</title>
        <style>
        .input-group input {
            background-color: transparent;
        }
        .input-group-text{
            background-color: transparent;
            color: white;
        }

        </style>
    </head>
    <body class="bg-dark h-100">
    <div class="container h-100">
        <div class="row p-3 h-100 justify-content-center align-items-center">
            <form class="p-3 border rounded -10 text-white text-center" action="main/signup" method="post">
                <fieldset class="h4">Sign Up</fieldset>
                <hr>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-envelope "></i>
                        </span>
                    <input type="email" color="bg-dark" class="form-control <?php if (count($errors["mail"]) > 0): ?>is-invalid<?php endif; ?>" id="mail" name="mail" placeholder="Email" value="<?= htmlspecialchars($mail) ?>">
                    <?php if (count($errors["mail"]) > 0): ?>
                        <div class="text-left invalid-feedback">
                            <ul class="list-unstyled">
                                <?php foreach ($errors["mail"] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-person"></i>
                        </span>
                    <input type="full_name" class="form-control <?php if (count($errors["full_name"]) > 0): ?>is-invalid<?php endif; ?>" id="full_name" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($full_name) ?>">
                    <?php if (count($errors["full_name"]) > 0): ?>
                        <div class="text-left invalid-feedback">
                            <ul class="list-unstyled">
                                <?php foreach ($errors["full_name"] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-key"></i>
                        </span>
                    <input type="password" class="form-control <?php if (count($errors["password"]) > 0): ?>is-invalid<?php endif; ?>" id="password" name="password" placeholder="Password" value="<?= htmlspecialchars($password) ?>" >
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
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-key"></i>
                        </span>
                    <input type="password" class="form-control <?php if (count($errors["password_confirm"]) > 0): ?>is-invalid<?php endif; ?>" id="password_confirm" name="password_confirm" placeholder="Confirm your password" value="<?= htmlspecialchars($password_confirm) ?>">
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
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary col-12 ">Sign Up</button>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-outline-danger col-12 ">Cancel</button>
                </div>
            </form>
        </div>
    </div>
        <footer>
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
                    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
                    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
                    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        </footer>
    </body>
</html>