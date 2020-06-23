<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/01/2017
 * Time: 16:07
 */
defined('PHPFOX') or exit('NO DICE!');
?>


<div class="advblog-app advblog-feed-item core-feed-item">
    <div class="item-outer">
        <div class="item-media">
            <a href="{$sLink}" class="item-media-src" style="background-image: url({$aBlog.image_url})" alt=""></a>
            {if $aBlog.total_view}
            <span class="item-view">{$aBlog.total_view} {if $aBlog.total_view == 1}{_p var='view'}{else}{_p var='views'}{/if}</span>
            {/if}
        </div>
        <div class="item-inner">
            <div class="item-title">
                <a href="{$sLink}" class="core-feed-title line-2">{$aBlog.title}</a>
                {if Phpfox::isUser()}
                <!-- button save -->
                <div class="item-action-container">
                    <div class="item-action">
                        <a href="" class="p-option-button p-advblog-btn-save {if !empty($aBlog.is_saved)}active{/if}" {if empty($aBlog.is_saved)}title="{_p var='save_this_ynblog'}"{else}title="{_p var='unsave_this_ynblog'}"{/if} onclick="{if isset($sView) && $sView == 'saved'}$(this).parents('div.js_blog_parent').remove(); {/if}ynadvancedblog.updateSavedBlog({$aBlog.blog_id}, {if empty($aBlog.is_saved)}1{else}0{/if})">
                        <i class="ico ico-bookmark-o"></i></a>
                    </div>
                </div>
                {/if}
            </div>
            <div class="item-category">
                <div class="core-feed-minor p-seperate-dot-wrapper">
                    <div class="p-seperate-dot-item">
                        {$aBlog.time_stamp|convert_time}
                    </div>
                    <div class="p-seperate-dot-item">
                        <span class="category-title">{_p var='category'}:</span> <a href="{permalink module='ynblog.category' id=$sCategory.category_id title=$sCategory.name}">{softPhrase var=$sCategory.name}</a>
                    </div>
                </div>
            </div>
            {if !empty($aBlog.text)}
            <div class="item-description">
                <div class="core-feed-description item_content line-2">
                    {$aBlog.text|striptag|stripbb|highlight:'search'|split:500|shorten:200:'...'}
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>
