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
{if !$bIsInBrowse}
<div id="js_event_browse_guest_list">
{/if}
	<div style="height:300px;" class="label_flow form-control">
		{foreach from=$aInvites name=invites item=aInvite}
		<div style="margin-bottom: 10px">
		<!-- <div class="{if is_int($phpfox.iteration.invites/2)}row1{else}row2{/if}{if $phpfox.iteration.invites == 1} row_first{/if}"> -->
			<div class="go_left">
				{img user=$aInvite suffix='_200' max_width=50 max_height=50}
			</div>
			<div style="margin-left:55px;">
				{$aInvite|user}
			</div>
			<div class="clear"></div>
		</div>
		{/foreach}
	</div>
{if !$bIsInBrowse}
</div>
{/if}
{literal}
<script type="text/javascript">
    $Behavior.globalInit();
</script>
{/literal}
