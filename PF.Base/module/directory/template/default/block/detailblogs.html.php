<div id="yndirectory_business_detail_module_blog" class="yndirectory_business_detail_module_blog">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeBlogListFilter'
		result_div_id='js_ynd_blog_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type=$sModuleId
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddBlogInBusiness}
		<div id="yndirectory_menu_button">
			<a class="btn btn-primary btn-sm" href="{$sUrlAddBlog}" id="yndirectory_add_new_item">{phrase var='add_new_blog'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_blog = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'blogs');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_blog_list">

		{module name='directory.detailblogslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
