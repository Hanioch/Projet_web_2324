<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <base href="<?= $web_root ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Caduc Notes</title>
</head>

<body class="bg-dark h-100">
    <div class="container h-100">
        <ul>
            <?php foreach ($notes as $note) : ?>
                <li>
                    <div class="card border-success mb-3" style="max-width: 18rem;">
                        <div class="card-header bg-transparent border-success"><?= $note->title ?></div>
                        <div class="card-body text-success">
                            <?php if (property_exists($note, 'content')) {

                            ?>
                                <p class='card-text'><?= $note->content ?></p>

                                <?php
                            } else {
                                foreach ($note->list_item as $item) :
                                ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" <?php echo $item->checked ? "checked" : ""  ?>>
                                        <label class="form-check-label" for="flexCheckDefault">
                                            <?= $item->content ?>
                                        </label>
                                    </div>
                                    <p class="card-text"> </p>
                            <?php
                                endforeach;
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <footer>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </footer>
</body>

</html>