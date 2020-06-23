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
<div id="js_event_item_holder" class="item-event-member-list events_item_holder js_fevent_member_list">
{/if}
    {if count($aInvites)}
        {foreach from=$aInvites name=invites item=aUser}
            {template file='user.block.rows'}
        {/foreach}
    {else}
        {if !PHPFOX_IS_AJAX}
        <div class="extra_info mx-2">
            {if $iRsvp == 1}
            {_p var='no_attendees'}
            {else}
            {_p var='no_results'}
            {/if}
        </div>
        {/if}
    {/if}
    {if $canPaging}
    {pager}
    {/if}
{if !PHPFOX_IS_AJAX}
</div>
{/if}