<?php
function show_note(array $arr_notes, string $title, string $titlePage): void
{
?>
    <h4 class=" title-note"><?= $title ?></h4>
    <ul class="list-note">
        <?php
        for ($i = 0; $i < count($arr_notes); $i++) {
            $note = $arr_notes[$i];
            $openNoteUrl = "./Notes/open_note/" . $note->getId() ;
        ?>
            <li class="note">
                <a href="<?= $openNoteUrl ?>">
                    <div class="header-in-note"><?= $note->getTitle() ?></div>
                    <div class="body-note">
                        <?php if (property_exists($note, 'content')) {
                            $max_lg = 75;
                            $content = $note->getContent() === null ? "" : $note->getContent();
                            $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..."  : $content;
                        ?>
                            <p class='card-text mb-0'><?= $content_sub ?></p>
                            <?php
                        } else {
                            $items = $note->getListItem();
                            $list_item_showable = count($items) > 3 ? array_slice($items, 0, 3) : $items;
                            foreach ($list_item_showable as $item) :
                                $max_lg = 15;
                                $content = $item->getContent();
                                $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..." : $content;
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input cursor-pointer" type="checkbox" value=""  <?= $item->isChecked() ? "checked" : ""  ?> disabled>
                                    <label class="form-check-label cursor-pointer">
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
                                    <i class="bi bi-chevron-double-left icon-mv-note i-left"></i>
                                </button>
                            <?php
                            }
                            if ($note->getId() != end($arr_notes)->getId()) {
                            ?>
                                <button class="button-mv-note" type="submit" name="action" value="decrement">
                                    <i class="bi bi-chevron-double-right icon-mv-note i-right"></i>
                                </button>
                            <?php
                            }
                            ?>
                            <input type="hidden" name="id" value=<?= $note->getId() ?>>

                        </form>
                    <?php
                    }
                    ?>
                </a>

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
}; ?>