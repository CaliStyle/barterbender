<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="todays_live_auctions" class="">
    <ul>
        {foreach from=$aTodaysLiveAuctions item=aAuction}
            <li>
                <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                    <i class="fa fa-bookmark"></i>
                    {$aAuction.name|clean|shorten:25:'...'|split:25}
                </a>
            </li>
        {/foreach}
    </ul>
     {if $bShowViewMoreToDayLiveAuctions}
        <div class="auction_view_more">
            <a href="{url link='auction' view='todaylive'}" class="btn btn-sm btn-success" title="{phrase var='view_more'}">
                {phrase var='view_more'}
            </a>
        </div>
    {/if}

</div>