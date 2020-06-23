{if !empty($aItems)}
    {if $sDataSource == 'featured' && $bIsSideLocation}
        <div class="p-item-flag-block">
            <div class="sticky-label-icon sticky-featured-icon" title="Featured">
                <span class="ico ico-diamond"></span>
                <span class="flag-style-arrow"></span>
            </div>
        </div>
    {/if}
    {if $sDataSource == 'sponsor_video' && $bIsSideLocation}
    <div class="p-item-flag-block">
        <div class="sticky-label-icon sticky-sponsored-icon" title="Sponsored">
            <span class="ico ico-sponsor"></span>
            <span class="flag-style-arrow"></span>
        </div>
    </div>
    {/if}
    {if isset($bIsSlider) && ($bIsSlider)}
        <div id="ultimatevideo_slider_video_container"
             class="dont-unbind-children  ultimatevideo-slider-video-container  {if count($aItems) > 1}owl-carousel ultimatevideo-slider-video-container-js multi-item {else} single-item {/if}">
            {foreach from=$aItems name=video item=aItem}
                <div class="item">
                    {template file='ultimatevideo.block.entry_video_slideshow'}
                </div>
            {/foreach}
        </div>
    {else}
        {module name='ynccore.mode_view'}
        <div class="p-listing-container col-4 {$sCustomContainerClassName}" data-mode-view="{$sModeViewDefault}">
            {foreach from=$aItems name=video item=aItem}
                {template file='ultimatevideo.block.entry'}
            {/foreach}
        </div>
    {/if}
{/if}
