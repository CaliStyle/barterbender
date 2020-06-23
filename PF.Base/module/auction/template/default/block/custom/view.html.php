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
	    <div class="subsection_header">{phrase var=$sGroupName}</div>
	    {foreach from=$aFields item=aField}
		    {if isset($aField.value) && $aField.value != "" }
			    <div class="ynauction-detail-overview-custom-item">
			        <div class="item_label">
			            {phrase var=$aField.phrase_var_name}:
			        </div>
			        <div class="item_value">
			            {if $aField.var_type=='text'}
			                {if isset($aField.value) } {$aField.value} {else} {phrase var="auction.none"}  {/if} 
			            
			            {elseif $aField.var_type=='textarea'}
			               {if isset($aField.value) } {$aField.value} {else} {phrase var="auction.none"}  {/if} 
			            {elseif $aField.var_type=='select'}
			
			                        {if isset($aField.value) } 
			                        {foreach from=$aField.value key=selected item=value_selected }
			                        {phrase var=$value_selected}<br> 
			                        {/foreach}
			                        {else}
			                        {phrase var="auction.none"} 
			                        {/if}
			            
			            {elseif $aField.var_type=='multiselect'}
			                        {if isset($aField.value) } 
			                        {foreach from=$aField.value key=selected item=value_selected }
			                        {phrase var=$value_selected}<br> 
			                        {/foreach}
			                        {else}
			                        {phrase var="auction.none"} 
			                        {/if}
			            
			            {elseif $aField.var_type=='checkbox'}
			                        {if isset($aField.value) } 
			                        {foreach from=$aField.value key=checked item=value_checked }
			                        {phrase var=$value_checked}<br> 
			                        {/foreach}
			                        {else}
			                        {phrase var="auction.none"} 
			                        {/if}
			            
			            {elseif $aField.var_type=='radio'}
			                       {if isset($aField.value) } 
			                        {foreach from=$aField.value key=checked item=value_checked }
			                        {phrase var=$value_checked}<br> 
			                        {/foreach}
			                        {else}
			                        {phrase var="auction.none"} 
			                        {/if}
			            {/if}
			        </div>
			    </div>
		    {/if}
    	{/foreach}
    {/if}
{/foreach}
{/if}