<?php


function wpm_autotraining_page()
{
    global $wpdb;
    wp_enqueue_script('admin-comments');
    wpm_enqueue_style('homework_response_css', plugins_url('../css/homework-response.css', __FILE__));
    wpm_enqueue_style('fontawesome', plugins_url('../plugins/file-upload/css/fontawesome/css/font-awesome.min.css', __FILE__));
    wpm_enqueue_style('wpm-fancybox', plugins_url('/member-luxe/js/fancybox/jquery.fancybox.css'));
    $response_table = $wpdb->prefix . "memberlux_responses";
    $users_table = $wpdb->prefix . "users";


    $condition = '';
    $filter_options = '';

    if(version_compare(get_bloginfo('version'), '3.9', '>=')) {
        $wppage_tinymce_options = array(
            'quicktags'     => false,
            'media_buttons' => false,
            'editor_height' => 100,
            'editor_class'  => 'wppage-footer-content',
            'tinymce'       => array(
                'toolbar1' => 'bold italic underline strikethrough | forecolor backcolor | justifyleft justifycenter justifyright | bullist numlist outdent indent |removeformat | link unlink hr',
                'toolbar2' => false,
                'toolbar3' => false,
                'forced_root_block' => 'p',
                'force_br_newlines' => false,
                'force_p_newlines'  => true,
                'remove_linebreaks' => true,
                'wpautop'           => true,
                'content_css_force' => ( plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                            .', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                                            .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                        )
            )
        );

    } else {
        $wppage_tinymce_options = array(
            'media_buttons' => false,
            'teeny'         => false,
            'quicktags'     => false,
            'textarea_rows' => 20,
            'editor_class'  => 'wppage-footer-content',
            'content_css'   => '',
            'tinymce'       => array(
                'theme_advanced_buttons1'   => 'bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,outdent,indent,|,removeformat,|,link,unlink,hr',
                'theme_advanced_buttons2'   => '',
                'theme_advanced_buttons3'   => '',
                'theme_advanced_buttons4'   => '',
                'theme_advanced_font_sizes' => '10px,11px,12px,13px,14px,15px,16px,17px,18px,19px,20px,21px,22px,23px,24px,25px,26px,27px,28px,29px,30px,32px,42px,48px,52px',
                'forced_root_block' => 'p',
                'force_br_newlines' => false,
                'force_p_newlines'  => true,
                'remove_linebreaks' => true,
                'wpautop'           => true,
                'content_css_force' => ( plugins_url() . '/member-luxe/templates/base/bootstrap/css/bootstrap.min.css'
                                            .', ' . plugins_url() . '/member-luxe/css/editor-style-wpm-homework.css?' . time()
                                            .', ' . plugins_url() . '/member-luxe/css/bullets.css'
                                        )
            )
        );
    }

    if (isset($_GET["status"])) {
        $status = $_GET["status"];
        $filter_options .= "&status=$status";

        if(in_array($status, array('approved', 'accepted'))) {
            $statusQuery = "IN ('approved','accepted')";
        } else {
            $statusQuery = "= '$status'";
        }

        if ($condition == '') {
            $condition .= " response_status $statusQuery";
        } else {
            $condition .= " AND response_status $statusQuery";
        }
    }else{
        $status = 'opened';
        $filter_options .= "&status=$status";

        if ($condition == '') {
            $condition .= " response_status = '$status'";
        } else {
            $condition .= " AND response_status = '$status'";
        }
    }

    if (isset($_GET['m']) && !empty($_GET['m'])) {
        if ($condition == '') {
            $condition .= " YEAR(response_date) = " . substr($_GET['m'], 0, 4) . " AND MONTH(response_date) = " . substr($_GET['m'], 4, 2);
        } else {
            $condition .= " AND YEAR(response_date) = " . substr($_GET['m'], 0, 4) . " AND MONTH(response_date) = " . substr($_GET['m'], 4, 2);
        }
    }

    if (isset($_GET['wpm-response-status']) && !empty($_GET['wpm-response-status'])) {
        if ($condition == '') {
            $condition .= " response_status = '" . $_GET['wpm-response-status'] . "' ";
        } else {
            $condition .= " AND response_status = '" . $_GET['wpm-response-status'] . "' ";
        }
    }

    $join = " ";

    if (isset($_GET['wpm-category']) && !empty($_GET['wpm-category'])) {
        $category_slug = urldecode($_GET['wpm-category']);

        $posts = get_posts(array(
                'post_type'      => 'wpm-page',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'wpm-category',
                        'field'    => 'slug',
                        'terms'    => $category_slug)
                )
            )
        );
        $ids = array();
        if (count($posts)) {
            foreach ($posts as $post) {
                $ids[] = $post->ID;
            }
        }

        if(!empty($ids)) {
            if (!empty($condition)) {
                $condition .= " AND post_id IN (" . implode(',', $ids) . ") ";
            } else {
                $condition .= " post_id IN (" . implode(',', $ids) . ") ";
            }
        }

    }

    if (isset($_GET['wpm-levels']) && !empty($_GET['wpm-levels'])) {
        $category_slug = urldecode($_GET['wpm-levels']);

        $posts = get_posts(array(
                'post_type'      => 'wpm-page',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'wpm-levels',
                        'field'    => 'slug',
                        'terms'    => $category_slug)
                )
            )
        );
        $ids = array();
        if (count($posts)) {
            foreach ($posts as $post) {
                $ids[] = $post->ID;
            }
        }

        if(!empty($ids)) {
            if (!empty($condition)) {
                $condition .= " AND post_id IN (" . implode(',', $ids) . ") ";
            } else {
                $condition .= " post_id IN (" . implode(',', $ids) . ") ";
            }
        }

    }

    if (isset($_GET['s']) && !empty($_GET['s'])) {
        $join .= " JOIN $users_table AS b ON user_id=b.ID ";
        $s = $_GET['s'];
        if (!empty($condition)) {
            $condition .= " AND ( b.user_login LIKE '%$s%')";
        } else {
            $condition .= " (b.user_login LIKE LIKE '%$s%')";
        }
    }

    if (isset($_GET["paged"])) {
        $page = $_GET["paged"];
        if ($page < 1) $page = 1;
    } else {
        $page = 1;
    };

    $posts_per_page = 100;
    $start_from = ($page - 1) * $posts_per_page;


    if (!empty($condition)) {
        $responses = $wpdb->get_results("SELECT *
                                         FROM $response_table
                                         $join
                                         WHERE $condition
                                         ORDER BY response_date DESC, response_status DESC
                                         LIMIT $start_from, $posts_per_page", OBJECT);

        $responses_count = $wpdb->get_results("SELECT COUNT(id)
                                               FROM $response_table
                                               $join
                                               WHERE $condition", ARRAY_A);

    } else {

        $responses = $wpdb->get_results("SELECT *
                                         FROM $response_table
                                         $join
                                         ORDER BY response_date DESC, response_status DESC
                                         LIMIT $start_from, $posts_per_page", OBJECT);

        $responses_count = $wpdb->get_results("SELECT COUNT(id)
                                               FROM $response_table $join", ARRAY_A);
    }

    $total_records = $responses_count[0]['COUNT(id)'];
    $total_pages = ceil($total_records / $posts_per_page);
    $responses_nav_links = '';
    $base_url = admin_url('edit.php?post_type=wpm-page&page=wpm-autotraining');

    if ($page == 1) {
        $prev_link = '';
    } else {
        $prev_link = $base_url . $filter_options . '&paged=' . ($page - 1);
    }
    if ($page == $total_pages) {
        $next_link = '';
    } else {
        $next_link = $base_url . $filter_options . '&paged=' . ($page + 1);
    }

    $first_link = $base_url . $filter_options . '&paged=1';
    $last_link = $base_url . $filter_options . '&paged=' . $total_pages;

    $status_list = array(
        array(
            'status' => 'all',
            'title'  => '-- Все --'
        ),
        array(
            'status' => 'opened',
            'title'  => 'Открытые'
        ),
        array(
            'status' => 'approved',
            'title'  => 'Утвержденные'
        ),
        array(
            'status' => 'rejected',
            'title'  => 'Не утвержденные'
        )
    );


    ?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"></div>
        <h2>Ответы</h2>
        <div class="page-content-wrap">
            <form id="posts-filter" action="<?php echo $base_url; ?>" onsubmit="resetFilter(this);">
                <input type="hidden" name="post_type" value="wpm-page">
                <input type="hidden" name="page" value="wpm-autotraining">
                <input type="hidden" name="status" value="<?php echo $_GET['status']; ?>">

                <ul class="subsubsub">
                    <li class="open"><a href="<?php echo $base_url.'&'; ?>status=opened" class="<?php if($status == 'opened') echo 'current'; ?>">Ожидающие <span class="count">(<span class="pending-count"><?php echo wpm_get_response_counter('opened') ?></span>)</span></a> |</li>
                    <li class="approved"><a href="<?php echo $base_url.'&'; ?>status=approved" class="<?php if($status == 'approved') echo 'current'; ?>">Одобренные <span class="count">(<span class="pending-count"><?php echo wpm_get_response_counter('approved') ?></span>)</span></a> |</li>
                    <li class="not_approved"><a href="<?php echo $base_url.'&'; ?>status=rejected" class="<?php if($status == 'rejected') echo 'current'; ?>">Не одобренные <span class="count">(<span class="pending-count"><?php echo wpm_get_response_counter('rejected') ?></span>)</span></a></li>
                    </span></a></li>
                </ul>

                <p class="search-box">
                    <label class="screen-reader-text" for="post-search-input">Пошук:</label>
                    <input type="search" id="post-search-input" name="s" value="<?php echo $_GET['s']; ?>">
                    <input type="submit" name="" id="search-submit" class="button" value="Поиск">
                </p>
                <br><br>

                <div class="keys-nav-links tablenav top">
                    <div class="alignleft actions">
                        <?php wpm_homework_filters();?>
                        <input type="submit" name="" id="post-query-submit" class="button" value="Фильтр">
                    </div>
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $total_records; ?> ответов</span>
		            <span class="pagination-links">
		                <a class="first-page disabled" title="Перейти на первую страницу"
                           href="<?php echo $first_link; ?>">«</a>
		                <a class="prev-page" title="Перейти на предыдущую страницу" href="<?php echo $prev_link; ?>">‹</a>
		                <span class="paging-input">
		                    <input class="current-page" title="Текущая страница" type="text" name="paged"
                                   value="<?php echo $page; ?>" size="1"> из
		                    <span class="total-pages"><?php echo $total_pages; ?></span>
		                </span>
		                <a class="next-page" title="Перейти на следующую страницу" href="<?php echo $next_link; ?>">›</a>
                            <a class="last-page" title="Перейти на последнюю страницу" href="<?php echo $last_link; ?>">»</a>
		            </span>
                    </div>
                </div>
                <div class="responses">
                    <?php
                    if (!empty($responses)) {
                        echo '<table id="the-comment-list" class="wp-list-table widefat fixed pages">
		            <thead>
		                <tr>
		                <th class="manage-column column-author sortable "><a>Автор</a></th>
		                <th class="manage-column column-comment sortable "><a>Ответ</a></th>
		                <th class="manage-column column-comment sortable "><a>Дата</a></th>
		                <th class="manage-column column-response sortable "><a>Урок</a></th>
		                <th class="manage-column column-date sortable"><a>Статус</a></th>
		                </tr>
		            </thead>
		            <tfoot>
		                <tr>
		                <th class="manage-column column-author sortable "><a>Автор</a></th>
		                <th class="manage-column column-comment sortable "><a>Ответ</a></th>
		                <th class="manage-column column-comment sortable "><a>Дата</a></th>
		                <th class="manage-column column-response sortable "><a>Урок</a></th>
		                <th class="manage-column column-date sortable"><a>Статус</a></th>
		                </tr>
		            </tfoot>
		            ';
                        $i = 0;
                        foreach ($responses as $item) {
                            $i++;
                            $alternative = ($i % 2) ? 'alternate' : '';
                            $status_class = (!in_array($item->response_status, array('approved', 'accepted'))) ? '' : '';

                            if($item->response_status == 'rejected') {
                                $status_class .= ' rejected ';
                            }

                            $user_info = get_userdata($item->user_id);
                            $user_profile_url = admin_url('/user-edit.php?user_id=' . $item->user_id);
                            $author = '<a href="'.$user_profile_url.'">'.$user_info->user_login.'</a>';

                            $date = ($item->response_date == "0000-00-00 00:00:00") ? "" : date("Y-m-d", strtotime($item->response_date));
                            $content = apply_filters('the_content', $item->response_content);
                            $lesson = get_the_title($item->post_id);
                            $lesson = '<a href="'.get_permalink($item->post_id).'" target="_blank">'.$lesson.'</a>';

                            switch ($item->response_status) {
                                case 'opened':
                                    $status = 'Ожидается проверка';
                                    break;
                                case 'approved':
                                    $status = 'Ответ правильный';
                                    break;
                                case 'accepted':
                                    $status = 'Ответ принят автоматически';
                                    break;
                                case 'rejected':
                                    $status = 'Ответ неправильный';
                                    break;

                                default:
                                    $status = '';
                                    break;
                            }

                            $reviews = getResponseReviews($item->id);
                            $reviewsHtml = '';
                            $last_review = '';
                            $last_review_id = 0;
                            if(is_array($reviews) && count($reviews)) {
                                foreach($reviews AS $review) {
                                    $reviewsHtml .= getReviewHtml($review->review_content, $review->id, $review->review_date);
                                    $last_review  = $review->review_content;
                                    $last_review_id  = $review->id;
                                }
                                $reviewsStyle = '';
                                $editReply = '<span class="edit-reply hide-if-no-js">
                                                  &nbsp;|&nbsp;
                                                  <a onclick="closeTaskEditor();window.commentReply && commentReply.open( \''.$item->id.'\',\''.$item->post_id.'\', \'wpm_update_response_action\' );initRedactor(' . $item->id . '); add_edit_review_input(' . $item->id . '); return false;"
                                                     class="vim-r"
                                                     title="Редактировать последний ответ"
                                                     href="#">Редактировать последний ответ</a>
                                              </span>';
                            } else {
                                $reviewsStyle = 'style="display:none;"';
                                $editReply = '';
                            }

                            $attachments = UploadHandler::getHomeworkAttachments($item->post_id, $item->user_id);
                        ?>
                            <tr class="status-publish hentry iedit <?php echo $alternative . $status_class ?>" id="comment-<?php echo $item->id; ?>">
                                <td class="manage-column column-title sortable desc"><?php echo $author; ?></td>
                                <td class="manage-column column-title sortable desc response_content">
                                    <?php echo stripslashes($content); ?>
                                    <?php if (!empty($attachments['files'])) : ?>
                                        <span class="homework-attachments">
                                            <?php foreach ($attachments['files'] AS $file) : ?>
                                                <?php if (isset($file->thumbnailUrl)) : ?>
                                                    <a href="<?php echo $file->url; ?>" title="<?php echo $file->name; ?>" rel="wpm_homework_file_<?php echo $item->id; ?>" class="fancybox">
                                                        <img src="<?php echo $file->thumbnailUrl; ?>">
                                                    </a>
                                                <?php else : ?>
                                                    <a href="<?php echo $file->url; ?>" title="<?php echo $file->name; ?>" download>
                                                        <i class="fa fa-file-<?php echo $file->extension=='rar'?'zip':$file->extension?>-o" aria-hidden="true"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="row-actions">
                                    <span class="approve"><a href="" data-action="approve" response-status="approved" post-id="<?php echo $item->post_id;?>" response-id="<?php echo $item->id;?>" class="update-response vim-a" title="Одобрить этот ответ">Одобрить</a> | </span>
                                    <span class="unapprove"><a href="" data-action="unapprove" response-status="opened" response-id="<?php echo $item->id;?>" class="update-response vim-u" title="Отклонить этот ответ">Отклонить</a></span>
                                    <span class="not_approve"> | <a href="" data-action="not_approve" response-status="rejected" post-id="<?php echo $item->post_id;?>" response-id="<?php echo $item->id;?>" class="update-response vim" title="Не правильно">Не правильно</a></span>
                                    <span class="reply hide-if-no-js"> | <a onclick="closeTaskEditor();window.commentReply && commentReply.open( '<?php echo $item->id;?>','<?php echo $item->post_id;?>', 'wpm_update_response_action' );initRedactor();return false;" class="vim-r" title="Ответить" href="#">Ответить</a></span>
                                    <?php echo $editReply; ?>
                                    <span class="delete_response hide-if-no-js"> | <a href="" data-action="delete_response" response-status="delete" post-id="<?php echo $item->post_id;?>" response-id="<?php echo $item->id;?>" class="update-response vim-d" title="Удалить навсегда">Удалить навсегда</a></span>
                                    </div>
                                    <div class="admin-response-reviews" <?php echo $reviewsStyle;?>>
                                        <div class="admin-review-title">Комментарии:</div><?php echo $reviewsHtml;?>
                                    </div>
                                </td>
                                <td class="manage-column column-title sortable desc"><?php echo  date('H:i', strtotime($item->response_date)) ;?> <small><?php echo date('d.m.Y', strtotime($item->response_date)) ;?></small></td>
                                <td class="manage-column column-title sortable desc"><?php echo  $lesson ;?></td>
                                <td class="manage-column column-title sortable desc">
                                    <?php echo $status ;?><br /><i>
                                    <?php echo  wpm_get_event_log($item->id, $item->response_status) ;?></i>
                                </td>
                            </tr>
                        <?php
		            }
                        echo '
		            </table>';
                    } else {
                        echo '<p>Нет ответов</p>';
                    }
                    ?>
                </div>
            </form>
            <form method="get" action="">
                <table style="display:none;"><tbody id="com-reply"><tr id="replyrow" style="display: none;"><td colspan="4" class="colspanchange">
                    <div id="replyhead" style=""><h5>Ответить</h5></div>
                    <div id="replycontainer">
                    <?php
                    $quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
                    wp_editor( '', 'replycontent', $wppage_tinymce_options );
                    ?>
                    </div>

                    <p id="replysubmit" class="submit">
                        <a href="#comments-form" class="wpm-reply-save button-primary alignright"><span style="">Ответить</span></a>
                        <a href="#comments-form" class="wpm-reply-cancel button-secondary alignleft">Отмена</a>
                        <span class="waiting spinner"></span>
                        <span class="error" style="display: none;"></span>
                        <br class="clear">
                    </p>

                    <!--<input type="hidden" name="user_ID" id="user_ID" value="<?php /*echo get_current_user_id(); */?>">-->
                    <input type="hidden" name="action" value="wpm_update_response_action">
                    <input type="hidden" name="response_id" id="comment_ID" value="">
                    <input type="hidden" name="post_id" id="comment_post_ID" value="">
                    <input type="hidden" name="response_status" id="status" value="reply">
                    <!--<input type="hidden" name="position" id="position" value="-1">
                    <input type="hidden" name="checkbox" id="checkbox" value="1">
                    <input type="hidden" name="mode" id="mode" value="detail">
                    <input type="hidden" id="_ajax_nonce-replyto-comment" name="_ajax_nonce-replyto-comment" value="be85cd71e2"><input type="hidden" id="_wp_unfiltered_html_comment" name="_wp_unfiltered_html_comment" value="2fe1af2039"></td></tr></tbody>
                    -->
                </table>
                </form>
                <div id="ajax-response"></div>
            <script type="text/javascript">
                function resetFilter(elem) {
                    var $ = jQuery,
                        $form = $(elem).closest('form');

                    if($('#post-search-input').val() != '') {
                        $form.find('.keys-nav-links select').each(function(){
                            $(this).val($(this).find('option:first').attr('value'));
                        });
                    }

                }

                function resetSearch() {
                    jQuery('#post-search-input').val('');
                }

                function add_edit_review_input (response_id) {
                    var id = jQuery('#comment-' + response_id).find('.admin-response-review:last').attr('data-review-id');

                    jQuery('<input type="hidden" name="edit_review" id="edit_review" value="' + id + '">').insertAfter('#status');
                }

                function initRedactor (response_id) {
                    var init, edId, qtId, firstInit, wrapper, c = '';

                    response_id = response_id || false;

                    if (response_id !== false) {
                        c = jQuery('#comment-' + response_id).find('.admin-response-review:last').html();
                    }

                    jQuery('#replycontainer').html('<div id="wp-replycontent-wrap" class="wp-core-ui wp-editor-wrap tmce-active"><link rel=\'stylesheet\' id=\'editor-buttons-css\'  href=\'http://wp/wp-includes/css/editor.min.css?ver=3.9.2\' type=\'text/css\' media=\'all\' />'
                        + '<div id="wp-replycontent-editor-container" class="wp-editor-container"><textarea class="wppage-footer-content wp-editor-area" style="height: 100px" autocomplete="off" cols="40" name="replycontent" id="replycontent">' + c + '</textarea></div></div>');
                    tinyMCE.editors = [];

                    if (typeof tinymce !== 'undefined') {
                        for (edId in tinyMCEPreInit.mceInit) {
                            if (firstInit) {
                                init = tinyMCEPreInit.mceInit[edId] = tinymce.extend({}, true, tinyMCEPreInit.mceInit[edId]);
                            } else {
                                init = firstInit = tinyMCEPreInit.mceInit[edId];
                            }

                            wrapper = tinymce.DOM.select('#wp-' + edId + '-wrap')[0];

                            if (( tinymce.DOM.hasClass(wrapper, 'tmce-active') || !tinyMCEPreInit.qtInit.hasOwnProperty(edId) ) && !init.wp_skip_init) {
                                try {
                                    tinymce.init(init);

                                    if (!window.wpActiveEditor) {
                                        window.wpActiveEditor = edId;
                                    }
                                } catch (e) {
                                }
                            }
                        }
                    }

                    if (typeof quicktags !== 'undefined') {
                        for (qtId in tinyMCEPreInit.qtInit) {
                            try {
                                quicktags(tinyMCEPreInit.qtInit[qtId]);

                                if (!window.wpActiveEditor) {
                                    window.wpActiveEditor = qtId;
                                }
                            } catch (e) {
                            }
                        }
                    }

                    if (typeof jQuery !== 'undefined') {
                        jQuery('.wp-editor-wrap').on('click.wp-editor', function () {
                            if (this.id) {
                                window.wpActiveEditor = this.id.slice(3, -5);
                            }
                        });
                    } else {
                        for (qtId in tinyMCEPreInit.qtInit) {
                            document.getElementById('wp-' + qtId + '-wrap').onclick = function () {
                                window.wpActiveEditor = this.id.slice(3, -5);
                            }
                        }
                    }
                }
            jQuery(function($){
                $('.fancybox').fancybox();
                $('.update-response').on('click', function(e){
                    var item = $(this);
                    $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            dataType: 'json',
                            data: {
                                'action': 'wpm_update_response_action',
                                'response_id': item.attr('response-id'),
                                'post_id': item.attr('post-id'),
                                'response_action': item.attr('data-action'),
                                'response_status': item.attr('response-status')
                            },
                            success: function (data) {
                                if(!data.error){
                                    window.location.reload();
                                }
                            }
                        });
                    e.preventDefault();
                });

                $(document).on('click', '.wpm-reply-cancel', function () {
                    closeTaskEditor();
                    return false;
                });

                $(document).on('click', '.wpm-reply-save', function () {
                    var post = {};

                    $('#replysubmit .error').hide();
                    $('#replysubmit .spinner').show();

                    $('#replyrow input').not(':button').each(function () {
                        var t = $(this);
                        post[ t.attr('name') ] = t.val();
                    });

                    post.content = tinyMCE.activeEditor.getContent();

                    $.ajax({
                        type     : 'POST',
                        url      : ajaxurl,
                        dataType : 'json',
                        data     : post,
                        success  : function (data) {
                            if (!data.error) {
                                closeTaskEditor();
                                if (post.edit_review === undefined) {
                                    $('#comment-' + post.response_id)
                                        .find('.admin-response-reviews')
                                        .show()
                                        .append(data.html);
                                } else {
                                    $('#comment-' + post.response_id)
                                        .find('.admin-response-reviews')
                                        .show();
                                    $('#edit_review').remove();
                                    $('[data-review-id="' + data.reply_id + '"]').replaceWith(data.html);
                                }
                            }
                        },
                        error    : function (r) {
                            commentReply.error(r);
                        }
                    });

                    return false;
                });
            });
                function closeTaskEditor () {
                    var $ = jQuery,
                        c,
                        replyrow = $('#replyrow');

                    $('#add-new-comment').css('display', '');

                    replyrow.hide();
                    $('#com-reply').append(replyrow);
                    $('#replycontent').css('height', '').val('');
                    $('#edithead input').val('');
                    $('.error', replyrow).html('').hide();
                    $('.spinner', replyrow).hide();
                }
            </script>
        </div>
    </div>


<?php
    wpm_enqueue_script('jquery-fancybox', plugins_url('/member-luxe/js/fancybox/jquery.fancybox.js'));
}

function wpm_get_event_log($response_id, $status)
{
    global $wpdb;

    $date = '';

    if ($status != 'opened') {

        $response_log_table = $wpdb->prefix . "memberlux_response_log";

        $log =  $wpdb->get_row("SELECT * FROM `" . $response_log_table . "`
                                         WHERE response_id = " . $response_id . " AND
                                               event = '" . $status . "'
                                         ORDER BY id DESC;", OBJECT);

        if ($log) {
            $date = date('H:i d.m.Y', strtotime($log->created_at));
        }
    }

    return $date;
}

function wpm_homework_filters ()
{
    global $wpdb, $wp_locale;

    $response_table = $wpdb->prefix . "memberlux_responses";

    $months = $wpdb->get_results("SELECT DISTINCT YEAR( response_date ) AS year, MONTH( response_date ) AS month
                                  FROM $response_table
                                  ORDER BY response_date DESC, response_status DESC", OBJECT);

    $month_count = count( $months );

    if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
        return;

    $m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
    ?>
    <label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
    <select name="m" id="filter-by-date" onchange="resetSearch()">
        <option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
        <?php
        foreach ($months as $arc_row) {
            if ( 0 == $arc_row->year ) {
                continue;
            }

            $month = zeroise( $arc_row->month, 2 );
            $year = $arc_row->year;

            printf( "<option %s value='%s'>%s</option>\n",
                selected( $m, $year . $month, false ),
                esc_attr( $arc_row->year . $month ),
                sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
            );
        }
        ?>
    </select>
<?php

    $filters = array(
        'wpm-category',
        'wpm-levels'
    );

    foreach ($filters as $tax_slug) {
        $tax_obj  = get_taxonomy($tax_slug);
        $tax_name = $tax_obj->labels->name;
        $tax_name = mb_strtolower($tax_name);
        $terms    = get_terms($tax_slug);

        echo "<select name='$tax_slug' id='$tax_slug' class='postform' onchange='resetSearch()'>";
        echo "<option value=''>Все $tax_name</option>";
        foreach ($terms as $term) {
            echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .'</option>';
        }
        echo "</select>";
    }

    ?>

    <select name="wpm-response-status" id="wpm-response-status" class="postform 111"  onchange="resetSearch()">
        <option value="">Все статусы</option>
        <option value="opened" <?php echo $_GET['wpm-response-status']=='opened'?' selected="selected"':'';?>>Ожидающие</option>
        <option value="approved" <?php echo $_GET['wpm-response-status']=='approved'?' selected="selected"':'';?>>Одобренные</option>
        <option value="not_approved" <?php echo $_GET['wpm-response-status']=='not_approved'?' selected="selected"':'';?>>Не одобренные</option>
    </select>

<?php
}


//-------------
add_action('wp_ajax_wpm_add_response_action', 'wpm_add_response'); // ajax for logged in users
function wpm_add_response()
{
    global $wpdb;
    $response_table = $wpdb->prefix . "memberlux_responses";

    $add_to_response_log = false;

    $result = array(
        'message' => '',
        'error' => false,
        'homework' => array()
    );

    $user_id = get_current_user_id();

    $page_meta = get_post_meta($_POST['post_id'], '_wpm_page_meta', true);
    $confirmation_method = array_key_exists('confirmation_method', $page_meta) ? $page_meta['confirmation_method'] : 'auto';

    if (!empty($_POST['response_content'])) {
        $response = wpm_response($user_id, $_POST['post_id']);

        if (empty($response)){
            switch ($confirmation_method) {
                case 'manually':
                    $args = array(
                        'user_id' => $user_id,
                        'post_id' => $_POST['post_id'],
                        'response_content' => $_POST['response_content'],
                        'response_date' => current_time('mysql'),
                        'response_status' => 'opened',
                        'response_type' => $_POST['response_type']
                    );
                    break;
                case 'auto_with_shift':
                    $approval_shift = array_key_exists('homework_shift_value', $page_meta)
                        ? ($page_meta['homework_shift_value'] * 60 * 60)
                        : 0;
                    $approval_shift = date('Y-m-d H:i:s', (time() + $approval_shift));

                    $args = array(
                        'user_id' => $user_id,
                        'post_id' => $_POST['post_id'],
                        'response_content' => $_POST['response_content'],
                        'response_date' => current_time('mysql'),
                        'response_status' => 'accepted',
                        'response_type' => $_POST['response_type'],
                        'approval_date' => $approval_shift,
                    );

                    $add_to_response_log = true;

                    break;
                default:
                    $args = array(
                        'user_id' => $user_id,
                        'post_id' => $_POST['post_id'],
                        'response_content' => $_POST['response_content'],
                        'response_date' => current_time('mysql'),
                        'response_status' => 'accepted',
                        'response_type' => $_POST['response_type']
                    );
            }

            $row = $wpdb->insert($response_table, $args);
        } else {
            $status = $confirmation_method=='manually' ? 'opened' : $response->response_status;

            $args = array(
                'response_content' => $_POST['response_content'],
                'response_date'    => current_time('mysql'),
                'response_status'  => $status
            );

            $where = array(
                'user_id' => $user_id,
                'post_id' => $_POST['post_id']
            );

            $row = $wpdb->update($response_table, $args, $where);
        }

        wpm_update_cat_autotraining_schedules($_POST['post_id'], $user_id, $confirmation_method);
        $result['homework'] = wpm_get_responses($user_id, $_POST['post_id'], $page_meta);
        if (isset($result['homework']['content'])) {
            $result['homework']['content'] .= UploadHandler::getHomeworkAttachmentsHtml($_POST['post_id'], $user_id);
        }

    }

    if($add_to_response_log) {
        $response = wpm_response($user_id, $_POST['post_id']);
        wpm_add_to_response_log($response->id, 'accepted');
    }

    wpm_alert_admin_by_email($_POST['response_content'], $_POST['post_id'], $user_id);

    if ($row === false) {
        $result['message'] = 'Произошла ошибка!';
        $result['error'] = true;
    } elseif ($row === 0) {
        $result['message'] = 'Ответ не сохранен!';
        $result['error'] = true;
    } elseif ($row > 0) {
        $result['message'] = 'Ответ отправлен!';
    }

    echo json_encode($result);
    die();
}

function wpm_alert_admin_by_email ($content, $post_id, $user_id)
{
    $post = get_post($post_id);

    $admin_email = get_option('admin_email');

    $user = get_user_by('id', $user_id);

    $content = trim($content);

    $text = 'Пользователь ' . $user->nice_name . ' (' . $user->user_email . ') отправил ответ на домашнее задание <i>"' . $post->post_title . '"</i>: <br /><br />' .
            wpautop($content);

    add_filter('wp_mail_content_type', 'set_html_content_type');

    wpm_send_mail($admin_email, 'Ответ на домашнее задание', $text, $user->nice_name, $user->user_email);

    remove_filter('wp_mail_content_type', 'set_html_content_type');
}

function wpm_add_to_response_log($response_id, $event)
{
    global $wpdb;

    $response_log_table = $wpdb->prefix . "memberlux_response_log";

    $args = array(
        'response_id' => $response_id,
        'event' => $event,
        'created_at' => current_time('mysql')
    );

    $wpdb->insert($response_log_table, $args);
}

function wpm_update_cat_autotraining_schedules($post_id, $user_id, $confirmation_method, $force = false)
{
    $term_list = wp_get_post_terms($post_id, 'wpm-category', array("fields" => "ids"));

    if (count($term_list)) {
        foreach ($term_list as $term_id) {
            wpm_update_schedule($term_id, $user_id, $confirmation_method, $force);
        }
    }
}

function wpm_homework_info($post_id, $user_id, $page_meta)
{
    $response = wpm_get_responses($user_id, $post_id, $page_meta);

    if (!empty($response['content'])) {
        if (in_array($response['real_status'], array('accepted', 'approved'))) {
            return array(
                'done' => true,
                'time' => strtotime($response['date'])
            );
        }
    }

    return array(
        'done' => false
    );
}

function wpm_update_schedule($term_id, $user_id, $confirmation_method, $force = false)
{
    $previous_post_id = null;
    $is_postponed_due_to_homework  = false;
    $last_homework_completion_time = null;

    $user_cat_data = wpm_user_cat_data($term_id, $user_id);

    if ($user_cat_data['is_training_started']) {

        foreach ($user_cat_data['schedule'] as $post_id => $data) {
            if ($data['is_postponed_due_to_homework'] && ($confirmation_method != 'manually' || $force)) {

                if(!$previous_post_id) {
                    $previous_post    = get_previous_post(true);
                    $previous_post_id = $previous_post->ID;
                }

                $previous_page_meta   = get_post_meta($previous_post_id, '_wpm_page_meta', true);
                $is_prev_has_homework = (array_key_exists('is_homework', $previous_page_meta) && $previous_page_meta['is_homework']=='on') ? true : false;
                $prev_homework_info   = wpm_homework_info($previous_post_id, $user_id, $previous_page_meta);

                if ($is_prev_has_homework && $prev_homework_info['done']) {
                    $last_homework_completion_time = $prev_homework_info['time'];
                }

                if (($is_prev_has_homework && !$prev_homework_info['done']) || $is_postponed_due_to_homework) {
                    $is_postponed_due_to_homework = true;
                    $user_cat_data['schedule'][$post_id]['is_postponed_due_to_homework'] = $is_postponed_due_to_homework;
                } else {
                    if (!empty($last_homework_completion_time)) {
                        $release_date = $last_homework_completion_time + $user_cat_data['schedule'][$post_id]['shift'];
                    } else {
                        $release_date = time() + $user_cat_data['schedule'][$post_id]['shift'];
                    }

                    $user_cat_data['schedule'][$post_id]['is_first'] = false;
                    $user_cat_data['schedule'][$post_id]['release_date'] = $release_date;
                    $user_cat_data['schedule'][$post_id]['is_postponed_due_to_homework'] = false;
                }
            }
            $previous_post_id = $post_id;
        }

        update_user_meta($user_id, 'cat_data_' . $term_id . '_' . $user_id, $user_cat_data);
    }
}

//-------------

add_action('wp_ajax_wpm_update_response_action', 'wpm_update_response'); // ajax for logged in users
function wpm_update_response()
{
    global $wpdb;
    $response_table = $wpdb->prefix . "memberlux_responses";
    $response_review_table = $wpdb->prefix . "memberlux_response_review";

    $result = array(
        'message' => '',
        'error'   => false
    );

    $response_id = $_POST['response_id'];
    $response_status = $_POST['response_status'];
    $approval_date = current_time('mysql');

    $is_edit = false;
    $reply_id = null;

    if ($response_status == 'reply') {

        if (isset($_POST['edit_review'])) {
            $edit_review = intval($_POST['edit_review']);
            $update = $wpdb->query($wpdb->prepare("UPDATE " . $response_review_table . "
                                                   SET review_content=%s
                                                   WHERE id=%d;", $_POST['content'], $edit_review));
            $is_edit = true;

            $result['reply_id'] = $edit_review;
        } else {
            $user_id = get_current_user_id();
            $update = $wpdb->query($wpdb->prepare("INSERT INTO " . $response_review_table . " (response_id, user_id, review_content, review_date)
                                                   VALUES (%d, %d, %s, %s);", $response_id, $user_id, $_POST['content'], $approval_date));
            $result['reply_id'] = $wpdb->insert_id;
        }
    } elseif($response_status == 'delete') {
        $update = $wpdb->delete($response_table, array('ID' => $response_id));
    }else {
        $update = $wpdb->query("UPDATE " . $response_table . "
                                SET response_status = '" . $response_status . "', approval_date = '" . $approval_date . "'
                                WHERE id = '" . $response_id . "';");
        wpm_add_to_response_log($response_id, $response_status);
    }

    if ($update === false) {
        $result['message'] = 'Произошла ошибка!';
        $result['error'] = true;
    } elseif ($update === 0) {
        $result['message'] = 'Не сохранен!';
        $result['error'] = true;
    } elseif ($update > 0) {
        $result['message'] = 'Сохранено!';
        if($response_status == 'reply') {

            $result['html'] = getReviewHtml($_POST['content'], $result['reply_id'], date('Y-m-d H:i:s'));

            if (!$is_edit) {
                mail_response_review($response_id, $_POST['content']);
            }
        } else {
            $condition = "id = " . intval($response_id);
            $response =  $wpdb->get_row("SELECT * FROM " . $response_table . "
                                         WHERE " . $condition . "", OBJECT);

            if ($response_status == 'approved') {
                wpm_update_cat_autotraining_schedules($response->post_id, $response->user_id, 'manually', true);
            }

            wpm_alert_user_about_homework ($response_status, $response);
        }
    }
    echo json_encode($result);
    die();
}

function wpm_alert_user_about_homework ($status, $response)
{
    $post = get_post($response->post_id);

    $user = get_user_by('id', $response->user_id);

    switch($status) {
        case 'opened':
            $message = '<b>ожидает проверку</b>';
            break;
        case 'approved':
            $message = 'отмечен как <b>правильный</b>';
            break;
        case 'rejected':
            $message = 'отмечен как <b>неправильный</b>';
            break;
        case 'accepted':
            $message = 'принят';
            break;
    }

    $text = 'Ваш ответ на домашнее задание <i>"' . $post->post_title . '"</i> ' . $message;

    add_filter('wp_mail_content_type', 'set_html_content_type');

    wpm_send_mail($user->user_email, 'Результат выполнения домашнего задания', $text, get_bloginfo("name"), get_option('admin_email'));

    remove_filter('wp_mail_content_type', 'set_html_content_type');
}

function mail_response_review($response_id, $review_content)
{
    global $wpdb;
    get_currentuserinfo();
    $response_table = $wpdb->prefix . "memberlux_responses";
    $condition = "id = " . intval($response_id);

    $response =  $wpdb->get_row("SELECT * FROM " . $response_table . "
                                          WHERE " . $condition . "", OBJECT);

    $user = get_user_by('id', $response->user_id);

    $response_content = trim($response->response_content);

    $text = 'Комментарий к Вашему ответу <br /> <i>"' . $response_content . '"</i>
    <br /><br />' . wpautop($review_content);

    add_filter('wp_mail_content_type', 'set_html_content_type');

    wpm_send_mail($user->user_email, 'Комментарий к Вашему ответу', $text, get_bloginfo("name"), get_option('admin_email'));

    remove_filter('wp_mail_content_type', 'set_html_content_type');
}

function set_html_content_type()
{

    return 'text/html';
}

function getReviewHtml($review_content, $reply_id, $date)
{
    return '<div class="admin-response-review" data-review-id="' . $reply_id . '">' .
                wpautop(stripslashes($review_content)) .
                '<small>Добавлен: ' . date('H:i d.m.Y', strtotime($date)) . '</small>' .
           '</div>';
}

function getResponseReviews($response_id)
{
    global $wpdb;
    $response_review_table = $wpdb->prefix . "memberlux_response_review";
    $condition = "response_id = " . intval($response_id);
    $main_options = get_option('wpm_main_options');
    $order =  array_key_exists('comments_order', $main_options['main']) ? $main_options['main']['comments_order'] : 'asc';

    return $wpdb->get_results("SELECT *
    FROM $response_review_table
    WHERE $condition
    ORDER BY review_date {$order}", OBJECT);
}

function wpm_response($user_id, $post_id)
{
    global $wpdb;

    $response_table = $wpdb->prefix . "memberlux_responses";

    return $wpdb->get_row("SELECT * FROM `" . $response_table . "`
                                             WHERE user_id = " . $user_id . " AND
                                                   post_id = " . $post_id . "
                                             ORDER BY response_date;", OBJECT);
}

function wpm_response_log($response_id)
{
    global $wpdb;

    $response_log_table = $wpdb->prefix . "memberlux_response_log";

    return $wpdb->get_row("SELECT * FROM `" . $response_log_table . "`
                                             WHERE response_id = " . $response_id . ";", OBJECT);
}

function wpm_get_responses($user_id, $post_id, $page_meta)
{
    $data = array(
        'date' => '',
        'status' => '',
        'content' => '',
        'reviews' => array()
    );

    $response = wpm_response($user_id, $post_id);

    if (!empty($response)){
        $data['date']        = date_format(date_create($response->response_date), 'H:i d/m/Y');
        $data['status']      = wpm_accepted_with_shift($response, $page_meta) ? 'opened' : $response->response_status;
        $data['real_status'] = $response->response_status;
        $data['content']     = apply_filters('the_content', stripslashes($response->response_content));
        $data['status_msg']  = wpm_get_response_status_message($response->response_status);
        $data['reviews']     = wpm_get_response_reviews($response->id);
    }

    return $data;
}

function wpm_get_response_reviews ($response_id)
{
    global $wpdb;

    $main_options = get_option('wpm_main_options');

    $order =  array_key_exists('comments_order', $main_options['main']) ? $main_options['main']['comments_order'] : 'asc';

    $data = array();

    $review_table = $wpdb->prefix . "memberlux_response_review";

    $reviews = $wpdb->get_results("SELECT * FROM `" . $review_table . "`
                                            WHERE `response_id` = " . $response_id . "
                                            ORDER BY review_date " . $order . ";", OBJECT);

    if(count($reviews)) {
        foreach ($reviews as $review) {
            $data[] = array(
                'date' => date_format(date_create($review->review_date), 'H:i d/m/Y'),
                'content' => $review->review_content
            );
        }
    }

    return $data;
}

function wpm_accepted_with_shift($response, $page_meta)
{
    if ($response->response_status == 'accepted' && array_key_exists('confirmation_method', $page_meta) && $page_meta['confirmation_method'] == 'auto_with_shift') {
        $shift = array_key_exists('homework_shift_value', $page_meta) ? $page_meta['homework_shift_value'] : 0;

        if ($shift > 0) {
            $approval_date = strtotime($response->approval_date) + $shift;

            if ($approval_date > time()) {
                return true;
            }
        }
    }

    return false;
}

function wpm_get_response_status_message ($status)
{
    switch($status) {
        case 'opened':
            $response_status_message = 'Ожидает проверки';
            break;
        case 'approved':
            $response_status_message = 'Правильно!';
            break;
        case 'rejected':
            $response_status_message = 'Неправильно';
            break;
        case 'accepted':
            $response_status_message = 'Ответ принят';
            break;
        default:
            $response_status_message = 'Ответ принят';
    }

    return $response_status_message;
}

function wpm_get_response_counter($status) {
    global $wpdb;
    $response_table = $wpdb->prefix . "memberlux_responses";

    if(in_array($status, array('approved', 'accepted'))) {
        $condition = "response_status IN ('approved', 'accepted')";
    } else {
        $condition = "response_status = '$status'";
    }


    $responses_count = $wpdb->get_results("SELECT COUNT(id)
        FROM $response_table WHERE $condition", ARRAY_A);


    return $responses_count[0]['COUNT(id)'];
}
