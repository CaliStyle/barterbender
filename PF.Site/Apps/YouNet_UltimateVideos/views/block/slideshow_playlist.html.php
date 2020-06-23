<link href="{$corePath}/assets/jscript/owl-carousel/owl.carousel.css" rel='stylesheet' type='text/css'>

{if !empty($aItems)}
<div class="ultimatevideo_slider_featured-playlist">
	<div id="ultimatevideo_slider_featured-1" data-js="{$corePath}/assets/jscript/owl-carousel/owl.carousel.js" class="owl-carousel owl-theme">
	    {foreach from=$aItems name=video item=aItem}
	    {template file='ultimatevideo.block.entry_playlist_slideshow'}
	    {/foreach}
	</div>
	<div id="ultimatevideo_slider_featured-2" data-js="{$corePath}/assets/jscript/owl-carousel/owl.carousel.js" class="owl-carousel owl-theme">
	    {foreach from=$aItems name=video item=aItem}
	    {template file='ultimatevideo.block.entry_playlist_slideshow'}
	    {/foreach}
	</div>
</div>
{/if}
