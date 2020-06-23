<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 10:52
 */
defined('PHPFOX') or exit('NO DICE!');

?>
{if $aItem.canPublish}
<li class="js_ynblog_publish_blog_btn_{$aItem.blog_id}">
    <a href="javascript:void(0)" onclick="ynadvancedblog.publishBlog({$aItem.blog_id})">
        <i class="fa fa-eye" aria-hidden="true"></i>
        {_p var='publish'}
    </a>
</li>
{/if}

{if $aItem.canApprove}
<li class="js_ynblog_approve_blog_btn_{$aItem.blog_id}">
    <a href="javascript:void(0)" onclick="ynadvancedblog.approveBlog({$aItem.blog_id})">
        <i class="fa fa-eye" aria-hidden="true"></i>
        {_p var='approve'}
    </a>
</li>
{/if}

{if $aItem.canDeny}
<li class="js_ynblog_deny_blog_btn_{$aItem.blog_id}">
    <a href="javascript:void(0)" onclick="ynadvancedblog.denyBlog({$aItem.blog_id})">
        <i class="fa fa-eye-slash" aria-hidden="true"></i>
        {_p var='deny'}
    </a>
</li>
{/if}

{if $aItem.canEdit}
	<li>
        <a href="{url link="ynblog.add" id=""$aItem.blog_id""}">
            <i class="fa fa-pencil" aria-hidden="true"></i>
            {_p var='edit'}
        </a>
    </li>
{/if}

{if $aItem.canFeature}
<li class="js_ynblog_featured_blog_btn_{$aItem.blog_id}">
    {if !empty($aItem.is_featured)}
        <a href="javascript:void(0)" onclick="ynadvancedblog.updateFeature({$aItem.blog_id}, 0)">
            <i class="fa fa-diamond" aria-hidden="true"></i>
                {_p var='un_feature'}
        </a>
    {else}
        <a href="javascript:void(0)" onclick="ynadvancedblog.updateFeature({$aItem.blog_id}, 1)">
            <i class="fa fa-diamond" aria-hidden="true"></i>
            {_p var='feature'}
        </a>
    {/if}
</li>
{/if}

{if isset($sView) && $sView == 'favorite'}
<li>
    <a href="javascript:void(0)" title="{_p('un_favorite')}" class="favorite" onclick="$(this).parents('li.ynadvblog_item').remove(); ynadvancedblog.updateFavorite({$aItem.blog_id},0);">
        <i class="fa fa-star"></i> {_p var='un_favorite'}
    </a>
</li>
{/if}

{if $aItem.canDelete }
    <li class="item_delete">
        <a href="{if isset($iCurrentProfileId)}{url link='ynblog.delete'.'/'.$aItem.blog_id profile=$iCurrentProfileId}{else}{url link='ynblog.delete'.'/'.$aItem.blog_id}{/if}" class="no_ajax_link sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_blog' phpfox_squote=true}" title="{_p var='delete_blog'}">
            <i class="fa fa-trash-o" aria-hidden="true"></i>
            {_p var='delete'}
        </a>
    </li>
{/if}
{plugin call='ynblog.template_block_entry_links_main'}