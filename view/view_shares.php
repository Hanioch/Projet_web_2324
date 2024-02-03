<?php
$title_page = "Shares";

if (isset($error) && !empty($error)) {
    include("utils/header_error.php");
}else{
    include('utils/header_settings.php');
}

?>
<?php if (isset($error) && !empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
    </div>
<?php else: ?>
            <div class="text-white fst-italic ">
                <?php if (empty($existingShares)): ?>
                    <p>This note is not shared yet.</p>
                <?php else: ?>
                    <ul class="list-group ">
                        <?php foreach ($existingShares as $share): ?>
                            <div class="input-group mb-3 ">
                                <input type="text" class="form-control text-white custom-placeholder bg-dark border-secondary" placeholder="<?=$share['full_name']?> (<?= $share['editor'] ? 'editor' : 'reader' ?>)" aria-label="Recipient's username with two button addons" disabled>
                                <form action="./notes/shares/<?php echo $noteId; ?>" method="post" >
                                    <input type="hidden" name="user" value="<?=$share['user']?>">
                                    <button class="btn btn-primary border" type="submit" name="changePermission">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <form action="./notes/shares/<?php echo $noteId; ?>" method="post" >
                                    <input type="hidden" name="user" value="<?=$share['user']?>">
                                    <button class="btn btn-danger border" type="submit" name="removeShare">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                    <form action="./notes/shares/<?php echo $noteId; ?>" method="post" >
                        <div class="input-group">
                            <select class="form-select bg-dark text-white border-secondary " name="user">
                                <option selected>-User-</option>
                                <?php foreach ($usersToShareWith as $user): ?>
                                        <option value="<?= $user->getId() ?>"><?= $user->getFullName() ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select bg-dark text-white border-secondary " name="permission">
                                <option selected>-Permission-</option>
                                <option value="1">Editor</option>
                                <option value="0">Reader</option>
                            </select>
                            <input type="hidden" name="noteId" value="<?= $noteId?>">
                            <button type="submit" name="addShare" class="btn btn-primary border">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </form>
            </div>
<?php endif; ?>
</div>
<?php include('utils/footer.php'); ?>