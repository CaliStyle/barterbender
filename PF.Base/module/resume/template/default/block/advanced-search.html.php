<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**

 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 *
 */?>
<div class="yns adv-search-block" id ="resume_adv_search" {if !$bIsAdvSearch }style="display:none"{else}style="display:block"{/if}>
	<form method="post" action="{url link='resume'}?bIsAdvSearch=1">
		<input type="hidden" id="form_flag" name="search[form_flag]" {if !$bIsAdvSearch }value="0"{else}value="1"{/if}>
		<!-- Keywords -->
		<div class="table form-group-follow">
			<div class="table_left">
				<label for="keywords">{_p var='headline'}:</label>
			</div>
			<div class="table_right ">
				<input type="text" class="form-control" name="search[keywords]" value="{if isset($aForms.keywords)}{$aForms.keywords}{/if}" id="keywords" size="30" maxlength="200" />
			</div>
		</div>
		<!-- Location -->
		<div class="table form-group-follow">
			<div class="table_left">
				<label for="country_iso">{_p var='location'}:</label>
			</div>
			<div class="table_right">
				{select_location}
				{module name='core.country-child'}
			</div>
			<div class="clear"></div>
		</div>
		<!-- City -->
		<div class="table form-group-follow">
			<div class="table_left" >
				<label for="city">{_p var='city'}:</label>
			</div>
			<div class="table_right ">
				<input type="text" class="form-control" name="search[city]" value="{if isset($aForms.city)}{$aForms.city}{/if}" id="city" size="30" maxlength="200" />
			</div>
		</div>
		<!-- Company -->
		<div class="table form-group-follow">
			<div class="table_left" >
				<label for="company">{_p var='company'}:</label>
			</div>
			<div class="table_right ">
				<input type="text" class="form-control" name="search[company]" value="{if isset($aForms.company)}{$aForms.company}{/if}" id="company" size="30" maxlength="200" />
			</div>
		</div>
		<!-- School -->
		<div class="table form-group-follow">
			<div class="table_left " >
				<label for="school">{_p var='school'}:</label>
			</div>
			<div class="table_right ">
				<input type="text" class="form-control" name="search[school]" value="{if isset($aForms.school)}{$aForms.school}{/if}" id="school" size="30" maxlength="200" />
			</div>
		</div>
		<!-- Category -->
		<div class="table form-group-follow">
			<div class="table_left ">
				<label for="postal_code">{_p var='categories'}:</label>
			</div>
			<div class="table_right ">
				<div class="label_flow label_hover labelFlowContent" style="height:100px;" id="js_category_content">
				<div id="js_add_new_category"></div>
					{foreach from=$aItems item=aItem}
						<label for="js_category{$aItem.category_id}"
							   id="js_category_label{$aItem.category_id}">
							<input value="{$aItem.category_id}" {
								   if in_array($aItem.category_id,$aItemData)}checked= true{/if} type="checkbox" name="search[category][]" id="js_category{$aItem.category_id}" class="checkbox" /> {$aItem.name|convert|clean}
						</label>
					{foreachelse}
					<div class="p_4">
						{_p var='no_categories_added'}
					</div>
					{/foreach}
				</div>
			</div>
		</div>
		<!-- Degree -->
		<div class="table form-group-follow">
			<div class="table_left " >
				<label for="degree">{_p var='degree'}:</label>
			</div>
			<div class="table_right ">
				<input type="text" class="form-control" name="search[degree]"
					   value="{if isset($aForms.degree)}{$aForms.degree}{/if}"
					   id="degree"
					   size="30"
					   maxlength="200" />
			</div>
		</div>
		<!-- Level -->
		<div class="table form-group-follow">
			<div class="table_left ">
				<label for="highest_level">{_p var='highest_level'}:</label>
			</div>
			<div class="table_right ">
				<select class="form-control" name="search[level_id]">
					    <option value ="">{phrase var="resume.select"}</option>
					{foreach from=$aLevels item=aLevel}
						<option value="{$aLevel.level_id}" {if $aForms.level_id == $aLevel.level_id} selected {/if}>{$aLevel.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<!-- Year of Experience -->
		<div class="table form-group-follow">
			<div class="table_left ">
				<label for="year_exp">{_p var='years_of_experience'}:</label>
			</div>
			<div class="table_right ">
				<!-- from -->
				<label for="year_exp_from">{_p var='from'}</label>
				<select class="form-control" name="search[year_exp_from]">
					{for $i=0;  $i<= 50 ; $i++}
						<option value="{$i}" {if $aForms.year_exp_from == $i} selected {/if}>{$i}</option>
					{/for}
				</select>
				<!-- to -->
				<label for="year_exp_to" style="margin-left:10px;">{_p var='to'}</label>
				<select class="form-control" name="search[year_exp_to]">
					{for $i=0; $i <= 50 ; $i++}
						<option value="{$i}" {if $aForms.year_exp_to == $i} selected {/if}>{$i}</option>
					{/for}
				</select>
			</div>
		</div>
		<!-- Gender -->
		<div class="table form-group-follow">
			<div class="table_left ">
				<label for="year_exp">{_p var='gender'}:</label>
			</div>
			<div class="table_right ">
				<label class="radio-inline">
					<input type="radio" name="search[gender]" value="" {if !$aForms.gender}checked{/if} >{phrase var="resume.all"}
				</label>

				<label class="radio-inline">
					<input type="radio" name="search[gender]" value="1" {if $aForms.gender == 1} checked{/if} >{phrase var="resume.male"}
				</label>

				<label class="radio-inline">
					<input type="radio" name="search[gender]" value="2" {if $aForms.gender == 2} checked{/if} >{phrase var="resume.female"}
				</label>
			</div>
		</div>
		<!-- Skill -->
		<div class="table form-group-follow">
			<div class="table_left " >
				<label for="skill">{_p var='skill'}:</label>
			</div>
			<div class="table_right ">
				<input class="form-control" type="text" name="search[skill]" value="{if isset($aForms.skill)}{$aForms.skill}{/if}" id="skill" size="30" maxlength="200" />
			</div>
		</div>
		<!-- Submit Button -->

	<div class="table_clear">
		<input type="submit" id="filter_submit" name="search[submit]" value="{_p var='search'}" class="button btn btn-primary btn-sm" />
		<input type="submit" id="filter_submit" name="search[submit]"
			   value="{_p var='reset'}" class="button btn btn-default btn-sm " />
	</div>
	</form>
</div>

{literal}
<script type="text/javascript">
	$Behavior.InitCountry = function()
	{
		try{
			$('#country_iso').attr('name','search[country_iso]');
			$('#js_country_child_id_value').attr('name','search[country_child_id]');
		}catch(ex){

		}
	}
</script>
{/literal}


{if !isset($aForms.country_iso) || $aForms.country_iso==""}
	{literal}
	<script type="text/javascript">

		$Behavior.LoadSelectCountry_AdSearch = function()
		{

				try{
					document.getElementById('country_iso').selectedIndex = 0;
					document.getElementById('js_country_child_id_value').selectedIndex = 0;
				}catch(ex)
				{

				}
		};
	</script>
{/literal}
{/if}

