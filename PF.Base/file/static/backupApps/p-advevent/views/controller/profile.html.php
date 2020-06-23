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
    <div class="p-profile-listing-container-theme">
        <div class="p-block">
            <div class="content">
                {module name='ynccore.mode_view'}
                <div class="p-listing-container p-fevent-listing-container col-4 p-mode-view has-list-one-column" data-mode-view="{$sModeViewDefault}">
                    {/if}
                    {foreach from=$aEvents name=event item=aItem}
                    {template file='fevent.block.event-item'}
                    {/foreach}
                    {pager}
                    {if !PHPFOX_IS_AJAX && $bShowModerator}
                    {moderation}
                    {/if}
                    {if !PHPFOX_IS_AJAX}
                </div>
            </div>
        </div>
    </div>
    {/if}
{/if}
