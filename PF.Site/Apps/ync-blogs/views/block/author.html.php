<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 09/01/2017
 * Time: 15:12
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<!-- html blogger detail -->
{if !empty($bIsInDetail)}
<div class="p-listing-container p-advblog-blogger-detail-container">
    <div class="p-item p-advblog-blogger-detail-item">
        <div class="item-outer">
            <div class="p-item-media-wrapper {if !$aCurrentAuthor.cover_photo && empty($sCoverDefaultUrl)}no-cover{/if}">
                <a class="item-media-link p-advblog-blogger-detail-cover" href="{url link=$aCurrentAuthor.user_name}">
                    {if $aCurrentAuthor.aCoverPhoto.destination}
                        <span class="item-media-src" style="background-image: url({img server_id=$aCurrentAuthor.aCoverPhoto.server_id path='photo.url_photo' file=$aCurrentAuthor.aCoverPhoto.destination suffix='_500' return_url=true})"></span>
                    {elseif !empty($sCoverDefaultUrl)}
                        <span class="item-media-src" style="background-image: url({$sCoverDefaultUrl})"></span>
                    {else}
                        <span class="item-media-src"></span>
                    {/if}
                </a>
                <div class="p-advblog-blogger-detail-avatar">
                    {img user=$aCurrentAuthor suffix='_200_square' }
                </div>
            </div>
            <div class="item-inner">
                <!-- title -->
                <div class="p-advblog-blogger-detail-title-wrapper">
                    <h4 class="p-item-title p-advblog-blogger-detail-title">
                        {$aItem|user:'':'':50:'':'author'}
                    </h4>
                </div>
                {if $aCurrentAuthor.is_follow}
                <div class="p-advblog-blogger-detail-option">
                    <div id="js_ynblog_update_follow_{$aCurrentAuthor.user_id}">
                        <button class="btn btn-sm btn-default btn-icon" onclick="ynadvancedblog.updateFollow({$aCurrentAuthor.user_id}, 0);return false;" title="{_p var='following'}">
                            <i class="ico ico-check" aria-hidden="true"></i>&nbsp;{_p var='Following'}
                        </button>
                    </div>
                </div>
                {elseif $aCurrentAuthor.canFollow}
                <div class="p-advblog-blogger-detail-option">
                    <div id="js_ynblog_update_follow_{$aCurrentAuthor.user_id}">
                        <button class="btn btn-sm btn-primary btn-icon" onclick="ynadvancedblog.updateFollow({$aCurrentAuthor.user_id}, 1);return false;" title="{_p var='follow'}">
                            <i class="ico ico-plus" aria-hidden="true"></i>&nbsp;{_p var='Follow'}
                        </button>
                    </div>
                </div>
                {/if}

                <div class="p-item-statistic p-seperate-dot-wrapper p-advblog-blogger-detail-statistic">
                    {if $aCurrentAuthor.total_entries}
                        <span class="p-seperate-dot-item">
                        <a href="{url link=$aItem.user_name|ynblog_profile}">
                            {$aCurrentAuthor.total_entries} <span class="p-text-lowercase">{$aCurrentAuthor.total_entries|ynblog_n:'entry':'entries'}</span>
                        </a>
                    </span>
                    {/if}
                    {if $aCurrentAuthor.total_follower}
                        <span class="p-seperate-dot-item js_ynblog_total_update_follow_{$aItem.user_id}">
                            {$aCurrentAuthor.total_follower} <span class="p-text-lowercase">{$aCurrentAuthor.total_follower|ynblog_n:'follower':'followers'}</span>
                        </span>
                    {/if}
                </div>
                {if $aCurrentAuthor.cf_about_me}
                <div class="p-advblog-blogger-detail-about">
                    {$aCurrentAuthor.cf_about_me}
                </div>
                {/if}
                {if !empty($aLatestPost)}
                    <div class="p-advblog-blogger-detail-latestpost">
                        <span class="newpost-title">{_p var='latest_article'}</span>
                        <span class="newpost-content">
                            <a title="{$aLatestPost.0.title|clean}" href="{permalink module='ynblog' id=$aLatestPost.0.blog_id title=$aLatestPost.0.title}">{$aLatestPost.0.title|clean}</a>
                        </span>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
{/if}
