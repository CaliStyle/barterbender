<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div style="position: relative">
<h3  class="yns add-res">
	<ul class="yns menu-add">
		<li>{_p var='languages'}</li>
		<li><a class="page_section_menu_link" href="{url link='resume.language'}id_{$id}/">{_p var='add_a_language'}</a></li>
	</ul>
</h3>
</div>

<form method="post" id="yresume_add_form" class="resume_add_content" name="js_resume_add_form" enctype="multipart/form-data">

<div id="headline">

	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="name">{required}{_p var='name'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[name]" value="{value type='input' id='name'}" id="name" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="level">{_p var='level'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[level]" value="{value type='input' id='level'}" id="level" size="40" maxlength="100" />
			</div>
	</div>


	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="note">{_p var='note'}:</label>
		</div>
		<div class="table_right">

			<textarea class="form-control" cols="70" rows="5" name='val[note]'>{if !empty($aForms)}{$aForms.note}{/if}</textarea>
		</div>
	</div>




		<div class="table_clear resume-btn-group">
			<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>
			<button type="button" class="button btn btn-default btn-sm" onclick="window.location.href='{url link='resume.publication'}id_{$id}'">{_p var='skip'}</button>
		</div>

</div>

</form>

{if count($aRows)>0}
<div class="yns_resume_listcontent">
	<h3>{_p var='list_of_languages'}</h3>
	{foreach from=$aRows item=aRow}
	<div class="section_row">
		<div class="resume_section_info_content info_section_title" id='language_{$aRow.language_id}'>
			<span>{if $aRow.name!=""}{$aRow.name}{else}{phrase var="resume.no_name"}{/if}</span>
			{if $aRow.level}({$aRow.level}){/if}
			<div class="info_section_link">
				<a href="{url link='resume.language'}id_{$id}/exp_{$aRow.language_id}/">{phrase var="resume.edit"}</a>
				|
				<a href="#" onclick="if(confirm( '{_p var='are_you_sure'}' ))$.ajaxCall('resume.delete_language','exp_id={$aRow.language_id}');return false;">{_p var='delete'}</a>
			</div>
		</div>
	</div>
	{/foreach}
</div>
{/if}
