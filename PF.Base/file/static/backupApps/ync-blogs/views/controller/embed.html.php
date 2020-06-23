<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 17/02/2017
 * Time: 15:57
 */
defined('PHPFOX') or exit('NO DICE!');

?>
{foreach from=$aFiles item=file}
{if !empty($file)}
<script type="text/javascript" src="{$file}"></script>
{/if}
{/foreach}

<link rel="stylesheet" type="text/css" href="{$appPath}assets/embed.css" />
<link rel="stylesheet" type="text/css" href="{$sCorePath}theme/frontend/default/style/default/css/font-awesome/css/font-awesome.min.css" />
<!--  -->

<div class="moderation_row js_blog_parent clearfix">
    <div class="ynadvblog_item">
        <div class="ynadvblog_avatar">
            <a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" class="item_image{if empty($aItem.text)} full{/if}" style="background-image: url(<?php echo Phpfox::getService('ynblog.helper')->getImagePath($this->_aVars['aItem']['image_path'], $this->_aVars['aItem']['server_id'], '_big'); ?>)">
            </a>
            <i title="{_p var='Feature'}" class="js_ynblog_featured_blog_{$aItem.blog_id} fa fa-diamond ynadvblog_feature_icon" aria-hidden="true" {if empty($aItem.is_featured)} style="display: none;" {/if}></i>
            <span class="ynadvblog_status ">{_p var=$aItem.post_status}</span>
        </div>

        <div class="ynadvblog_info {if empty($aItem.is_featured)}no_feature{/if} {if isset($sView) && in_array($sView, array('friend', 'my'))} friend_blog{/if}">
            <i title="{_p var='Feature'}" class="js_ynblog_featured_blog_{$aItem.blog_id} fa fa-diamond ynadvblog_feature_icon big_view" aria-hidden="true" {if empty($aItem.is_featured)} style="display: none;" {/if}></i>
            {if !empty($aItem.category_id)}<a href="{permalink module='ynblog.category' id=$aItem.category_id title=$aItem.name|clean}" class="ynadvblog_category_title">{softPhrase var=$aItem.name|clean}</a>{/if}
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
            <a title="{$aItem.title|clean}" href="{permalink module='ynblog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link fw-bold ynadvblog_post_title" itemprop="url">{$aItem.title|clean}</a>
            {if !empty($aItem.text)}
                <div class="ynadvblog_desc item_content">{$aItem.text|striptag|stripbb|highlight:'search'|shorten:500:'...'}</div>
            {/if}
            <div class="show_on_grid clearfix">
                <div class="ynadvblog-total_favorite">
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
            </div>
        </div>
    </div>
</div>