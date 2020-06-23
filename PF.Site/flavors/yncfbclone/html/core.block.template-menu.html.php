<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

{if !$bOnlyMobileLogin}
	{plugin call='core.template_block_template_menu_1'}
    <span class="btn-close">
        <span class="ico ico-close"></span>
    </span>
    { logo }
    <ul class="site-menu-small site_menu">
        {if Phpfox::isUser()}
            <li class="user-icon">
                <div class="item-user">
                    <div class="item-avatar">{img user=$aGlobalUser suffix='_50_square'}</div>
                    <div class="item-name">{$aGlobalUser|user}</div>
                </div>
            </li>
            
            {if Phpfox::isModule('mail')}
                <li id="hd-message" class="menu_messenger menu_messenger-js">
                    <a class="ajax_link"
                       href="{url link='mail'}">
                       <i class="ico ico-comment-dots"></i>
                        <span>{_p var='messages'}</span>
                    </a>
                </li>
            {/if}
        {/if}
        <div class="fbclone-menu-title">{_p var='explore'}</div>
        {if Phpfox::getUserBy('profile_page_id') <= 0 && isset($aMainMenus)}
            {plugin call='theme_template_core_menu_list'}
            {if ($iMenuCnt = 0)}{/if}
            {foreach from=$aMainMenus key=iKey item=aMainMenu name=menu}
                {if !isset($aMainMenu.is_force_hidden)}
                    {iterate int=$iMenuCnt}
                {/if}
                <li class="{if $aMainMenu.url == ''}menu-home {/if}" rel="menu{$aMainMenu.menu_id}" {if (isset($iTotalHide) && isset($iMenuCnt) && $iMenuCnt > $iTotalHide)} style="display:none;" {/if} {if (($aMainMenu.url == 'apps' && count($aInstalledApps)) || (isset($aMainMenu.children) && count($aMainMenu.children))) || (isset($aMainMenu.is_force_hidden))}class="{if isset($aMainMenu.is_force_hidden) && isset($iTotalHide)}is_force_hidden{else}explore{/if}{if ($aMainMenu.url == 'apps' && count($aInstalledApps))} explore_apps{/if}"{/if}>
                    <a {if !isset($aMainMenu.no_link) || $aMainMenu.no_link != true}href="{url link=$aMainMenu.url}" {else} href="#" onclick="return false;" {/if} class="{if isset($aMainMenu.is_selected) && $aMainMenu.is_selected} menu_is_selected {/if}{if isset($aMainMenu.external) && $aMainMenu.external == true}no_ajax_link {/if}ajax_link">
                    	{if $aMainMenu.url == '' && Phpfox::isUser()}
							<i class="ico ico-newspaper"></i>
                    	{else}
	                    	{if isset($aMainMenu.mobile_icon) && $aMainMenu.mobile_icon}
	                            <i class="{$aMainMenu.mobile_icon}"></i>
	                        {else}
	                            <i class="ico ico-box-o"></i>

	                        {/if}
                        {/if}
                        
                        <span>
                            {if $aMainMenu.url == '' && Phpfox::isUser()}{_p var='news_feed'}{else}{_p var=$aMainMenu.var_name}{/if}{if isset($aMainMenu.suffix)}{$aMainMenu.suffix}{/if}
                        </span>
                    </a>
                    {if $aMainMenu.url == '' && Phpfox::isUser()}
                        <div class="feed-sort-order">
                            <div class="feed-sort-holder dropdown" data-action="feed_sort_holder_click">
                                <a href="#" class="feed-sort-order-link" data-toggle="dropdown"><span class="ico ico-dottedmore"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="#"{if !Phpfox::getUserBy('feed_sort')} class="active"{/if} rel="0">{_p var='top_stories'}</a></li>
                                    <li><a href="#"{if Phpfox::getUserBy('feed_sort')} class="active"{/if} rel="1">{_p var='most_recent'}</a></li>
                                </ul>
                            </div>
                        </div>
                    {/if}
                </li>
            {/foreach}
        {/if}
    </ul>
{/if}