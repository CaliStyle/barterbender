{if $aField.var_type=='text'}
        {if !empty($aField.value) }
        <div class='table'>
            <div class="table_left view_application_title">
                {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
            </div>
            <div class="table_right">
                {$aField.value}
            </div>
        </div>
        {/if}
{/if}

{if $aField.var_type=='textarea'}
{if !empty($aField.value) }
<div class='table'>
    <div class="table_left view_application_title">
        {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
    </div>
    <div class="table_right">
        {$aField.value}
    </div>
</div>
{/if}
{/if}


{if $aField.var_type=='select'}
{if !empty($aField.value) }
<div class='table'>
    <div class="table_left view_application_title">
        {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
    </div>
    <div class="table_right">
        {if isset($aField.value) }
            {foreach from=$aField.value key=selected item=value_selected }
            {phrase var=$value_selected}<br>
            {/foreach}
        {/if}
    </div>
</div>
{/if}
{/if}



{if $aField.var_type=='multiselect'}
{if !empty($aField.value) }
<div class='table'>
    <div class="table_left view_application_title">
        {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
    </div>
    <div class="table_right">
        {if isset($aField.value) }
        {foreach from=$aField.value key=selected item=value_selected }
        {phrase var=$value_selected}<br>       
        {/foreach}
        {/if}
    </div>
</div>
{/if}
{/if}


{if $aField.var_type=='checkbox'}
{if !empty($aField.value) }
<div class='table'>
    <div class="table_left view_application_title">
        {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
    </div>
    <div class="table_right">
        {if isset($aField.value) }
            {foreach from=$aField.value key=checked item=value_checked }
            {phrase var=$value_checked}<br>
            {/foreach}
        {/if}
    </div>
</div>
{/if}
{/if}


{if $aField.var_type=='radio'}
{if !empty($aField.value)}
<div class='table'>
    <div class="table_left view_application_title">
        {if $aField.is_required==1}{required}{/if}{phrase var=$aField.phrase_var_name}
    </div>
    <div class="table_right">
        {if isset($aField.value)}
        {foreach from=$aField.value key=checked item=value_checked }
        {phrase var=$value_checked}<br>
        {/foreach}

        {/if}
    </div>
</div>
{/if}
{/if}

 