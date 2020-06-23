<div class="block_listing_inline">
    <ul>
{foreach from=$aUsers name=loggedusers item=aUser}
    <li>
        {img user=$aUser suffix='_50_square' max_width=32 max_height=32}
    </li>
{/foreach}
    </ul>
    <div class="clear"></div>
</div>