<div id="yndirectory_managepages">
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="managepages" id="yndirectory_pagename" name="yndirectory_pagename">
	</div>

	{if $sView == 'maincontent' }

	<div id='main_content_pages'>
		{phrase var='manage_pages'}

		{phrase var='introduce_manage_pages'}
		<form method="post" action="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_maincontent'}" onsubmit=""  enctype="multipart/form-data">
				
		<input type="hidden" name="val[business_id]" value="{$iEditedBusinessId}" >

		<input type="hidden" name="val[view]" value="{$sView}" >
		<table class="yndirectory-table-full">
		<tr>
			<th>{phrase var='page_name'}</th>
			<th>{phrase var='show'}</th>
			<th>{phrase var='landing_page'}</th>
			<th>{phrase var='options'}</th>
		</tr>

		{if isset($aModuleView.overview)}
			<tr>
				<td>{$aModuleView.overview.module_phrase|convert}</td>
				<td>
				<div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.overview.data_id}] value="{if isset($aModuleView.overview.is_show)}{$aModuleView.overview.is_show}{/if}" {if isset($aModuleView.overview.is_show) && $aModuleView.overview.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.overview.data_id}" {if isset($aModuleView.overview.module_landing) && $aModuleView.overview.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.overview.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.aboutus)}
			<tr>
				<td>{$aModuleView.aboutus.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.aboutus.data_id}] value="{if isset($aModuleView.aboutus.is_show)}{$aModuleView.aboutus.is_show}{/if}" {if isset($aModuleView.aboutus.is_show) && $aModuleView.aboutus.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.aboutus.data_id}" {if isset($aModuleView.aboutus.module_landing) && $aModuleView.aboutus.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editaboutus'}" >{phrase var='edit_page'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.activities)}
			<tr>
				<td>{$aModuleView.activities.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.activities.data_id}] value="{if isset($aModuleView.activities.is_show)}{$aModuleView.activities.is_show}{/if}" {if isset($aModuleView.activities.is_show) && $aModuleView.activities.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.activities.data_id}" {if isset($aModuleView.activities.module_landing) && $aModuleView.activities.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.activities.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}
		
		{if isset($aModuleView.members)}
			<tr>
				<td>{_p var = 'members_up'}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.members.data_id}] value="{if isset($aModuleView.members.is_show)}{$aModuleView.members.is_show}{/if}" {if isset($aModuleView.members.is_show) && $aModuleView.members.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.members.data_id}" {if isset($aModuleView.members.module_landing) && $aModuleView.members.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.members.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.followers)}
			<tr>
				<td>{_p var = 'followers_up'}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.followers.data_id}] value="{if isset($aModuleView.followers.is_show)}{$aModuleView.followers.is_show}{/if}" {if isset($aModuleView.followers.is_show) && $aModuleView.followers.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.followers.data_id}" {if isset($aModuleView.followers.module_landing) && $aModuleView.followers.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.followers.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.reviews)}
			<tr>
				<td>{$aModuleView.reviews.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.reviews.data_id}] value="{if isset($aModuleView.reviews.is_show)}{$aModuleView.reviews.is_show}{/if}" {if isset($aModuleView.reviews.is_show) && $aModuleView.reviews.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.reviews.data_id}" {if isset($aModuleView.reviews.module_landing) && $aModuleView.reviews.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.reviews.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.musics)}
			<tr>
				<td>{$aModuleView.musics.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.musics.data_id}] value="{if isset($aModuleView.musics.is_show)}{$aModuleView.musics.is_show}{/if}" {if isset($aModuleView.musics.is_show) && $aModuleView.musics.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.musics.data_id}" {if isset($aModuleView.musics.module_landing) && $aModuleView.musics.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.musics.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.blogs)}
			<tr>
				<td>{$aModuleView.blogs.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.blogs.data_id}] value="{if isset($aModuleView.blogs.is_show)}{$aModuleView.blogs.is_show}{/if}" {if isset($aModuleView.blogs.is_show) && $aModuleView.blogs.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.blogs.data_id}" {if isset($aModuleView.blogs.module_landing) && $aModuleView.blogs.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.blogs.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.photos)}
			<tr>
				<td>{$aModuleView.photos.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.photos.data_id}] value="{if isset($aModuleView.photos.is_show)}{$aModuleView.photos.is_show}{/if}" {if isset($aModuleView.photos.is_show) && $aModuleView.photos.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.photos.data_id}" {if isset($aModuleView.photos.module_landing) && $aModuleView.photos.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.photos.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.videos)}
			<tr>
				<td>{$aModuleView.videos.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.videos.data_id}] value="{if isset($aModuleView.videos.is_show)}{$aModuleView.videos.is_show}{/if}" {if isset($aModuleView.videos.is_show) && $aModuleView.videos.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.videos.data_id}" {if isset($aModuleView.videos.module_landing) && $aModuleView.videos.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.videos.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}


		{if isset($aModuleView.polls)}
			<tr>
				<td>{$aModuleView.polls.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.polls.data_id}] value="{if isset($aModuleView.polls.is_show)}{$aModuleView.polls.is_show}{/if}" {if isset($aModuleView.polls.is_show) && $aModuleView.polls.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.polls.data_id}" {if isset($aModuleView.polls.module_landing) && $aModuleView.polls.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.polls.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.coupons) && Phpfox::isModule('coupon')}
			<tr>
				<td>{$aModuleView.coupons.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.coupons.data_id}] value="{if isset($aModuleView.coupons.is_show)}{$aModuleView.coupons.is_show}{/if}" {if isset($aModuleView.coupons.is_show) && $aModuleView.coupons.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.coupons.data_id}" {if isset($aModuleView.coupons.module_landing) && $aModuleView.coupons.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.coupons.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.events)}
			<tr>
				<td>{$aModuleView.events.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.events.data_id}] value="{if isset($aModuleView.events.is_show)}{$aModuleView.events.is_show}{/if}" {if isset($aModuleView.events.is_show) && $aModuleView.events.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.events.data_id}" {if isset($aModuleView.events.module_landing) && $aModuleView.events.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.events.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.jobs) && Phpfox::isModule('jobposting')}
			<tr>
				<td>{$aModuleView.jobs.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.jobs.data_id}] value="{if isset($aModuleView.jobs.is_show)}{$aModuleView.jobs.is_show}{/if}" {if isset($aModuleView.jobs.is_show) && $aModuleView.jobs.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.jobs.data_id}" {if isset($aModuleView.jobs.module_landing) && $aModuleView.jobs.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.jobs.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.marketplace)}
			<tr>
				<td>{$aModuleView.marketplace.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.marketplace.data_id}] value="{if isset($aModuleView.marketplace.is_show)}{$aModuleView.marketplace.is_show}{/if}" {if isset($aModuleView.marketplace.is_show) && $aModuleView.marketplace.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.marketplace.data_id}" {if isset($aModuleView.marketplace.module_landing) && $aModuleView.marketplace.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="javascript:void(0)" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aModuleView.marketplace.data_id}'));">{phrase var='edit_title'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.faq)}
			<tr>
				<td>{$aModuleView.faq.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.faq.data_id}] value="{if isset($aModuleView.faq.is_show)}{$aModuleView.faq.is_show}{/if}" {if isset($aModuleView.faq.is_show) && $aModuleView.faq.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.faq.data_id}" {if isset($aModuleView.faq.module_landing) && $aModuleView.faq.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editfaq'}" >{phrase var='edit_page'}</a>
				</td>
			</tr>
		{/if}

		{if isset($aModuleView.contactus)}
			<tr>
				<td>{$aModuleView.contactus.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aModuleView.contactus.data_id}] value="{if isset($aModuleView.contactus.is_show)}{$aModuleView.contactus.is_show}{/if}" {if isset($aModuleView.contactus.is_show) && $aModuleView.contactus.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aModuleView.contactus.data_id}" {if isset($aModuleView.contactus.module_landing) && $aModuleView.contactus.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
					{if $aBusiness.setting_support.allow_business_owner_to_edit_contact_form}
						<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editcontactus'}" >{phrase var='edit_form'}</a>
					{/if}
				</td>
			</tr>
		{/if}

		{foreach from=$aPagesModule item=aPage}
			{if $aPage.module_type == 'contentpage'}
			<tr>
				<td>{$aPage.module_phrase|convert}</td>
				<td><div class="checkbox ync-checkbox-custom"><label ><input type="checkbox" name=val[page_show][{$aPage.data_id}] value="{if isset($aPage.is_show)}{$aPage.is_show}{/if}" {if isset($aPage.is_show) && $aPage.is_show}checked="checked"{/if}><i class="ico ico-square-o"></i></label></div></td>
				<td><div class="radio ync-radio-custom"><label><input type="radio" name=val[page_landing] value="{$aPage.data_id}" {if isset($aPage.module_landing) && $aPage.module_landing}checked="checked"{/if}><i class="ico ico-circle-o"></i></label>
    	            </div>
				</td>
				<td>
				{*
				{if $aPage.module_name == 'aboutus'}
					<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editaboutus'}" >{phrase var='edit_page'}</a>
				{elseif $aPage.module_name == 'faq'}
					<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editfaq'}" >{phrase var='edit_page'}</a>
				{elseif $aPage.module_name == 'contactus'}
					<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editcontactus'}" >{phrase var='edit_form'}</a>
				{elseif $aPage.module_id == 0}
				*}

					<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editcustompage.idcustom_'.$aPage.data_id}" >{phrase var='edit_page'}</a>
					/
					<a style="cursor:pointer;" onclick="$.ajaxCall('directory.deleteCustomPage', 'height=300&amp;width=300&idcustom='+{$aPage.data_id});" >{phrase var='delete_page'}</a>

				{*
				{else }
					<a href="#" onclick="tb_show(oTranslations['directory.edit_title'], $.ajaxBox('directory.editTitlePageBusinessBlock', 'height=300&width=530&data_id={$aPage.data_id}'));">{phrase var='edit_title'}</a>
				{/if}
				*}
				</td>
			</tr>
			{/if}

		{/foreach}
		

		</table>

		{if $aBusiness.setting_support.allow_business_owner_to_add_new_content_pages}
			<a href="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_addnewcontent'}">
				{phrase var='add_new_content_page'}
			</a>
		{/if}
			<div id="js_submit_manage_page" class="table_clear">
				<button type="submit" name="val[manage_page]" value="{phrase var='save_changes'}" class="btn btn-sm btn-primary">{phrase var='save_changes'}</button>
			</div>
		</form>
	</div>

	{elseif $sView == 'editaboutus'}
	<div id='edit_about_us' >
			{$sCreateJs}
		<form method="post" action="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editaboutus'}" id="js_edit_about_us" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
			<input type="hidden" name="val[business_id]" value="{$iEditedBusinessId}" >
			<input type="hidden" name="val[view]" value="{$sView}" >

			<div class="table form-group">
				<div class="table_left">
					{phrase var='content'}
				</div>
				<div class="table_right">
					{editor id='contentpage'}
				</div>
			</div>

			<div class="yndirectory-button">
				<button class="btn btn-sm btn-primary" type="submit" name="val[edit_about_us]" id="edit_about_us" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
			</div>
		</form>

	</div>
	{elseif $sView == 'editcustompage'}
	<div id='edit_custom_page' >
		{$sCreateJs}
		<form method="post" action="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editcustompage.idcustom_'.$aForms.data_id}" id="js_edit_custom_page" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">

			<input type="hidden" name="val[business_id]" value="{$iEditedBusinessId}" >
			<input type="hidden" name="val[view]" value="{$sView}" >
			<input type="hidden" name="val[custompage_id]" value="{$aForms.data_id}" >

			<div class="table form-group">
				<div class="table_left">
					{phrase var='title'}
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="val[page_title]" value="{if isset($aForms.module_phrase)}{$aForms.module_phrase}{/if}" >
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='content'}
				</div>
				<div class="table_right">
					{editor id='contentpage'}
				</div>
			</div>

			<div class="yndirectory-button">
				<button class="btn btn-sm btn-primary" type="submit" name="val[edit_custom_page]" id="edit_custom_page" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
			</div>
		</form>

	</div>
	{elseif $sView == 'addnewcontent'}
	<div id='add_new_content' >
		{$sCreateJs}
		<form method="post" action="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_addnewcontent'}" id="js_add_new_page" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
			<input type="hidden" name="val[business_id]" value="{$iEditedBusinessId}" >
			<input type="hidden" name="val[view]" value="{$sView}" >
			<div class="table form-group">
				<div class="table_left">
					{phrase var='title'}
				</div>
				<div class="table_right">
					<input class="form-control" type="text" name="val[page_title]" id="page_title" value="">
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='content'}
				</div>
				<div class="table_right">
					{editor id='contentpage'}
				</div>
			</div>

			<div class="yndirectory-button main_break">
				<button class="btn btn-sm btn-primary" type="submit" name="val[add_new_page]" id="add_new_page" value="{phrase var='add_new_page'}">{phrase var='add_new_page'}</button>
			</div>
		</form>

	</div>
	{elseif $sView == 'editcontactus'}
	<div id='edit_contact_us' >
		<form method="post" action="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editcontactus'}" id="js_edit_contact_us" onsubmit="" enctype="multipart/form-data">
			<input type="hidden" name="val[business_id]" value="{$iEditedBusinessId}" >
			<input type="hidden" name="val[contact_us_id]" value="{if isset($aContactUs.contactus_id) }{$aContactUs.contactus_id}{/if}" >
			<input type="hidden" name="val[view]" value="{$sView}" >
			<div class="table form-group">
				<div class="table_left">
					{phrase var='contact_description'}
				</div>
				<div class="table_right">
					<textarea class="form-control" name="val[contact_description]" id="contact_description"  rows="6" cols="24">{if isset($aContactUs.description)}{$aContactUs.description|parse}{/if}</textarea>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='email'}
				</div>
				<div class="table_right">
					<div class="checkbox">
						<label for="">
							<input type="checkbox" name=val[email_enable] value="{if isset($aContactUs.email_enable)}1{/if}" {if isset($aContactUs.email_enable) && $aContactUs.email_enable}checked="checked"{/if}>
							{phrase var='enabled_this_field'}
						</label>
					</div>
					<div class="checkbox">
						<label for="">
							<input type="checkbox" name=val[email_require] value="{if isset($aContactUs.email_require)}1{/if}" {if isset($aContactUs.email_require) && $aContactUs.email_require}checked="checked"{/if}>
							{phrase var='required_this_field'}
						</label>
					</div>
				</div>
			</div>
			<div class="table form-group">
				<div class="table_left">
					{phrase var='receiver'}
				</div>
				<div class="table_right">
					<div class="checkbox">
						<label for="">
							<input type="checkbox" name=val[receiver_enable] value="{if isset($aContactUs.receiver_enable)}1{/if}" {if isset($aContactUs.receiver_enable) && $aContactUs.receiver_enable}checked="checked"{/if}>
							{phrase var='enabled_this_field'}
						</label>
					</div>
					<div class="checkbox">
						<label for="">
							<input type="checkbox" name=val[receiver_require] value="{if isset($aContactUs.receiver_require)}1{/if}" {if isset($aContactUs.receiver_require) && $aContactUs.receiver_require}checked="checked"{/if}>
							{phrase var='required_this_field'}
						</label>
					</div>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='title'}
				</div>
				<div class="table_right">
					<div class="checkbox">
						<label for="">
							
							<input type="checkbox" name=val[title_enable] value="{if isset($aContactUs.title_enable)}1{/if}" {if isset($aContactUs.title_enable) && $aContactUs.title_enable}checked="checked"{/if}>
							{phrase var='enabled_this_field'}
						</label>
					</div>
					<div class="checkbox">
						<label for="">
							<input type="checkbox" name=val[title_require] value="{if isset($aContactUs.title_require)}1{/if}" {if isset($aContactUs.title_require) && $aContactUs.title_require}checked="checked"{/if}>
							{phrase var='required_this_field'}
						</label>
					</div>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var='content'}
				</div>
				<div class="table_right">
					<div class="checkbox">
						<label for="">
							
							<input type="checkbox" name=val[content_enable] value="{if isset($aContactUs.content_enable)}1{/if}" {if isset($aContactUs.content_enable) && $aContactUs.content_enable}checked="checked"{/if}>
							{phrase var='enabled_this_field'}
						</label>
					</div>
					<div class="checkbox">
						<label for="">
							<input type="checkbox" name=val[content_require] value="{if isset($aContactUs.content_require)}1{/if}" {if isset($aContactUs.content_require) && $aContactUs.content_require}checked="checked"{/if}>
							{phrase var='required_this_field'}
						</label>
					</div>
				</div>
			</div>

			{if $aBusiness.setting_support.allow_business_owner_to_add_more_custom_fields_to_his_business}
			{/if}
			<h4 class="yndirectory-doashboard-title">{phrase var='custom_fields'}</h4>
			<div class="yndirectory-doashboard-custom_fieldscustom-fields">
				{foreach from=$aContactUsCustomfield key=iKey item=iField}
					<div class="table from-group">
						<div class="table_left">
							{phrase var=$iField.phrase_var_name}
							<input type="hidden" name="val[customfield_contactus][]" value="{$iField.field_id}"> 					
						</div>
						<div class="table_right">
							<div class="checkbox">
								<!--<input type="checkbox" name=val[cf_active][{$iField.field_id}] value="{if isset($iField.is_active)}{$iField.is_active}{/if}" {if isset($iField.is_active) && $iField.is_active}checked="checked"{/if}>-->
								<a href="javascript:void(0);" onclick="editCustomFieldContactUs({$iField.field_id});">{phrase var='edit'}</a>
								/
								<a href="javascript:void(0);" onclick="deleteCustomFieldContactUs({$iField.field_id});">{phrase var='delete'}</a>
							</div>
						</div>
					</div>
				{/foreach}
			</div>

			{if $aBusiness.setting_support.allow_business_owner_to_add_more_custom_fields_to_his_business}
				<div style="margin-bottom: 20px;">
					<a href="javascript:void(0);" onclick="tb_show('{phrase var='add_custom_field'}', $.ajaxBox('directory.addCustomFieldBlockContactUs', 'height=300&width=300&action=add&contact_us_id='+{if isset($aContactUs.contactus_id) }{$aContactUs.contactus_id}{/if})); return false;">{phrase var='add_custom_field'}</a>
				</div>
			{/if}

			<h4 class="yndirectory-doashboard-title">{phrase var='manage_receiver'}</h4>
			<div id="yndirectory_receivers">
				{if count($aContactUs.receiver_data)}
					{foreach from=$aContactUs.receiver_data key=iKeyReceiver item=aDataReceiver }
		            	<div class="yndirectory-receivers">
			            	<div class="table form-group">
								<div class="table_left">
				                    <label>{phrase var='department'}: </label>
				                </div>
				                <div class="table_right">
				                    <input class="form-control" type="text" name="val[department_receiver][]" size="15" value="{$aDataReceiver.department}" />
				                    <a id="yndirectory_delete" href="#" onclick="yndirectory.removePredefined(this,'receivers'); return false;">
				                    	<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
				                    </a>
				                </div>
							</div>
							<div class="table form-group">
								<div class="table_left">
				                    <label>{phrase var='email'}: </label>
				                </div>
				                <div class="table_right">
				                    <input class="form-control" type="text" name="val[email_receiver][]" size="15" value="{$aDataReceiver.email}" />
				                </div>	
							</div>	            		
		            	</div>
					{/foreach}
				{/if}

	        	<div class="yndirectory-receivers receivers-first" style="display:none;">
		        	<div class="table form-group">
						<div class="table_left">
		                    <label>{phrase var='department'}: </label>
		                </div>
		                <div class="table_right">
		                    <input class="form-control" type="text" name="val[department_receiver][]" size="15" />
		                    <a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'receivers'); return false;">
		                    	{img theme='misc/add.png' class='v_middle'}
		                    </a>
		                    <a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'receivers'); return false;">
		                    	<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
		                    </a>
		                </div>
					</div>

					<div class="table form-group">
						<div class="table_left">
		                    <label>{phrase var='email'}: </label>
		                </div>
		                <div class="table_right">
		                    <input class="form-control" type="text" name="val[email_receiver][]" size="15" />
		                </div>	
					</div>	            		
	        	</div>

			</div>
			
			<div style="margin-bottom: 20px;">
				<a href="javascript:void(0);" onclick="yndirectory.appendPredefined($('.receivers-first #yndirectory_add'),'receivers'); return false;" >{phrase var='add_more_receivers'}</a>
			</div>

			<div class="yndirectory-button">
				<button class="btn btn-sm btn-primary" type="submit" name="val[edit_contact_us]" id="edit_contact_us" value="{phrase var='save_changes'}">{phrase var='save_changes'}</button>
			</div>
		</form>

	</div>

	</div>
	{elseif $sView == 'editfaq'}
	<div id='edit_faq' >
		<form method="post" action="{url link='directory.manage-pages.id_'.$iEditedBusinessId.'.view_editfaq'}" id="js_edit_faq"  enctype="multipart/form-data">
			<input type="hidden" name="val[business_id]" value="{$iEditedBusinessId}" >
			<input type="hidden" name="val[view]" value="{$sView}" >
			<table class="yndirectory-table-full">
				<tr>
					<th>{phrase var='faq'}</th>
					<th>{phrase var='options'}</th>
				</tr>
				{foreach from=$aFAQs key=iKey item=iFAQ}
					<tr>
						<input type="hidden" name="val[faq][]" value="{$iFAQ.faq_id}"> 					
						<td>{$iFAQ.question_parsed|parse}</td>
						<td>
						<a href="#" onclick="editFAQ({$iFAQ.faq_id});">{phrase var='edit'}</a>
						/
						<a href="#" onclick="if(confirm('{phrase var='are_you_sure'}'))deleteFAQ({$iFAQ.faq_id});">{phrase var='delete'}</a>
						</td>
					</tr>
				{/foreach}
			</table>
			
			<div style="margin-bottom: 20px;">
				<a href="#" onclick="tb_show('Add FAQ', $.ajaxBox('directory.AddFaqBusinessBlock', 'height=300&width=300&action=add&business_id='+{$iEditedBusinessId})); return false;">{phrase var='add_faq'}</a>
			</div>

		</form>

	</div>
	{/if}
</div>

{literal}
<script type="text/javascript">
;
function deleteFAQ(iFAQId){
	$.ajaxCall('directory.deleteFAQ', 'height=300&amp;width=300&action=edit&faq_id='+iFAQId+'&business_id='+{/literal}{$iEditedBusinessId}{literal});
}
function editFAQ(iFAQId){
	tb_show('{/literal}{phrase var='edit_faq'}{literal}', $.ajaxBox('directory.AddFaqBusinessBlock', 'height=300&amp;width=300&action=edit&faq_id='+iFAQId+'&business_id='+{/literal}{$iEditedBusinessId}{literal}));
}

function deleteCustomFieldContactUs(iCustomFieldId){
	$.ajaxCall('directory.deleteCustomFieldContactUs', 'height=300&amp;width=300&action=edit&customfield_id='+iCustomFieldId);
}

function editCustomFieldContactUs(iCustomFieldId){
	tb_show('Edit Custom Field', $.ajaxBox('directory.addCustomFieldBlockContactUs', 'height=300&amp;width=300&action=edit&id='+iCustomFieldId));
}
;
</script>
{/literal}