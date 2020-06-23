<?php
?>

<div class="p-advmarketplace-feature-container">
    <ul class="owl-carousel p-advmarketplace-slider-container" >
        {foreach from=$aItems key=iKey item=aListing}
			{template file="advancedmarketplace.block.entry_slider"}
		{/foreach}
    </ul>
    <div class="p-advmarketplace-slider-bottom dont-unbind-children">
        <div class="p-advmarketplace-slider-control-wrapper">
            <div class="advmarketplace_prev_slide p-advmarketplace-slider-nav-btn">
                <i class="ico ico-angle-left"></i>
            </div>
            <ul class='advmarketplace_carousel_custom_dots owl-dots'>
                <li class='owl-dot'></li>
            </ul>
            <div class="advmarketplace_next_slide p-advmarketplace-slider-nav-btn">
                <i class="ico ico-angle-right"></i>
            </div>
        </div>
    </div>
</div>
