<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="featured_auctions" class="ynauction_featuredSlider flexslider dont-unbind-children">
    <ul class="slides">
        {foreach from=$aFeaturedAuctions item=aAuction}
            <li class="ynauction-item-featured">
                <div class="ynauction-sw">
                    <span class="ynauction-featured-image" style="background-image: url(
                    {if isset($aAuction.logo_path)}
                        {img server_id=$aAuction.server_id path='core.url_pic' file=$aAuction.logo_path suffix='_400' return_url=true}
                    {else}
                        {$aAuction.default_logo_path}
                    {/if}
                    )">
                </span>
                    <div class="label_text">
                        <div class="ynauction-featured-title">
                            <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                                <span>{$aAuction.name|clean|shorten:75:'...'|split:75}</span>
                            </a>
                        </div>
                    </div>
                    <div class="ynauction-featured-info">
                        <div class="auction_bid_number ynauction-infocell">
                            <div class="info-label">{phrase var='bid_s'}</div>
                            <div class="info-bids">{$aAuction.auction_total_bid}</div>
                        </div>
                        <div class="auction_bid_current_bid ynauction-infocell">
                            <div class="info-label">{phrase var='current_bid'}</div>
                            <div class="info-price">
			                        {$aAuction.sSymbolCurrency}{$aAuction.auction_latest_bid_price|number_format:2}
                        	</div>
                        </div>
                        <div class="auction_bid_owner ynauction-infocell">
                            <div class="info-label">{phrase var='owner'}</div>
                            <div class="info-owner">
                                <a href="{url link=$aAuction.user_name}" title="{$aAuction.full_name|clean}">
                                    {$aAuction.full_name|clean|shorten:25:'...'|split:10}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="ynaunction-featred-bid-endtime">
                        <span class="info-label">{phrase var='end'}:</span> {$aAuction.end_time|date:'core.global_update_time'}
                    </div>
                </div>
            </li>
        {/foreach}
    </ul>
</div>
{literal}
<script type="text/javascript" >
    (function(){
		var _stage = '#featured_auctions',
		_options = {
			animation: "fade",
            slideshowSpeed: 3000,
            prevText: "",
            nextText: ""
		},
		_required = function(){
			return !/undefined/i.test(typeof jQuery.flexslider);
		},
		_initFeaturedSlideshow_flag = false,
		initFeaturedSlideshow = function (){
			var stage =  $(_stage);
			if(!stage.length) return;
			if(_initFeaturedSlideshow_flag) return;
			if(!_required()) return;
			_initFeaturedSlideshow_flag = true;
			$(_stage).flexslider(_options);
		}

		$Behavior.featuredSlideshow = function() {
			if(!$(_stage).length) return;
			function checkCondition(){
				var stage =  $(_stage);
				if(!stage.length) return;
				if(_initFeaturedSlideshow_flag) return;
				if(!_required()){
					window.setTimeout(checkCondition, 400);
				}else{
					initFeaturedSlideshow();
				}
			}
			window.setTimeout(checkCondition, 400);
		}
	})();
</script>
{/literal}