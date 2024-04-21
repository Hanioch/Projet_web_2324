<?php
function show_note(array $arr_notes, string $title, string $titlePage): void
{
?>
    <h4 class="title-note"><?= $title ?></h4>
    <?php
    $numList = $title === "Pinned" ? 1 : 2;
    ?>
    <ul id="sortable<?= $numList ?>" class="list-note connectedSortable">
        <?php
        for ($i = 0; $i < count($arr_notes); $i++) {
            $note = $arr_notes[$i];
            $openNoteUrl = "./Notes/open_note/" . $note->get_Id();
        ?>
            <li id="<?= $note->get_Id() ?>" class="note ui-state-<?= $numList === 1 ? "default" : "highlight" ?>ui-state-default">
                <a href="<?= $openNoteUrl ?>" class="link-open-note">
                    <div class="header-in-note"><?= $note->get_Title() ?></div>
                    <div class="body-note">
                        <?php
                        if (property_exists($note, 'content')) {
                            $max_lg = 75;
                            $content = $note->get_Content() === null ? "" : $note->get_Content();
                            $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..."  : $content;
                        ?>
                            <p class='card-text mb-0'><?= $content_sub ?></p>
                            <?php
                        } else {
                            $items = $note->get_List_Item();
                            $list_item_showable = count($items) > 3 ? array_slice($items, 0, 3) : $items;
                            foreach ($list_item_showable as $item) :
                                $max_lg = 15;
                                $content = $item->get_Content();
                                $content_sub = strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..." : $content;
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input cursor-pointer" type="checkbox" value="" <?= $item->is_Checked() ? "checked" : ""  ?> disabled>
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
                </a>
                <?php
                if ($titlePage === Page::Notes->value) {
                ?>
                    <noscript>
                        <div class="footer-in-note">
                            <?php
                            $url_base = "./notes/move_note/" . $note->get_Id();

                            if ($i !== 0) {
                                $url_increment = $url_base . "/increment";
                            ?>
                                <a href="<?= $url_increment ?>" class="button-mv-note" value="increment">
                                    <i class="bi bi-chevron-double-left icon-mv-note i-left"></i>
                                </a>
                            <?php
                            }
                            if ($note->get_Id() != end($arr_notes)->get_Id()) {
                                $url_decrement = $url_base . "/decrement";
                            ?>
                                <a href="<?= $url_decrement ?>" class="button-mv-note" value="decrement">
                                    <i class="bi bi-chevron-double-right icon-mv-note i-right"></i>
                                </a>
                            <?php
                            }
                            ?>
                            <input type="hidden" name="id" value=<?= $note->get_Id() ?>>

                        </div>
                    </noscript>
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
}; ?>