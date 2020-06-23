<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='current'}" id="js_global_multi_form_holder">
	{if !empty($sCustomModerationFields)}
	{$sCustomModerationFields}
	{/if}
	<div id="js_global_multi_form_ids">{$sInputFields}</div>
	<div class="moderation_holder">
		<a href="javascript:;" class="moderation_action moderation_action_select" rel="select">{phrase var='core.select_all'}</a>
		<a href="javascript:;" class="moderation_action moderation_action_unselect" rel="unselect">{phrase var='core.un_select_all'}</a>
		<span class="moderation_process">{img theme='ajax/add.gif'}</span>
		<a href="javascript:;" class="moderation_drop{if !$iTotalInputFields} not_active{/if}"><span>{phrase var='core.with_selected'} (<strong class="js_global_multi_total">{$iTotalInputFields}</strong>)</span></a>		
		<ul>
			
			{if $sView == 'myauctions' }
				<li><a href="javascript:;" class="moderation_clear_all">{phrase var='core.clear_all_selected'}</a></li>
				<li><a onclick="ynauction.confirmDeleteManyAuctions();">{phrase var='delete_auction'}</a></li>
			{/if}
			{if $sView == 'pending' }
				<li><a href="javascript:;" class="moderation_clear_all">{phrase var='core.clear_all_selected'}</a></li>
				<li><a href="#approve" class="moderation_process_action" rel="auction.moderation">{phrase var='approve'}</a></li>
				<li><a href="#deny" class="moderation_process_action" rel="auction.moderation">{phrase var='deny'}</a></li>
			{/if}
		</ul>
	</div>
</form>