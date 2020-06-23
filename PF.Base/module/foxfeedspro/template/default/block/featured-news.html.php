<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02p5
 * 
 */
?>
<script type="text/javascript" src="{$sCorePath}module/foxfeedspro/static/jscript/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="{$sCorePath}module/foxfeedspro/static/jscript/owl.carousel.min.js"></script>


<div id="foxfeeds_featured" class="owl-carousel owl-theme">
	{foreach from=$aRenderItems item=itemsList}
		<div class="items items-{$itemsList.count}">
			{foreach from=$itemsList.items item=aNews}
				{template file='foxfeedspro.block.featured-news-items'}
			{/foreach}
		</div>
	{/foreach}
</div>

{literal}
<script type="text/javascript">
if(!/undefined/i.test(typeof jQuery)){
    true;
	$('._block[data-location="2"]','#panel').remove(); 
}
{/literal}
</script>

{literal}
<script type="text/javascript">
	$Behavior.FoxFeedsFeaturedSlider = function() {
	    if($(".owl-wrapper-outer").length == 0){
    		//console.log($("#foxfeeds_featured"));
    		$("#foxfeeds_featured").owlCarousel({
    			navigation : true, // Show next and prev buttons
    			slideSpeed : 300,
    			paginationSpeed : 400,
    			singleItem:true,
    			autoPlay: true,
    		});
		}
		else{
		 $("#foxfeeds_featured").data("owl-init", 0);
		 $(".owl-item .items").clone().appendTo("#foxfeeds_featured");
		 $("#foxfeeds_featured").removeAttr("style"); 
		 $(".owl-wrapper-outer").remove();
		 $(".owl-controls").remove();
		 //console.log($("#foxfeeds_featured"));
         $("#foxfeeds_featured").owlCarousel({
                navigation : true, // Show next and prev buttons
                slideSpeed : 300,
                paginationSpeed : 400,
                singleItem:true,
                autoPlay: true,
            });
		}
	};
</script>
{/literal}
