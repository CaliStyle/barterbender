<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="p-listing-container p-advmarketplace-seller-container col-4" data-mode-view="{$sModeViewDefault}">
    {foreach from=$aItems item=aItem}
        {template file='advancedmarketplace.block.entry_seller'}
    {/foreach}
</div>

