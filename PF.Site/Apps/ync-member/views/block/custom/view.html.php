{if count($aCustomFields)}
<div>
    {foreach from=$aCustomFields key=aKey item=aField}
        {if !empty($aField.value) }
            <div class="mb-1">
                <span class="ynmember_custom_field fw-bold">{phrase var=$aField.phrase_var_name}:</span>
                <span>
                    {if $aField.var_type=='text'}
                        {if isset($aField.value) } {$aField.value} {else} {_p('None')}   {/if}

                    {elseif $aField.var_type=='textarea'}
                       {if isset($aField.value) } {$aField.value} {else} {_p('None')}   {/if}
                    {elseif $aField.var_type=='select'}

                                {if isset($aField.value) }
                                {foreach from=$aField.value key=selected item=value_selected }
                                {phrase var=$value_selected}<br>
                                {/foreach}
                                {else}
                                {_p('None')}
                                {/if}

                    {elseif $aField.var_type=='multiselect'}
                                {if isset($aField.value) }
                                {foreach from=$aField.value key=selected item=value_selected }
                                {phrase var=$value_selected}<br>
                                {/foreach}
                                {else}
                                {_p('None')}
                                {/if}

                    {elseif $aField.var_type=='checkbox'}
                                {if isset($aField.value) }
                                {foreach from=$aField.value key=checked item=value_checked }
                                {phrase var=$value_checked}<br>
                                {/foreach}
                                {else}
                                {_p('None')}
                                {/if}

                    {elseif $aField.var_type=='radio'}
                               {if isset($aField.value) }
                                {foreach from=$aField.value key=checked item=value_checked }
                                {phrase var=$value_checked}<br>
                                {/foreach}
                                {else}
                                {_p('None')}
                                {/if}
                    {/if}
                </span>
            </div>
        {/if}
    {/foreach}
</div>
{/if}