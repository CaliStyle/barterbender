<link href="{$corePath}/assets/jscript/owl-carousel/owl.carousel.css" rel='stylesheet' type='text/css'>

{if !empty($aItems)}
<div id="ultimatevideo_slider_featured" data-js="{$corePath}/assets/jscript/owl-carousel/owl.carousel.js" class="owl-carousel owl-theme">
    {foreach from=$aItems name=video item=aItem}
    	{template file='ultimatevideo.block.entry_video_slideshow'}
    {/foreach}
</div>
{/if}
