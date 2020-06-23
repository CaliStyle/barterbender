{if $bIsSideLocation}
    <!-- side block -->
    <div class="dont-unbind-children p-advblog-category-container p-listing-container" data-mode-view="list">
        {foreach from=$aCategories item=aCategory}
            {template file='ynblog.block.entry_category'}
        {/foreach}
    </div>
{else}
<!-- middle block -->
    <div class="p-advblog-category-wrapper">
        <div class="dont-unbind-children p-advblog-category-container owl-carousel p-advblog-slider-category-container-js p-listing-container">
            {foreach from=$aCategories item=aCategory}
                {template file='ynblog.block.entry_category'}
            {/foreach}
        </div>
        <div class="p-advblog-slider-category-bottom">
            <div class="p-advblog-slider-control-wrapper">
                <div id="advblog_category_prev_slide" class="p-advblog-slider-nav-btn">
                    <i class="ico ico-angle-left"></i>
                </div>
                <ul id='advblog_category_carousel_custom_dots' class='owl-dots'>
                    <li class='owl-dot'></li>
                </ul>
                <div id="advblog_category_next_slide" class="p-advblog-slider-nav-btn">
                    <i class="ico ico-angle-right"></i>
                </div>
            </div>
        </div>
    </div>
{/if}
