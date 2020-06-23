{if Phpfox::isModule('suggestion')}
    {module name="suggestion.people-you-may-know"}
{elseif Phpfox::isModule('userconnect')}
    {module name="userconnect.mayyouknow"}
{else}
    <ul>
        {foreach from=$aUsers name=users item=aUser}
            {template file='ynmember.block.entry_side'}
        {/foreach}
    </ul>
{/if}

