<div id="yndirectory_business_detail_module_music" class="yndirectory_business_detail_module_music">	
	{module name='directory.filterinbusiness' 
		sPlaceholderKeyword=$sPlaceholderKeyword 

		ajax_action='directory.changeMusicListFilter'
		result_div_id='js_ynd_music_list'
		custom_event='ondatachanged'
		is_prevent_submit='true'

		hidden_type='musics'
		hidden_businessid=$aYnDirectoryDetail.aBusiness.business_id
		aYnDirectoryDetail=$aYnDirectoryDetail
		
		hidden_select=$hidden_select

	}	

	{if $bCanAddMusicInBusiness}
		<div id="yndirectory_menu_button">
			<a class="btn btn-primary btn-sm" href="{$sUrlAddMusic}" id="yndirectory_add_new_item">{phrase var='upload_a_song'}</a>
		</div>
		{literal}
			<script type="text/javascript">
				;$Behavior.init_yndirectory_business_detail_module_music = function(){
					yndirectory.addAjaxForCreateNewItem({/literal}{$aYnDirectoryDetail.aBusiness.business_id}{literal}, 'musics');		
				};
			</script>
		{/literal}
	{/if}

	<div id="js_ynd_music_list">

		{module name='directory.detailmusicslist'

			aYnDirectoryDetail=$aYnDirectoryDetail
		}
	</div>

</div>
