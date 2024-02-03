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
                                <input type="text" name="listShares" class="form-control text-white custom-placeholder bg-dark border-secondary fst-italic" placeholder="<?=$share['full_name']?> (<?= $share['editor'] ? 'editor' : 'reader' ?>)" aria-label="Recipient's username with two button addons" disabled>
                                <form action="./notes/shares/<?php echo $noteId; ?>" method="post" >
                                    <input type="hidden" name="user" value="<?=$share['user']?>">
                                    <button class="btn btn-primary border-secondary border rounded-0" type="submit" name="changePermission">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <form action="./notes/shares/<?php echo $noteId; ?>" method="post" >
                                    <input type="hidden" name="user" value="<?=$share['user']?>">
                                    <button class="arrondirbtn btn btn-danger border-secondary" type="submit" name="removeShare">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                    <form action="./notes/shares/<?php echo $noteId; ?>" method="post" >
                        <div class="input-group mb-3">
                            <select class="form-select bg-dark text-white border-secondary " name="user">
                                <option disabled selected>-User-</option>
                                <?php foreach ($usersToShareWith as $user): ?>
                                        <option value="<?= $user->getId() ?>"><?= $user->getFullName() ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select bg-dark text-white border-secondary " name="permission">
                                <option disabled selected>-Permission-</option>
                                <option value="1">Editor</option>
                                <option value="0">Reader</option>
                            </select>
                            <input type="hidden" name="noteId" value="<?= $noteId?>">
                            <button type="submit" name="addShare" class="btn btn-primary border-secondary">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </form>
                <?php if (!empty($errorAdd)): ?>
                    <div class="alert alert-warning" role="alert">
                        <?= htmlspecialchars($errorAdd) ?>
                    </div>
                <?php endif; ?>
            </div>
<?php endif; ?>
</div>
<?php include('utils/footer.php'); ?>