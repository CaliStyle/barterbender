<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<!-- html blogger listing , when in sideblock change data moview to list-->
<div class="p-item p-advblog-blogger-item">
    <div class="item-outer">
        <div class="p-item-media-wrapper {if !$aItem.cover_photo && empty($sCoverDefaultUrl)}no-cover{/if}">
            <a class="item-media-link p-advblog-blogger-cover" href="{url link=$aItem.user_name}">
                {if $aItem.aCoverPhoto.destination}
                    <span class="item-media-src" style="background-image: url({img server_id=$aItem.aCoverPhoto.server_id path='photo.url_photo' file=$aItem.aCoverPhoto.destination suffix='_500' return_url=true})"></span>
                {elseif !empty($sCoverDefaultUrl)}
                    <span class="item-media-src" style="background-image: url({$sCoverDefaultUrl})"></span>
                {else}
                    <span class="item-media-src"></span>
                {/if}
            </a>
            <div class="p-advblog-blogger-avatar">
                {img user=$aItem suffix='_50_square' max_width=50 max_height=50}
            </div>
        </div>
        <div class="item-inner">
            <!-- title -->
            <div class="p-advblog-blogger-title-wrapper">
                <h4 class="p-item-title p-advblog-blogger-title">
                    <a href="{url link=$aItem.user_name}">
                        {$aItem.full_name}
                    </a>
                </h4>
                <div class="p-advblog-blogger-option">
                    <!-- Followed -->
                    {if $aItem.is_followed}
                        <a href="javascript:void(0);" class="btn btn-xs btn-default item-btn-follow followed js_ynblog_follow_btn_{$aItem.user_id}"
                           onclick="ynadvancedblog.updateFollowLink({$aItem.user_id},0);return false;" title="{_p var='ynblog_following'}">
                            <i class="ico ico-plus"></i>
                        </a>
                    <!-- Can follow -->
                    {elseif $aItem.canFollow}
                        <a href="javascript:void(0);" class="btn btn-xs btn-primary item-btn-follow js_ynblog_follow_btn_{$aItem.user_id}"
                           onclick="ynadvancedblog.updateFollowLink({$aItem.user_id},1);return false;" title="{_p var='follow'}">
                            <i class="ico ico-plus"></i>
                        </a>
                    {/if}
                    {if Phpfox::getParam('ynblog.yn_advblog_on_off_rss')}
                    <a href="{permalink module='ynblog.rss.author' id=$aItem.user_id title=$aItem.user_name}"
                       class="no_ajax btn btn-xs btn-default item-btn-rss p-hidden-side-block" target="_blank" title="{_p var='RSS'}">
                        <i class="ico ico-rss-o"></i>
                    </a>
                    {/if}
                </div>
            </div>
            <div class="p-item-statistic p-seperate-dot-wrapper p-advblog-blogger-statistic">
                {if $aItem.total_entries}
                    <span class="p-seperate-dot-item">
                        <a href="{url link=$aItem.user_name|ynblog_profile}">
                            {$aItem.total_entries} <span class="p-text-lowercase">{$aItem.total_entries|ynblog_n:'entry':'entries'}</span>
                        </a>
                    </span>
                {/if}
                {if $aItem.total_follower}
                <span class="p-seperate-dot-item js_ynblog_total_update_follow_{$aItem.user_id}">
                    {$aItem.total_follower} <span class="p-text-lowercase">{$aItem.total_follower|ynblog_n:'follower':'followers'}</span>
                </span>
                {/if}
            </div>
            {if !empty($aItem.cf_about_me)}
                <div class="p-advblog-blogger-about p-hidden-side-block">
                    {$aItem.cf_about_me}
                </div>
            {/if}
            {if !empty($aItem.aLatestPost)}
            <div class="p-advblog-blogger-newpost p-hidden-side-block">
                <span class="newpost-title pgmf-text-uppercase">{_p var='ynblog_new'}:</span>
                <span class="newpost-content">
                    <a title="{$aItem.aLatestPost.0.title|clean}" href="{permalink module='ynblog' id=$aItem.aLatestPost.0.blog_id title=$aItem.aLatestPost.0.title}">
                        {$aItem.aLatestPost.0.title|clean}
                    </a>
                </span>
            </div>
            {/if}
        </div>
    </div>
</div>