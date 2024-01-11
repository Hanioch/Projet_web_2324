<?php
function show_note($arr_notes, $title)
{
?>
    <h4 class=" title-note"><?= $title ?></h4>
    <ul class="list-note">
        <?php
        for ($i = 0; $i < count($arr_notes); $i++) {
            $note = $arr_notes[$i];
        ?>
            <li class="note">
                <div class="header-in-note"><?= $note->title ?></div>
                <div class="body-note">
                    <?php if (property_exists($note, 'content')) {
                        $max_lg = 75;
                        $content = $note->content === null ? "" : $note->content;
                        $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..."  : $content;
                    ?>
                        <p class='card-text mb-0'><?= $content_sub ?></p>
                        <?php

                    } else {
                        $items = $note->list_item;
                        $list_item_showable = count($items) > 3 ? array_slice($items, 0, 3) : $items;
                        foreach ($list_item_showable as $item) :
                            $max_lg = 15;
                            $content = $item->content;
                            $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..." : $content;
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?= $item->checked ? "checked" : ""  ?> disabled>
                                <label class="form-check-label" for="flexCheckDefault">
                                    <?= $content_sub ?>
                                </label>
                            </div>
                        <?php
                        endforeach;
                        if (count($items) > 3) {
                        ?>
                            <p class="card-text">...</p>
                    <?php
                        }
                    }
                    ?>
                </div>
                <div class="footer-in-note">
                    <?php if ($i !== 0) {
                    ?>
                        <i class="bi bi-chevron-double-left icon i-left"></i>
                    <?php
                    }
                    if ($note->id != end($arr_notes)->id) {
                    ?>
                        <i class="bi bi-chevron-double-right icon i-right"></i>
                    <?php
                    }
                    ?>
                </div>
            </li>
        <?php };
        ?>
    </ul>

<?php
}
$title_page = "My notes";
include("/Applications/MAMP/htdocs/prwb_2324_a04/utils/header.php");

$pinned_notes = $notes['pinned'];
$other_notes = $notes['other'];
if (count($pinned_notes) > 0) {
    show_note($pinned_notes, "Pinned");
}
if (count($other_notes) > 0) {
    show_note($other_notes, "Others");
}

if (count($pinned_notes) == 0 && count($other_notes) == 0) {
?>
    <h4 class="title-note">Your notes are empty.</h4>
<?php
}
?>
</div>
<footer class="footer-note">
    <i class="bi bi-file-earmark"></i>
    <i class="bi bi-ui-checks"></i>
</footer>

<?php
include('/Applications/MAMP/htdocs/prwb_2324_a04/utils/footer.php'); ?>