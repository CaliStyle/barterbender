<div id="yndirectory_business_detail_module_photo" class="yndirectory_business_detail_module_photo">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changePhotoListFilter'
		result_div_id='js_ynd_photo_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='photos'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddPhotoInBusiness}
		<div id="yndirectory_menu_button">
			<a class="btn btn-primary btn-sm" href="{$sUrlAddPhoto}" id="yndirectory_add_new_item">{phrase var='upload_a_new_image'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_photo = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'photos');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_photo_list">

		{module name='directory.detailphotoslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
