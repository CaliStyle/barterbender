{if count($aCustomFields)}
    {foreach from=$aCustomFields item=aField}
        <div class="table form-group">
            <label>
                {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
            </label>
            <div>
                {if $aField.var_type=='text'}
                    <input class="form-control" {if $aField.is_required==1}data-isrequired="1"
                           {else}data-isrequired="0"{/if} data-type="text" id="js_jp_cf_{$aField.field_id}" type="text"
                           name="val[custom][{$aField.field_id}]"
                           maxlength="255" {if isset($aField.value) } value="{$aField.value}" {/if} />
                {elseif $aField.var_type=='textarea'}
                    <textarea class="form-control" {if $aField.is_required==1}data-isrequired="1"
                              {else}data-isrequired="0"{/if} data-type="textarea" id="js_jp_cf_{$aField.field_id}"
                              cols="35" rows="4"
                              name="val[custom][{$aField.field_id}]">{if isset($aField.value) } {$aField.value} {/if}</textarea>
                {elseif $aField.var_type=='select'}
                    <select class="form-control" {if $aField.is_required==1}data-isrequired="1"
                            {else}data-isrequired="0"{/if} data-type="select" id="js_jp_cf_{$aField.field_id}"
                            name="val[custom][{$aField.field_id}][]">
                        {if !$aField.is_required}
                            <option value="">{phrase var='directory.select'}:</option>
                        {/if}
                        {foreach from=$aField.option key=opId item=opPhrase}
                            {if isset($aField.value) }
                                {foreach from=$aField.value key=selected item=value_selected }
                                    <option value="{$opId}" {if $opId == $selected} selected="selected" {/if} > {phrase var=$opPhrase}</option>
                                {/foreach}
                            {else}
                                <option value="{$opId}"> {phrase var=$opPhrase}</option>
                            {/if}
                        {/foreach}

                    </select>
                {elseif $aField.var_type=='multiselect'}
                    <select class="form-control" {if $aField.is_required==1}data-isrequired="1"
                            {else}data-isrequired="0"{/if} data-type="multiselect" id="js_jp_cf_{$aField.field_id}"
                            name="val[custom][{$aField.field_id}][]" size="4" multiple="yes">
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
                        <div class="checkbox">
                            <label><input {if $aField.is_required==1}data-isrequired="1" {else}data-isrequired="0"{/if}
                                          data-type="checkbox" id="js_jp_cf_{$aField.field_id}" type="checkbox"
                                          name="val[custom][{$aField.field_id}][]" value="{$opId}"
                                        {if isset($aField.value)}
                                            {foreach from=$aField.value key=checked item=value_checked}
                                                {if $opId == $checked}
                                                    checked="checked"
                                                {/if}
                                            {/foreach}
                                        {/if}
                                /> {phrase var=$opPhrase}</label>
                        </div>
                    {/foreach}

                {elseif $aField.var_type=='radio'}
                    {foreach from=$aField.option key=opId item=opPhrase}
                        {if isset($aField.value) }
                            {foreach from=$aField.value key=checked item=value_checked }
                                <div class="radio">
                                    <label>
                                        <input {if $aField.is_required==1}data-isrequired="1"
                                               {else}data-isrequired="0"{/if} data-type="radio"
                                               id="js_jp_cf_{$aField.field_id}" type="radio"
                                               name="val[custom][{$aField.field_id}][]"
                                               value="{$opId}" {if $opId == $checked} checked {/if} />
                                        {phrase var=$opPhrase}
                                    </label>
                                </div>
                            {/foreach}
                        {else}
                            <div class="radio">
                                <label>
                                    <input {if $aField.is_required==1}data-isrequired="1" {else}data-isrequired="0"{/if}
                                           data-type="radio" id="js_jp_cf_{$aField.field_id}" type="radio"
                                           name="val[custom][{$aField.field_id}][]" value="{$opId}"/>
                                    {phrase var=$opPhrase}
                                </label>
                            </div>
                        {/if}
                    {/foreach}
                {/if}
            </div>
        </div>
    {/foreach}
{/if}
