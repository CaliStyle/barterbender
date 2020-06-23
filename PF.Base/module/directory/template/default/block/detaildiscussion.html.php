<div id="yndirectory_business_detail_module_dicussion" class="yndirectory_business_detail_module_dicussion">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeDicussionListFilter'
		result_div_id='js_ynd_dicussion_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='dicussion'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	
	{*
	<div id="js_ynd_dicussion_list">
		{module name='directory.detaildicussionlist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>
	*}
	{if $bCanAddDiscussionInBusiness}
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_dicussion = function(){
					if($('#yndirectory_business_detail_module_dicussion').length > 0 && $('#yndirectory_business_detail_module_dicussion_add').length == 0){
						if($('#section_menu').length > 0){
						} else {
							$('#breadcrumb_holder').append('<div id="section_menu" class="yndirectory-section-menu"><ul><li id="yndirectory_business_detail_module_dicussion_add"><a id="yndirectory_add_new_item" href="{/literal}{$sUrlAddDiscussion}{literal}">{/literal}{phrase var='post_a_new_thread'}{literal}</a></li></ul></div>');
							yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'dicussion');		
						}					
					}
				};
			</script>
		{/literal}
	{/if}

</div>
