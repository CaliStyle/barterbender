<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 * @package  		Module FoxFavorite
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($sView) && $sView}
{literal}
<script>
    var sMenuId = '#yn_submenu_{/literal}{$sView}{literal}';
    $Behavior.onCreateMenu = function()
    {
        $('.sub_section_menu').find('ul').find('li').removeClass('active');
        $('.sub_section_menu').find('ul').find('li').find(sMenuId).addClass('active');
    }
</script>
{/literal}
{/if}
 
<div class="sub_section_menu">
    <ul>		
        {foreach from=$aProfileLinksFavorite item=aProfileLink}
        {if strpos($aProfileLink.url,'foxfavorite') !== false}
        <li class="{if isset($aProfileLink.is_selected)} active{/if}">
            <a href="{url link=$aProfileLink.url}" class="ajax_link">{$aProfileLink.phrase}{if isset($aProfileLink.total)}<span class="pending">{$aProfileLink.total|number_format}</span>{/if}</a>
            {if isset($aProfileLink.sub_menu) && is_array($aProfileLink.sub_menu) && count($aProfileLink.sub_menu)}
            <ul>
                {foreach from=$aProfileLink.sub_menu item=aProfileLinkSub}
                <li id="yn_submenu_{$aProfileLinkSub.module_name}" class="{if isset($aProfileLinkSub.is_selected)}active{/if}">
		                <a href="{url link=$aProfileLinkSub.url}">
                            {if $aProfileLinkSub.module_name == 'fevent'}
                                {phrase var='foxfavorite.advanced_events'}
                            {else}
                                {$aProfileLinkSub.phrase}
                            {/if}
		                	{if isset($aProfileLinkSub.total) && $aProfileLinkSub.total > 0}
		                	<span class="pending">
		                		{$aProfileLinkSub.total|number_format}
		                	</span>
		                	{/if}		                	
		                </a>
                </li>
                {/foreach}
            </ul>
            {/if}
        </li>
        {/if}
        {/foreach}
    </ul>
    <div class="clear"></div>
    <div class="js_cache_check_on_content_block" style="display:none;"></div>
    <div class="js_cache_profile_id" style="display:none;">{$aUser.user_id}</div>
    <div class="js_cache_profile_user_name" style="display:none;">{$aUser.user_name}</div>
</div>