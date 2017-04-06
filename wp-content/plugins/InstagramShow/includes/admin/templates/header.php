<?php

if (!defined('ABSPATH')) exit;

?><header class="instashow-admin-header">
    <div class="instashow-admin-header-title"><?php _e('Instagram Feed Plugin', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></div>

    <a class="instashow-admin-header-logo" href="<?php echo admin_url('admin.php?page=instashow'); ?>" title="<?php _e('InstaShow - WordPress Instagram Feed Plugin', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
        <img src="<?php echo plugins_url('assets/img/logo.png', ELFSIGHT_INSTASHOW_FILE); ?>" width="169" height="44" alt="<?php _e('InstaShow - WordPress Instagram Feed Plugin', ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?>">
    </a>

    <div class="instashow-admin-header-version">
        <span class="instashow-admin-tooltip-trigger">
            <span class="instashow-admin-tag-2"><?php _e('Version ' . ELFSIGHT_INSTASHOW_VERSION, ELFSIGHT_INSTASHOW_TEXTDOMAIN); ?></span>
        </span>
    </div>
</header>