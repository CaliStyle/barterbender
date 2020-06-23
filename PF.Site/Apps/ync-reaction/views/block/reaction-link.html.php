<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox
 * @version          $Id: link.html.php 6671 2013-09-25 10:06:46Z Fern $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if $aLike.like_type_id == 'feed_mini'}
    {/if}
    <div class="ync-reaction-container ync-reaction-container-js">
        <a role="button"
         data-toggle="ync_reaction_toggle_cmd"
         data-label1="{_p var='like'}"
         data-label2="{_p var='unlike'}"
         data-liked="{if $aLike.like_is_liked}1{else}0{/if}"
         data-type_id="{$aLike.like_type_id}"
         data-item_id="{$aLike.like_item_id}"
         data-reaction_color="{$aYncLike.color}"
         data-reaction_id="{$aYncLike.id}"
         data-reaction_title="{_p var=$aYncLike.title|clean}"
         data-full_path="{$aYncLike.full_path}"
         data-feed_id="{if isset($aFeed.feed_id)}{$aFeed.feed_id}{else}0{/if}"
         data-is_custom="{if $aLike.like_is_custom}1{else}0{/if}"
         data-table_prefix="{if isset($aFeed.feed_table_prefix)}{$aFeed.feed_table_prefix}{elseif defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')}pages_{/if}"
         class="js_like_link_toggle {if $aLike.like_is_liked}liked{else}unlike{/if} ync-reaction-link" style="-webkit-user-select: none; -webkit-touch-callout: none;">
            {if $aLike.like_is_liked && !empty($aUserReacted)}
                <div class="ync-reacted-icon-outer"><img src="{$aUserReacted.full_path}" alt="" class="ync-reacted-icon" oncontextmenu="return false;"> </div>{$aUserReacted|yncreaction_color_title}
            {else}
                <div class="ync-reacted-icon-outer"></div>
                <strong class="ync-reaction-title"></strong>
            {/if}
        </a>
        {if !empty($aYncReactions) && count($aYncReactions) > 1}
            <div class="ync-reaction-list">
                {foreach from=$aYncReactions item=aYncReaction}
                <div class="ync-reaction-item dont-unbind " data-toggle="tooltip" data-placement="top" data-original-title="{_p var=$aYncReaction.title|clean}">

                    <a class="item-outer"
                       data-toggle="ync_reaction_toggle_cmd"
                       data-label1="{_p var='like'}"
                       data-label2="{_p var='unlike'}"
                       data-liked="{if $aLike.like_is_liked}1{else}0{/if}"
                       data-type_id="{$aLike.like_type_id}"
                       data-reaction_color="{$aYncReaction.color}"
                       data-reaction_id="{$aYncReaction.id}"
                       data-reaction_title="{_p var=$aYncReaction.title|clean}"
                       data-full_path="{$aYncReaction.full_path}"
                       data-item_id="{$aLike.like_item_id}"
                       data-feed_id="{if isset($aFeed.feed_id)}{$aFeed.feed_id}{else}0{/if}"
                       data-is_custom="{if $aLike.like_is_custom}1{else}0{/if}"
                       data-table_prefix="{if isset($aFeed.feed_table_prefix)}{$aFeed.feed_table_prefix}{elseif defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')}pages_{/if}"
                       style="-webkit-user-select: none;"
                       title="{_p var=$aYncReaction.title|clean}"
                    >
                        <img src="{$aYncReaction.full_path}" alt="">
                    </a>
                </div>
                {/foreach}
            </div>
        {/if}
    </div>

{if $aLike.like_type_id == 'feed_mini' && !empty($aLike.like_is_custom)}
    {if isset($aFeed.feed_table_prefix)}
        {assign var='sPrefixTable' value=$aFeed.feed_table_prefix}
    {elseif defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE')}
        {assign var='sPrefixTable' value='pages_' }
    {/if}
    {module name='yncreaction.reaction-list-mini' type_id='feed_mini' item_id=$aLike.like_item_id table_prefix=$sPrefixTable}
{/if}