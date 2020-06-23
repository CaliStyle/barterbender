<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div style="position: relative">
<h3  class="yns add-res">
<ul class="yns menu-add">
	<li>{_p var='education'}</li>
	<li><a class="page_section_menu_link" href="{url link='resume.education'}id_{$id}/">{_p var='add_a_school'}</a></li>
</ul>
</h3>
</div>

<form class="resume_add_content" id="yresume_add_form" method="post" name="js_resume_add_form" enctype="multipart/form-data">

<div id="headline">

	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="school_name">{required}{_p var='shool_name'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[school_name]" value="{value type='input' id='school_name'}" id="school_name" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="degree">{required}{_p var='degree'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[degree]" value="{value type='input' id='degree'}" id="degree" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="field">{required}{_p var='field_of_study'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[field]" value="{value type='input' id='field'}" id="field" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group-follow">
		<div class="table_left table_left_add">
			<label for="postal_code">{required}{_p var='dates_attended'}:</label>
		</div>
		<div class="table_right">
			<select class="form-control" name='val[start_year]' id="start_year">
				<option value="">{_p var='year'} ...</option>
				{foreach from=$aYear item=year}
					<option {if !empty($aForms) && $aForms.start_year==$year}selected{/if}>{$year}</option>
				{/foreach}
			</select>
			<span class="text_label">{_p var='to'}</span>
			<select class="form-control" name='val[end_year]' id="end_year">
			<option value="">{_p var='year'} ...</option>
				{foreach from=$aYear item=year}
					<option {if !empty($aForms) && $aForms.end_year==$year}selected{/if}>{$year}</option>
				{/foreach}
			</select>
			<div class="text_tip">
				{_p var='tip_current_students_enter_your_expected_graduation_year'}
			</div>
		</div>
	</div>

	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="shoolname">{_p var='grade'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[grade]" value="{value type='input' id='grade'}" id="grade" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="activity">{_p var='activities_and_societies'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name="val[activity]">{if !empty($aForms)}{$aForms.activity}{/if}</textarea>
		</div>
		<div>
			{_p var='tip_use_commas_to_separate_multiple_activities'}
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="note">{_p var='additional_notes'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[note]'>{if !empty($aForms)}{$aForms.note}{/if}</textarea>
		</div>
	</div>



		<div class="table_clear resume-btn-group">
			<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>
			<button type="button" class="button btn btn-default btn-sm" value ="" onclick="window.location.href='{url link='resume.skill'}id_{$id}'">{_p var='skip'}</button>
		</div>

</div>

</form>

{if count($aRows)>0}
<div class="yns_resume_listcontent">
	<h3>{phrase var="resume.list_of_schools"}</h3>
	{foreach from=$aRows item=aRow}
	<div class="section_row" id='education_{$aRow.education_id}'>
	<div class="resume_section_info_content info_section_title">
		<span>{if $aRow.school_name!=""}{$aRow.school_name}{else}No name{/if}</span>
	<div class="info_section_link">
		<a href="{url link='resume.education'}id_{$id}/exp_{$aRow.education_id}/">{phrase var="resume.edit"}</a>
		|
		<a href="#" onclick="if(confirm( '{_p var='are_you_sure'}' ))$.ajaxCall('resume.delete_education','exp_id={$aRow.education_id}');return false;">{_p var='delete'}</a></div>
	<!-- Degree, Field -->
	<p>{$aRow.degree}, {$aRow.field}</p>
	<!-- Time Period -->
	<p>{$aRow.start_year} - {$aRow.end_year}</p>
	</div>
	</div>
	{/foreach}
</div>
{/if}
