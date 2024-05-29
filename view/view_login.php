<?php
include("utils/header_login.php");
?>
            <div class="row p-3 h-100 justify-content-center align-items-center">
                <form class="p-3 border rounded-4 text-white text-center" action="main/login" method="post">
                    <fieldset class="h4">Sign in</fieldset>
                    <hr>
                    <div class="form-group">
                        <label class="hide" for="mail"></label>
                        <input type="email" class="form-control <?php if (count($errors["mail"]) > 0): ?>is-invalid<?php endif; ?>" id="mail" name="mail" placeholder="Enter email">
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
                    <div class="form-group">
                        <label class="hide" for="password"></label>
                        <input type="password" class="form-control <?php if (count($errors["password"]) > 0): ?>is-invalid<?php endif; ?>" id="password" name="password" placeholder="Enter password">
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
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary col-12 my-4">Login</button>
                    </div>
                    <div class="form-group">
                        <a href="main/signup"><u>New here ? Click here to subscribe !</u></a>
                    </div>
                </form>
            </div>
        </div>
<?php
include('./utils/footer.php');
?>