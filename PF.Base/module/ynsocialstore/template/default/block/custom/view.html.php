{if count($aCustomFields)}
{foreach from=$aCustomFields key=sGroupName item=aFields}
	{php}
		$this->_aVars['isDisplayGroup'] = false;
	{/php}
	{foreach from=$aFields item=aField}
		{if isset($aField.value) && $aField.value != "" }
			{php}
				$this->_aVars['isDisplayGroup'] = true;
			{/php}
		{/if}
	{/foreach}
	{if $isDisplayGroup}
		<div class="ynstore-product-custom-field-block">
		    <div class="ynstore-cf-title subsection_header">{_p var=$sGroupName}</div>
		    <div class="panel-group" role="tablist">
		    {foreach from=$aFields item=aField}
			    {if isset($aField.value) && $aField.value != "" }
				    <div class="panel ynstore-detail-overview-custom-item">
				        <div class="panel-heading item_label" role="tab">
				        	<a role="button" class="collapsed" data-toggle="collapse" href="#collapse_{$aField.field_id}" aria-expanded="false" aria-controls="collapse_{$aField.field_id}">
				            	{_p var=$aField.phrase_var_name}:
				        	</a>
				        </div>

				       <div id="collapse_{$aField.field_id}" class="panel-collapse collapse" role="tabpanel">
				            {if $aField.var_type=='text'}
				                {if isset($aField.value) } {$aField.value} {else} {_p var="auction.none"}  {/if}
				            
				            {elseif $aField.var_type=='textarea'}
				               {if isset($aField.value) } {$aField.value} {else} {_p var="auction.none"}  {/if}
				            {elseif $aField.var_type=='select'}
				
				                        {if isset($aField.value) } 
				                        {foreach from=$aField.value key=selected item=value_selected }
				                        {_p var=$value_selected}<br>
				                        {/foreach}
				                        {else}
				                        {_p var="auction.none"}
				                        {/if}
				            
				            {elseif $aField.var_type=='multiselect'}
				                        {if isset($aField.value) } 
				                        {foreach from=$aField.value key=selected item=value_selected }
				                        {_p var=$value_selected}<br>
				                        {/foreach}
				                        {else}
				                        {_p var="auction.none"}
				                        {/if}
				            
				            {elseif $aField.var_type=='checkbox'}
				                        {if isset($aField.value) } 
				                        {foreach from=$aField.value key=checked item=value_checked }
				                        {_p var=$value_checked}<br>
				                        {/foreach}
				                        {else}
				                        {_p var="auction.none"}
				                        {/if}
				            
				            {elseif $aField.var_type=='radio'}
				                       {if isset($aField.value) } 
				                        {foreach from=$aField.value key=checked item=value_checked }
				                        {_p var=$value_checked}<br>
				                        {/foreach}
				                        {else}
				                        {_p var="auction.none"}
				                        {/if}
				            {/if}
				        </div>
				    </div>
			    {/if}
	    	{/foreach}
	    	</div>
		</div>
    {/if}
{/foreach}
{/if}