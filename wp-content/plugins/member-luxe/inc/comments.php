<?php
/**
 * Comments for wpm
 */
add_action('wp_ajax_wpm_the_comments_action', 'wpm_the_comments'); // ajax for logged in users
add_action('wp_ajax_nopriv_wpm_the_comments_action', 'wpm_the_comments'); // ajax for not logged in users
function wpm_the_comments()
{
    $post_id = $_POST['id'];
    $section = $_POST['section'];
    $section = $section == 'user' ? 'user' : 'all';
    if (!wpm_comments_is_visible() || !comments_open($post_id)) {
        echo 'no-comments';
        die();
    }
    $current_user = wp_get_current_user();
    $accessible_levels = wpm_get_all_user_accesible_levels($current_user->ID);

    if (!wpm_check_access($post_id, $accessible_levels)) {
        echo 'no-comments';
        die();
    } else {
        $comments = null;
        if ($section == 'user') {
            $comments = MBLComment::getUserTree($post_id);
        }
        wpm_comments_wordpress($post_id, $comments, $section);
    }
    die();

}

function wpm_comments_wordpress($post_id, $comments=null, $section='all')
{
    $design_options = get_option('wpm_design_options');
    ?>
<?php if (comments_open($post_id) || have_comments() || wpm_comments_is_visible()): ?>
    <?php if (!wpm_option_is('main.comments_mode', 'cackle')) : ?>
        <script>
            jQuery(function ($) {
                $('.refresh-comments').on('click', function () {
                    var button = $(this);
                    button.text('Загрузка...');
                    $.ajax({
                        type    : 'POST',
                        url     : ajaxurl,
                        data    : {
                            'action' : 'wpm_the_comments_action',
                            'id'     : '<?php echo $post_id; ?>'
                        },
                        success : function (data) {
                            button.text('Обновлено!');
                            $('.wpm-comments-wrap').html(data);
                        }
                    });
                });
                $('.comment-tabs>li>a').on('click', function () {
                    var $this = $(this),
                        commentsContent = $('.wpm-comments-content'),
                        data;
                    if (!$this.closest('li').hasClass('active')) {
                        data = {
                            action  : 'wpm_the_comments_action',
                            id      : '<?php echo $post_id; ?>',
                            section : ($this.attr('id') == 'wmp_user_comments' ? 'user' : 'all')
                        };
                        commentsContent.addClass('loading');
                        $.post(ajaxurl, data, function (response) {
                            $('.wpm-comments-wrap').html(response);
                        });
                    }
                    return false;
                });
                $('#mbl_comment_subscription').on('change', function () {
                    var $this = $(this),
                        data = {
                            action  : 'wpm_add_comment_subscription',
                            id      : '<?php echo $post_id; ?>'
                        };
                    $.post(ajaxurl, data, function (response) {
                        $this.prop('checked', !!response);
                    }, "json");
                    return false;
                });
            });
        </script>
        <div id="comments">
            <?php global $post;
            if (is_null($comments)) {
                $comments = get_comments('post_id=' . $post_id);
            }
            $commentsNb = count($comments);
            ?>
            <ul class="nav nav-tabs comment-tabs">
              <li <?php echo $section == 'all' ? 'class="active"' : ''; ?>><a id="wmp_all_comments" href="#">Все комментарии</a></li>
              <li <?php echo $section == 'user' ? 'class="active"' : ''; ?>><a id="wmp_user_comments" href="#">Мои комментарии</a></li>
            </ul>
            <br>
            <label>
                <input type="checkbox" id="mbl_comment_subscription" <?php echo MBLComment::hasSubscription($post_id) ? 'checked="checked"' : ''; ?>>
                <?php _e('Уведомлять по почте об ответах на мои комментарии. ', 'wpm'); ?>
            </label>
            <div class="wpm-comments-content">
                <h2 id="comments-title" class="clearfix"><?php
                        printf(_n('Один комментарий', '%1$s комментариев', $commentsNb),
                            number_format_i18n($commentsNb));
                    ?>
                    <a class="refresh-comments pull-right"><?php echo $design_options['buttons']['refresh_comments']['text']; ?></a>
                </h2>
    
                <?php if (comments_open($post_id)): ?>
                    <div id="respond" class="no-index">
                        <?php wpm_comment_form($post_id); ?>
                    </div>
                <?php endif; ?>
    
                <ol class="commentlist clearfix">
                    <?php
                    $args = array(
                        'walker'            => null,
                        'max_depth'         => '',
                        'style'             => 'ul',
                        'callback'          => 'wpm_comment_template',
                        'end-callback'      => null,
                        'type'              => 'all',
                        'reply_text'        => __('Ответить', 'wpm'),
                        'page'              => '',
                        'per_page'          => '',
                        'avatar_size'       => 48,
                        'reverse_top_level' => null,
                        'reverse_children'  => '',
                        'format'            => 'html5', //or xhtml if no HTML5 theme support
                        'short_ping'        => false, // @since 3.6,
                        'echo'              => true // boolean, default is true
                    );
    
                    wp_list_comments($args, $comments); ?>
                </ol>
    
                <?php if (get_comment_pages_count($post_id) > 1 && get_option('page_comments')) : // Are there comments to navigate through? ?>
                    <div class="navigation">
                        <div class="nav-previous"><?php previous_comments_link(); ?></div>
                        <div class="nav-next"><?php next_comments_link(); ?></div>
                        <div class="clear"></div>
                    </div><!-- .navigation -->
                <?php endif; // check for comment navigation ?>
            </div>
        </div>
    <?php else : ?>
        <div id="mc-container"></div>
        <script type="text/javascript">
            cackle_widget = window.cackle_widget || [];
            cackle_widget.push({
                widget : 'Comment',
                channel : '<?php echo $post_id; ?>',
                stream : <?php echo wpm_option_is('main.cackle_auto_update', 'on') ? 'true' : 'false'; ?>,
                id : <?php echo wpm_get_option('main.cackle_id'); ?>
            });
            (function () {
                var mc = document.createElement('script');
                mc.type = 'text/javascript';
                mc.async = true;
                mc.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cackle.me/widget.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(mc, s.nextSibling);
            })();
        </script>
    <?php endif; ?>
<?php endif; ?>
<?php
}

/**
 *
 */

function wpm_comment_template($comment, $args, $depth) {
    global $user;

    $GLOBALS['comment'] = $comment;
    $post_id = $comment->comment_post_ID;
    $avatar = get_user_meta($comment->user_id, 'avatar', true);
    $attachments_is_disabled = wpm_attachments_is_disabled();
    ?>

    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
        <div id="comment-<?php comment_ID(); ?>" class="comment-body">
            <div class="comment-avatar-wrap pull-left">
                <?php
                if(!empty($avatar)){
                    echo wp_get_attachment_image($avatar, 'thumbnail', '', array('class'	=> "avatar avatar-48 photo"));
                }else{
                    echo get_avatar($comment,$size='48' );
                }

                ?>
            </div>
            <div class="comment-content">
                <div class="comment-meta">
                    <div class="comment-author vcard">
                        <?php printf(__('<cite class="name">%s</cite>'), get_comment_author_link()) ?>
                        <span class="coment-date"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></span>
                    </div>
                    <div class="comment-metadata">

                        <?php if ($comment->comment_approved == '0' && is_user_logged_in()) : ?>
                            <em class="not-approved-comment" ><?php _e('Your comment is awaiting moderation.') ?></em>
                            <br />
                        <?php endif; ?>
                    </div>
                </div>
                <div class="comment-text">
                    <?php comment_text() ?>
                    <div class="comment-image row">
                        <?php if(!$attachments_is_disabled):?>
                            <?php $images = get_comment_meta($comment->comment_ID, "comment_image");

                            if(!empty($images)){
                                foreach ($images as $image) {
                                    echo '<div class=" col-lg-3 col-md-4 col-sm-6 col-xs-12"><a href="'.$image['url'].'" rel="group_'.$comment->comment_ID.'" class="fancybox wpm-comment-image-item" style="background-image:url('.$image['url'].')"></a></div>';
                                }
                            }?>
                        <?php endif;?>
                    </div>
                </div>
                <div class="comment-nav">
                    <?php if(is_user_logged_in()):?>
                        <?php edit_comment_link(__('Edit'),'',' • ') ?>
                        <?php comment_reply_link(array_merge( $args, array('depth' => $depth))) ?>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </li>
<?php
}

/**
 *
 */

function wpm_comment_form($post_id)
{
    global $user_identity;
    $commenter = wp_get_current_commenter();
    $design_options = get_option('wpm_design_options');
    $attachments_is_disabled = wpm_attachments_is_disabled();
    ?>

    <script type="text/javascript">
        jQuery(function ($) {
            var commentform = $('#commentform');
            var options = {
                beforeSubmit : function () {
                    $('#save-form').val('Загрузка...');
                },
                success      : function (data) {
                    $('.refresh-comments').click();
                },
                type         : 'post',
                clearForm    : true
            };
            commentform.ajaxForm(options);
        });
    </script>

    <?php if (is_user_logged_in()):?>

        <form class="wpm-comment-form" method="post" action="<?php echo site_url('/wp-comments-post.php'); ?>" id="commentform" enctype="multipart/form-data">
            <header class="info">
                <h4><?php comment_form_title(); ?></h4>
                <?php cancel_comment_reply_link(__('Отменить', 'wpm')); ?>
            </header>
            <div class="clearfix comment-form-wrap">
                <div id="comment-response"></div>
                <textarea name="comment" id="comment" cols="50" rows="10" required
                          class="field textarea medium" placeholder="<?php _e('Текст комментария', 'wpm'); ?>"></textarea>
                <div class="comment-image-wrap">
                </div>
                <div class="clearfix">
                    <?php if(!$attachments_is_disabled):?>
                        <input type="hidden" name="comment-images" value="">
                        <label class="upload-image">
                            <input name="comment_image_<?php echo $post_id; ?>[]" id="comment_image" type="file" multiple="" />
                        </label>
                    <?php endif;?>
                    <input id="save-form" name="save-form" class="submit wpm-button pull-right wpm-comment-button" type="submit"
                           value="<?php echo $design_options['buttons']['send_comment']['text']; ?>"/>
                </div>
            </div>


            <?php comment_id_fields($post_id); ?>
            <?php echo do_action('comment_form', $post_id); ?>
        </form>
    <?php endif;?>
<?php
}

/**
 *
 */
add_action('comment_post', 'wpm_ajaxify_comments', 20, 2);
function wpm_ajaxify_comments($comment_ID, $comment_status)
{
    $comment = get_comment($comment_ID);
    $post_id = $comment->comment_post_ID;
    $isFrontend = isset($_POST['save-form']);
    if (get_post_type($post_id) == 'wpm-page' && $isFrontend) {

        $result = array(
            'status'         => 'error',
            'comment_parent' => '',
            'comment'        => ''
        );

        $maybe_notify = get_option( 'comments_notify' );
        $maybe_notify = apply_filters( 'notify_post_author', $maybe_notify, $comment_ID );

        //If AJAX Request Then
        switch ($comment_status) {
            case '0':
                //notify moderator of unapproved comment
                if ($maybe_notify) {
                    wp_notify_moderator($comment_ID);
                }
            case '1': //Approved comment
                $result['status'] = "success";
                $comment = get_comment($comment_ID);
                // Allow the email to the author to be sent
                if ($maybe_notify) {
                    wp_notify_postauthor($comment_ID);
                }
                // Get the comment HTML from my custom comment HTML function
                $result['comment'] = wpm_get_comment_html($comment);
                $result['comment_parent'] = $comment->comment_parent;
                break;
            default:
                $result['status'] = "error";
        }
        echo json_encode($result);
    }

}

/**
 * @param $comment
 */

function wpm_get_comment_html($comment) {
    $comment_html = '';
    $comment_html .= '<li '.comment_class("","","", false).'id="li-comment-'.$comment->comment_ID.'">';
    $comment_html .= '<div id="comment-'.$comment->comment_ID.'" class="comment-body">';
    $comment_html .= '<div class="comment-avatar-wrap pull-left">'.get_avatar($comment,$size='48' ).'</div>';
    $comment_html .= '<div class="comment-content">';
    $comment_html .= '<div class="comment-meta">';
    $comment_html .= '<div class="comment-author vcard">';
    $comment_html .= '<cite class="name">'.$comment->comment_author.'</cite><span class="coment-date">'.$comment->comment_date.'</span>';
    $comment_html .='</div>';
    $comment_html .='<div class="comment-metadata">';
                     if ($comment->comment_approved == '0') :
                         $comment_html .='<em>'.__('Your comment is awaiting moderation.').'</em>';
                    endif;
    $comment_html .='</div></div>';
    $comment_html .='<div class="comment-text">'.get_comment_text($comment->comment_ID).'</div>';
   // $comment_html .='<div class="comment-nav">'.get_comment_reply_link(array(), $comment->comment_ID, $comment->comment_post_ID).'</div>';
    $comment_html .='</div></li>';
    return $comment_html;
}