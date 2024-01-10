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
        .head{
            margin-top: px;
            margin-right: 5px;
            text-align: right;
            color: white;
        }
        .link-with-icon {
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<header>
    <div class="head">
        <nav class="navbar navbar-dark">
            <a class="navbar-brand" href="Settings/test">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h5>Settings</h5>
        </nav>
    </div>
</header>
<body class="bg-dark h-100">
<div class="container h-100">
    <div class="row p-3">
        <div class="row-col-md-6">
            <h6 class="text-white">Hey <?= $user->full_name ?>!</h6>
        </div>
        <div class="col-md-6 p-3">
            <a href="Settings/edit_profile" class="text-white link-with-icon"><i class="bi bi-person-gear"> Edit Profile</i></a><br>
            <a href="Settings/change_password" class="text-white link-with-icon"><i class="bi bi-lock"> Change Password</i></a><br>
            <a href="Settings/logout_user" class="text-white link-with-icon"><i class="bi bi-box-arrow-right"> Logout</i></a>
        </div>
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