<div id="ynauction_menu_edit_auction_link" class="ynauction-menu-options sub_section_menu header_display">
    <ul class="action">
        <li class="ynecommerce-insight {if Phpfox::getLib('module')->getFullControllerName() == 'auction.edit'}active{/if}"  >
        	<a href="{url link='auction.edit.id_'.$aAuction.product_id}">{phrase var='edit_info'}</a>
        </li>
        <li class="ynecommerce-insight {if Phpfox::getLib('module')->getFullControllerName() == 'auction.cover-photos'}active{/if}"  >
        	<a href="{url link='auction.cover-photos.id_'.$aAuction.product_id}">{phrase var='cover_photos'}</a>
        </li>
        <li class="ynecommerce-insight {if Phpfox::getLib('module')->getFullControllerName() == 'auction.bid-history'}active{/if}"  >
        	<a href="{url link='auction.bid-history.id_'.$aAuction.product_id}">{phrase var='bid_history'}</a>
        </li>
		<li class="ynecommerce-insight {if Phpfox::getLib('module')->getFullControllerName() == 'auction.offer-list'}active{/if}"  >
        	<a href="{url link='auction.offer-list.id_'.$aAuction.product_id}">{phrase var='offer_list'}</a>
        </li>
        <li class="ynecommerce-insight"><a href="{url link='auction.detail.'.$iAuctionId}">{phrase var='view_this_auction'}</a></li>
    </ul> 
</div>
