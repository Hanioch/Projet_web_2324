<?php
include("utils/header_error.php");
?>
<div class="main">
    <form method="POST" action="session1/index">
        <h6>User</h6>
        <select name="selected_user">
            <option >-- Select a User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= $user->get_id() ?>">
                    <?= htmlspecialchars($user->get_full_name()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">OK</button>
    </form>
    <?php if (!empty($notes)): ?>
        <div
            <h6>Checklist Notes for this User :</h6>
            <ul>
                <?php foreach ($notes as $note): ?>
                    <li>
                        <input type="checkbox" value="<?= $note->get_id() ?>">
                        <?= htmlspecialchars($note->get_title()) ?> (<?= $note->get_checked_count() ?>/<?= $note->get_total_count() ?> checked)
                    </li>
                <?php endforeach; ?>
            </ul>
            <button disabled>Toggle check for all items of selected notes</button>
        </div>
    <?php endif; ?>
</div>