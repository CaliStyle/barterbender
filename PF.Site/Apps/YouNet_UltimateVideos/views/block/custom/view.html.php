{if count($aCustomFields)}
    <div class="ultimatevideo-panel-group" id="ultimatevideo-accordion" role="tablist" aria-multiselectable="true">
        {foreach from=$aCustomFields key=aKey item=aField}
            {if isset($aField.value) }
                <div class="ultimatevideo-detail-overview-additional-item">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne-{$aKey}"
                       aria-expanded="true" aria-controls="collapseOne" class="">
                        {phrase var=$aField.phrase_var_name}:
                    </a>
                    <div id="collapseOne-{$aKey}" class="panel-collapse collapse in" role="tabpanel"
                         aria-labelledby="headingOne" aria-expanded="true">
                        {if $aField.var_type=='text'}
                            {if isset($aField.value) } {$aField.value} {else} {_p('None')}   {/if}

                        {elseif $aField.var_type=='textarea'}
                            {if isset($aField.value) } {$aField.value} {else} {_p('None')}   {/if}
                        {elseif $aField.var_type=='select'}

                            {if isset($aField.value) }
                                {foreach from=$aField.value key=selected item=value_selected }
                                    {phrase var=$value_selected}
                                    <br>
                                {/foreach}
                            {else}
                                {_p('None')}
                            {/if}

                        {elseif $aField.var_type=='multiselect'}
                            {if isset($aField.value) }
                                {foreach from=$aField.value key=selected item=value_selected }
                                    {phrase var=$value_selected}
                                    <br>
                                {/foreach}
                            {else}
                                {_p('None')}
                            {/if}

                        {elseif $aField.var_type=='checkbox'}
                            {if isset($aField.value) }
                                {foreach from=$aField.value key=checked item=value_checked }
                                    {phrase var=$value_checked}
                                    <br>
                                {/foreach}
                            {else}
                                {_p('None')}
                            {/if}

                        {elseif $aField.var_type=='radio'}
                            {if isset($aField.value) }
                                {foreach from=$aField.value key=checked item=value_checked }
                                    {phrase var=$value_checked}
                                    <br>
                                {/foreach}
                            {else}
                                {_p('None')}
                            {/if}
                        {/if}
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
{/if}