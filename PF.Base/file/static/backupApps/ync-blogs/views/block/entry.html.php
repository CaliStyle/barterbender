<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 10:47
 */
defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_blog_entry{$aItem.blog_id}" class="moderation_row js_blog_parent clearfix {if isset($sView) && $sView == 'my'}my_blog{/if} {if $aItem.user_id == Phpfox::getUserId()}no_show_saved{/if} {if empty($aItem.permission_enable)}no_show_edit{/if} {if empty($aItem.image_path)}no_avatar{/if}">
    
    <div class="ynadvblog_avatar">
        <a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="item_image{if empty($aItem.text)} full{/if}" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_big'); ?>)">
        </a>
        {if !empty($bShowModerator) && ($aItem.user_id == Phpfox::getUserId() || Phpfox::getUserParam('yn_advblog_approve') || Phpfox::getUserParam('yn_advblog_delete'))}
        <div class="moderation_row" style="position: absolute;top: 10px; left: 10px; opacity:0.8">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.blog_id}" id="check{$aItem.blog_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        {/if}
        <i title="{_p var='Feature'}" class="js_ynblog_featured_blog_{$aItem.blog_id} fa fa-diamond ynadvblog_feature_icon" aria-hidden="true" {if empty($aItem.is_featured)} style="display: none;" {/if}></i>
        <span class="ynadvblog_status ">{if $aItem.post_status == 'public' && $aItem.is_approved == 0}{_p('pending')}{else}{_p var=$aItem.post_status}{/if}</span>
    </div>

    <div class="ynadvblog_info {if empty($aItem.is_featured)}no_feature{/if} {if isset($sView) && in_array($sView, array('friend'))} friend_blog{/if}">
        <i title="{_p var='Feature'}" class="js_ynblog_featured_blog_{$aItem.blog_id} fa fa-diamond ynadvblog_feature_icon big_view" aria-hidden="true" {if empty($aItem.is_featured)} style="display: none;" {/if}></i>
        {if !empty($aItem.category_id)}<a href="{permalink module='ynblog.category' id=$aItem.category_id title=$aItem.name|clean}" class="ynadvblog_category_title "><span>{softPhrase var=$aItem.name|clean}</span><?php $this->_aVars['aItem']['time_stamp_display'] = date('M j, Y',$this->_aVars['aItem']['time_stamp']); ?>
            <i class="yn_dots hide">-</i><span class="hide">{$aItem.time_stamp_display}<span>{plugin call='ynblog.template_block_entry_date_end'}</span></span></a>{/if}
        <div class="ynadvblog_author big_view">
            <span class="fw-bold"> 
                <span class="lowercase overflow-hi">{_p('by')}</span>
                {$aItem|user:'':'':50:'':'author'}
            </span>
            <i class="yn_dots overflow-hi">-</i>
            <?php $this->_aVars['aItem']['time_stamp_display'] = date('M j, Y',$this->_aVars['aItem']['time_stamp']); ?>
            <span class=" overflow-hi">{$aItem.time_stamp_display}<span>{plugin call='ynblog.template_block_entry_date_end'}</span></span>
        </div>
        <div class="ynadvblog_author grid_view">
            <span class="fw-bold"> 
                {$aItem|user:'':'':50:'':'author'}
            </span>
            <i class="yn_dots overflow-hi">-</i>
            <span class=" overflow-hi">{$aItem.time_stamp_display}<span>{plugin call='ynblog.template_block_entry_date_end'}</span></span>
        </div>
        <a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link fw-bold ynadvblog_post_title" itemprop="url">
            <span><i title="{_p var='Feature'}" class="fa fa-diamond ynadvblog_feature_icon hide" aria-hidden="true"></i></span>
            {$aItem.title|clean}
        </a>
        <div class="ynadvblog_author list_view">
            <span class="fw-bold"> 
                <span class="overflow-hi">{_p('Posted by')}</span>
                {$aItem|user:'':'':50:'':'author'}
            </span>
            <i class="yn_dots overflow-hi">-</i>
            <span class=" overflow-hi">{$aItem.time_stamp_display}<span>{plugin call='ynblog.template_block_entry_date_end'}</span></span>
        </div>
        <div class="ynadvblog_desc item-desc item_content">
            {if !empty($aItem.text)}
                {$aItem.text|striptag|stripbb|highlight:'search'|shorten:500:'...'}
            {/if}
        </div>
        
        <div class="ynadvblog_user_avatar {if isset($sView) && $sView == 'friend'}show_friend_blog{/if}">
            {if !empty($aItem.user_image)}
                <a href="#" style="background-image: url('{img user=$aItem suffix='_100_square' return_url=true}');"></a>
            {else}
                {img user=$aItem suffix='_100_square'}
            {/if}
        </div>

        {if isset($sTypeBlock)}
            <div class="ynadvblog-{$sTypeBlock} hidden_on_grid">
                <span>
                    {if $aItem.total_favorite == 1}
                        {$aItem.total_favorite}&nbsp;<span class="text-lowercase">{_p('favorite')}</span>
                    {else}
                        {$aItem.total_favorite}&nbsp;<span class="text-lowercase">{_p('favorites')}</span>
                    {/if}
                </span>
                <span>
                    {if $aItem.total_view == 1}
                        {$aItem.total_view}&nbsp;<span class="text-lowercase">{_p('view')}</span>
                    {else}
                        {$aItem.total_view}&nbsp;<span class="text-lowercase">{_p('views')}</span>
                    {/if}
                </span>
            </div>
        {elseif isset($bIsRecent)}
            <div class="ynadvblog-recent-comment">
                <?php $this->_aVars['aLastComment'] = Phpfox::getService('ynblog.blog')->getLastCommentByBlogId($this->_aVars['aItem']['blog_id'], $this->_aVars['aItem']['latest_comment']); ?>
                {if !empty($aLastComment)}
                    {$aLastComment|user}&nbsp;{_p var='wrote:'}&nbsp;{$aLastComment.text|feed_strip|split:500|shorten:200:'...'}
                {/if}
            </div>
        {/if}
        <div class="show_on_grid clearfix">
            {if isset($sTypeBlock)}
                <div class="ynadvblog-{$sTypeBlock}">
                    <span class="text-right">
                        {if $aItem.total_favorite == 1}
                            {$aItem.total_favorite}&nbsp;<span class="text-lowercase">{_p('favorite')}</span>
                        {else}
                            {$aItem.total_favorite}&nbsp;<span class="text-lowercase">{_p('favorites')}</span>
                        {/if}
                    </span>
                    <span class="text-left">
                        {if $aItem.total_view == 1}
                            {$aItem.total_view}&nbsp;<span class="text-lowercase">{_p('view')}</span>
                        {else}
                            {$aItem.total_view}&nbsp;<span class="text-lowercase">{_p('views')}</span>
                        {/if}
                    </span>
                </div>
            {/if}
        </div>
        <div class="show_on_big clearfix">
            {if isset($sTypeBlock)}
                <div class="ynadvblog-{$sTypeBlock}">
                    <span class="text-right">
                        {if $aItem.total_favorite == 1}
                            {$aItem.total_favorite}&nbsp;<span class="text-lowercase">{_p('favorite')}</span>
                        {else}
                            {$aItem.total_favorite}&nbsp;<span class="text-lowercase">{_p('favorites')}</span>
                        {/if}
                    </span>
                    <span class="text-left">
                        {if $aItem.total_view == 1}
                            {$aItem.total_view}&nbsp;<span class="text-lowercase">{_p('view')}</span>
                        {else}
                            {$aItem.total_view}&nbsp;<span class="text-lowercase">{_p('views')}</span>
                        {/if}
                    </span>
                </div>
            {/if}
        </div>
    </div>

    <div class="ynadvblog_option">
        {if Phpfox::getUserId() && $aItem.user_id != Phpfox::getUserId() && $aItem.is_approved == 1 && $aItem.post_status = 'public'}
        <span class="js_ynblog_saved_blog_{$aItem.blog_id} {if !empty($aItem.is_saved)}active{/if} btn">
            <a {if empty($aItem.is_saved)}title="{_p var='save_this_ynblog'}"{else}title="{_p var='unsave_this_ynblog'}"{/if} href="javascript:void(0)" onclick="{if isset($sView) && $sView == 'saved'}$(this).parents('div.js_blog_parent').remove(); {/if}ynadvancedblog.updateSavedBlog({$aItem.blog_id}, {if empty($aItem.is_saved)}1{else}0{/if})">
                <i class="fa fa-bookmark hover" aria-hidden="true"></i>
            </a>
        </span>
        {/if}
        {if !empty($aItem.permission_enable) || (isset($sView) && $sView == 'favorite')}
        <div class="dropdown">
            <span class="dropdown-toggle btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-pencil-square-o hover" aria-hidden="true"></i>
            </span>
            <ul class="dropdown-menu dropdown-menu-right">
                {template file='ynblog.block.link'}
            </ul>
        </div>
        {/if}
    </div>
</div>