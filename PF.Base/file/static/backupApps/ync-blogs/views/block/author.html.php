<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 09/01/2017
 * Time: 15:12
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($bIsInDetail)}
<div class="ynadvblog_detail_author">
    <div class="ynadvblog_avatar">
        {if $aCurrentAuthor.user_image}
            <a href="#" style="background-image: url('{img user=$aCurrentAuthor suffix='_200_square' return_url=true}')"></a>
        {else}
            {img user=$aCurrentAuthor suffix='_200_square' return_url=true}
        {/if}
    </div>
    {$aCurrentAuthor|user}
    <div class="ynadvblog_detail_author_button">
        {if $aCurrentAuthor.is_follow}
            <div id="js_ynblog_update_follow_{$aCurrentAuthor.user_id}">
                <button class="btn btn-primary btn-sm" onclick="ynadvancedblog.updateFollow({$aCurrentAuthor.user_id}, 0);return false;"><i class="fa fa-minus" aria-hidden="true"></i>&nbsp;{_p var='Un-Follow'}</button>
            </div>
        {elseif $aCurrentAuthor.canFollow}
            <div id="js_ynblog_update_follow_{$aCurrentAuthor.user_id}">
                <button class="btn btn-primary btn-sm" onclick="ynadvancedblog.updateFollow({$aCurrentAuthor.user_id}, 1);return false;"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{_p var='Follow'}</button>
            </div>
        {/if}
        <div>
            <a href="{permalink module='ynblog.rss.author' id=$aCurrentAuthor.user_id title=$aCurrentAuthor.user_name}" class="no_ajax btn btn-default text-uppercase btn-sm" target="_blank"><i class="fa fa-rss" aria-hidden="true"></i>&nbsp;{_p var='RSS'}</a>
        </div>
    </div>
    <div class="ynadvblog_desc">
        {if !empty($aCurrentAuthor.cf_about_me)}
            {$aCurrentAuthor.cf_about_me}
        {/if}
    </div>
    {if !empty($aLatestPost)}
    <div class="ynadvblog_new_post">
        <i class="fa fa-angle-right" aria-hidden="true"></i>
        <span>{_p var='New Post:'}</span>
        <a title="{$aLatestPost.0.title|clean}" href="{permalink module='ynblog' id=$aLatestPost.0.blog_id title=$aLatestPost.0.title}">{$aLatestPost.0.title|clean}</a>
    </div>
    {/if}
    <div class="ynadvblog_info">
        <div class="text-center">
            {if $aCurrentAuthor.total_entries == 1}
                {$aCurrentAuthor.total_entries}&nbsp;{_p var='entry'}
            {else}
                {$aCurrentAuthor.total_entries}&nbsp;{_p var='entries'}
            {/if}
        </div>
        <div id="js_ynblog_total_update_follow_{$aCurrentAuthor.user_id}" class="text-center">
            {if $aCurrentAuthor.total_follower == 1}
                {$aCurrentAuthor.total_follower}&nbsp;{_p var='follower'}
            {else}
                {$aCurrentAuthor.total_follower}&nbsp;{_p var='followers'}
            {/if}
        </div>
    </div>
    <div class="dropdown ynblog_author_dropdown">
        <span class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <i class="fa fa-angle-down" aria-hidden="true"></i>
        </span>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a  href="{permalink module='ynblog.rss.author' id=$aCurrentAuthor.user_id title=$aCurrentAuthor.user_name}" target="_blank" class="no_ajax uppercase"><i class="fa fa-rss" aria-hidden="true"></i>{_p var="Rss"}</a></li>
            <li><a href="javascript:void(0)" onclick="ynadvancedblog.updateFollow({$aCurrentAuthor.user_id}, 0);return false;"><i class="fa fa-minus" aria-hidden="true"></i>{_p var="Un-Follow"}</a></li>
        </ul>
    </div>
</div>
{else}
<div class="ynadvblog_info my_blog clearfix">
    <div class="text-center">
        {if $aCurrentAuthor.total_entries == 1}
            <div class="title text-center fw-bold">{$aCurrentAuthor.total_entries}</div>
            <div class="number text-center">{_p var='entry'}</div>
        {else}
            <div class="title text-center fw-bold">{$aCurrentAuthor.total_entries}</div>
            <div class="number text-center">{_p var='entries'}</div>
        {/if}
    </div>
    <div class="text-center" id="js_ynblog_total_update_follow_{$aCurrentAuthor.user_id}">
        {if $aCurrentAuthor.total_follower == 1}
            <div class="title text-center fw-bold">{$aCurrentAuthor.total_follower}</div>
            <div class="number text-center">{_p var='follower'}</div>
        {else}
            <div class="title text-center fw-bold">{$aCurrentAuthor.total_follower}</div>
            <div class="number text-center">{_p var='followers'}</div>
        {/if}
    </div>
</div>
{/if}
