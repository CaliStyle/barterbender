{if count($aList)}
	<div style="height:300px;" class="label_flow">
		{foreach from=$aList name=checkin item=aItem}

			<div id="js_row_like_{$aItem.user_id}" style="position:relative;" class="yndirectory_row_list_checkin {if is_int($phpfox.iteration.checkin/2)}row1{else}row2{/if}{if $phpfox.iteration.checkin == 1} row_first{/if}">
				
				<div class="go_left">
					{img user=$aItem suffix='_50_square' max_width=50 max_height=50}	
				</div>

				<div class="yndirectory_username">
					{$aItem|user:'':'':30}
				</div>

				<div class="clear"></div>
			</div>

		{/foreach}
	</div>
{else}
	<div class="help-block">
		{phrase var='nobody_checks_in_here'}
	</div>
{/if}
<script type="text/javascript">
	$Behavior.globalInit();
</script>