<?php


function wpm_view_autotraining_page()
{
    if(!empty($_GET['cat_id'])) {
        wpm_autotraining_map($_GET['cat_id']);
    } else {
        wpm_autotrainings_list();
    }
}

function wpm_autotraining_map ($cat_id)
{
    global $wpdb;

    wpm_enqueue_style('view_autotraining', plugins_url('../css/view-autotraining.css', __FILE__));

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";

    $autotraining = $wpdb->get_row("SELECT a.*, b.count, b.parent
                                    FROM " . $terms_table . " AS a
                                    JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                    WHERE b.taxonomy='wpm-category' AND a.term_id=" . $cat_id . ";", OBJECT);


    if (count($autotraining)) {
        $schedule     = wpm_autotraining_schedule_option($cat_id);
        $cnt_schedule = count($schedule);
    }
    ?>

    <div class="wrap nosubsub">
        <?php if(count($autotraining)):?>
            <h2>Обзор автотренинга «<?php echo $autotraining->name;?>»</h2>

            <br class="clear">

            <?php if($cnt_schedule):?>
                <div id="col-container">
                    <div class="col-wrap">

                        <table class="wp-list-table widefat fixed tags">
                            <tbody id="the-list" class="ui-sortable">
                            <tr>
                                <td>
                                    <div class="view-autotraining-list">
                                        <?php $cnt = 1;?>
                                        <?php foreach($schedule as $post_id => $data):?>
                                            <?php $post = get_post($post_id);?>

                                            <div class="autotraining-material">
                                                <div class="inner-wrapper">
                                                    <div class="title"><?php echo $post->post_title;?></div>
                                                    <div class="total-shift">
                                                        <?php if($data['shift']>0):?>
                                                            <b>Общее смещение: <?php echo wpm_get_time_text($data['shift']/3600);?></b>
                                                            <ul>
                                                                <?php $shift = ($data['shift'] - $data['transmitted_shift'])/3600;?>
                                                                <?php $shift = $shift > 0 ? '+' . wpm_get_time_text($shift) : 'Отсутствует';?>
                                                                <li>Собственное смещение <b><?php echo $shift;?></b></li>
                                                                <?php if($data['transmitted_shift'] > 0 && $data['is_postponed_due_to_homework']):?>
                                                                    <li>Домашнее задание с автоподтверждением и со смещением в одном из предыдущих материалов <b>+<?php echo $data['transmitted_shift']/3600;?>ч</b></li>
                                                                <?php endif;?>
                                                            </ul>
                                                        <?php else:?>
                                                            <b>Нет смещения.</b>
                                                        <?php endif;?>
                                                    </div>
                                                    <div class="homework">
                                                        <b>Домашнее задание: <?php echo $data['is_homework'] ? 'Есть' : 'Отсутствует';?></b>
                                                        <?php if($data['is_homework']):?>
                                                            <ul>
                                                                <li>
                                                                    <?php echo wpm_get_homework_title($data['homework_info']);?>
                                                                </li>
                                                            </ul>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <?php if($cnt != $cnt_schedule):?>
                                                    <div class="timeline-arrow"></div>
                                                <?php endif;?>
                                            </div>
                                            <?php $cnt++;?>
                                        <?php endforeach;?>

                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            <?php else:?>
                <p>График публикции материалов еще не составлен.</p>
            <?php endif;?>

        <?php else:?>
            <p>Произошла ошибка: Автотренинг не найден</p>
        <?php endif;?>

    </div>

    <?php
}

function wpm_autotrainings_list ()
{
    global $wpdb;

    $terms_table = $wpdb->prefix . "terms";
    $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";

    $autotrainings = $wpdb->get_results("SELECT a.*, b.count, b.parent
                                         FROM " . $terms_table . " AS a
                                         JOIN " . $term_taxonomy_table . " AS b ON a.term_id = b.term_id
                                         WHERE b.taxonomy='wpm-category';", OBJECT);

    $categories = array();

    if (count($autotrainings)) {
        foreach ($autotrainings as $autotraining) {
            if (wpm_is_autotraining($autotraining->term_id)) {
                $categories[] = array(
                    'edit_url'  => admin_url('/edit-tags.php?action=edit&taxonomy=wpm-category&tag_ID=' . $autotraining->term_id . '&post_type=wpm-page'),
                    'map_url'   => admin_url('/edit.php?post_type=wpm-page&page=wpm-view-autotraining&cat_id=' . $autotraining->term_id),
                    'cat_url'   => site_url('?wpm-category=' . $autotraining->slug),
                    'posts_url' => admin_url('/edit.php?wpm-category=' . $autotraining->slug . '&post_type=wpm-page'),
                    'count'     => $autotraining->count,
                    'parent'    => $autotraining->parent,
                    'name'      => $autotraining->name,
                    'slug'      => $autotraining->slug,
                    'id'        => $autotraining->term_id,
                );
            }
        }
    }
    $nb_categories = count($categories);
    ?>

    <div class="wrap nosubsub">
        <h2>Автотренинги</h2>

        <div id="ajax-response"></div>

        <form class="search-form" method="get" action="">
            <input type="hidden" value="wpm-view-autotraining" name="taxonomy">
            <input type="hidden" value="wpm-page" name="post_type">
            <p class="search-box">
                <label class="screen-reader-text" for="tag-search-input">Найти автотренинг:</label>
                <input id="tag-search-input" type="search" value="" name="s">
                <input id="search-submit" class="button" type="submit" value="Найти автотренинг" name="">
            </p>
        </form>

        <br class="clear">

        <div id="col-container">
            <div class="col-wrap">
                <form id="posts-filter" method="post" action="">

                    <div class="tablenav top">
                        <div class="tablenav-pages one-page">
                            <span class="displaying-num"><?php echo $nb_categories;?> элемента</span>
                        </div>
                        <br class="clear">
                    </div>

                    <?php if ($nb_categories > 0):?>
                        <table class="wp-list-table widefat fixed tags">
                            <thead>
                            <tr>
                                <th class="manage-column column-name sortable"><a><span>Название</span></a></th>
                                <th class="manage-column column-description sortable"><a><span>Описание</span></a></th>
                                <th class="manage-column column-slug sortable"><a><span>Ярлык</span></a></th>
                                <th class="manage-column column-posts num sortable"><a><span>МemberLux</span></a></th>
                            </tr>
                            </thead>

                            <tfoot>
                            <tr>
                                <th class="manage-column column-name sortable"><a><span>Название</span></a></th>
                                <th class="manage-column column-description sortable"><a><span>Описание</span></a></th>
                                <th class="manage-column column-slug sortable"><a><span>Ярлык</span></a></th>
                                <th class="manage-column column-posts num sortable"><a><span>МemberLux</span></a></th>
                            </tr>
                            </tfoot>

                            <tbody id="the-list" class="ui-sortable">

                            <?php $is_alternate = true;?>
                            <?php foreach($categories as $category):?>
                                <tr class="<?php echo $is_alternate ? 'alternate' : '';?>">
                                    <td class="name column-name">
                                        <strong>
                                            <a class="row-title" title="Обзор автотренинга «<?php echo $category['name'];?>»" href="<?php echo $category['map_url'];?>"><?php echo $category['name'];?></a>
                                        </strong>
                                        <br>
                                        <div class="row-actions">
                                                <span class="map">
                                                    <a href="<?php echo $category['map_url']?>">Обзор автотренинга</a>|
                                                </span>
                                                <span class="edit">
                                                    <a href="<?php echo $category['edit_url']?>">Изменить</a>|
                                                </span>
                                                <span class="view">
                                                    <a href="<?php echo $category['cat_url'];?>">Перейти</a>
                                                </span>
                                        </div>
                                        <div id="inline_2" class="hidden">
                                            <div class="name"><?php echo $category['name'];?></div>
                                            <div class="slug"><?php echo $category['slug'];?></div>
                                            <div class="parent"><?php echo $category['count'];?></div>
                                        </div>
                                    </td>
                                    <td class="description column-description"></td>
                                    <td class="slug column-slug"><?php echo urldecode($category['slug']);?></td>
                                    <td class="posts column-posts">
                                        <a href="<?php echo $category['posts_url']?>"><?php echo $category['count'];?></a>
                                    </td>
                                </tr>
                                <?php $is_alternate = !$is_alternate;?>
                            <?php endforeach;?>

                            </tbody>
                        </table>

                        <div class="tablenav bottom">
                            <div class="tablenav-pages one-page">
                                <span class="displaying-num"><?php echo $nb_categories;?> элемента</span>
                            </div>
                            <br class="clear">
                        </div>
                    <?php else:?>
                        <p>Нет автотренингов</p>
                    <?php endif;?>

                </form>
            </div>
        </div>
    </div>

<?php
}
