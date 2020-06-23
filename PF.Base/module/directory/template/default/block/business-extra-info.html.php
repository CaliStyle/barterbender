{if count($aInfo)}
    {foreach from=$aInfo item=iItem}
    	{if $iItem.type == 'website'}
            <div><a href="{$iItem.link}" target="_blank">{$iItem.text}</a></div>                        
    	{elseif $iItem.type == 'location' }
			{*<div> <a href="https://maps.google.com/maps?daddr={$iItem.lat},{$iItem.lng}" target="_blank">{$iItem.text}</a></div>*}
            <div> <a href="https://maps.google.com/maps?daddr={$iItem.text_location}" target="_blank">{$iItem.text}</a></div>

    	{else}
            <div>{$iItem.text}</div>    
    	{/if}
    {/foreach}
{else}
	{phrase var='no_information'}
{/if} 