<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 *
 *
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
{if $aCustomFields}
    {foreach from=$aCustomFields item=aField}
        <div class="form-group">
            <label class="ync-customfield__label">{if $aField.is_required==1}{required}{/if}{_p var=$aField.phrase_var_name}:</label>
            {if isset($aField.options)}
                {if $aField.var_type=='select'}
                <select class="form-control" id="cf_{$aField.field_name}" name="val[custom][{$aField.field_id}]">
                    {foreach from=$aField.options item=aOption}
                        <option value="{_p var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}selected{/if}>{_p var=$aOption.phrase_var_name}</option>
                    {/foreach}
                </select>
                {/if}

                {if $aField.var_type=='multiselect'}
                <select class="form-control" id="cf_{$aField.field_name}" name="val[custom][{$aField.field_id}][]" size="5" multiple="yes">
                    {foreach from=$aField.options item=aOption}
                        <option value="{_p var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}selected{/if}>{_p var=$aOption.phrase_var_name}</option>
                    {/foreach}
                </select>
                {/if}

                {if $aField.var_type=='checkbox'}
                {foreach from=$aField.options item=aOption}
                <div class="checkbox ync-checkbox-custom">
	                <label>
	                	<input id="cf_{$aField.field_name}" type="checkbox" name="val[custom][{$aField.field_id}][]" value="{_p var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}checked{/if} /><i class="ico ico-square-o mr-1"></i> {_p var=$aOption.phrase_var_name}
	                </label>
                </div>
                {/foreach}
                {/if}

                {if $aField.var_type=='radio'}
                {foreach from=$aField.options item=aOption}
                <div class="radio ync-radio-custom">
                	<label>
	                	<input id="cf_{$aField.field_name}" type="radio" name="val[custom][{$aField.field_id}]" value="{_p var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}checked{/if} /><i class="ico ico-circle-o mr-1"></i> {_p var=$aOption.phrase_var_name}
                	</label>
                </div>
                {/foreach}
                {/if}
            {else}
                {if $aField.var_type=='text'}
                <input class="form-control" id="cf_{$aField.field_name}" size="40" type="text" maxlength="255" name="val[custom][{$aField.field_id}]" value="{if !empty($aField.value)}{$aField.value}{/if}" />
                {else}
                <textarea class="form-control" id="cf_{$aField.field_name}" cols="50" rows="3" name="val[custom][{$aField.field_id}]">{if !empty($aField.value)}{$aField.value}{/if}</textarea>
                {/if}
            {/if}
        </div>
    {/foreach}
{/if}
