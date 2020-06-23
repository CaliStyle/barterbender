<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 10:47
 */
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="p-item p-advblog-item {if !$aItem.image_url}no-image{/if} {if $aInfo.display_ranking}has-ranking{/if}">
    <div class="item-outer">
        {if !empty($bShowModerator) && ($aItem.user_id == Phpfox::getUserId() || Phpfox::getUserParam('yn_advblog_approve') || Phpfox::getUserParam('yn_advblog_delete'))}
            <div class="moderation_row" style="">

                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.blog_id}" id="check{$aItem.blog_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
        {/if}
        
        <div class="p-item-flag-wrapper show-when-no-image">
            {if $aInfo.featured && $aItem.is_featured}
            <!-- Featured -->
            <div class="sticky-label-icon sticky-featured-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-diamond"></i>
            </div>
            {/if}
            {if $aItem.is_approved == 0 && $aItem.post_status == 'public'}
            <!-- Pending -->
            <div class="sticky-label-icon sticky-pending-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-clock-o"></i>
            </div>
            {/if}
        </div>
        
        
        {if $aInfo.display_ranking}
        <div class="p-advblog-ranking-flag-wrapper show-when-no-image">
            <div class="item-flag {if $aItem.ranking < 4}top-ranking{/if}">
                {$aItem.ranking}
            </div>
        </div>
        {/if}
        <div class="p-item-media-wrapper p-margin-default">
            <a class="item-media-link" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}">
                <span href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="item-media-src p-advblog-hidden-casual-mode" style="background-image: url({$aItem.image_url})">
                </span>

                <img class="hidden p-advblog-show-casual-mode p-advblog-item-media-img" src="{$aItem.image_url}" alt="">
                <div class="p-item-flag-wrapper">
                    {if $aInfo.featured && $aItem.is_featured}
                    <!-- Featured -->
                    <div class="sticky-label-icon sticky-featured-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-diamond"></i>
                    </div>
                    {/if}
                    {if $aItem.is_approved == 0 && $aItem.post_status == 'public'}
                    <div class="sticky-label-icon sticky-pending-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-clock-o"></i>
                    </div>
                    {/if}
                </div>
                
                
                {if $aInfo.display_ranking}
                <div class="p-advblog-ranking-flag-wrapper">
                    <div class="item-flag {if $aItem.ranking < 4}top-ranking{/if}">
                        {$aItem.ranking}
                    </div>
                </div>
                {/if}
            </a>
        </div>
        {if $aInfo.display_ranking}
        <div class="p-advblog-ranking-wrapper">
            {$aItem.ranking}
        </div>
        {/if}
        <div class="item-inner">
            {if $aItem.category_id}
            <!-- h-2 for fix layout when cagtegory disable -->
            <div class="p-item-category p-advblog-hidden-casual-mode h-2">
                <a href="{permalink module='ynblog.category' id=$aItem.category_id title=$aItem.name}">{softPhrase var=$aItem.name}</a>
            </div>
            {/if}
            <div class="p-advblog-item-title-wrapper p-flex-wrapper">
                <h4 class="p-item-title truncate-2">
                    <a href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link" itemprop="url">
                        {if $aItem.post_status == 'draft'}<span class="p-advblog-draft-label p-label-status solid draft">{_p var='draft'}</span> {/if} <span>{$aItem.title|clean}</span>
                    </a>
                </h4>
                <div class="p-ml-auto p-advblog-item-option-container">
                    {if !empty($aItem.permission_enable) && $bShowCommand}
                    <div class="item-option-list dropdown">
                        <span class="p-option-button dropdown-toggle" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='ynblog.block.link'}
                        </ul>
                    </div>
                    {/if}
                    {if Phpfox::isUser() && $aItem.is_approved && $aItem.post_status != 'draft'}
                    <!-- class active when saved -->
                    <div class="item-option-list js_ynblog_saved_blog_{$aItem.blog_id}">
                        <a class="p-option-button p-advblog-btn-save {if !empty($aItem.is_saved)}active{/if}" {if empty($aItem.is_saved)}title="{_p var='save_this_ynblog'}"{else}title="{_p var='unsave_this_ynblog'}"{/if} href="javascript:void(0)" onclick="{if isset($sView) && $sView == 'saved'}$(this).parents('div.js_blog_parent').remove(); {/if}ynadvancedblog.updateSavedBlog({$aItem.blog_id}, {if empty($aItem.is_saved)}1{else}0{/if})">
                            <i class="ico ico-bookmark-o"></i>
                        </a>
                    </div>
                    {/if}
                </div>
            </div>
            <div class="p-item-minor-info p-seperate-dot-wrapper p-seperate-dot-item">
                <span class="p-seperate-dot-item item-author"><span class="p-text-capitalize">{_p var='by'}</span> {$aItem|user:'':'':50:'':'author'}</span>
                <span class="p-seperate-dot-item item-time">{$aItem.time_stamp|convert_time}</span>
                {if $aItem.category_id}
                <span class="p-seperate-dot-item item-category hidden p-advblog-show-casual-mode">
                    <a href="{permalink module='ynblog.category' id=$aItem.category_id title=$aItem.name}">{softPhrase var=$aItem.name}</a>
                </span>
                {/if}
            </div>
            <div class="p-item-statistic">
                {if $aInfo.view && $aItem.total_view}
                    <span class="p-text-lowercase">{$aItem.total_view|short_number} {$aItem.total_view|ynblog_n:'view':'views'}</span>
                {/if}
                {if $aInfo.like && $aItem.total_like}
                    <span class="p-text-lowercase">{$aItem.total_like|short_number} {$aItem.total_like|ynblog_n:'like':'likes'}</span>
                {/if}
                {if $aInfo.comment && $aItem.total_comment}
                    <span class="p-text-lowercase">{$aItem.total_comment|short_number} {$aItem.total_comment|ynblog_n:'comment':'comments'}</span>
                {/if}
                {if $aInfo.favorite && $aItem.total_favorite}
                    <span class="p-text-lowercase">{$aItem.total_favorite|short_number} {$aItem.total_favorite|ynblog_n:'favorite':'favorites'}</span>
                {/if}
            </div>
            <div class="p-item-description truncate-2 p-hidden-side-block item_view_content">
                {$aItem.text|striptag|stripbb|highlight:'search'|shorten:500:'...'}
            </div>
        </div>
    </div>
</div>
