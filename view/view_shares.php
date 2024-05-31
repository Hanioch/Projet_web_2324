<?php
$title_page = "Shares";

if (isset($error) && !empty($error)) {
    include("utils/header_error.php");
} else {
    include('utils/header_settings.php');
}
?>
<?php if (isset($error) && !empty($error)): ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
<?php else: ?>
    <div class="text-white fst-italic ">
        <div class="" id="sharesContainer">
        <?php if (empty($existing_shares)): ?>
            <p>This note is not shared yet.</p>
        <?php else: ?>
            <ul class="list-group ">
                <?php foreach ($existing_shares as $share): ?>
                    <div class="input-group mb-3 ">
                        <input type="text" name="listShares" class="form-control text-white custom-placeholder bg-dark border-secondary fst-italic" placeholder="<?=$share['full_name']?> (<?= $share['editor'] ? 'editor' : 'reader' ?>)" aria-label="Recipient's username with two button addons" disabled>
                        <form action="./notes/shares/<?php echo $note_id; ?>" method="post" >
                            <input type="hidden" name="user" value="<?=$share['user']?>" id="userPermission">
                            <button class="btn btn-primary border-secondary border rounded-0" name="changePermission" id="changePermission" type="submit" onclick="changePermissions('<?php echo $share['note']; ?>', '<?php echo $share['user']; ?>')">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </form>
                       <form action="./notes/shares/<?php echo $note_id; ?>" method="post" >
                            <input type="hidden" name="user" value="<?=$share['user']?>" id="userRemove">
                            <button class="arrondirbtn btn btn-danger border-secondary" name="removeShare" id="removeShare"  type="submit" onclick="removeShares('<?php echo $share['note']; ?>', '<?php echo $share['user']; ?>')">
                                <i class="bi bi-x"></i>
                            </button>
                       </form>
                    </div>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        </div>
        <div class="" id="addContainer">
        <?php if (!empty($users_to_share_with)): ?>
            <form action="./notes/shares/<?php echo $note_id; ?>" method="post" >
                <div class="input-group mb-3">
                    <select class="form-select bg-dark text-white border-secondary " name="user" id="user">
                        <option disabled selected>-User-</option>
                        <?php foreach ($users_to_share_with as $user): ?>
                            <option value="<?= $user->get_id() ?>"><?= $user->get_full_name() ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select bg-dark text-white border-secondary" name="permission" id="permission">
                        <option disabled selected>-Permission-</option>
                        <option value="1">Editor</option>
                        <option value="0">Reader</option>
                    </select>
                    <input type="hidden" name="note_id" value="<?= $note_id?>" id="note_id">
                    <button id="addShare" name="addShare" class="btn btn-primary border-secondary"  type="submit" onclick="addShareOnClick()">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                This note has been shared with all users. There are no more users to share this note with.
            </div>
        <?php endif; ?>
        </div>
        <div class="" id="errorContainer">
        <?php if (!empty($error_add)): ?>
            <div class="alert alert-warning" role="alert">
                <?= $error_add ?>
            </div>
        <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/shares.js"></script>
<?php include('utils/footer.php'); ?>