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
        .head{
            margin-top: px;
            margin-right: 5px;
            text-align: right;
            color: white;
        }
        .invalid-feedback {
             display: block;

        }
    </style>
</head>
<header>
    <div class="head">
        <nav class="navbar navbar-dark">
            <a class="navbar-brand" href="Settings/">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h5>Change Password</h5>
        </nav>
    </div>
</header>
<body class="bg-dark h-100">
<div class="container h-100">
    <div class="row p-3 h-100 justify-content-center ">
        <form class=" text-white" action="Settings/change_password" method="post">
            <div class="form-group">
                <label for="old_password" class="form-label">Old password</label>
                <div class="input-group">
                    <input type="password" class="form-control <?php if (count($errors["old_password"]) > 0): ?>is-invalid<?php endif; ?>" id="old_password" name="old_password" placeholder="Old">
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
            <div class="form-group">
                <label for="password" class="form-label">New password</label>
                <div class="input-group">
                    <input type="password" class="form-control <?php if (count($errors["password"]) > 0): ?>is-invalid<?php endif; ?>" id="password" name="password" placeholder="New">
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
            <div class="form-group">
                <label for="password_confirm" class="form-label">Confirm password</label>
                <div class="input-group">
                    <input type="password" class="form-control <?php if (count($errors["password_confirm"]) > 0): ?>is-invalid<?php endif; ?>" id="password_confirm" name="password_confirm" placeholder="Confirm">
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
            <div class="form-group">
                <button type="submit" class="btn btn-primary col-12 ">Save</button>
            </div>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?= $success ?>
                </div>
            <?php endif; ?>
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