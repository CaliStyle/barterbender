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
{if !PHPFOX_IS_AJAX}
    {module name='fevent.search'}
    <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key={$apiKey}&libraries=places"></script>
{/if}
{if !$bInHomepage}
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
                    <div class="item-container ynfevent-content-item-list ync-listing-container col-2 fevent-content-{$sView} full-border ync-view-modes-js" data-mode-view="" data-mode-view-default="grid">
                        {/if}
                            {foreach from=$aEvents name=event item=aItem}
                                <article class="ync-item">{template file='fevent.block.listing-edit-item'}</article>
                            {/foreach}
                            {pager}
                            {if !PHPFOX_IS_AJAX && $bShowModerator}
                                {moderation}
                            {/if}
                        {if !PHPFOX_IS_AJAX}
                    </div>
                </div>
            </div>
        {/if}
    {/if}
{/if}