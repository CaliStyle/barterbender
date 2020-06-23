<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 13:47
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsSlider}
    <div class="p-advblog-feature-container">
    <ul class="owl-carousel p-advblog-slider-container" id="ynadvblog_feature_main_item">
        {foreach from=$aItems item=aItem}
            <li class="item">
                {template file='ynblog.block.entry'}
            </li>
        {/foreach}
    </ul>
    <div class="p-advblog-slider-bottom dont-unbind-children">
        <div class="p-advblog-slider-control-wrapper">
            <div id="advblog_prev_slide" class="p-advblog-slider-nav-btn">
                <i class="ico ico-angle-left"></i>
            </div>
            <ul id='advblog_carousel_custom_dots' class='owl-dots'>
                <li class='owl-dot'></li>
            </ul>
            <div id="advblog_next_slide" class="p-advblog-slider-nav-btn">
                <i class="ico ico-angle-right"></i>
            </div>
        </div>
        {if !empty($aSliderFooter)}
            {foreach from=$aSliderFooter key=sSliderMoreLabel item=sSliderMoreLink}
                <div class="p-advblog-slider-more">
                    <a href="{$sSliderMoreLink}">{$sSliderMoreLabel}</a>
                </div>
            {/foreach}
        {/if}
    </div>
</div>
{else} <!-- if $bIsSlider -->
    {module name='ynccore.mode_view'}
    <div class="p-listing-container p-advblog-listing-container col-4 casual-col-3 p-mode-view" data-mode-view="{$sModeViewDefault}">
        {foreach from=$aItems name=ynblog item=aItem}
            {template file='ynblog.block.entry'}
        {/foreach}
    </div>
{/if}  <!-- if $bIsSlider -->
