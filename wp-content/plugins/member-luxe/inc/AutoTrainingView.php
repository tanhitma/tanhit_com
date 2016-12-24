<?php

class AutoTrainingView
{
    public $mainOptions;
    public $designOptions;
    public $categoryId;
    /**
     * @var WP_User
     */
    public $user;

    /**
     * @var array
     */
    public $accessibleLevels;

    /**
     * @var bool
     */
    public $isAutoTraining;


    /**
     * @var int
     */
    public $postId;

    /**
     * @var bool
     */
    public $isAuthor;

    /**
     * @var bool
     */
    public $hasAccess = false;

    /**
     * @var bool
     */
    public $hasDirectAccess = false;

    /**
     * @var bool
     */
    public $isPostponedDueToHomework = false;

    /**
     * @var array
     */
    public $pageMeta;

    /**
     * @var int
     */
    public $prevPostId = null;

    /**
     * @var array
     */
    public $prevPostMeta = null;

    /**
     * @var int
     */
    public $postIterator;

    /**
     * @var bool
     */
    public $onlyPreview = false;
    /**
     * @var bool
     */
    public $isFirstPreview = null;

    private $_userCategoryData;
    private $_isCurrentNumberAccessible;
    private $_hasNextPosts = null;
    private $_countPosts;

    /**
     * AutoTrainingView constructor.
     * @param $categoryId
     */
    public function __construct($categoryId)
    {
        global $paged, $wp_query;

        if (!$paged) {
            $paged = 1;
        }

        if ($paged > 1) {
            $this->_setPrevPostData();
        }

        $this->mainOptions = get_option('wpm_main_options');
        $this->designOptions = get_option('wpm_design_options');
        $this->categoryId = $categoryId;
        $this->user = wp_get_current_user();
        $this->accessibleLevels = wpm_get_all_user_accesible_levels($this->user->ID);
        $this->isAutoTraining = wpm_is_autotraining($this->categoryId);
        $this->postIterator = $this->mainOptions['main']['posts_per_page'] * ($paged - 1) + 1;
        $this->_countPosts = count($wp_query->posts);
    }

    private function _setPrevPostData()
    {
        $prevPost = get_previous_post();

        if ($prevPost) {
            $this->prevPostId = $prevPost->ID;
            $this->prevPostMeta = get_post_meta($this->prevPostId, '_wpm_page_meta', true);
        }
    }

    public function iterate()
    {
        $this->postId = get_the_ID();
        $this->isAuthor = wpm_is_author($this->user->ID, get_the_author_meta('ID'));
        $this->hasAccess = wpm_check_access($this->postId, $this->accessibleLevels);
        $this->pageMeta = get_post_meta($this->postId, '_wpm_page_meta', true);
    }

    public function showPost()
    {
        return (is_user_logged_in() || $this->mainOptions['main']['opened'])
        && $this->_hasLevelAccess()
        && $this->hasAccess();
    }

    private function _hasLevelAccess()
    {
        $showPost = false;
        $levelsList = wp_get_post_terms($this->postId, 'wpm-levels', array("fields" => "ids"));
        foreach ($levelsList AS $termId) {
            $termMeta = get_option("taxonomy_term_{$termId}");
            if ($termMeta['hide_for_no_access'] != 'hide') {
                $showPost = true;
            }
        }

        return $showPost || $this->hasAccess || $this->isAuthor;
    }

    public function hasToCheckAccess()
    {
        return !$this->isAuthor && $this->isAutoTraining && !in_array('administrator', $this->user->roles);
    }

    public function checkAccess()
    {
        $this->_userCategoryData = wpm_user_cat_data($this->categoryId, $this->user->ID);
        $this->_isCurrentNumberAccessible = wpm_is_current_number_accessible($this->_userCategoryData, $this->postIterator)
            && wpm_is_current_number_accessible($this->_userCategoryData, get_post()->menu_order); //for compatibility with previous versions
        $this->hasDirectAccess = wpm_has_direct_access($this->postId);

        $isPostVisible = wpm_is_post_visible($this->isAutoTraining, $this->_userCategoryData, $this->pageMeta, $this->postIterator, $this->postId, $this->prevPostId);

        $this->_checkHomework();

        $this->_updatePreviousPostValues();

        return $this->hasDirectAccess
        || ($isPostVisible && !$this->isPostponedDueToHomework);
    }

    public function hasAccess()
    {
        $result = true;

        if ($this->hasToCheckAccess()) {
            wpm_update_autotraining_data($this->postId);
            if ($this->checkAccess()) {
                wpm_update_accessible_material_number($this->_userCategoryData, $this->postIterator, $this->categoryId);
            } else {
                $result = false;
            }
        }

        return $result;
    }

    public function updatePostIterator()
    {
        $this->postIterator++;
    }

    private function _updatePreviousPostValues()
    {
        $this->prevPostId = $this->postId;
        $this->prevPostMeta = $this->pageMeta;
    }

    private function _checkHomework()
    {
        if (!$this->isPostponedDueToHomework) {
            $this->isPostponedDueToHomework =
                previous_post_has_undone_homework($this->prevPostId, $this->prevPostMeta, $this->isAutoTraining)
                && !$this->_isCurrentNumberAccessible;
        }
    }

    public function hasNextPosts()
    {
        if (is_null($this->_hasNextPosts)) {
            $nextPost = get_next_post();

            $this->_hasNextPosts = (bool)$nextPost;

            if ($nextPost && $this->isAutoTraining) {
                $this->postId = $nextPost->ID;
                $this->isAuthor = wpm_is_author($this->user->ID, $nextPost->post_author);
                $this->hasAccess = wpm_check_access($this->postId, $this->accessibleLevels);
                $this->pageMeta = get_post_meta($this->postId, '_wpm_page_meta', true);

                $this->_hasNextPosts = $this->showPost() && (!$this->hasToCheckAccess() || $this->checkAccess());
            }
        }

        return $this->_hasNextPosts;
    }

    public function showAll()
    {
        $showAll = isset($this->designOptions['page']['show_all']) && $this->designOptions['page']['show_all'];

        if ($showAll) {
            $this->onlyPreview = true;
            $this->isFirstPreview = is_null($this->isFirstPreview);
        }

        return $showAll;
    }

    public function isLastRow()
    {
        return $this->_countPosts == $this->postIterator;
    }

    public function getShowButtonText()
    {
        return array_key_exists('text', $this->designOptions['buttons']['show'])
            ? $this->designOptions['buttons']['show']['text']
            : 'Показать';
    }

    public function getNoAccessButtonText()
    {
        return array_key_exists('text', $this->designOptions['buttons']['no_access'])
            ? $this->designOptions['buttons']['no_access']['text']
            : 'Нет доступа';
    }

    public function transliterate($string)
    {
        $iso = array(
            "Є" => "YE", "І" => "I", "Ѓ" => "G", "і" => "i", "№" => "", "є" => "ye", "ѓ" => "g",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
            "Е" => "E", "Ё" => "YO", "Ж" => "ZH",
            "З" => "Z", "И" => "I", "Й" => "J", "К" => "K", "Л" => "L",
            "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H",
            "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "'",
            "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
            "е" => "e", "ё" => "yo", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
            "—" => "-", "«" => "", "»" => "", "…" => ""
        );

        foreach ($iso AS $cyr => $lat) {
            $string = str_replace($cyr, $lat, $string);
        }

        return $string;
    }
}