 <?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div style="position: relative">
	<h3  class="yns add-res">
		<ul class="yns menu-add">
			<li>{_p var='experience'}</li>
			<li><a class="page_section_menu_link" href="{url link='resume.experience'}id_{$id}/">{_p var='add_a_position'}</a></li>
		</ul>

	</h3>
</div>

<form class="resume_add_content" id="yresume_add_form" method="post" name="js_resume_add_form" enctype="multipart/form-data">

<div id="headline">
	<div class="table form-group" style="padding-top: 10px;">
			<div class="table_left table_left_add">
			<label for="company_name">{required}{_p var='company_name'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[company_name]" value="{value type='input' id='company_name'}" id="company_name" size="40" maxlength="100" />
			</div>
	</div>

	<div class="table form-group-follow form-group">
		<div class="table_left table_left_add">
			<label for="level">{required}{_p var='level'}:</label>
		</div>
		<div class="table_right">
			<select class="form-control" name="val[level_id]">
					<option value="">{phrase var="resume.select"}</option>
				{foreach from=$aLevel item=level}
					<option value="{$level.level_id}" {if ($iExp!=0 || !$is_calloff) && isset($aForms.level_id) && $level.level_id==$aForms.level_id}selected{/if}>{$level.name}</option>
				{/foreach}
			</select>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="title">{required}{_p var='title'}:</label>
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" maxlength="200" />
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="location">{_p var='location'}:</label>
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[location]" value="{value type='input' id='location'}" id="location" size="40" maxlength="200" />
		</div>
	</div>


	<!-- Time working period -->
	<div class="table form-group-follow form-group">
		<div class="table_left table_left_add">
			<label for="postal_code">{required}{_p var='time_period'}:</label>
		</div>
		<div class="table_right">
			<!-- Working here -->
            <label><input type="checkbox" class="checkbox" {if ($iExp!=0 || !$is_calloff) && (isset($aForms.is_working_here) && $aForms.is_working_here==1)}checked=true{/if} id='check_experience' name='val[is_working_here]'/> {_p var='i_currently_work_here'}</label>
			<!-- Working Period-->
			<div class="yns_timeperiod">
				<!-- Start Month -->
				<select name='val[start_month]' class="yns_exptime_month form-control">
				<option value="-1">{_p var='uppercase_month'} ...</option>
				{foreach from=$aMonth item=month}
					<option {if ($iExp!=0 || !$is_calloff) && (isset($aForms.start_month) && $aForms.start_month==$month)}selected{/if}>{$month}</option>
				{/foreach}
				</select>
				<!-- Start Year -->
				<select name='val[start_year]' id="start_year" class="yns_exptime_year form-control">
					<option value="-1">{_p var='year'} ...</option>
						{foreach from=$aYear item=year}
					<option {if ($iExp!=0 || !$is_calloff) && (isset($aForms.start_year) && $aForms.start_year==$year)}selected{/if}>{$year}</option>
				{/foreach}
				</select>
				<!-- End Period -->
				<span class='end_experience' {if ($iExp!=0 || !$is_calloff) && (isset($aForms.is_working_here) && $aForms.is_working_here==1)}style="display:none"{/if}>
					<span class="text_label">{_p var='to'}</span>
					<!-- End Month -->
					<select name='val[end_month]' class="yns_exptime_month form-control">
						<option value="0">{_p var='uppercase_month'} ...</option>
						{foreach from=$aMonth item=month}
							<option {if ($iExp!=0 || !$is_calloff) && (isset($aForms.end_month) && $aForms.end_month==$month)}selected{/if}>{$month}</option>
						{/foreach}
					</select>
					<!-- End Year -->
					<select name='val[end_year]' id="end_year" class="yns_exptime_year form-control">
						<option value="0">{_p var='year'} ...</option>
							{foreach from=$aYear item=year}
						<option {if ($iExp!=0 || !$is_calloff) && (isset($aForms.end_year) && $aForms.end_year==$year)}selected{/if}>{$year}</option>
						{/foreach}
					</select>
				</span>
			</div>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="description">{_p var='description'}:</label>
		</div>
		<div class="table_right">
			{editor id='description' rows='4'}
		</div>
	</div>



		<div class="table_clear resume-btn-group">
			<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>
			<button type="button" class="button btn btn-default btn-sm" onclick="window.location.href='{url link='resume.education'}id_{$id}'">{_p var='skip'}</button>
		</div>

</div>

</form>

<!-- Experience Listig Space -->
{if count($aRows)>0}
<div class="yns_resume_listcontent">
	<h3>{_p var='list_of_positions'}</h3>
	{foreach from=$aRows item=aRow}
	<div class="section_row" id='experience_{$aRow.experience_id}'>
		<div class="resume_section_info_content info_section_title">
			<!-- Company Name -->
			<span>{if $aRow.company_name!=""}{$aRow.company_name}{else}No name{/if} {if $aRow.title!=""}- {$aRow.title}{/if}</span>
			<!-- Options -->
			<div class="info_section_link">
				<a href="{url link='resume.experience'}id_{$id}/exp_{$aRow.experience_id}/">{_p var='edit'}</a>
				|
				<a href="#" onclick="if(confirm( '{_p var='are_you_sure'}'))$.ajaxCall('resume.delete_experience','exp_id={$aRow.experience_id}');return false;">{_p var='delete'}</a>
			</div>
		</div>
        {if !empty($aRow.level_name)}
            <span>
                {$aRow.level_name}
            </span>
        {/if}
		<!-- Time period -->
		<p>
		   <!-- Start Time -->
		   <?php echo date('F, Y',mktime(0,0,0,$this->_aVars["aRow"]["start_month"],1,$this->_aVars["aRow"]["start_year"])); ?>
		    -
		   <!-- End Time -->
		   {if $aRow.is_working_here || !$aRow.end_month || !$aRow.end_year}
		   		{phrase var="resume.present"}
		   {else}
	   	 		<?php echo date('F, Y',mktime(0,0,0,$this->_aVars["aRow"]["end_month"],1,$this->_aVars["aRow"]["end_year"])); ?>
	   	   {/if}
		</p>
	</div>
	{/foreach}
</div>
{/if}
