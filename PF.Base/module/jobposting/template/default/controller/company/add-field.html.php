<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author			AnNT
 * @package 		Module_jobposting
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>


<div id="js_field_holder">
	<form method="post" id="js_custom_field" onsubmit="return ynjobposting.processCustomField(this,{if $bIsEdit}true{else}false{/if})">
        <input type="hidden" name="val[company_id]" value="{$iCompanyId}" />
        {if $bIsEdit}<input type="hidden" name="val[id]" value="{$iId}" />{/if}
		
		<div class="table form-group"{if $bIsEdit} style="display:none;"{/if}>
			<div class="table_left">
				{required}{phrase var='custom.type'}:
			</div>
			<div class="table_right">
				<select name="val[var_type]" class="var_type">
					<option value="">{phrase var='custom.select'}:</option>
					<option value="textarea"{value type='select' id='var_type' default='textarea'}>{phrase var='custom.large_text_area'}</option>
					<option value="text"{value type='select' id='var_type' default='text'}>{phrase var='custom.small_text_area_255_characters_max'}</option>
					<option value="select"{value type='select' id='var_type' default='select'}>{phrase var='custom.selection'}</option>
					<option value="multiselect"{value type='select' id='var_type' default='multiselect'}>{phrase var='core.multiple_selection'}</option>
					<option value="radio"{value type='select' id='var_type' default='radio'}>{phrase var='core.radio'}</option>
					<option value="checkbox"{value type='select' id='var_type' default='checkbox'}>{phrase var='core.checkbox'}</option>
				</select>
			</div>
		</div>
		
		<div class="table form-group">
			<div class="table_left">
				{required}{phrase var='custom.name'}: 
			</div>
			<div class="table_right">
    			{if $bIsEdit && isset($aForms.name) && Phpfox::getLib('locale')->isPhrase('$aForms.name')}
    				{module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
    			{else}
    				{if isset($aForms.name) && is_array($aForms.name)}
    					{foreach from=$aForms.name key=sPhrase item=aValues}
    						{module name='language.admincp.form' type='text' id='name' mode='text' value=$aForms.name}
    					{/foreach}
    				{else}
    					{module name='language.admincp.form' type='text' id='name' mode='text'}
    				{/if}				
    			{/if}
                <label><input type="checkbox" name="val[is_required]" id="is_required" {if $aForms.is_required}checked="checked" {/if}/> {phrase var='require_field'}</label>
			</div>
		</div>	
		
		{if $bIsEdit && isset($aForms.option)}
		    <div class="table form-group" id="tbl_edit">
			    <div class="table_left">
				    {phrase var='custom.current_values'}:
			    </div>
			    <div class="table_right">		
				{foreach from=$aForms.option name=options key=iKey item=aOptions}
					<div class="p_4 js_current_value js_option_holder" id="js_current_value_{$iKey}">
						<b>{phrase var='custom.option_count' count=$phpfox.iteration.options}:</b> <a href="#?id={$iKey}" class="js_jc_delete_current_option">{img theme='misc/delete.png' alt='' class='v_middle'}</a>
						<div class="p_4">			
							{module name='language.admincp.form' type='text' id='current' value=$aOptions mode='text'}
						</div>
					</div>
				{/foreach}
			    </div>
		    </div>
		{/if}	
		
		{* This next block is used as a template *}
		<div class="table form-group" id="js_multi_select"{if $bHideOptions || $bIsEdit} style="display:none;"{/if}>
			<div class="table_left">
				{if $bIsEdit}Extra Values{else}{phrase var='custom.values'}{/if}: 
			</div>
			<div class="table_right">			
				<div id="js_sample_option">
					<div class="js_option_holder">
						<div class="p_4">
							<b>{phrase var='custom.option_html_count'}:</b> <span class="js_option_delete"></span>
							<div class="p_4">								
								{foreach from=$aLanguages item=aLang}
								<div>
								    <input type="text" name="val[option][#][{$aLang.language_code}][text]" value="" /> {$aLang.title}
								</div>
								{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

        {if empty($bIsEdit) || ($aForms.var_type != 'textarea' && $aForms.var_type != 'text')}
        <div class="form-group" id="tbl_option_holder">
            <label>{if !$bIsEdit}{_p var='Values'}: {else}{_p var='Extra Values'}:{/if}</label>
            <div id="js_option_holder"></div>
        </div>
        <div class="form-group" id="tbl_add_custom_option">
            <label></label>
            <a href="#" class="js_add_custom_option">{phrase var='custom.add_new_option'}</a>
        </div>
        {/if}

		<div class="table_clear">
			<input type="submit" value="{if $bIsEdit}{phrase var='custom.update'}{else}{phrase var='custom.add'}{/if}" class="button" id="js_add_field_button" />
            <span id="js_add_field_loading"></span>			
		</div>
	</form>
</div>

<script type="text/javascript" src="{$urlModule}/custom/static/jscript/admin.js"></script>
<script type="text/javascript">
var bIsEdit = false;

$(function(){l}
	$Core.custom.init({$iDefaultSelect});
    {if !$bIsEdit}
    $('#tbl_option_holder').hide();
    $('#tbl_add_custom_option').hide();
    {/if}
{r});

$('.js_jc_delete_current_option').click(function(){l}
	if (confirm('{$phrase}')){l}
		aParams = $.getParams(this.href);
		$.ajaxCall('jobposting.deleteOption', 'id=' + aParams['id'] + '&company_id={$iCompanyId}');
	{r}
	return false;
{r});

$Behavior.custom_admin_init();
</script>


