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
    <div class="table form-group">
        <div class="table_left">
            <label for="category">{if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}:</label>
        </div>
        <div class="table_right">
            {if isset($aField.options)}
                {if $aField.var_type=='select'}
                <select class="form-control" id="cf_{$aField.field_name}" name="val[custom][{$aField.field_id}]">
                    {foreach from=$aField.options item=aOption}
                        <option value="{phrase var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}selected{/if}>{phrase var=$aOption.phrase_var_name}</option>
                    {/foreach}
                </select>                
                {/if}
                
                {if $aField.var_type=='multiselect'}
                <select class="form-control" id="cf_{$aField.field_name}" name="val[custom][{$aField.field_id}][]" size="5" multiple="yes">
                    {foreach from=$aField.options item=aOption}
                        <option value="{phrase var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}selected{/if}>{phrase var=$aOption.phrase_var_name}</option>
                    {/foreach}
                </select>                
                {/if}
                
                {if $aField.var_type=='checkbox'}
                {foreach from=$aField.options item=aOption}
                <div>
                <input id="cf_{$aField.field_name}" class="checkbox" type="checkbox" name="val[custom][{$aField.field_id}][]" value="{phrase var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}checked{/if} /> {phrase var=$aOption.phrase_var_name}
                </div>
                {/foreach}
                {/if}
                
                {if $aField.var_type=='radio'}
                {foreach from=$aField.options item=aOption}
                <div>
                <input id="cf_{$aField.field_name}" type="radio" name="val[custom][{$aField.field_id}]" value="{phrase var=$aOption.phrase_var_name}" {if !empty($aOption.selected)}checked{/if} /> {phrase var=$aOption.phrase_var_name}
                </div>
                {/foreach}
                {/if}
            {else}
                {if $aField.var_type=='text'}
                <input id="cf_{$aField.field_name}" class="form-control" size="40" type="text" name="val[custom][{$aField.field_id}]" value="{if !empty($aField.value)}{$aField.value}{/if}" />
                {else}
                <textarea class="form-control" id="cf_{$aField.field_name}" cols="50" rows="3" name="val[custom][{$aField.field_id}]">{if !empty($aField.value)}{$aField.value}{/if}</textarea>
                {/if}
            {/if}
        </div>
    </div>
{/foreach}
<div class="separate"></div>
{/if}