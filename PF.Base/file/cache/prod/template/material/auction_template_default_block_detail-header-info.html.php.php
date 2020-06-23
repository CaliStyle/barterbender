<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: June 1, 2020, 8:35 pm */ ?>
<?php

?>

<?php if (! isset ( $this->_aVars['sHidden'] )):  $this->assign('sHidden', '');  endif; ?>

<?php if (( isset ( $this->_aVars['sHeader'] ) && ( ! PHPFOX_IS_AJAX || isset ( $this->_aVars['bPassOverAjaxCall'] ) || isset ( $this->_aVars['bIsAjaxLoader'] ) ) ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>

<div class="<?php echo $this->_aVars['sHidden']; ?> block<?php if (( defined ( 'PHPFOX_IN_DESIGN_MODE' ) ) && ( ! isset ( $this->_aVars['bCanMove'] ) || ( isset ( $this->_aVars['bCanMove'] ) && $this->_aVars['bCanMove'] == true ) )): ?> js_sortable<?php endif;  if (isset ( $this->_aVars['sCustomClassName'] )): ?> <?php echo $this->_aVars['sCustomClassName'];  endif; ?>"<?php if (isset ( $this->_aVars['sBlockBorderJsId'] )): ?> id="js_block_border_<?php echo $this->_aVars['sBlockBorderJsId']; ?>"<?php endif;  if (defined ( 'PHPFOX_IN_DESIGN_MODE' ) && Phpfox_Module ::instance()->blockIsHidden('js_block_border_' . $this->_aVars['sBlockBorderJsId'] . '' )): ?> style="display:none;"<?php endif; ?> data-toggle="<?php echo $this->_aVars['sToggleWidth']; ?>">
<?php if (! empty ( $this->_aVars['sHeader'] ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>
		<div class="title <?php if (defined ( 'PHPFOX_IN_DESIGN_MODE' )): ?>js_sortable_header<?php endif; ?>">
<?php if (isset ( $this->_aVars['sBlockTitleBar'] )): ?>
<?php echo $this->_aVars['sBlockTitleBar']; ?>
<?php endif; ?>
<?php if (( isset ( $this->_aVars['aEditBar'] ) && Phpfox ::isUser())): ?>
			<div class="js_edit_header_bar">
				<a href="#" title="<?php echo _p('edit_this_block'); ?>" onclick="$.ajaxCall('<?php echo $this->_aVars['aEditBar']['ajax_call']; ?>', 'block_id=<?php echo $this->_aVars['sBlockBorderJsId'];  if (isset ( $this->_aVars['aEditBar']['params'] )):  echo $this->_aVars['aEditBar']['params'];  endif; ?>'); return false;">
					<span class="ico ico-pencilline-o"></span>
				</a>
			</div>
<?php endif; ?>
<?php if (empty ( $this->_aVars['sHeader'] )): ?>
<?php echo $this->_aVars['sBlockShowName']; ?>
<?php else: ?>
<?php echo $this->_aVars['sHeader']; ?>
<?php endif; ?>
		</div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aEditBar'] )): ?>
	<div id="js_edit_block_<?php echo $this->_aVars['sBlockBorderJsId']; ?>" class="edit_bar hidden"></div>
<?php endif; ?>
<?php if (isset ( $this->_aVars['aMenu'] ) && count ( $this->_aVars['aMenu'] )): ?>
<?php unset($this->_aVars['aMenu']); ?>
<?php endif; ?>
	<div class="content"<?php if (isset ( $this->_aVars['sBlockJsId'] )): ?> id="js_block_content_<?php echo $this->_aVars['sBlockJsId']; ?>"<?php endif; ?>>
<?php endif; ?>
		<div class="ynauction_detail_page detail_header_info">
    <div class="header"><?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aAuction']['name']); ?></div>
    <div class="item_bar">
        <input type="hidden" name="val[auction_id]" value="<?php echo $this->_aVars['aAuction']['product_id']; ?>" id="auction_id" />
        <input type="hidden" name="ynauction_load_slider" value="1" id="ynauction_load_slider" />
        <input type="hidden" name="ynauction_cover_photos" value="<?php echo $this->_aVars['iCoverPhotos']; ?>" id="ynauction_cover_photos" />
<?php if ($this->_aVars['bShowAuctionFunctions']): ?>
        <div class="item_bar_action_holder">
            <a role="button" data-toggle="dropdown" class="item_bar_action"><span><?php echo _p('actions'); ?></span>
                <i id="icon_edit" class="fa fa-edit" style="font-size:16px; margin:12px; color:#626262; position: absolute;top: 0"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
<?php if ($this->_aVars['bCanEditAuction']): ?>
                <li>
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('auction.edit', array('id' => $this->_aVars['aAuction']['product_id'])); ?>">
<?php echo _p('dashboard'); ?>
                    </a>
                </li>
<?php endif; ?>

<?php if ($this->_aVars['bCanApproveAuction'] && $this->_aVars['aAuction']['product_status'] == 'pending'): ?>
                <li class="item_approved">
                    <a href="javascript:;" class="approve_product" onclick="$Core.jsConfirm({ message : '<?php echo _p("are_you_sure_you_want_to_approve_this_auction"); ?>'}, function(){$.ajaxCall('auction.approveProduct', 'id=<?php echo $this->_aVars['aAuction']['product_id']; ?>');}, function(){}); return false;">
<?php echo _p('approve'); ?>
                    </a>
                </li>
<?php endif; ?>

<?php if ($this->_aVars['bCanDenyAuction'] && $this->_aVars['aAuction']['product_status'] == 'pending'): ?>
                <li class="item_denied">
                    <a href="javascript:;" class="deny_product" onclick="$Core.jsConfirm({ message : '<?php echo _p("are_you_sure_you_want_to_deny_this_auction"); ?>'}, function(){$.ajaxCall('auction.denyProduct', 'id=<?php echo $this->_aVars['aAuction']['product_id']; ?>');}, function(){}); return false;">
<?php echo _p('deny'); ?>
                    </a>
                </li>
<?php endif; ?>

<?php if ($this->_aVars['aAuction']['user_id'] == Phpfox ::getUserId() && $this->_aVars['aAuction']['product_status'] == 'denied'): ?>
                <li class="item_publish">
                    <a href="javascript:;" class="publish_product" onclick="$Core.jsConfirm({ message : '<?php echo _p("are_you_sure_you_want_to_publish_this_auction"); ?>'}, function(){$.ajaxCall('auction.publishProduct', 'id=<?php echo $this->_aVars['aAuction']['product_id']; ?>');}, function(){}); return false;">
<?php echo _p('publish'); ?>
                    </a>
                </li>
<?php endif; ?>

<?php if ($this->_aVars['bCanDeleteAuction']): ?>
<?php if ($this->_aVars['aAuction']['product_status'] == 'draft' || $this->_aVars['aAuction']['product_status'] == 'pending' || $this->_aVars['aAuction']['product_status'] == 'approved' || $this->_aVars['aAuction']['product_status'] == 'denied' || $this->_aVars['aAuction']['product_status'] == 'running' || $this->_aVars['aAuction']['product_status'] == 'bidden'): ?>
                <li class="item_delete">
                    <a href="javascript:;" class="delete_product" onclick="$Core.jsConfirm({ message : '<?php echo _p("are_you_sure_you_want_to_delete_this_auction"); ?>'}, function(){$.ajaxCall('auction.deleteAuction', 'iProductId=<?php echo $this->_aVars['aAuction']['product_id']; ?>');}, function(){}); return false;">
<?php echo _p('delete'); ?>
                    </a>

                </li>
<?php endif; ?>
<?php endif; ?>

<?php if ($this->_aVars['bCanCloseAuction']): ?>
<?php if ($this->_aVars['aAuction']['product_status'] == 'running' || $this->_aVars['aAuction']['product_status'] == 'bidden'): ?>
                <li class="item_close">
                    <a href="javascript:;" class="close_product" onclick="$Core.jsConfirm({ message : '<?php echo _p("are_you_sure_you_want_to_close_this_auction1"); ?>'}, function(){$.ajaxCall('auction.closeProduct', 'id=<?php echo $this->_aVars['aAuction']['product_id']; ?>');}, function(){}); return false;">
<?php echo _p('close'); ?>
                    </a>
                </li>
<?php endif; ?>
<?php endif; ?>

<?php if ($this->_aVars['aAuction']['user_id'] == Phpfox ::getUserId()): ?>
                <li class="item_clone">
                    <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('auction.add', array('cloneid' => $this->_aVars['aAuction']['product_id'])); ?>">
<?php echo _p('clone'); ?>
                    </a>
                </li>
<?php endif; ?>
            </ul>
        </div>
<?php endif; ?>
    </div>
    <div class="detail">
        <div class="ynauction-masterslider dont-unbind-children">
            <div class="masterslider-stage">

                <!-- masterslider -->
                <div class="master-slider ms-skin-default" id="masterslider">
<?php if ($this->_aVars['aDetailHeaderInfoImages']): ?>
                    <?php 
						$aDetailHeaderInfoImages = $this->_aVars['aDetailHeaderInfoImages'];
					 ?>
<?php $coverCount = count($aDetailHeaderInfoImages);?>
<?php if (count((array)$this->_aVars['aDetailHeaderInfoImages'])):  foreach ((array) $this->_aVars['aDetailHeaderInfoImages'] as $this->_aVars['aDetailHeaderInfoImage']): ?>
                    <div class="ms-slide">
                        <img src="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aDetailHeaderInfoImage']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aDetailHeaderInfoImage']['image_path'],'suffix' => '_100','return_url' => true)); ?>" data-src="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aDetailHeaderInfoImage']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aDetailHeaderInfoImage']['image_path'],'suffix' => '','return_url' => true)); ?>" alt="lorem ipsum dolor sit"/>
                        <img src="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aDetailHeaderInfoImage']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aDetailHeaderInfoImage']['image_path'],'suffix' => '_200','return_url' => true)); ?>" alt="lorem ipsum dolor sit" class='ms-thumb'/>

<?php if($coverCount > 1) :?>
	                        <a href="<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('server_id' => $this->_aVars['aDetailHeaderInfoImage']['server_id'],'path' => 'core.url_pic','file' => $this->_aVars['aDetailHeaderInfoImage']['image_path'],'suffix' => '_1024','return_url' => true)); ?>" class="ms-lightbox no_ajax" rel="prettyPhoto[ajax]" title="">
	                            <i class="fa fa-search fa-lg"></i>
	                        </a>
<?php endif;?>
                    </div>
<?php endforeach; endif; ?>
<?php else: ?>
                    <div class="ms-slide">
                        <img src="<?php echo $this->_aVars['sCorePath']; ?>module/auction/static/image/default_ava_large.png" data-src="<?php echo $this->_aVars['sCorePath']; ?>module/auction/static/image/default_ava_large.png" alt="lorem ipsum dolor sit"/>
                        <img src="<?php echo $this->_aVars['sCorePath']; ?>module/auction/static/image/default_ava.png" alt="lorem ipsum dolor sit" class='ms-thumb'/>
                    </div>
<?php endif; ?>
                </div>
                <!-- end of masterslider -->

            </div>
        </div>
        <div class="detail_info">
<?php if ($this->_aVars['aAuction']['start_time'] > PHPFOX_TIME): ?>
                <div class="time_view">
<?php echo _p('start_time'); ?>: <?php echo Phpfox::getLib('phpfox.parse.output')->clean($this->_aVars['aAuction']['time_view']); ?>
                </div>
<?php elseif ($this->_aVars['aAuction']['end_time'] <= PHPFOX_TIME): ?>
            <div class="alert alert-warning"><strong><?php echo _p('Warning!'); ?> </strong><?php echo _p('This auction has been completed'); ?></div>
<?php endif; ?>

<?php if ($this->_aVars['aAuction']['end_time'] > PHPFOX_TIME): ?>
            <div class="countdown_holder" unix_timestamp="<?php if ($this->_aVars['aAuction']['start_time'] > PHPFOX_TIME):  echo $this->_aVars['aAuction']['start_time'];  else:  echo $this->_aVars['aAuction']['end_time'];  endif; ?>">
                <div class="countdown" id="defaultCountdown"></div>
            </div>
<?php endif; ?>
            <div class="detail_reserve_price_current_bid">
<?php if (! $this->_aVars['aAuction']['is_hide_reserve_price']): ?>
                <div class="detail_reserve_price">
                    <span class="item_label"><?php echo _p('reserve_price'); ?>:</span> <span class="item_value"><?php echo $this->_aVars['aAuction']['sSymbolCurrency'];  echo number_format($this->_aVars['aAuction']['auction_item_reserve_price'], 2); ?></span>
                </div>
<?php endif; ?>
                <div class="detail_current_bid">
                    <span class="item_label"><?php echo _p('current_bid'); ?>:</span>
                    <span id="detail_current_bid_value" class="item_value"><?php echo $this->_aVars['aAuction']['sSymbolCurrency'];  echo number_format($this->_aVars['aAuction']['auction_latest_bid_price'], 2); ?></span>
                </div>
            </div>
			
<?php if (( $this->_aVars['aAuction']['start_time'] < PHPFOX_TIME )): ?>

<?php if (Phpfox ::getUserId() != $this->_aVars['aAuction']['user_id'] && $this->_aVars['isLiveAuction']): ?>
<?php if ($this->_aVars['bCanBidAuction'] && $this->_aVars['isLiveAuction']): ?>
<?php if (! empty ( $this->_aVars['sSuggestBidPrice'] )): ?>
	        				<!-- check if has lastest bidder is not user -->
<?php if (( $this->_aVars['aAuction']['auction_latest_bidder'] != Phpfox ::getUserId())): ?>
			                    <div class="detail_bid_function">
			                        <div class="detail_bid_input">
			                            <div class="bid_field_group"><input id="bid_field_<?php echo $this->_aVars['aAuction']['product_id']; ?>" type="text" name="val[bid]" value="" class="bid_field"/></div>
			                                <div id="detail_bid_suggest_value" class="bid_suggest"><?php echo _p('enter_price_or_more', array('price' => $this->_aVars['sSuggestBidPrice'])); ?></div>
			                        </div>
			                        <div class="detail_bid_button">
			                            <div class="place_bid_loading_<?php echo $this->_aVars['aAuction']['product_id']; ?>" style="display: none;"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif')); ?></div>
			                            <button id="bid_button_<?php echo $this->_aVars['aAuction']['product_id']; ?>" type="button" name="val[place_bid]" class="btn btn-sm btn-primary" onclick="placeBid(<?php echo $this->_aVars['aAuction']['product_id']; ?>);"><?php echo _p('place_bid'); ?></button>
			                        </div>
			                    </div>
<?php endif; ?>
		                    <!-- end if -->
<?php endif; ?>
<?php endif; ?>
        			
<?php if ($this->_aVars['bCanMakeOffer']): ?>
                        <div class="detail_offer_function">
                            <div class="detail_offer_input">
                                <div class="offer_field_group"><input id="offer_field_<?php echo $this->_aVars['aAuction']['product_id']; ?>" type="text" name="val[offer]" value="" class="offer_field"/></div>
<?php if ($this->_aVars['fSuggestOfferPrice'] > 0): ?>
                                	<div class="offer_suggest"><?php echo _p('enter_price_or_more', array('price' => $this->_aVars['sSuggestOfferPrice'])); ?></div>
<?php endif; ?>
                            </div>
                            <div class="detail_offer_button">
                                <div class="place_offer_loading_<?php echo $this->_aVars['aAuction']['product_id']; ?>" style="display: none;"><?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif')); ?></div>
                                <button id="offer_button_<?php echo $this->_aVars['aAuction']['product_id']; ?>" type="button" name="val[make_offer]" class="btn btn-sm btn-warning" onclick="makeOffer(<?php echo $this->_aVars['aAuction']['product_id']; ?>);"><?php echo _p('make_offer'); ?></button>
                            </div>
                        </div>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<?php if (Phpfox ::getUserId() != $this->_aVars['aAuction']['user_id'] && $this->_aVars['isLiveAuction']): ?>
<?php if ($this->_aVars['bCanBuyItNow']): ?>
                    <div class="detail_buy_now">
                        <div class="buy_now_title">
                            <span><?php echo _p('buy_now_price'); ?></span>
                        </div>
                        <div class="buy_now_price"><?php echo $this->_aVars['aAuction']['sSymbolCurrency'];  echo number_format($this->_aVars['aAuction']['auction_item_buy_now_price'], 2); ?></div>
                        <div class="buy_now_button">
                            <button type="button" name="val[buy_now]" class="btn btn-sm btn-danger" onclick="buyItNow(<?php echo $this->_aVars['aAuction']['product_id']; ?>);"><?php echo _p('buy_it_now'); ?></button>
                        </div>
                    </div>
<?php endif; ?>
<?php endif; ?>
                

            <div class="detail_bid_number_view_number">
                <div class="detail_bid_number">
                    <div class="bids"><span class="bid_icon"></span><?php echo _p('bid_s'); ?></div>
                    <div id="detail_bid_number_value" class="bid_number"><?php echo $this->_aVars['aAuction']['auction_total_bid']; ?></div>
                </div>
                <div class="detail_view_number">
                    <div class="views"><span class="view_icon"></span><?php echo _p('views'); ?></div>
                    <div id="detail_view_number_value" class="view_number"><?php echo $this->_aVars['aAuction']['total_view']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo '
<script type="text/javascript">
if(!/undefined/i.test(typeof jQuery)){
        $(\'._block[data-location="2"]\',\'#panel\').remove();    
    }
'; ?>

</script>
<?php if ($this->_aVars['aAuction']['end_time'] > PHPFOX_TIME): ?>
    <?php echo '
    <script type="text/javascript">
	    (function(){
			var _stageCountdown = \'#defaultCountdown\',
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
    '; ?>

<?php endif; ?>

<?php echo '
<script type="text/javascript">

    function placeBid(iAuctionId)
    {
        $(\'#bid_button_\' + iAuctionId).prop("disabled", true);
        $(\'.place_bid_loading_\' + iAuctionId).show();
        
        var fBidValue = $("#bid_field_" + iAuctionId).val();
        
        $.ajaxCall(\'auction.placeBid\', \'value=\' + fBidValue + \'&id=\' + iAuctionId);
    }
    function makeOffer(iAuctionId)
    {
        $(\'#offer_button_\' + iAuctionId).prop("disabled", true);
        $(\'.place_offer_loading_\' + iAuctionId).show();
        
        var fOfferValue = $(\'#offer_field_\' + iAuctionId).val();
        
        $.ajaxCall(\'auction.makeOffer\', \'value=\' + fOfferValue + \'&id=\' + iAuctionId);
    }

     function buyItNow(iProductId)
    {
        $(\'#offer_button_\' + iProductId).prop("disabled", true);
        $(\'.place_offer_loading_\' + iProductId).show();
                
        $.ajaxCall(\'auction.buyItNow\', \'&id=\' + iProductId);
    }

	(function(){
		var
        _debug = true,
		_stageSlider = \'#ynauction_load_slider\',
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
			
			if($(\'#ynauction_load_slider\').val() == 1)
			{
				var slider = new MasterSlider();
	            var size = $(\'.ynauction-masterslider\').width();
	            slider.setup(\'masterslider\' , {
	                width: size,
	                height: size,
	                space: 5,
	                loop: true,
	                autoplay: true,
	                speed: 10,
	                view: \'fade\'
	            });

	            slider.control(\'arrows\');
	            slider.control(\'lightbox\');
	            slider.control(\'thumblist\' , {autohide: false ,dir:\'h\'});
	                
	            $(\'.ms-thumb-list.ms-dir-h\').width(size - 60);
	
	            $(\'#ynauction_load_slider\').val(0);
			    if($(\'#ynauction_cover_photos\').val() <= 1){
	                $(\'.ms-ctrl-hide\').hide();
	                $(\'.ms-thumb-list\').hide();
	                $(\'.ynauction-masterslider\').css(\'padding-bottom\',0);
	            }
	        }

			_initAuctionDetailSlide_flag = true;
		},
		initAuctionPrettyPhoto = function()
		{
			jQuery("a[rel^=\'prettyPhoto\']").prettyPhoto();
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
				if($(\'.ms-lightbox-btn\').length == 0)
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
			var id = $(\'#auction_id\').val();
			$.ajaxCall(\'auction.refreshInfo\', \'id=\' + id);
		}
		
	    setInterval(refreshInfo, ';  echo $this->_aVars['refreshTime'];  echo ');
	}
</script>
'; ?>




<?php if (( isset ( $this->_aVars['sHeader'] ) && ( ! PHPFOX_IS_AJAX || isset ( $this->_aVars['bPassOverAjaxCall'] ) || isset ( $this->_aVars['bIsAjaxLoader'] ) ) ) || ( defined ( "PHPFOX_IN_DESIGN_MODE" ) && PHPFOX_IN_DESIGN_MODE )): ?>
	</div>
<?php if (isset ( $this->_aVars['aFooter'] ) && count ( $this->_aVars['aFooter'] )): ?>
	<div class="bottom">
<?php if (count ( $this->_aVars['aFooter'] ) == 1): ?>
<?php if (count((array)$this->_aVars['aFooter'])):  $this->_aPhpfoxVars['iteration']['block'] = 0;  foreach ((array) $this->_aVars['aFooter'] as $this->_aVars['sPhrase'] => $this->_aVars['sLink']):  $this->_aPhpfoxVars['iteration']['block']++; ?>

<?php if ($this->_aVars['sLink'] == '#'): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'ajax_image')); ?>
<?php endif; ?>
<?php if (is_array ( $this->_aVars['sLink'] )): ?>
            <a class="btn btn-block <?php if (! empty ( $this->_aVars['sLink']['class'] )): ?> <?php echo $this->_aVars['sLink']['class'];  endif; ?>" href="<?php if (! empty ( $this->_aVars['sLink']['link'] )):  echo $this->_aVars['sLink']['link'];  else: ?>#<?php endif; ?>" <?php if (! empty ( $this->_aVars['sLink']['attr'] )):  echo $this->_aVars['sLink']['attr'];  endif; ?> id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php else: ?>
            <a class="btn btn-block" href="<?php echo $this->_aVars['sLink']; ?>" id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php else: ?>
		<ul>
<?php if (count((array)$this->_aVars['aFooter'])):  $this->_aPhpfoxVars['iteration']['block'] = 0;  foreach ((array) $this->_aVars['aFooter'] as $this->_aVars['sPhrase'] => $this->_aVars['sLink']):  $this->_aPhpfoxVars['iteration']['block']++; ?>

				<li id="js_block_bottom_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"<?php if ($this->_aPhpfoxVars['iteration']['block'] == 1): ?> class="first"<?php endif; ?>>
<?php if ($this->_aVars['sLink'] == '#'): ?>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('theme' => 'ajax/add.gif','class' => 'ajax_image')); ?>
<?php endif; ?>
					<a href="<?php echo $this->_aVars['sLink']; ?>" id="js_block_bottom_link_<?php echo $this->_aPhpfoxVars['iteration']['block']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
				</li>
<?php endforeach; endif; ?>
		</ul>
<?php endif; ?>
	</div>
<?php endif; ?>
</div>
<?php endif;  unset($this->_aVars['sHeader'], $this->_aVars['sComponent'], $this->_aVars['aFooter'], $this->_aVars['sBlockBorderJsId'], $this->_aVars['bBlockDisableSort'], $this->_aVars['bBlockCanMove'], $this->_aVars['aEditBar'], $this->_aVars['sDeleteBlock'], $this->_aVars['sBlockTitleBar'], $this->_aVars['sBlockJsId'], $this->_aVars['sCustomClassName'], $this->_aVars['aMenu']); ?>
