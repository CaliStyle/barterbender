<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="weekly_hot_auctions" class="weekly_hot_auctions_sliders dont-unbind-children flexslider">
    <ul class="slides ynauction_gridview">
        {foreach from=$aWeeklyHotAuctions item=aWeeklyHotAuction}
            {foreach from=$aWeeklyHotAuction item=aProduct name=auction}
            {template file='auction.block.listing-product-item-gridview-slide'}
            {/foreach}
        {/foreach}
    </ul>
</div>


{literal}
<script type="text/javascript" >
    $Behavior.weeklyHotSlideshow = function() {
        function buildHotAuctionSlider(){
            var container = $('#weekly_hot_auctions');
            var width = container.width();
            var itemWidth = 0;
            var itemMargin = 5;

            if(width > 700){
                itemWidth  = Math.ceil(width/3.0) - 5;
            }else if (width > 490   ){
                itemWidth  = Math.ceil(width/2.0) - 5;
            }else{
                itemWidth = width + 2;
            }

            container.flexslider({
                animation: "slide",
                animationLoop: false,
                itemWidth: itemWidth,
                itemMargin: itemMargin
            });
        }

        window.setTimeout(buildHotAuctionSlider, 1000);
    };
</script>
{/literal}