<div class="form-group">
    <label for="">{if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}</label>
    {if $aField.var_type=='text'}
        {if isset($aField.value) } {$aField.value} {else} {phrase var="none"}  {/if}

    {elseif $aField.var_type=='textarea'}
       {if isset($aField.value) } {$aField.value} {else} {phrase var="none"}  {/if}
    {elseif $aField.var_type=='select'}

                {if isset($aField.value) }
                {foreach from=$aField.value key=selected item=value_selected }
                {phrase var=$value_selected}<br>
                {/foreach}
                {else}
                {phrase var="none"}
                {/if}

    {elseif $aField.var_type=='multiselect'}
                {if isset($aField.value) }
                {foreach from=$aField.value key=selected item=value_selected }
                {phrase var=$value_selected}<br>
                {/foreach}
                {else}
                {phrase var="none"}
                {/if}

    {elseif $aField.var_type=='checkbox'}
                {if isset($aField.value) }
                {foreach from=$aField.value key=checked item=value_checked }
                {phrase var=$value_checked}<br>
                {/foreach}
                {else}
                {phrase var="none"}
                {/if}

    {elseif $aField.var_type=='radio'}
               {if isset($aField.value) }
                {foreach from=$aField.value key=checked item=value_checked }
                {phrase var=$value_checked}<br>
                {/foreach}
                {else}
                {phrase var="none"}
                {/if}
    {/if}
</div>