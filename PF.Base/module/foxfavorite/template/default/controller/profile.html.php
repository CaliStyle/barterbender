<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: profile.html.php 1124 2009-10-02 14:07:30Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if ($iPage > 1) }
{foreach from=$aFavorites item=aGroups}

{foreach from=$aGroups.items name=favorites item=aFavorite}
<div class="rowfavorite">
    <div style="float:left; width:80px; text-align:center;">
        {if !empty($aFavorite.image) && !isset($aFavorite.event_id)}
        <a href="{$aFavorite.link}">{$aFavorite.image}</a>
        {else}
        {img user=$aFavorite suffix='_50' max_width=50 max_height=50}
        {/if}
    </div>
    <div style="margin-left:85px; height:80px;">
        
        <a class="link_title" title="{$aFavorite.title}" href="{$aFavorite.link}">{$aFavorite.title|clean|shorten:55:'...'|split:20}</a>
        <div class="extra_info">
            {if isset($aFavorite.extra_info)}
            {$aFavorite.extra_info}
            {else}
            {$aFavorite.time_stamp_phrase}
            {/if}
        </div>
        <div class="t_right">
            {*{if isset($iFavoriteUserId) && $iFavoriteUserId == Phpfox::getUserId()}
                <ul class="item_menu">
                        <li><a href="#" onclick="if (confirm('{phrase var='favorite.are_you_sure' phpfox_squote=true}')) {left_curly} $('#js_favorite_item_{$aFavorite.favorite_id}').remove(); $.ajaxCall('favorite.delete', 'favorite_id={$aFavorite.favorite_id}'); {right_curly} return false;">{phrase var='favorite.delete'}</a></li>
                </ul>
            {/if}*}
        </div>
    </div>
    
    <div class="clear"></div>
</div>
{/foreach}

{pager}


{/foreach}


{else}
{literal}
<style>
    .favorite_section_menu ul a
    {
        border-radius: 6px 6px 6px 6px;
        display: block;
        font-weight: bold;
        line-height: 26px;
        margin-right: 5px;
        padding: 0 6px;
    }
    .favorite_section_menu ul li {
        float: left;
    }
</style>
<script type="text/javascript">
    function showActiveFavoriteGroup(sGroupName)
    {
        $('.yn_favorite_group').css('display', 'none');
        $('#yn_favorite_'+sGroupName).show();
        $('.title').css('display', 'none');
    }
    $Behavior.disableSearchElement = function ()
    {
        $(".header_filter_holder").children(".header_bar_float").first().remove();
        $(".header_filter_holder").children(".header_bar_float").last().remove();
    }
</script>
{/literal}

	
{if count($aFavorites)}
<div class="favorite_section_menu">
    <ul>
        {*{foreach from=$aFavorites item=aGroups}
        <li>
            <a rel="js_pages_block_detail" onclick="showActiveFavoriteGroup('{$aGroups.title}'); return false;" href="#">{$aGroups.title}</a>		
        </li>
        {/foreach}*}
    </ul>
    <div class="clear"></div>
    {foreach from=$aFavorites item=aGroups}
    <div id="yn_favorite_{$aGroups.title}" class="yn_favorite_group" style="clear:both;" >
        
        {module name='foxfavorite.entry' favorite_title=$aGroups.title}
        
        {if !$sView}
        <div class="view_all"><a href="{$aGroups.link}">{phrase var='foxfavorite.view_all'}</a></div>
        {/if}
    </div>
    {/foreach}
</div>
{else}
<div class="extra_info">
    {phrase var='foxfavorite.no_favorites_found'}
</div>
{/if}




{/if}
