<div class="table form-group-follow">

    <div {if isset($isCompany) && $isCompany == 1}{else}class="table_left"{/if}>
    <label for="">
        {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}:
    </label>
    </div>

    <div class="table_right">
        {if $aField.var_type=='text'}
        <input id="js_jp_cf_{$aField.field_id}" type="text" class="form-control" name="val[custom][{$aField.field_id}]" {if isset($aField.value) } value = "{$aField.value}" {/if} maxlength="255" />

        {elseif $aField.var_type=='textarea'}
        <textarea id="js_jp_cf_{$aField.field_id}" class="form-control" rows="4" name="val[custom][{$aField.field_id}]">{if isset($aField.value) }{$aField.value}{/if}</textarea>

        {elseif $aField.var_type=='select'}
        <select id="js_jp_cf_{$aField.field_id}" class="form-control" name="val[custom][{$aField.field_id}][]"  >
            {if !$aField.is_required}
            <option value="">{phrase var='select'}:</option>
            {/if}
            {foreach from=$aField.option key=opId item=opPhrase}
                    {if isset($aField.value) }
                    {foreach from=$aField.value key=selected item=value_selected }
                    <option value="{$opId}" {if $opId == $value_selected || $opId == $selected} selected = "selected" {/if} > {phrase var=$opPhrase}</option>
                    {/foreach}
                    {else}
                    <option value="{$opId}" > {phrase var=$opPhrase}</option>
                    {/if}
            {/foreach}

        </select>

        {elseif $aField.var_type=='multiselect'}
        <select id="js_jp_cf_{$aField.field_id}" class="form-control" name="val[custom][{$aField.field_id}][]" size="4" multiple="yes">
            {foreach from=$aField.option key=opId item=opPhrase}
            <option value="{$opId}"

             {if isset($aField.value)}
                {foreach from=$aField.value key=selected item=value_selected}
                    {if $opId == $value_selected || $opId == $selected}
                        selected="selected"
                    {/if}
                {/foreach}
            {/if}

             >{phrase var=$opPhrase}</option>
            {/foreach}
        </select>

        {elseif $aField.var_type=='checkbox'}
            {foreach from=$aField.option key=opId item=opPhrase}
                <div class="checkbox">

                <label><input id="js_jp_cf_{$aField.field_id}" type="checkbox" name="val[custom][{$aField.field_id}][]" value="{$opId}"
                {if isset($aField.value)}
                {foreach from=$aField.value key=checked item=value_checked}
                    {if $opId == $value_checked || $opId == $checked}
                        checked="checked"
                    {/if}
                {/foreach}
                {/if}
                  /> {phrase var=$opPhrase}</label></div>
            {/foreach}

        {elseif $aField.var_type=='radio'}
            {foreach from=$aField.option key=opId item=opPhrase}
                    {if isset($aField.value) }
                    {foreach from=$aField.value key=checked item=value_checked }
                    <div class="radio">
                        <label>
                            <input id="js_jp_cf_{$aField.field_id}" type="radio" name="val[custom][{$aField.field_id}][]" value="{$opId}" {if $opId == $value_checked || $opId == $checked} checked {/if} /> {phrase var=$opPhrase}
                        </label>
                    </div>
                    {/foreach}
                    {else}
                    <div class="radio">
                        <label>
                            <input id="js_jp_cf_{$aField.field_id}" type="radio" name="val[custom][{$aField.field_id}][]" value="{$opId}" /> {phrase var=$opPhrase}
                        </label>
                    </div>
                    {/if}
            {/foreach}
        {/if}
    </div>
</div>