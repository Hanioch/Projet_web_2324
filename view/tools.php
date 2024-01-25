<?php
function show_note(array $arr_notes, string $title, string $titlePage): void
{
?>
    <h4 class=" title-note"><?= $title ?></h4>
    <ul class="list-note">
        <?php
        for ($i = 0; $i < count($arr_notes); $i++) {
            $note = $arr_notes[$i];
            $noteType = determineNoteType($titlePage);
            $openNoteUrl = "./Notes/open_note/" . $note->id . "/" . $noteType ;
            ?>
            <li class="note" onclick="window.location='<?= $openNoteUrl ?>'">
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
                <?php
                if ($titlePage === Page::Notes->value) {
                ?>
                    <form class="footer-in-note" action="notes/" method="post">
                        <?php if ($i !== 0) {
                        ?>
                            <button class="button-mv-note" type="submit" name="action" value="increment">
                                <i class="bi bi-chevron-double-left icon i-left"></i>
                            </button>
                        <?php
                        }
                        if ($note->id != end($arr_notes)->id) {
                        ?>
                            <button class="button-mv-note" type="submit" name="action" value="decrement">
                                <i class="bi bi-chevron-double-right icon i-right"></i>
                            </button>
                        <?php
                        }
                        ?>
                        <input type="hidden" name="id" value=<?= $note->id ?>>

                    </form>
                <?php
                }
                ?>

            </li>
        <?php };
        ?>
    </ul>

<?php
}
enum Page: string
{
    case Notes = "My notes";
    case Archives = "My archives";
    case Shared_by = "Shared by";
    case Settings = "Settings";
};?>
<?php
function determineNoteType(string $titlePage): string {

    return match ($titlePage) {
        Page::Notes->value => 'notes',
        Page::Archives->value => 'archives',
        default => 'shared_by'
    };} ?>
