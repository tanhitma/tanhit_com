<?php
function wppage_useful_page()
{
    ?>
<div class="wrap wppage_useful_page">
    <div id="icon-edit" class="icon32 icon32-posts-page_selling"><br/></div>
    <h2>Полезное</h2>
    <div id="wppage_useful_page">
        <?php echo @file_get_contents('http://www.wppage.ru/poleznoe/'); ?>
    </div>
</div>
<?php
}