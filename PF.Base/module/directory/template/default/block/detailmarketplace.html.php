<div id="yndirectory_business_detail_module_marketplace" class="yndirectory_business_detail_module_marketplace">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeMarketplaceListFilter'
		result_div_id='js_ynd_marketplace_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='marketplace'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddMarketplaceInBusiness}
		<div id="yndirectory_menu_button">
			<a class="btn btn-primary btn-sm" href="{$sUrlAddMarketplace}" id="yndirectory_add_new_item">{phrase var='add_new_listing'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_marketplace = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'marketplace');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_marketplace_list">

		{module name='directory.detailmarketplacelist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
