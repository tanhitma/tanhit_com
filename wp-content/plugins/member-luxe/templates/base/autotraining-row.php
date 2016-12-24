<?php if ($viewComposer->onlyPreview && $viewComposer->isFirstPreview) : ?>
    <div class="autotrainig-preview">
        <div class="blocker"></div>
<?php endif; ?>
<div
    <?php if (!$viewComposer->onlyPreview) : ?>
         data-id="<?php the_ID(); ?>"
         href="<?php the_permalink(); ?>"
         data-slug="<?php echo $viewComposer->transliterate(urldecode(get_post()->post_name)); ?>"
     <?php endif; ?>
    class="post-row <?php echo $viewComposer->isLastRow() ? 'rounded' : ''; ?>">
    <div class="col-title col-lg-4 col-md-12 col-sm-12 col-xs-12">
        <div class="text-wrap"><?php the_title(); ?></div>
    </div>
    <div class="col-description <?php echo $date_is_hidden ? 'col-lg-6 col-md-12' : 'col-lg-4 col-md-12'; ?> col-sm-12 col-xs-12">
        <?php if (!empty($viewComposer->pageMeta['description'])) echo '<div class="text-wrap">'.$viewComposer->pageMeta['description'].'</div>'; ?>
    </div>
    <?php if (!$date_is_hidden): ?>
        <div class="col-date col-lg-2 col-md-6  col-sm-6 col-xs-6">
            <div class="text-wrap"><?php echo get_the_date(); ?></div>
        </div>
    <?php endif; ?>
    <div class="col-button col-lg-2 <?php echo $date_is_hidden ? 'col-md-12 col-sm-12 col-xs-12' : 'col-md-6 col-sm-6 col-xs-6'; ?>">
        <div class="text-wrap">
            <?php if ($viewComposer->onlyPreview) : ?>
                <span class="show-content show-content-button"><?php echo $viewComposer->getShowButtonText(); ?></span>
            <?php elseif ($viewComposer->hasAccess || $viewComposer->hasDirectAccess) : ?>
                <button class="show-content show-content-button"><?php echo $viewComposer->getShowButtonText(); ?></button>
            <?php else : ?>
                <button class="show-content no-access no-access-button"><?php echo $viewComposer->getNoAccessButtonText(); ?></button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if ($viewComposer->onlyPreview && $viewComposer->isLastRow()) : ?>
    </div>
<?php endif; ?>