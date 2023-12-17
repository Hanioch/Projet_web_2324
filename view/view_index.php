<!DOCTYPE html>
<html lang="fr" class="h-100">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <base href="<?= $web_root ?>"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
              integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Caduc Notes</title>
    </head>
    <body class="bg-dark h-100">
        <div class="container h-100">
            <div class="row p-3 h-100 justify-content-center align-items-center">
                <form class="p-3 border text-white text-center" action="main/login" method="post">
                    <fieldset class="h4">Sign in</fieldset>
                    <hr>
                    <div class="form-group">
                        <label class="hide" for="email"></label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label class="hide" for="password"></label>
                        <input type="password" class="form-control" id="password" placeholder="Enter password">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary col-12 my-4">Login</button>
                    </div>
                    <div class="form-group">
                        <a href="main/signup"><u>New here ? Click here to subscribe !</u></a>
                    </div>
                </form>
<!--                --><?php //if (count($errors) != 0): ?>
<!--                    <div class='errors'>-->
<!--                        <p>Please correct the following error(s) :</p>-->
<!--                        <ul>-->
<!--                            --><?php //foreach ($errors as $error): ?>
<!--                                <li>--><?php //= $error ?><!--</li>-->
<!--                            --><?php //endforeach; ?>
<!--                        </ul>-->
<!--                    </div>-->
<!--                --><?php //endif; ?>
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