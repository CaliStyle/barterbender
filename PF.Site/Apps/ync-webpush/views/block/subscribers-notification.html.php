<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div style="max-height: 500px;overflow-y: auto">
    <ul>
        {foreach from=$aUsers item=aUser}
            <li style="display: flex;align-items: center" class="p-1">
                <span class="mr-1">{img user=$aUser suffix='_50_square' max_width=50 max_height=50}</span>
                <span>{$aUser|user:'':'':100}</span>
            </li>
        {/foreach}
    </ul>
</div>