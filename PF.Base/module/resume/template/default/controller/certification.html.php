<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div style="position: relative">
	<h3  class="yns add-res">
		<ul class="yns menu-add">
			<li>{_p var='certifications'}</li>
			<li><a class="page_section_menu_link" href="{url link='resume.certification'}id_{$id}/">{phrase var="resume.add_a_certification"}</a></li>
		</ul>
	</h3>
</div>

<form method="post" id="yresume_add_form" enctype="multipart/form-data" name="js_resume_add_form">

<div id="headline">

	<div class="table form-group">
			<div class="table_left table_left_add">
			<label for="certification_name">{required}{_p var='certification_s_name'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[certification_name]" value="{value type='input' id='certification_name'}" id="certification_name" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group">
			<div class="table_left table_left_add">
			<label for="course_name">{_p var='course_s_name'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[course_name]" value="{value type='input' id='course_name'}" id="course_name" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group">
			<div class="table_left table_left_add">
			<label for="training_place">{_p var='training_in_place'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[training_place]" value="{value type='input' id='training_place'}" id="training_place" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="postal_code">{_p var='dates_attended'}:</label>
		</div>
		<div class="table_right">
			<div class="yns_timeperiod">
				<select name="val[start_month]" class="yns_exptime_month form-control">
				<option value="">{_p var='uppercase_month'} ...</option>
				{foreach from=$aMonth item=month}
					<option {if !empty($aForms) && $aForms.start_month==$month}selected{/if}>{$month}</option>
				{/foreach}
				</select>
				<select name='val[start_year]' id="start_year" class="yns_exptime_year form-control">
					<option value="">{_p var='year'} ...</option>
					{foreach from=$aYear item=year}
						<option {if !empty($aForms) && $aForms.start_year==$year}selected{/if}>{$year}</option>
					{/foreach}
				</select>
				<span class='end_experience'>
					<span class="text_label">{_p var='to'}</span>
					<select name="val[end_month]" class="yns_exptime_month form-control">
					<option value="">{_p var='uppercase_month'}</option>
					{foreach from=$aMonth item=month}
						<option {if !empty($aForms) && $aForms.end_month==$month}selected{/if}>{$month}</option>
					{/foreach}
					</select>
					<select name='val[end_year]' id="end_year" class="yns_exptime_year form-control">
					<option value="">{_p var='year'}</option>
						{foreach from=$aYear item=year}
							<option {if !empty($aForms) && $aForms.end_year==$year}selected{/if}>{$year}</option>
						{/foreach}
					</select>
				</span>
			</div>
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
			<button type="button" class="button btn btn-default btn-sm" onclick="window.location.href='{url link='resume.language'}id_{$id}'">{_p var='skip'}</button>
		</div>

</div>

</form>

{if count($aRows)>0}
<div class="yns_resume_listcontent">
	<h3>{phrase var="resume.list_of_certifications"}</h3>
	{foreach from=$aRows item=aRow}
	<div class="section_row">
	<div class="resume_section_info_content" id='certification_{$aRow.certification_id}'>
	<div class="info_section_title">
		<span>{if $aRow.certification_name!=""}{$aRow.certification_name}{else}No name{/if}</span>
		<div class="info_section_link">
			<a href="{url link='resume.certification'}id_{$id}/exp_{$aRow.certification_id}/">{phrase var="resume.edit"}</a>
			|
			<a href="#" onclick="if(confirm( '{_p var='are_you_sure'}' ))$.ajaxCall('resume.delete_certification','exp_id={$aRow.certification_id}');return false;">{_p var='delete'}</a>
		</div>
	</div>
	<!-- Course Name -->
	<p>
		{if $aRow.course_name}
			{$aRow.course_name}
		{/if}
	</p>
	<!-- Time Period and Training Place -->
	<p>
	{if $aRow.start_month and $aRow.start_year and $aRow.end_month and $aRow.end_year}
		<?php echo date('F, Y',mktime(0,0,0,$this->_aVars["aRow"]["start_month"],1,$this->_aVars["aRow"]["start_year"]));?>
		-
		<?php echo date('F, Y',mktime(0,0,0,$this->_aVars["aRow"]["end_month"],1,$this->_aVars["aRow"]["end_year"])); ?>
	{/if}

	{if $aRow.training_place}
		{phrase var="resume.at"} {$aRow.training_place}
	{/if}
	</p>
</div>
	</div>
	{/foreach}
</div>
{/if}
