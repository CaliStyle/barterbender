<div class="table form-group">
	<div class="ynaucton_table_header">
		<div class="bidincrement_row">
			<div class="bidincrement_from">{phrase var='from'}</div>
			<div class="bidincrement_to">{phrase var='to'}</div>
			<div class="bidincrement_increment">{phrase var='increment'}</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="ynaucton_table_body">
		{if isset($aForms.data_id)}
			<input type="hidden" name="val[data_id]" value="{value type='input' id='data_id'}" />
			<input type="hidden" name="val[user_id]" value="{value type='input' id='user_id'}" />
		{/if}
		{if isset($aForms.data_increasement) && count($aForms.data_increasement)}
			{foreach from=$aForms.data_increasement item=aItem key=iKey}
				<div class="bidincrement_items">
					<div class="bidincrement_row">
						<div class="bidincrement_from"><input class="field_from control" type="text" name="val[from][]" value="{$aItem.from}" readonly /></div>
						<div class="bidincrement_to"><input class="field_to control" type="text" name="val[to][]" value="{$aItem.to}" {if ($iKey + 1) != count($aForms.data_increasement)} readonly {/if} /></div>
						<div class="bidincrement_increment"><input class="field_increment control" type="text" name="val[increment][]" value="{$aItem.increment}" /></div>
						<div class="bidincrement_delete"><a href="javascript:;" onclick="removeBidIncrement(this);" >{img theme="misc/delete.png"}</a></div>
					</div>
					<div class="clear"></div>
				</div>
			{/foreach}
		{/if}
	</div>
	<div class="bidincrement_add_new">
		<a href="javascript:;" onclick="addNewBidIncrement();" >{phrase var='add_new_bid_increment'}</a>
	</div>
</div>
