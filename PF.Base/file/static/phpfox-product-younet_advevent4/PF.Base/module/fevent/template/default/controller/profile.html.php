<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>

{if !count($aEvents)}
    {if !PHPFOX_IS_AJAX}
        <div class="help-block">
            {_p var='fevent.no_events_found'}
        </div>
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}
        <div class="ync-block">
            <div class="content">
                <div class="ync-mode-view-container page-{$sView}-js fevent-page-index-js" data-page="{$sView}">
                    <span class="ync-mode-view-btn grid" data-mode="grid" title="{_p var='grid_view'}"><i class="ico ico-th"></i></span>
                    <span class="ync-mode-view-btn list" data-mode="list" title="{_p var='list_view'}">
                        <i class="ico ico-list icon-normal" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list.svg)"></i>
                        <i class="ico ico-list icon-hover" style="background-image: url({param var='core.path_actual'}/PF.Site/Apps/ync-core/assets/icon/ico_list_dark.svg)"></i>
                    </span>
                </div>
                {if $sponsor == 1}<div class="bg-info" style="padding: 10px;margin-bottom: 10px;">{_p var='fevent.sponsor_help'}</div>{/if}
                <div class="item-container ynfevent-content-item-list ync-listing-container col-2 fevent-content-{$sView} ync-view-modes-js" data-mode-view="" data-mode-view-default="grid">
                    {/if}
                        {foreach from=$aEvents name=event item=aItem}
                            <article class="ync-item">{template file='fevent.block.listing-edit-item'}</article>
                        {/foreach}
                        {pager}
                        {if !PHPFOX_IS_AJAX && $bShowModerator}
                           {if Phpfox::getUserParam('fevent.can_approve_events') || Phpfox::getUserParam('fevent.can_delete_other_event')}
								{moderation}
							{/if}
                        {/if}
                    {if !PHPFOX_IS_AJAX}
                </div>
            </div>
        </div>
    {/if}
{/if}
