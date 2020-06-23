<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
 
<div class="table_header">
	{phrase var='petition.helps'}
</div>
{if count($aHelps)}
<table id="js_drag_drop" cellpadding="0" cellspacing="0">
<tr>
	<th style="width:20px;"></th>
	<th style="width:20px;"></th>
	<th style="width: 80px">{phrase var='petition.icon'}</th>
	<th style="width: 200px">{phrase var='petition.title'}</th>
	<th>{phrase var='petition.content'}</th>
</tr>
{foreach from=$aHelps key=iKey item=aHelp}        
<tr id="js_row{$aHelp.help_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
	<td class="drag_handle"><input type="hidden" name="val[ordering][{$aHelp.help_id}]" value="{$aHelp.ordering}" /></td>
	<td class="t_center" style="vertical-align: middle;">
		<a href="#" class="js_drop_down_link" title="{phrase var='petition.manage'}">{img theme='misc/bullet_arrow_down.png' alt=''}</a>
		<div class="link_menu">
			<ul>				
				<li><a href="{url link='admincp.petition.help' id=$aHelp.help_id}">{phrase var='petition.edit'}</a></li>						
				<li><a href="{url link='admincp.petition.help' delete=$aHelp.help_id}" onclick="return confirm('{phrase var='admincp.are_you_sure' phpfox_squote=true}');">{phrase var='petition.delete'}</a></li>					
			</ul>
		</div>		
	</td>	
	<td>{img server_id=$aHelp.server_id path='core.url_pic' file=$aHelp.image_path suffix='_80' max_width='80' max_height='80' class='js_mp_fix_width'}</td>
	<td><a href="{permalink module='petition.help' id=$aHelp.help_id title=$aHelp.title}" class="link">{$aHelp.title|convert|clean|shorten:55'...'}</a></td>
	<td>{$aHelp.content_parsed|shorten:500'...'}</td>		
</tr>
{/foreach}
</table>	
{else}
<div class="p_4">
	{phrase var='petition.no_helps_have_been_added_yet'}
</div>
{/if}
{pager}
<br/>
{$sCreateJs}
<form method="post" action="{url link="admincp.petition.help"}" id="js_form" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
	<input type="hidden" name="val[help_id]" value="{value type='input' id='help_id'}" id="help_id" size="50" />	
	<div class="table_header">
		{phrase var='petition.add_help'}
	</div>
	<div class="table">
		<div class="table_left">
			{required}{phrase var='petition.title'}:
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="50" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table">
		<div class="table_left">
			{phrase var='petition.icon'}:
		</div>
		<div class="table_right">
			<input type="file" name="icon">
			<div class="extra_info">
				{phrase var='petition.you_can_upload_a_jpg_gif_or_png_file'}
				{if $iMaxFileSize !== null}
				<br />
				{phrase var='petition.the_file_size_limit_is_filesize_if_your_upload_does_not_work_try_uploading_a_smaller_picture' filesize=$iMaxFileSize}
				{/if}				
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table">
		<div class="table_left">
			{required}{phrase var='petition.content'}:
		</div>
		<div class="table_right">
			{editor id='content' rows='15'}
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">         
	   <input type="submit" value="{phrase var='petition.submit'}" class="button" />
         <input type="reset" value="{phrase var='petition.clear'}" class="button" />
	</div>
</form>