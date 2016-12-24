<?php

function wpm_lessons_page()
{
    ?>
<div class="wrap wppage_useful_page">
    <div id="icon-edit" class="icon32 icon32-posts-page_selling"><br/></div>
    <h2>Уроки</h2>
    <br>
    <div id="wppage_useful_page">
        <?php echo @file_get_contents('http://wppage.ru/mbl-lessons-plugin/'); ?>
    </div>
</div>

<?php } ?>