<?php
/**
 * @package tanhit
 */
?>
<?php
$post = $wp_query->post;

if (in_category('blog')) {
    include(TEMPLATEPATH.'/single-post-blog.php');
} else {
    include(TEMPLATEPATH.'/single-post-default.php');
}
?>