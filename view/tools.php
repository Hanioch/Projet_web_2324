<?php
function show_note(array $arr_notes, string $title, string $title_page): void
{
?>
    <h4 id="<?=$title?>" class="title-note"><?= $title ?></h4>
    <?php
    $is_param_exist = isset($_GET["param1"]);
    $param = $is_param_exist ? $_GET["param1"] : "";

    $num_list = $title === "Pinned" ? 1 : 2;
    ?>
    <ul id="sortable<?= $num_list ?>" class="list-note connectedSortable">
        <?php
        for ($i = 0; $i < count($arr_notes); $i++) {
            $note = $arr_notes[$i];
            $open_note_url = "./Notes/open_note/" . $note->get_id();

            if ($is_param_exist) $open_note_url = $open_note_url . "/" . $param;
        ?>
            <li id="<?= $note->get_id() ?>" class="note ui-state-<?= $num_list === 1 ? "default" : "highlight" ?>ui-state-default">
                <a href="<?= $open_note_url ?>" class="link-open-note">
                    <div class="header-in-note"><?= $note->get_title() ?></div>
                    <div class="body-note">
                        <?php
                        if (property_exists($note, 'content')) {
                            $max_lg = 75;
                            $content = $note->get_content() === null ? "" : $note->get_content();
                            $content_sub = mb_strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..."  : $content;
                        ?>
                            <p class='card-text mb-0'><?= $content_sub ?></p>
                            <?php
                        } else {
                            $items = $note->get_list_item();
                            $list_item_showable = count($items) > 3 ? array_slice($items, 0, 3) : $items;
                            foreach ($list_item_showable as $item) :
                                $max_lg = 15;
                                $content = $item->get_content();
                                $content_sub = mb_strlen($content) > $max_lg ? substr($content, 0, $max_lg) . "..." : $content;
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input cursor-pointer" type="checkbox" value="" <?= $item->is_checked() ? "checked" : ""  ?> disabled>
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
                    <div class="form-check">
                        <?php
                        $labels = Label::get_labels_by_note_id($note->get_id());
                        if (!empty($labels)) { ?>
                            <form action="notes/edit_labels/<?= $note->get_id() ?>" method="POST" class="navbar-brand d-inline-block">
                                <input type="hidden" name="note_id" value="<?= $note->get_id() ?>">
                                <button type="submit" class="btn-icon" style="background: none; border: none; color: inherit; ">
                                    <i class="bi bi-tag"></i>
                                </button>
                            </form>
                        <?php } ?>
                        <?php
                        foreach ($labels as $label) { ?>
                            <span class="badge rounded-pill bg-secondary opacity-50"><?= $label->get_label_name() ?></span>
                        <?php }
                        ?>
                    </div>
                </a>
                <?php
                if ($title_page === Page::Notes->value) {
                ?>
                    <noscript>
                        <form action="./notes/move_note/" method="post" class="footer-in-note">
                            <?php
                            if ($i !== 0) {
                            ?>
                                <button name="action" type="submit" class="button-mv-note" value="increment">
                                    <i class="bi bi-chevron-double-left icon-mv-note i-left"></i>
                                </button>

                            <?php
                            }
                            if ($note->get_id() != end($arr_notes)->get_id()) {
                            ?>
                                <button name="action" type="submit" class="button-mv-note" value="decrement">
                                    <i class="bi bi-chevron-double-right icon-mv-note i-right"></i>
                                </button>

                            <?php
                            }
                            ?>
                            <input type="hidden" name="id" value=<?= $note->get_id() ?>>

                        </form>
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
    case Search = "Search";
}; ?>