<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style type="text/css">
	input[name='quick_edit_input']{
		width: 90%;
		margin-bottom: 2px;
	}
</style>
{/literal}
<form method="get" action="{url link="admincp.petition"}">
<div class="table_header">
	{phrase var='petition.search_filter'}
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.search_for_text'}: 
	</div>
	<div class="table_right">
		{$aFilters.search}
	</div>
	<div class="clear"></div>
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.search_for_user'}: 
	</div>
	<div class="table_right">
		{$aFilters.user}
	</div>
	<div class="clear"></div>
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.petition_status'}: 
	</div>
	<div class="table_right">
		{$aFilters.status} 
	</div>
	<div class="clear"></div>
</div>

<div class="table">
	<div class="table_left">
		{phrase var='petition.featured'}: 
	</div>
	<div class="table_right">
		{$aFilters.featured} 
	</div>
	<div class="clear"></div>
</div>

<div class="table">
	<div class="table_left">
		{phrase var='petition.approved'}: 
	</div>
	<div class="table_right">
		{$aFilters.approved} 
	</div>
	<div class="clear"></div>
</div>

<div class="table">
	<div class="table_left">
		{phrase var='petition.page'}: 
	</div>
	<div class="table_right">
		{$aFilters.pages} 
	</div>
	<div class="clear"></div>
</div>

<div class="table">
	<div class="table_left">
		{phrase var='petition.display'}: 
	</div>
	<div class="table_right">
		{$aFilters.display}
	</div>
	<div class="clear"></div>
</div>
<div class="table">
	<div class="table_left">
		{phrase var='petition.sort'}: 
	</div>
	<div class="table_right">
		{$aFilters.sort} {$aFilters.sort_by}
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<input type="submit" name="search[submit]" value="{phrase var='petition.submit'}" class="button" />
	<input type="submit" name="search[reset]" value="{phrase var='petition.reset'}" class="button" />	
</div>
</form>
{pager}



{if count($aPetitions)}
<form method="post" action="{url link='admincp.petition'}">
	<table colspan='1'>
	<tr>
		<th style="width:20px;"></th>
		<th>{phrase var='petition.title'}</th>
		<th>{phrase var='petition.status'}</th>
		<th>{phrase var='petition.featured'}</th>
		<th>{phrase var='petition.direct_sign'}</th>
		<th>{phrase var='petition.created_user'}</th>
		<th>{phrase var='petition.end_date'}</th>
		<th>{phrase var='petition.page'}</th>
		<th>{phrase var='petition.signatures'}</th>
		<th>{phrase var='petition.views'}</th>
		<th>{phrase var='petition.likes'}</th>
	</tr>
	{foreach from=$aPetitions key=iKey item=aPetition}        
	<tr id="js_row{$aPetition.petition_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<td class="t_center">
			<a href="#" class="js_drop_down_link" title="{phrase var='petition.manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
			<div class="link_menu">
				<ul>				
					<li><a href="{permalink module='petition' id=$aPetition.petition_id title=$aPetition.title}">{phrase var='petition.view'}</a></li>		
					<li><a href="{url link='petition.add' id=$aPetition.petition_id}">{phrase var='petition.edit'}</a></li>		
					{if $aPetition.is_approved == '0'}					
					<li><a href="{url link='admincp.petition' approve=$aPetition.petition_id}">{phrase var='petition.approve'}</a></li>										
					{/if}
					<li><a href="{url link='admincp.petition' delete=$aPetition.petition_id}" onclick="return confirm('{phrase var='admincp.are_you_sure' phpfox_squote=true}');">{phrase var='petition.delete'}</a></li>					
				</ul>
			</div>		
		</td>		
		<td id="js_petition_edit_title{$aPetition.petition_id}"><a href="#?type=input&amp;id=js_petition_edit_title{$aPetition.petition_id}&amp;content=js_petition{$aPetition.petition_id}&amp;call=petition.updatePetition&amp;petition_id={$aPetition.petition_id}&amp;user_id={$aPetition.user_id}" class="quickEdit" id="js_petition{$aPetition.petition_id}">{$aPetition.title|convert|clean}</a></td>
		<td>{$aPetition.petition_status_text}</td>
		{if $aPetition.is_approved == 1 }
		<td>		
		    <div class="js_item_is_active"{if !$aPetition.is_featured} style="display:none;"{/if}>
				<a href="#?call=petition.feature&amp;petition_id={$aPetition.petition_id}&amp;active=0&amp;admin=true" class="js_item_active_link" title="{phrase var='petition.un_feature'}">{img theme='misc/bullet_green.png' alt=''}</a>
		    </div>
              <div class="js_item_is_not_active"{if $aPetition.is_featured} style="display:none;"{/if}>
				<a href="#?call=petition.feature&amp;petition_id={$aPetition.petition_id}&amp;active=1&amp;admin=true" class="js_item_active_link" title="{phrase var='petition.feature'}">{img theme='misc/bullet_red.png' alt=''}</a>
              </div>                
		</td>
		<td>
			{if $aPetition.petition_status == 2 && $aPetition.module_id == 'petition'}
                <div class="js_item_is_active js_item_directsign_active"{if !$aPetition.is_directsign} style="display:none;"{/if}>
				<a href="#?call=petition.directsign&amp;petition_id={$aPetition.petition_id}&amp;active=0" class="js_item_active_link js_item_directsign_link" title="{phrase var='petition.unset_direct_sign'}">{img theme='misc/bullet_green.png' alt=''}</a>
		    </div>
                <div class="js_item_is_not_active js_item_directsign_not_active"{if $aPetition.is_directsign} style="display:none;"{/if}>
				<a href="#?call=petition.directsign&amp;petition_id={$aPetition.petition_id}&amp;active=1" class="js_item_active_link js_item_directsign_link" title="{phrase var='petition.set_direct_sign'}">{img theme='misc/bullet_red.png' alt=''}</a>
                </div>
			 {else}
			 {img theme='misc/bullet_red.png' alt=''}
			 {/if}
		</td>
		{else}
		<td>		              
			{img theme='misc/bullet_red.png' alt=''}
		</td>
		<td>
               {img theme='misc/bullet_red.png' alt=''}
		</td>
		{/if}
		<td>{$aPetition|user}</td>
		<td>{$aPetition.end_time|date:'petition.petition_time_stamp'}</td>
		
		<td>
			{if $aPetition.module_id == 'pages'}
				<a href="{$aPetition.page_link}">{$aPetition.page_name}</a>			
			{else}
				{phrase var='petition.none'}
			{/if}
		</td>
		
		<td>{$aPetition.total_sign}</td>
		<td>{$aPetition.total_view}</td>
		<td>{$aPetition.total_like}</td>	
	</tr>
	{/foreach}
	</table>	
	{else}
	<div class="p_4">
		{phrase var='petition.no_petitions_found'}
	</div>
	{/if}
</form>

{pager}