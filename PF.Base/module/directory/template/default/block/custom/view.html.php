{if count($aCustomFields)}
{foreach from=$aCustomFields item=aField}
{if isset($aField.value) }
<div class="yndirectory-detail-overview-additional-item">
    <div>
        {phrase var=$aField.phrase_var_name}:
    </div>
    <div>
        {if $aField.var_type=='text'}
            {if isset($aField.value) } {$aField.value} {else} {phrase var="directory.none"}  {/if} 
        
        {elseif $aField.var_type=='textarea'}
           {if isset($aField.value) } {$aField.value} {else} {phrase var="directory.none"}  {/if} 
        {elseif $aField.var_type=='select'}

                    {if isset($aField.value) } 
                    {foreach from=$aField.value key=selected item=value_selected }
                    {phrase var=$value_selected}<br> 
                    {/foreach}
                    {else}
                    {phrase var="directory.none"} 
                    {/if}
        
        {elseif $aField.var_type=='multiselect'}
                    {if isset($aField.value) } 
                    {foreach from=$aField.value key=selected item=value_selected }
                    {phrase var=$value_selected}<br> 
                    {/foreach}
                    {else}
                    {phrase var="directory.none"} 
                    {/if}
        
        {elseif $aField.var_type=='checkbox'}
                    {if isset($aField.value) } 
                    {foreach from=$aField.value key=checked item=value_checked }
                    {phrase var=$value_checked}<br> 
                    {/foreach}
                    {else}
                    {phrase var="directory.none"} 
                    {/if}
        
        {elseif $aField.var_type=='radio'}
                   {if isset($aField.value) } 
                    {foreach from=$aField.value key=checked item=value_checked }
                    {phrase var=$value_checked}<br> 
                    {/foreach}
                    {else}
                    {phrase var="directory.none"} 
                    {/if}
        {/if}
    </div>
</div>
{/if}
{/foreach}
{/if}