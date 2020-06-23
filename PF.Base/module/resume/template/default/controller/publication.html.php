<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>


<div style="position: relative">
<h3  class="yns add-res">
	<ul class="yns menu-add">
		<li>{_p var='publications'}</li>
		<li><a class="page_section_menu_link" href="{url link='resume.publication'}id_{$id}/">{_p var='add_a_publication'}</a></li>
	</ul>
</h3>
</div>

<form class="resume_add_content" id="yresume_add_form" method="post" name="js_resume_add_form" enctype="multipart/form-data">
<div id="headline">
	<!-- Publication type -->
	<div class="table form-group-follow">
		<div class="table_left table_left_add">
			<label for="magazine">{required}{_p var='publication_type'}:</label>
		</div>
		<div class="table_right">
			<select name="val[type_id]" class="form-control" id ="publication_type">
				<option value="1" {if isset($aForms.type_id) and $aForms.type_id == 1}selected{elseif !isset($aForms.type_id)}selected{/if}>{_p var='book'}</option>
				<option value="2" {if isset($aForms.type_id) and $aForms.type_id == 2}selected{/if}>{_p var='magazine'}</option>
				<option value="0" {if isset($aForms.type_id) and $aForms.type_id == 0}selected{/if}>{_p var='other'}</option>
			</select>
			<input type="text" class="form-control" name="val[other_type]" value="{value type='input' id='other_type'}" id="other_type" size="20" maxlength="255" {if isset($aForms.type_id) and $aForms.type_id == 0}style="display:inline;"{else}style="display:none;"{/if}/>
		</div>
	</div>
	<!-- Publication Title -->
	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="title">{required}{_p var='title'}:</label>
		</div>
		<div class="table_right">
			<input type="text" name="val[title]" class="form-control" value="{value type='input' id='title'}" id="title" size="60" maxlength="255" />
		</div>
	</div>
	<!-- Publication Publisher -->
	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="publisher">{_p var='publisher'}:</label>
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[publisher]" value="{value type='input' id='publisher'}" id="publisher" size="60" maxlength="255" />
		</div>
	</div>
	<!-- Publication URL -->
	<div class="table form-group">
		<div class="table_left table_left_add">
			<label for="publication_url">{_p var='publication_url'}:</label>
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[publication_url]" value="{value type='input' id='publication_url'}" id="publication_url" size="60" maxlength="255" />
		</div>
	</div>
	<!-- Publication Time -->
	<div class="table form-group-follow">
		<div class="table_left table_left_add">
			<label for="published_year">{_p var='year_of_publication'}:</label>
		</div>
		<div class="table_right">
			<!-- Day -->
			<select name='val[published_day]' class="js_datepicker_day form-control">
				<option value="">{_p var='uppercase_day'} ...</option>
				{foreach from=$aDay item=day}
					<option {if !empty($aForms) && $aForms.published_day==$day}selected{/if}>{$day}</option>
				{/foreach}
			</select>
			<!-- Month -->
			<select name='val[published_month]' class="js_datepicker_month form-control">
				<option value="">{_p var='uppercase_month'} ...</option>
				{foreach from=$aMonth item=month}
					<option {if !empty($aForms) && $aForms.published_month==$month}selected{/if}>{$month}</option>
				{/foreach}
			</select>
			<!-- Year -->
			<select name='val[published_year]' id="published_year" class="js_datepicker_year form-control">
				<option value="">{_p var='year'} ...</option>
				{foreach from=$aYear item=year}
					<option {if !empty($aForms) && $aForms.published_year==$year}selected{/if}>{$year}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<!-- Publication Author -->
	<div class="table form-group">
		<div class="table_left table_left_add">
			{phrase var="resume.author"}:
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[author]" value="" size="20" maxlength="200" id='element_name'/>
			<a id="add_more_element" class="fa" href="#" onclick="javascript:void(0);return false;" title="{phrase var ='resume.add_author'}">
				{img theme='misc/add.png' class='v_middle'}
			</a>
		</div>
	</div>

	<div class="table form-group" style="display:none">
		<div class="table_left table_left_add">
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[author_list]' id='element_list'>
				{if isset($aForms.author_list)}{$aForms.author_list}{/if}
			</textarea>
		</div>
	</div>

	<div class="table form-group textareaselect ynr-textareaselect-author">
		<div class="table_left table_left_add">
		</div>
		<div class="table_right tablecontent" >
			{if isset($aForms.array_author_list)}
			{foreach from=$aForms.array_author_list item=sAuthor}
				<ul class="chzn-choices">
					<li id="selEEW_chzn_c_1" class="search-choice">
						<span>{$sAuthor}</span>
						<a rel="1" class="search-choice-close closeskill" href="javascript:void(0)" onclick="removeElement($(this));return false;" ></a>
					</li>
				</ul>
			{/foreach}
			{/if}
		</div>
	</div>
	<!-- Publication Note -->
	<div class="table form-group" style="padding-top: 10px;">
		<div class="table_left table_left_add">
			<label for="note">{_p var='summary'}:</label>
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="70" rows="5" name='val[note]'>{if !empty($aForms)}{$aForms.note}{/if}</textarea>
		</div>
	</div>


		<div class="table_clear resume-btn-group">
			<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>
			<button type="button" class="button btn btn-default btn-sm" onclick="window.location.href='{url link='resume.addition'}id_{$id}'">{_p var='skip'}</button>
		</div>

</div>

</form>

{if count($aRows)>0}
<div class="yns_resume_listcontent">
	<h3>{phrase var="resume.list_of_publication"}</h3>
	{foreach from=$aRows item=aRow}
	<div class="section_row" id='publication_{$aRow.publication_id}'>
		<div class="resume_section_info_content info_section_title">
			<!-- Publication Type -->
			<span>

					{if $aRow.type_id == 1}
						{phrase var="resume.book"}
					{elseif $aRow.type_id == 2}
						{phrase var="resume.magazine"}
					{else}
						{$aRow.other_type}
					{/if}

			</span>
			{if $aRow.publisher}
				 -
				 <span>
				 	<strong>{$aRow.publisher}</strong>
				 </span>
			{/if}
			<!-- Published Time -->
			{if $aRow.published_month and $aRow.published_year}
				 , <?php echo date('d F Y',mktime(0,0,0,$this->_aVars["aRow"]["published_month"],$this->_aVars["aRow"]["published_day"],$this->_aVars["aRow"]["published_year"])); ?>
			{/if}
			<!-- Publication Manage Action  -->
			<div class="info_section_link">
				<a href="{url link='resume.publication'}id_{$id}/exp_{$aRow.publication_id}/">{phrase var="resume.edit"}</a>
				|
				<a href="#" onclick="if(confirm( '{_p var='are_you_sure'}' ))$.ajaxCall('resume.delete_publication','exp_id={$aRow.publication_id}');return false;">{phrase var="resume.delete"}</a>
			</div>
		</div>
		<!-- Publication Title and Url -->

		<!-- Publication Authors -->
		{if isset($aRow.author_list)}
			<div class="resume_section_info_content">
				<i>{phrase var="resume.author"}:</i>
				{$aRow.author_list}
			</div>
		{/if}
		<!-- Publication Summary -->
		{if $aRow.note_parsed}
			<div class ="publication_summary">
				<i>{phrase var="resume.summary"}:</i>
				<div class="summary_text">
					{$aRow.note_parsed}
				</div>
			</div>
		{/if}
	</div>
	{/foreach}
</div>
{/if}
