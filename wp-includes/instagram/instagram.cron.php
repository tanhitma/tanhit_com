<?php
set_time_limit(0);
chdir(dirname(__FILE__));

if (!isset($argv)) {
    header("HTTP/1.1 403 Forbidden", true, 403);
    exit('403');
}

require_once(dirname(dirname( __DIR__)) . '/wp-load.php');
require_once('instagram-parser.php');

$inst = new InstagramParser();
$data = [];
if (is_array($inst->getRecent()) && !empty($inst->getRecent())){
    foreach ($inst->getRecent() as $post){
        print_r($post);
        $link_parts = explode('/', trim($post->link,'/'));
        $post_id = (int)explode('_', $post->id)[0];
        $data[$post_id] = [
            'type' => $post->type,
            'inst_username' => $post->user->username,
            'inst_user_id' => (int)$post->user->id,
            'profile_picture' => $post->user->profile_picture,
            'full_name' => $post->user->full_name,
            'images' => json_encode($post->images),
            'caption' => base64_encode($post->caption->text),
            'tags' => json_encode($post->tags),
            'created_time' => (new DateTime())->setTimestamp((int)$post->created_time)->format('Y-m-d H:i:s'),
            'comments_count' => (int)$post->comments->count,
            'inst_id' => $post_id,
            'likes' => (int)$post->likes->count,
            'inst_link' => $post->link,
            'inst_link_id' => (string)end($link_parts),
            'videos' => $post->type == 'video' ? json_encode($post->videos) : null,
        ];
        if($data[$post_id]['comments_count'] > 0){
            $data[$post_id]['comments_data'] = json_encode($inst->getComments($data[$post_id]['inst_link_id'])->media->comments->nodes);
        }
    }
}

if(!empty($data)){
    foreach ($data as $post){
        $wpdb->delete($wpdb->prefix . 'instagram', ['inst_id' => $post['inst_id']]);
        $wpdb->insert($wpdb->prefix . 'instagram', $post);
    }
}