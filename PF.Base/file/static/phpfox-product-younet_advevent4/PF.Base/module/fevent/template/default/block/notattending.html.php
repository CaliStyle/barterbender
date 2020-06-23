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

{if count($aNotAttendingInvites)}
    <ul class="fevent-invite-result">
	{foreach from=$aNotAttendingInvites name=invites item=aInvite}
        <li class="fevent-invite-result__item dont-unbind-children" data-toggle="tooltip" data-placement="bottom" title="{$aInvite.full_name}">{img user=$aInvite suffix='_50_square' max_width=32 max_height=32 class='v_middle'}</li>
	{/foreach}
	</ul>
{/if}

{if $iNotAttendingCnt > 8 }
<a href="#" id="js_block_bottom_link_1"></a>
{/if}

<script type="text/javascript">
var sEventId = {$aEvent.event_id};
{literal}
	$Behavior.onClickEventGuestListNotAttending = function()
	{
		if ($Core.exists('#js_block_border_fevent_notattending')){
			$('#js_block_border_fevent_notattending #js_block_bottom_link_1').click(function()
			{
				$Core.box('fevent.browseList', '400', 'id=' + sEventId + '&rsvp=' + 3);
				return false;
			});
		}


	}
{/literal}
</script>