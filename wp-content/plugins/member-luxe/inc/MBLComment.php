<?php

class MBLComment
{
    private static $_metaKey = 'wpm_comment_subscribtions';

    public static function addSubscription($postId)
    {
        $subscriptions = self::getSubscriptions($postId);
        $user = wp_get_current_user();
        if (isset($subscriptions[$user->ID])) {
            return self::removeSubscription($postId);
        } else {
            $subscriptions[$user->ID] = $user->user_email;
            return update_post_meta($postId, self::$_metaKey, $subscriptions);
        }
    }

    public static function hasSubscription($postId)
    {
        $subscriptions = self::getSubscriptions($postId);
        $user = wp_get_current_user();

        return isset($subscriptions[$user->ID]) ? 1 : 0;
    }

    public static function getSubscriptions($postId)
    {
        $subscriptions = get_post_meta($postId, self::$_metaKey, true);

        if (empty($subscriptions)) {
            $subscriptions = array();
        }

        return $subscriptions;
    }


    public static function commentPosted($commentId = 0, $commentStatus = 0)
    {
        $keywords = array('[user_name]', '[page_link]', '[page_title]');
        $comment = get_comment($commentId);
        $post = get_post($comment->comment_post_ID);
        $terms = get_the_terms($comment->comment_post_ID, 'wpm-category');

        if (!empty($terms)) {
            $link = get_term_link($terms[0]->term_id);
        }

        if (!isset($link)) {
            $link = get_permalink($comment->comment_post_ID);
        }

        $comment->comment_author_email;

        foreach (self::getSubscriptions($comment->comment_post_ID) AS $userId => $email) {
            $user = get_userdata($userId);

            if (self::_isAnswerToUsersComment($comment, $userId) && $user) {
                $replacements = array($user->display_name, $link, $post->post_title);
                $text = str_replace($keywords, $replacements, wpm_get_option('letters.comment_subscription.content'));
                $title = str_replace($keywords, $replacements, wpm_get_option('letters.comment_subscription.title'));
                wpm_send_mail($email, $title, $text);
            }
        }

        return $commentId;
    }

    private static function _isAnswerToUsersComment($comment, $userId)
    {
        if ($comment->user_id == $userId) {
            return false;
        }

        while ($comment->comment_parent) {
            $comment = get_comment($comment->comment_parent);
            if ($comment->user_id == $userId) {
                return true;
            }
        }

        return false;
    }

    public static function removeSubscription($postId)
    {
        $subscriptions = self::getSubscriptions($postId);
        $user = wp_get_current_user();

        if (isset($subscriptions[$user->ID])) {
            unset($subscriptions[$user->ID]);
        }

        return update_post_meta($postId, self::$_metaKey, $subscriptions);
    }

    public static function getUserTree($postId)
    {
        $user = wp_get_current_user();

        $comments = self::_getPostComments($postId);
        $_comments = array();
        $_deleted = array();

        while (count($comments)) {
            $key = key($comments);
            $comment = $comments[$key];

            $isAuthor = $comment->user_id == $user->ID;
            $hasChildren = self::_hasChildren($comment, $comments);
            $isRoot = $comment->comment_parent == '0';

            if (!$isAuthor && $isRoot && !$hasChildren) {
                unset($comments[$key]);
                continue;
            }

            if ($isAuthor) {
                $_comments[$key] = $comment;
                if (!$isRoot && isset($comments[$comment->comment_parent])) {
                    while ($comment->comment_parent && isset($comments[$comment->comment_parent])) {
                        $comment = $comments[$comment->comment_parent];
                        $_comments[$comment->comment_ID] = $comments[$comment->comment_ID];
                    }
                }

            } elseif (!$isRoot && isset($_comments[$comment->comment_parent])) {
                $_comments[$key] = $comment;
            }

            if (isset($_deleted[$key])) {
                unset($comments[$key]);
            } else {
                $_deleted[$key] = 1;
            }

            next($comments) || reset($comments);
        }

        krsort($_comments);

        return $_comments;
    }

    private static function _fillCollectionKeys($comments)
    {
        $_comments = array();

        foreach ($comments AS $comment) {
            $_comments[$comment->comment_ID] = $comment;
        }

        return $_comments;
    }

    private static function _hasChildren($comment, $comments)
    {
        foreach ($comments AS $_comment) {
            if ($_comment->comment_parent == $comment->comment_ID) {
                return true;
            }
        }

        return false;
    }

    private static function _getPostComments($postId)
    {
        $args = array(
            'post_id'      => $postId,
            'hierarchical' => 'flat'
        );

        $comments = self::_fillCollectionKeys(get_comments($args));

        return $comments;
    }
}