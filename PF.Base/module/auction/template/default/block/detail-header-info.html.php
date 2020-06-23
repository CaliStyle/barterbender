<div class="ynauction_detail_page detail_header_info">
    <div class="header">{$aAuction.name|clean}</div>
    <div class="item_bar">
        <input type="hidden" name="val[auction_id]" value="{$aAuction.product_id}" id="auction_id" />
        <input type="hidden" name="ynauction_load_slider" value="1" id="ynauction_load_slider" />
        <input type="hidden" name="ynauction_cover_photos" value="{$iCoverPhotos}" id="ynauction_cover_photos" />
        {if $bShowAuctionFunctions}
        <div class="item_bar_action_holder">
            <a role="button" data-toggle="dropdown" class="item_bar_action"><span>{phrase var='actions'}</span>
                <i id="icon_edit" class="fa fa-edit" style="font-size:16px; margin:12px; color:#626262; position: absolute;top: 0"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                {if $bCanEditAuction}
                <li>
                    <a href="{url link='auction.edit' id=$aAuction.product_id}">
                        {phrase var='dashboard'}
                    </a>
                </li>
                {/if}

                {if $bCanApproveAuction && $aAuction.product_status == 'pending'}
                <li class="item_approved">
                    <a href="javascript:;" class="approve_product" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_approve_this_auction"}'{r}, function(){l}$.ajaxCall('auction.approveProduct', 'id={$aAuction.product_id}');{r}, function(){l}{r}); return false;">
                        {phrase var='approve'}
                    </a>
                </li>
                {/if}

                {if $bCanDenyAuction && $aAuction.product_status == 'pending'}
                <li class="item_denied">
                    <a href="javascript:;" class="deny_product" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_deny_this_auction"}'{r}, function(){l}$.ajaxCall('auction.denyProduct', 'id={$aAuction.product_id}');{r}, function(){l}{r}); return false;">
                        {phrase var='deny'}
                    </a>
                </li>
                {/if}

                {if $aAuction.user_id == Phpfox::getUserId() && $aAuction.product_status == 'denied'}
                <li class="item_publish">
                    <a href="javascript:;" class="publish_product" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_publish_this_auction"}'{r}, function(){l}$.ajaxCall('auction.publishProduct', 'id={$aAuction.product_id}');{r}, function(){l}{r}); return false;">
                        {phrase var='publish'}
                    </a>
                </li>
                {/if}

                {if $bCanDeleteAuction}
                {if $aAuction.product_status == 'draft' || $aAuction.product_status == 'pending' || $aAuction.product_status == 'approved' || $aAuction.product_status == 'denied' || $aAuction.product_status == 'running' || $aAuction.product_status == 'bidden'}
                <li class="item_delete">
                    <a href="javascript:;" class="delete_product" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_delete_this_auction"}'{r}, function(){l}$.ajaxCall('auction.deleteAuction', 'iProductId={$aAuction.product_id}');{r}, function(){l}{r}); return false;">
                        {phrase var='delete'}
                    </a>

                </li>
                {/if}
                {/if}

                {if $bCanCloseAuction}
                {if $aAuction.product_status == 'running' || $aAuction.product_status == 'bidden'}
                <li class="item_close">
                    <a href="javascript:;" class="close_product" onclick="$Core.jsConfirm({l} message : '{_p var="are_you_sure_you_want_to_close_this_auction1"}'{r}, function(){l}$.ajaxCall('auction.closeProduct', 'id={$aAuction.product_id}');{r}, function(){l}{r}); return false;">
                        {phrase var='close'}
                    </a>
                </li>
                {/if}
                {/if}

                {if $aAuction.user_id == Phpfox::getUserId()}
                <li class="item_clone">
                    <a href="{url link='auction.add' cloneid=$aAuction.product_id}">
                        {phrase var='clone'}
                    </a>
                </li>
                {/if}
            </ul>
        </div>
        {/if}
    </div>
    <div class="detail">
        <div class="ynauction-masterslider dont-unbind-children">
            <div class="masterslider-stage">

                <!-- masterslider -->
                <div class="master-slider ms-skin-default" id="masterslider">
                    {if $aDetailHeaderInfoImages}
                    {php}
						$aDetailHeaderInfoImages = $this->_aVars['aDetailHeaderInfoImages'];
					{/php}
					<?php $coverCount = count($aDetailHeaderInfoImages);?>
                    {foreach from=$aDetailHeaderInfoImages item=aDetailHeaderInfoImage}
                    <div class="ms-slide">
                        <img src="{img server_id=$aDetailHeaderInfoImage.server_id path='core.url_pic' file=$aDetailHeaderInfoImage.image_path suffix='_100' return_url=true}" data-src="{img server_id=$aDetailHeaderInfoImage.server_id path='core.url_pic' file=$aDetailHeaderInfoImage.image_path suffix=''  return_url=true}" alt="lorem ipsum dolor sit"/>
                        <img src="{img server_id=$aDetailHeaderInfoImage.server_id path='core.url_pic' file=$aDetailHeaderInfoImage.image_path suffix='_200' return_url=true}" alt="lorem ipsum dolor sit" class='ms-thumb'/>

						<?php if($coverCount > 1) :?>
	                        <a href="{img server_id=$aDetailHeaderInfoImage.server_id path='core.url_pic' file=$aDetailHeaderInfoImage.image_path suffix='_1024'  return_url=true}" class="ms-lightbox no_ajax" rel="prettyPhoto[ajax]" title="">
	                            <i class="fa fa-search fa-lg"></i>
	                        </a>
						<?php endif;?>
                    </div>
                    {/foreach}
                    {else}
                    <div class="ms-slide">
                        <img src="{$sCorePath}module/auction/static/image/default_ava_large.png" data-src="{$sCorePath }module/auction/static/image/default_ava_large.png" alt="lorem ipsum dolor sit"/>
                        <img src="{$sCorePath }module/auction/static/image/default_ava.png" alt="lorem ipsum dolor sit" class='ms-thumb'/>
                    </div>
                    {/if}
                </div>
                <!-- end of masterslider -->

            </div>
        </div>
        <div class="detail_info">
            {if $aAuction.start_time > PHPFOX_TIME}
                <div class="time_view">
                    {phrase var='start_time'}: {$aAuction.time_view|clean}
                </div>
            {elseif $aAuction.end_time <= PHPFOX_TIME}
            <div class="alert alert-warning"><strong>{phrase var='Warning!'} </strong>{phrase var='This auction has been completed'}</div>
            {/if}

            {if $aAuction.end_time > PHPFOX_TIME}
            <div class="countdown_holder" unix_timestamp="{if $aAuction.start_time > PHPFOX_TIME}{$aAuction.start_time}{else}{$aAuction.end_time}{/if}">
                <div class="countdown" id="defaultCountdown"></div>
            </div>
            {/if}
            <div class="detail_reserve_price_current_bid">
                {if !$aAuction.is_hide_reserve_price}
                <div class="detail_reserve_price">
                    <span class="item_label">{phrase var='reserve_price'}:</span> <span class="item_value">{$aAuction.sSymbolCurrency}{$aAuction.auction_item_reserve_price|number_format:2}</span>
                </div>
                {/if}
                <div class="detail_current_bid">
                    <span class="item_label">{phrase var='current_bid'}:</span>
                    <span id="detail_current_bid_value" class="item_value">{$aAuction.sSymbolCurrency}{$aAuction.auction_latest_bid_price|number_format:2}</span>
                </div>
            </div>
			
            {if ($aAuction.start_time < PHPFOX_TIME) }

                {if Phpfox::getUserId() != $aAuction.user_id && $isLiveAuction}
        			{if $bCanBidAuction && $isLiveAuction}
	        			{if !empty($sSuggestBidPrice)}
	        				<!-- check if has lastest bidder is not user -->
	        				{if ($aAuction.auction_latest_bidder != Phpfox::getUserId())}
			                    <div class="detail_bid_function">
			                        <div class="detail_bid_input">
			                            <div class="bid_field_group"><input id="bid_field_{$aAuction.product_id}" type="text" name="val[bid]" value="" class="bid_field"/></div>
			                                <div id="detail_bid_suggest_value" class="bid_suggest">{phrase var='enter_price_or_more' price=$sSuggestBidPrice}</div>
			                        </div>
			                        <div class="detail_bid_button">
			                            <div class="place_bid_loading_{$aAuction.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
			                            <button id="bid_button_{$aAuction.product_id}" type="button" name="val[place_bid]" class="btn btn-sm btn-primary" onclick="placeBid({$aAuction.product_id});">{phrase var='place_bid'}</button>
			                        </div>
			                    </div>
		                    {/if}
		                    <!-- end if -->
	                    {/if}
        			{/if}
        			
                    {if $bCanMakeOffer}
                        <div class="detail_offer_function">
                            <div class="detail_offer_input">
                                <div class="offer_field_group"><input id="offer_field_{$aAuction.product_id}" type="text" name="val[offer]" value="" class="offer_field"/></div>
                                {if $fSuggestOfferPrice > 0}
                                	<div class="offer_suggest">{phrase var='enter_price_or_more' price=$sSuggestOfferPrice}</div>
                                {/if}
                            </div>
                            <div class="detail_offer_button">
                                <div class="place_offer_loading_{$aAuction.product_id}" style="display: none;">{img theme='ajax/add.gif'}</div>
                                <button id="offer_button_{$aAuction.product_id}" type="button" name="val[make_offer]" class="btn btn-sm btn-warning" onclick="makeOffer({$aAuction.product_id});">{phrase var='make_offer'}</button>
                            </div>
                        </div>
                    {/if}
                {/if}
            {/if}
                {if Phpfox::getUserId() != $aAuction.user_id && $isLiveAuction}
                    {if $bCanBuyItNow}
                    <div class="detail_buy_now">
                        <div class="buy_now_title">
                            <span>{phrase var='buy_now_price'}</span>
                        </div>
                        <div class="buy_now_price">{$aAuction.sSymbolCurrency}{$aAuction.auction_item_buy_now_price|number_format:2}</div>
                        <div class="buy_now_button">
                            <button type="button" name="val[buy_now]" class="btn btn-sm btn-danger" onclick="buyItNow({$aAuction.product_id});">{phrase var='buy_it_now'}</button>
                        </div>
                    </div>
                    {/if}
                {/if}
                

            <div class="detail_bid_number_view_number">
                <div class="detail_bid_number">
                    <div class="bids"><span class="bid_icon"></span>{phrase var='bid_s'}</div>
                    <div id="detail_bid_number_value" class="bid_number">{$aAuction.auction_total_bid}</div>
                </div>
                <div class="detail_view_number">
                    <div class="views"><span class="view_icon"></span>{phrase var='views'}</div>
                    <div id="detail_view_number_value" class="view_number">{$aAuction.total_view}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">
if(!/undefined/i.test(typeof jQuery)){
        $('._block[data-location="2"]','#panel').remove();    
    }
{/literal}
</script>
{if $aAuction.end_time > PHPFOX_TIME}
    {literal}
    <script type="text/javascript">
	    (function(){
			var _stageCountdown = '#defaultCountdown',
			_requiredcountdown = function(){
				return !/undefined/i.test(typeof jQuery.countdown);
			},
			_initCountdownTime_flag = false,
			initCountdownTime = function (){
				var stageCountdown =  $(_stageCountdown);
				if(!stageCountdown.length) return;
				if(_initCountdownTime_flag) return;
				if(!_requiredcountdown()) return;
				ynauction.initCountdownTime();
				_initCountdownTime_flag = true;
			}
	
			$Behavior.featuredSlideshow = function() {
				function checkCondition(){
					var stageCountdown =  $(_stageCountdown);
					if(!stageCountdown.length) return;
					if(_initCountdownTime_flag) return;
					if(!_requiredcountdown()){
						window.setTimeout(checkCondition, 400);
					}
					else
					{
						initCountdownTime();
					}
				}
				window.setTimeout(checkCondition, 400);
			}	
		})();
    </script>
    {/literal}
{/if}

{literal}
<script type="text/javascript">

    function placeBid(iAuctionId)
    {
        $('#bid_button_' + iAuctionId).prop("disabled", true);
        $('.place_bid_loading_' + iAuctionId).show();
        
        var fBidValue = $("#bid_field_" + iAuctionId).val();
        
        $.ajaxCall('auction.placeBid', 'value=' + fBidValue + '&id=' + iAuctionId);
    }
    function makeOffer(iAuctionId)
    {
        $('#offer_button_' + iAuctionId).prop("disabled", true);
        $('.place_offer_loading_' + iAuctionId).show();
        
        var fOfferValue = $('#offer_field_' + iAuctionId).val();
        
        $.ajaxCall('auction.makeOffer', 'value=' + fOfferValue + '&id=' + iAuctionId);
    }

     function buyItNow(iProductId)
    {
        $('#offer_button_' + iProductId).prop("disabled", true);
        $('.place_offer_loading_' + iProductId).show();
                
        $.ajaxCall('auction.buyItNow', '&id=' + iProductId);
    }

	(function(){
		var
        _debug = true,
		_stageSlider = '#ynauction_load_slider',
		_required = function(){
			return !/undefined/i.test(typeof MasterSlider) 
			&& !/undefined/i.test(typeof jQuery.prettyPhoto);
		},

		_initAuctionDetailSlide_flag = false,
		initAuctionDetailSlide = function (){
			var stageSlider =  $(_stageSlider);
			if(!stageSlider.length) return;
			if(_initAuctionDetailSlide_flag) return;
			if(!_required()) return;
			
			if($('#ynauction_load_slider').val() == 1)
			{
				var slider = new MasterSlider();
	            var size = $('.ynauction-masterslider').width();
	            slider.setup('masterslider' , {
	                width: size,
	                height: size,
	                space: 5,
	                loop: true,
	                autoplay: true,
	                speed: 10,
	                view: 'fade'
	            });

	            slider.control('arrows');
	            slider.control('lightbox');
	            slider.control('thumblist' , {autohide: false ,dir:'h'});
	                
	            $('.ms-thumb-list.ms-dir-h').width(size - 60);
	
	            $('#ynauction_load_slider').val(0);
			    if($('#ynauction_cover_photos').val() <= 1){
	                $('.ms-ctrl-hide').hide();
	                $('.ms-thumb-list').hide();
	                $('.ynauction-masterslider').css('padding-bottom',0);
	            }
	        }

			_initAuctionDetailSlide_flag = true;
		},
		initAuctionPrettyPhoto = function()
		{
			jQuery("a[rel^='prettyPhoto']").prettyPhoto();
		}

		$Behavior.initAuctionDetailSlide = function() {
			function checkCondition(){
				var stageSlider =  $(_stageSlider);
				if(!stageSlider.length) return;
				if(_initAuctionDetailSlide_flag) return;
				if(!_required()){
					window.setTimeout(checkCondition, 400);
				}
				else
				{
					initAuctionDetailSlide();
				}
			}
			window.setTimeout(checkCondition, 400);
		}	
		
		
		$Behavior.initAuctionPrettyPhoto = function() {
			function checkPrettyPhoto(){
				if($('.ms-lightbox-btn').length == 0)
				{
					window.setTimeout(checkPrettyPhoto, 400);
				}
				else
				{
					initAuctionPrettyPhoto();
				}
			}
			window.setTimeout(checkPrettyPhoto, 400);
		}	
	})();


    $Behavior.initAuctionDetailMasterSlide = function(){
		function refreshInfo() {
			var id = $('#auction_id').val();
			$.ajaxCall('auction.refreshInfo', 'id=' + id);
		}
		
	    setInterval(refreshInfo, {/literal}{$refreshTime}{literal});
	}
</script>
{/literal}