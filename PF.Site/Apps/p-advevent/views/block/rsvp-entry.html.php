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
{if isset($aEvent.rsvp_id)}
<div class="feed_comment_extra">
	<a href="#" onclick="tb_show('{_p var='fevent.rsvp' phpfox_squote=true}', $.ajaxBox('fevent.rsvp', 'height=130&amp;width=300&amp;id={$aEvent.event_id}{if $aCallback !== false}&amp;module={$aCallback.module}&amp;item={$aCallback.item}{/if}')); return false;" id="js_event_rsvp_{$aEvent.event_id}">
	{if $aEvent.rsvp_id == 3}
		{_p var='fevent.not_attending'}
	{elseif $aEvent.rsvp_id == 2}
		{_p var='fevent.maybe_attending'}
	{elseif $aEvent.rsvp_id == 1}
		{_p var='fevent.attending'}
	{else}
		{_p var='fevent.respond'}
	{/if}						
	</a>
</div>
{/if}