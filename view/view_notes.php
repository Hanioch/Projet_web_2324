<?php
function show_note($arr_notes)
{
?>
    <ul class="list-note">
        <?php
        for ($i = 0; $i < count($arr_notes); $i++) {
            $note = $arr_notes[$i];
        ?>
            <li class="note">
                <div class="head-note"><?= $note->title ?></div>
                <div class="body-note">
                    <?php if (property_exists($note, 'content')) {
                        $max_lg = 75;
                        $content = $note->content === null ? "" : $note->content;
                        $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) : $content;
                    ?>
                        <p class='card-text mb-0'><?= $content_sub ?></p>
                        <?php
                        if (strlen($content) > $max_lg) {
                        ?>
                            <p class="card-text">...</p>
                        <?php
                        }
                    } else {
                        $items = $note->list_item;
                        $list_item_showable = count($items) > 3 ? array_slice($items, 0, 3) : $items;
                        foreach ($list_item_showable as $item) :
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php echo $item->checked ? "checked" : ""  ?> disabled>
                                <label class="form-check-label" for="flexCheckDefault">
                                    <?= $item->content ?>
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <base href="<?= $web_root ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Caduc Notes</title>
</head>

<body class="bg-dark min-vh-100">
    <div class=" container" style="margin-bottom: 15rem;">

        <?php
        $pinned_notes = $notes['pinned'];
        $other_notes = $notes['other'];
        if (count($pinned_notes) > 0) {
        ?>
            <h4 class="title-note">Pinned</h4>
        <?php
            show_note($pinned_notes);
        }
        if (count($other_notes) > 0) {
        ?>
            <h4 class="title-note">Others</h4>
        <?php
        }
        show_note($other_notes);

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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>