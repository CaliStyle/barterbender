<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="item p-item p-advblog-category-item">
    <div class="item-outer">
        <div class="item-media">
            <a href="{permalink module='ynblog.category' id=$aCategory.category_id title=$aCategory.name}" class="item-media-link">
                <span class="item-media-src" style="background-image: url(
                {if $aCategory.image_path}
                    <?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aCategory']['image_path'], $this->_aVars['aCategory']['server_id'], '_240', $this->_aVars['aCategory']['is_old_suffix']); ?>
                {else}
                    {$appPath}/assets/image/blog_photo_default.png
                {/if}
                )">
                </span>
            </a>
        </div>
        <div class="item-inner">
            <div class="item-name">
                <a href="{permalink module='ynblog.category' id=$aCategory.category_id title=$aCategory.name}">
                    {_p var=$aCategory.name}
                </a>
            </div>
            <div class="item-number-post">
                {$aCategory.total_posts} <span class="p-text-lowercase">{$aCategory.total_posts|ynblog_n:'post':'posts'}</span>
            </div>
        </div>
    </div>
</div>