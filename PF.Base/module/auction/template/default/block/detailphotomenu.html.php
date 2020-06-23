<div class="ynauction-profile-avatar-holder">
    <a class="ynauction-profile-avatar" href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}" title="{$aAuction.name|clean}">
        {img user=$aAuction suffix='' max_width=150 max_height=150 class='v_middle'}
    </a>
</div>
<div class="ynauction-username-holder">
    <span>{phrase var='by'}: </span><a href="{url link=$aAuction.user_name}" title="{$aAuction.full_name|clean}">{$aAuction.full_name|clean|shorten:75:'...'|split:10}</a>
</div>
<div class="ynauction-userinfo-holder">
    {if $aAuction.location != '' }
    <div class="ynauction-location">
        <span class="ynauction-location-icon">

        </span>{$aAuction.location|clean}
    </div>
    {/if}

    <div class="ynauction-bid">
        <span class="ynauction-bid-icon"></span>
        <a href="{url link=$aAuction.user_name}auction">
	        {if $aAuction.total_auction > 1}
	            {phrase var='number_auctions' number=$aAuction.total_auction}
	        {else}
	            {phrase var='number_auction' number=$aAuction.total_auction}
	        {/if}
        </a>
    </div>
</div>
