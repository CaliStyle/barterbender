<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_petition_entry{$aItem.petition_id}"{if !isset($bPetitionView)} class="js_petition_parent {if is_int($phpfox.iteration.petition/2)}row1{else}row2{/if}{if $phpfox.iteration.petition == 1 && !PHPFOX_IS_AJAX} row_first{/if}{if $aItem.is_approved != 1} {/if}"{/if}>
{if $phpfox.iteration.petition == 1 && !PHPFOX_IS_AJAX && $iPage == 0}
<div class="yns_petition_carousel_title">{phrase var='petition.recent_petitions'}</div>
{/if}
{if (Phpfox::getUserParam('petition.edit_own_petition') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('petition.edit_user_petition')
|| (Phpfox::getUserParam('petition.delete_own_petition') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('petition.delete_user_petition')
|| (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
}
<div class="_moderator">
	
	{if Phpfox::getUserParam('petition.can_approve_petitions') }
	<a rel="petition"
	   class="moderate_link built" href="#{$aItem.petition_id}">
		<i class="fa"></i>
	</a>
	{/if}

	<div class="row_edit_bar_parent">		
	    <div class="row_edit_bar">
			<a role="button" class="row_edit_bar_action" data-toggle="dropdown" href="#">
			    <i class="fa fa-action"></i>			    
			</a>
			<ul class="dropdown-menu">
				{template file='petition.block.link'}
			</ul>
		</div>
	</div>

</div>
{/if}


	{if !isset($bPetitionView)}
	{if !phpfox::isMobile()}
	<div class="row_title_image_header" style="">
          {if $aItem.is_approved != 1}
	     <div class="row_pending_link">
		    {phrase var='petition.pending'}
	     </div>
		{elseif $aItem.petition_status == 1}
	     <div class="row_featured_link">
		    {phrase var='petition.closed'}
	     </div>
          {elseif $aItem.petition_status == 3}
	     <div class="row_sponsored_link">
		    {phrase var='petition.victory'}
	     </div>
          {else}
		<div class="js_featured_petition row_featured_link"{if !$aItem.is_featured} style="display:none;"{/if}>
			{phrase var='petition.featured'}
		</div>
          {/if}

		<a href="{permalink module='petition' id=$aItem.petition_id title=$aItem.title}" title="{$aItem.title|clean}">		
			{if $aItem.image_path}
				{img server_id=$aItem.server_id path='core.url_pic' 
				file=$aItem.image_path suffix='_500' 				
				max_width='120' max_height='120' class='js_mp_fix_width'}
			{else}
				<img
					
					src="{$corepath}module/petition/static/image/no_photo.png" />
			{/if}

		</a>
	</div>

	<div class="row_title_image_header_body">
	{/if}
		<div class="row_title">
			<div class="row_title_image" style="margin-left: 5px;">
                        {img user=$aItem suffix='_50_square' max_width=50 max_height=50}
				{if (Phpfox::getUserParam('petition.edit_own_petition') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('petition.edit_user_petition')
					|| (Phpfox::getUserParam('petition.delete_own_petition') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('petition.delete_user_petition')
                              || (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getService('pages')->isAdmin('' . $aPage.page_id . ''))
				}
				<div class="row_edit_bar_parent">
					<div class="row_edit_bar">
						<a role="button" href="#" class="row_edit_bar_action" data-toggle="dropdown"><i class="fa fa-action"></i></a>
						<ul class="dropdown-menu">
							{template file='petition.block.link'}
						</ul>
					</div>

				</div>
				{/if}
				{if Phpfox::getUserParam('petition.can_approve_petitions') || Phpfox::getUserParam('petition.delete_user_petition') }<a href="#{$aItem.petition_id}" class="moderate_link" rel="petition"><i class="fa"></i></a>{/if}
			</div>

			<div class="row_title_info">
				<span id="js_petition_edit_title{$aItem.petition_id}">
					<a href="{permalink module='petition' id=$aItem.petition_id title=$aItem.title}" id="js_petition_edit_inner_title{$aItem.petition_id}" class="link ajax_link">{$aItem.title|clean|shorten:55:'...'|split:55}</a>
				</span>

				<div class="extra_info">
                    {phrase var='petition.created_by'} {$aItem|user}{if !defined('PHPFOX_IS_PAGES_VIEW') && $aItem.category_name} {phrase var='petition.in'} <a href="{permalink module='petition.category' id=$aItem.category_id title=$aItem.category_name}">{$aItem.category_name}</a>{/if}
                    <br/>
					{if $aItem.is_directsign == 1}<span class="total_sign"><i class="fa fa-pencil"></i> {$aItem.total_sign}</span>
					{else}
					<i class="fa fa-pencil"></i> {$aItem.total_sign}
					{/if}
					&nbsp;
					<i class="fa fa-thumbs-up"></i> {$aItem.total_like}
					&nbsp; 
					<i class="fa fa-eye"></i> {$aItem.total_view}
                    {plugin call='petition.template_block_entry_date_end'}
				</div>
				{if phpfox::isMobile()}
					<a href="{permalink module='petition' id=$aItem.petition_id title=$aItem.title}" title="{$aItem.title|clean}">
						{img server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path suffix='_120' max_width='120' max_height='120' class='js_mp_fix_width'}
					</a>
				{/if}

		{/if}
			<div class="petition_content">
				<div id="js_petition_edit_text{$aItem.petition_id}">
					<div class="item_content item_view_content">
					{if isset($bPetitionView)}
						{$aItem.description|parse|highlight:'search'|split:55}
						{else}
						<div>
							{$aItem.description|strip_tags|highlight:'search'|split:55|shorten:$iShorten:'...'}
						</div>
					{/if}
					</div>


				</div>

				{if Phpfox::isModule('tag') && !defined('PHPFOX_IS_PAGES_VIEW') && isset($aItem.tag_list)}
				{module name='tag.item' sType=$sTagType sTags=$aItem.tag_list iItemId=$aItem.petition_id iUserId=$aItem.user_id}
				{/if}

				{if !isset($bPetitionView)}
				 
				{/if}

				{plugin call='petition.template_block_entry_text_end'}
			</div>

		{plugin call='petition.template_block_entry_end'}
		{if !isset($bPetitionView)}
			</div>
		</div>
	{if !phpfox::isMobile()}
	</div>
	{/if}
	{/if}
</div>