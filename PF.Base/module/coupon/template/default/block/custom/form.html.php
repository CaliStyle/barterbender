<div class="form-group">
    <label for="">{if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}</label>
    {if $aField.var_type=='text'}
    <input id="js_jp_cf_{$aField.field_id}" type="text" name="val[custom][{$aField.field_id}]" maxlength="255" {if isset($aField.value) } value = "{$aField.value}" {/if} />

    {elseif $aField.var_type=='textarea'}
    <textarea id="js_jp_cf_{$aField.field_id}" class="form-control" cols="35" rows="4" name="val[custom][{$aField.field_id}]">{if isset($aField.value) } {$aField.value} {/if}</textarea>

    {elseif $aField.var_type=='select'}
    <select class="form-control" id="js_jp_cf_{$aField.field_id}" name="val[custom][{$aField.field_id}][]"  >
        {if !$aField.is_required}
        <option value="">{phrase var='select'}:</option>
        {/if}
        {foreach from=$aField.option key=opId item=opPhrase}
                {if isset($aField.value) }
                {foreach from=$aField.value key=selected item=value_selected }
                <option value="{$opId}" {if $opId == $selected} selected = "selected" {/if} > {phrase var=$opPhrase}</option>
                {/foreach}
                {else}
                <option value="{$opId}" > {phrase var=$opPhrase}</option>
                {/if}
        {/foreach}

    </select>

    {elseif $aField.var_type=='multiselect'}
    <select class="form-control" id="js_jp_cf_{$aField.field_id}" name="val[custom][{$aField.field_id}][]" size="4" multiple="yes">
        {foreach from=$aField.option key=opId item=opPhrase}
        <option value="{$opId}"

         {if isset($aField.value)}
            {foreach from=$aField.value key=selected item=value_selected}
                {if $opId == $selected}
                    selected="selected"
                {/if}
            {/foreach}
        {/if}

         >{phrase var=$opPhrase}</option>
        {/foreach}
    </select>

    {elseif $aField.var_type=='checkbox'}
        {foreach from=$aField.option key=opId item=opPhrase}
            <label><input id="js_jp_cf_{$aField.field_id}" type="checkbox" name="val[custom][{$aField.field_id}][]" value="{$opId}"
            {if isset($aField.value)}
            {foreach from=$aField.value key=checked item=value_checked}
                {if $opId == $checked}
                    checked="checked"
                {/if}
            {/foreach}
            {/if}
              /> {phrase var=$opPhrase}</label><br />
        {/foreach}

    {elseif $aField.var_type=='radio'}
        {foreach from=$aField.option key=opId item=opPhrase}
                {if isset($aField.value) }
                {foreach from=$aField.value key=checked item=value_checked }
        <label><input id="js_jp_cf_{$aField.field_id}" type="radio" name="val[custom][{$aField.field_id}][]" value="{$opId}" {if $opId == $checked} checked {/if} /> {phrase var=$opPhrase}</label><br />
                {/foreach}
                {else}
        <label><input id="js_jp_cf_{$aField.field_id}" type="radio" name="val[custom][{$aField.field_id}][]" value="{$opId}" /> {phrase var=$opPhrase}</label><br />
                {/if}
        {/foreach}
    {/if}
</div>