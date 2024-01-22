<?php
include("./utils/header_login.php");
?>

    <div class="row justify-content-center align-items-center">
        <form class="p-3 border rounded-4 text-white text-center" action="main/addchecklistnote" method="post">
            <div class="mb-3">
                <div class="input-group">
                    <label class="input-group" for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title">
                    <label class="input-group" for="title">Items</label>
                    <ul>
                        <?php for($i = 0; $i < 5; $i++){?>
                            <li><input type="text" class="form-control" id="title" name="title"></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </form>
    </div>
<?php
include('./utils/footer.php');
?>