<div class=" form-group">
	<div class="table_left">
		{phrase var='your_item'}:
	</div>
	{if $aSaItems && count($aSaItems) > 0} 
		<div class="table_right">			
			<select class="form-control ynsaMultipleChosen" name="val[ad_item_id]" id="js_ynsa_ad_item" >
				{foreach from=$aSaItems key=iKey item=aItem}
					<option data-item-type-id="{$aSaItemTypeId}" data-title="{$aItem.title|clean|shorten:22:'...'}" data-description="{$aItem.description|clean|shorten:87:'...'}" data-is-have-image="{$aItem.is_have_image}" value="{$aItem.id}"
						{if isset($aForms) && $aForms.ad_item_id == $aItem.id}
							selected="selected"
						{/if}

					>{$aItem.title}</option>
				{/foreach}
			</select>	
		</div>
		{literal}
		<script type="text/javascript">
			$Behavior.initSelectItemWhenEdit = function() { 
				if($('#js_ynsa_is_in_edit').val() == '1'){
					if($('#js_ynsa_ad_select_item').is(':visible')){
						ynsocialad.addForm.selectItem.iItemTypeIdSelected = '{/literal}{$aSaItemTypeId}{literal}';
					}					
				}
			}		
		</script>
		{/literal}
	{else}
		<div class="extra_info ynsaError" > {phrase var='you_have_no_item_of_this_type'} </div>
	{/if}
		<div class="clear"></div>
</div>


