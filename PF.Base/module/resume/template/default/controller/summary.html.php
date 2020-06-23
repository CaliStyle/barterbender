<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

{if !isset($aForms.country_iso) || $aForms.country_iso==""}
	{literal}
	<script type="text/javascript">
		$Behavior.LoadSelectCountry_Summary = function()
		{

				try{
					document.getElementById('country_iso').selectedIndex = 0;
					document.getElementById('authorized_country_iso').selectedIndex = 0;
					$('#js_country_child_id_value').val(0);
				}catch(ex)
				{

				}
		};
	</script>
{/literal}
{/if}
<table class="ynf-loaction-added template_multi_location" style="display: none;">
   <tr class="ynf-location-item">
        <td style="width:250px">
            <span class="label_country_iso"></span>
            <input class="value_country_iso" type="hidden" name="val[authorized_country_iso][]" value="" />
        </td>
        <td class="template_country_child" style="width:200px">
        	<span class="label_country_child"></span>
            <input class="value_country_child" type="hidden" name="val[authorized_country_child][]" value="" />
        </td>
        <td style="width:400px">
        	<span class="label_location"></span>
            <input class="value_location" type="hidden" name="val[authorized_location][]" value="" />
        </td>
        <td style="width:200px">
        	<span class="label_level_id"></span>
            <input class="value_level_id" type="hidden" name="val[authorized_level_id][]" value="" />
        </td>
        <td style="width:200px">
            <span class="label_other_level"></span>
            <input class="value_other_level" type="hidden" name="val[authorized_other_level][]" value="" />
        </td>
       	<td style="width:50px">
       		<a href="javascript:void(0);" class="remove_icon" onclick="return removeLocation(this);">
            	{img theme='misc/delete.png' class='v_middle'}
        	</a>
       	</td>

   </tr>
</table>
{literal}
<script type="text/javascript">
function removeLocation(e) {
    console.log($(e).parent().parent());
    $(e).parent().parent().remove();
}

function resetTemplate()
{
    $('.template_multi_location .label_country_iso').html('');
    $('.template_multi_location .value_country_iso').val('');

    $('.template_multi_location .label_other_level').parent().show();
    $('.template_multi_location .label_level_id').parent().show();

    $('.template_multi_location .label_country_child').html('');
    $('.template_multi_location .value_country_child').val('');

    $('.template_multi_location .label_location').html('');
    $('.template_multi_location .value_location').val('');

    $('.template_multi_location .label_level_id').html('');
    $('.template_multi_location .value_level_id').val(0);

    $('.template_multi_location .label_other_level').html('');
    $('.template_multi_location .value_other_level').val('');
}

function resetInput()
{
    $('#authorized_country_iso').val('');

    var oCountryChild = $('#authorized_js_country_child_id_value');
    if (oCountryChild && oCountryChild.attr('type') != 'hidden')
    {
        oCountryChild.val('0');
    }

    $('#authorized_location').val('');
    $('#select_authorized_level_id').val(0)
    $('#authorized_other_level').val('');
    $('#div_authorized_other_level').hide();
}

function addLocation()
{
    if ($('.multi_location_holder .ynf-location-item').length >= 6)
    {
        alert(oTranslations['resume.you_reach_the_maximum_of_total_predefined']);
        return;
    }

    var sCountryChildLabel = '';
    var iCountryChildValue = 0;

    var oCountryChild = $('#authorized_js_country_child_id_value');
    if (oCountryChild)
    {
        iCountryChildValue = oCountryChild.val();
        sCountryChildLabel = $('#authorized_js_country_child_id_value :selected').text();
		if (oCountryChild.val()=='0')
		{
			sCountryChildLabel = "";
		}
    }

    var sCountryIso = $('#authorized_country_iso').val();
    var sCountryIsoLabel = $('#authorized_country_iso option:selected').text();

    var sLocation = $('#authorized_location').val();

    var iPosition = $('#select_authorized_level_id').val();
    var iPositionLabel = $('#select_authorized_level_id option:selected').text();

    var sOtherPosition = $('#authorized_other_level').val();
    if (sCountryIso.length == 0)
    {
        alert('Please select country!');
        return;
    }
    $('.multi-location-label').show();
    $('.multi_location_holder').show();

    $('.template_multi_location .label_country_iso').html(sCountryIsoLabel);
    $('.template_multi_location .value_country_iso').val(sCountryIso);

    $('.template_multi_location .label_country_child').html(sCountryChildLabel);
    $('.template_multi_location .value_country_child').val(iCountryChildValue);

    $('.template_multi_location .label_location').html(sLocation);
    $('.template_multi_location .value_location').val(sLocation);

    $('.template_multi_location .value_level_id').val(iPosition);
    $('.template_multi_location .value_other_level').val(sOtherPosition);

    if (iPosition != 0)
    {
        $('.template_multi_location .label_level_id').html(iPositionLabel);
        $('.template_multi_location .label_other_level').parent().hide();
    }
    else
    {
        $('.template_multi_location .label_level_id').parent().hide();
        $('.template_multi_location .label_other_level').html(sOtherPosition);
    }

    var oTemplateLocation = $('.template_multi_location tbody');

    $('.multi_location_holder table tbody').append(oTemplateLocation.html());

    resetTemplate();
    resetInput();
}

$Behavior.loadsummary = function(){

	$('#select_authorized_level_id').bind('change',function(){
		document.getElementById('div_authorized_other_level').style.display = "none";
		$('#authorized_other_level').val("");
	});

	$('#summary_other').bind('click',function(event){
		event.preventDefault();
		$('#div_authorized_other_level').toggle();
		document.getElementById('select_authorized_level_id').selectedIndex = 0;
	});
};

$Behavior.checklimitCategory = function(){
	$('#js_category_content').find('.checkbox').bind('click',function(){
		   	var res_max_cats = 0;
		    $('#js_category_content').find('.checkbox').each(function(i,val){
		    if(val.checked)
		        res_max_cats +=1;
		    });
		    if(res_max_cats > {/literal}{$iMaxCategories}{literal})
		    {
		        this.checked = false;
		        alert("{/literal}{_p var='you_can_only_select_number_categories' number = $iMaxCategories}{literal}");
		    }
	});
}
</script>
{/literal}


<div>
<h3 class="yns add-res">
<ul class="yns menu-add">
	<li>{required}{_p var='summary'}</li>
</ul>
</h3>
</div>

<form class="resume_add_content" id="yresume_add_form" name='js_resume_summary_form' method="post" action="{url link='resume.summary'}id_{$id}/">

<div id="headline">
	<div class="summary_label">
		<strong>{_p var='resume_name'}</strong>
	</div>
	<div class="summary_content">
		<div class="table form-group" >
			<div class="table_left table_left_add">
			{required}<label for="headline">{_p var='resume_name'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="val[headline]" value="{value type='input' id='headline'}" id="headline" size="40" maxlength="100" />
			</div>
		</div>
	</div>

	<div class="summary_label">
		<strong>{phrase var="resume.authorized_to_work_in"}</strong>
	</div>
	<div class="summary_content">
		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="country_iso">{_p var='country'}:</label>
			</div>
			<div class="table_right">
				{select_location name='authorized_country_iso'}
				{module name='resume.country-child'}
			</div>
		</div>

		<div class="table form-group">
			<div class="table_left table_left_add">
				<label for="city">{_p var='location'}:</label>
			</div>
			<div class="table_right">
				<input type="text" class="form-control" id="authorized_location" size="20" />
			</div>
		</div>

		<div class="table form-group-follow mb-2">
			<div class="table_left table_left_add">
				<label for="authorized_level_id">{_p var='position'}:</label>
			</div>
			<div class="table_right">
				<select class="form-control" style="margin-bottom: 4px;" id="select_authorized_level_id" name="val[authorized_level_id]" >
						<option value="0">{phrase var="resume.select"}</option>
					{foreach from=$aLevel item=level}
						<option value="{$level.level_id}" {if $aForms.authorized_level_id == $level.level_id} selected {/if} >{$level.name}</option>
					{/foreach}
				</select>
				 <div style="margin-left: 15px;display: inline">{_p var='or'}</div> <a href="javascript:void(0);" id='summary_other'>{_p var='other'}</a>
				<div id="div_authorized_other_level" style="margin-top: 3px;{if $aForms.authorized_other_level==""}display: none{/if}">
					<input type="text" class="form-control" name="val[authorized_other_level]" value="" id="authorized_other_level" size="20" maxlength="200" />
				</div>
			</div>

		</div>

        <div class="table form-group-follow mb-2">
			<div class="table_right my-2">
				<button type="button" class="button btn btn-primary btn-sm" onclick="addLocation();">{_p var='add_location'}</button>
			</div>
		</div>
        <div class="table form-group-follow">
            <div class="table_left table_left_add multi-location-label" {if !isset($aForms.authorized) || count($aForms.authorized) == 0 } style="display: none;" {/if} >
                <label>{_p var='multi_location'}:</label>
            </div>
            {if count($aCountryChildren) == 0} <div style="display: none;" class="no-country-child">&nbsp;</div> {/if}
            <div class="table_right multi_location_holder" {if !isset($aForms.authorized) || count($aForms.authorized) == 0 } style="display: none;" {/if}>
                <table class="ynf-loaction-added">
                     <tr class="ynf-location-item">
                        <td style="width:250px">{_p var='country'}:</td>
                        <td style="width:200px">{_p var='state_province'}:</td>
                        <td style="width:400px">{_p var='location'}:</td>
                        <td style="width:200px">{_p var='position'}:</td>
                        <td style="width:50px">&nbsp;</td>
                    </tr>
                    {if count($aForms.authorized) > 0}
                    {foreach from=$aForms.authorized item=aLocation}
                        <tr class="ynf-location-item">
                            <td style="width:250px">
                                <span class="label_country_iso">{$aLocation.label_country_iso}</span>
                                 <input class="value_country_iso" type="hidden" name="val[authorized_country_iso][]" value="{$aLocation.country_iso}" />
                            </td>

                            <td class="field_country_child" style="width:200px">
                                <span class="label_country_child">{if $aLocation.label_country_child!=""}{$aLocation.label_country_child}{else}{/if}</span>
                                <input class="value_country_child" type="hidden" name="val[authorized_country_child][]" value="{$aLocation.country_child}" />
                            </td>

                            <td style="width:400px">
                                <span class="label_location">{if $aLocation.location}{$aLocation.location}{else}{/if}</span>
                                <input class="value_location" type="hidden" name="val[authorized_location][]" value="{$aLocation.location}" />
                            </td>
                            {if !empty($aLocation.label_level_id)}
                            <td {if empty($aLocation.label_level_id)} style="display: none;width:0px" {else} style="width:200px" {/if} >
                                <span class="label_level_id">{$aLocation.label_level_id}</span>
                                <input class="value_level_id" type="hidden" name="val[authorized_level_id][]" value="{$aLocation.level_id}" />

                            </td>
                            {else}

                            <td style="width:200px">
                                <span class="label_other_level">{if $aLocation.other_level}{$aLocation.other_level}{else}{/if}</span>
                                <input class="value_other_level" type="hidden" name="val[authorized_other_level][]" value="{$aLocation.other_level}" />

                            </td>
                            {/if}

                            <td style="width:50px">
                                <a  class="remove_icon" href="javascript:void(0);" onclick="return removeLocation(this);">
                                 {img theme='misc/delete.png' class='v_middle'}
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                    {/if}
                </table>
            </div>
        </div>
	</div>
	<div class="summary_label">
		<strong>{phrase var="resume.your_information"}</strong>
	</div>
	<div class="summary_content">
		<div class="table form-group-follow">
			<div class="table_left">
				<label for="country_iso">{_p var='location'}: {if !isset($aForms.country_phrase) || $aForms.country_phrase==""}{_p var='none'}{else}{$aForms.country_phrase}{/if}</label>
			</div>
			<div class="table_right" style="display:none">
				{select_location}
				{module name='core.country-child'}
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left">
				<label for="city">{_p var='city'}: {if !isset($aForms.city) || $aForms.city==""}{_p var='none'}{else}{$aForms.city}{/if}</label>
			</div>
			<div class="table_right" style="display:none">
				<input type="text" class="form-control" name="val[city]" value="{value type='input' id='city'}" id="city" size="20" maxlength="200" />
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left">
				<label for="postal_code">{_p var='zip_code'}: {if !isset($aForms.zip_code) || $aForms.zip_code==""}{_p var='none'}{else}{$aForms.zip_code}{/if}</label>
			</div>
			<div class="table_right" style="display:none">
				<input type="text" class="form-control" name="val[zip_code]" value="{value type='input' id='zip_code'}" id="zip_code" size="10" maxlength="20" />
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="categories">{required}{_p var='categories'}:</label>
			</div>
			<div class="table_right">
				<div style="margin-bottom: 7px;">
					{phrase var="resume.maximum_selected_category_number_is_number" number= $iMaxCategories}
				</div>
				<div class="label_flow label_hover labelFlowContent" style="height:100px;" id="js_category_content">
				{if $bIsEdit}
						{module name='resume.add-category-list' resume_id=$aForms.resume_id}
					{else}
						{module name='resume.add-category-list' resume_id=0}
					{/if}
				</div>
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="highest_level">{_p var='highest_level'}:</label>
			</div>
			<div class="table_right">
				<select class="form-control" name="val[level_id]">
						<option value="0">{phrase var="resume.select"}</option>
					{foreach from=$aLevel item=level}
						<option value="{$level.level_id}" {if $aForms.level_id == $level.level_id} selected {/if} >{$level.name}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="year_exp">{_p var='years_of_experience'}:</label>
			</div>
			<div class="table_right">
				<select class="form-control" name="val[year_exp]">
					{for $i=0;$i<=50;$i++}
					<option value={$i} {if $aForms.year_exp==$i}selected=selected{/if}>{$i}</option>
					{/for}
				</select>
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="summary">{required}{_p var='summary'}:</label>
			</div>
			<div class="table_right">
				{editor id='summary' rows='4'}
			</div>
		</div>

		<div class="table_clear">


				<input type="submit" class="button btn btn-primary btn-sm" value = "{_p var='update'}"/>

		</div>
	</div>
</div>
</form>



