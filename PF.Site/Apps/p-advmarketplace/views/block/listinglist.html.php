<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $dataSource == 'sponsored' && $isSideLocation}
<div class="p-item-flag-block">
    <div class="sticky-label-icon sticky-sponsored-icon" title="{_p var='sponsored'}">
        <span class="ico ico-sponsor"></span>
        <span class="flag-style-arrow"></span>
    </div>
</div>
{/if}
{if $dataSource == 'featured' && $isSideLocation}
<div class="p-item-flag-block">
    <div class="sticky-label-icon sticky-featured-icon" title="{_p var='sponsored'}">
        <span class="ico ico-diamond"></span>
        <span class="flag-style-arrow"></span>
    </div>
</div>
{/if}
{if $isSlider}
    {template file="advancedmarketplace.block.listingslideshow"}
{else}
    <div class="p-listing-container p-advmarketplace-listing-container {$pCustomClassName}" data-mode-view="{$sModeViewDefault}">
        {foreach from=$aItems key=iKey item=aListing}
            {template file="advancedmarketplace.block.entry"}
        {/foreach}
    </div>
{/if}