<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="upcoming_auctions" class="">
    <ul>
        {foreach from=$aUpcomingAuctions item=aAuction}
            <li>
                <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}">
                    {img theme='layout/thickbox_bg.png'}
                    <span>{$aAuction.name|clean|shorten:25:'...'|split:25}</span>
                </a>
                <div class="auction_date_time">{phrase var='start'}: {$aAuction.start_time|date:'core.global_update_time'}</div>
            </li>
        {/foreach}
    </ul>
    {if $bShowViewMoreUpcomingAuctions}
        <div class="auction_view_more">
            <a href="{url link='auction' view='upcoming'}" class="btn btn-sm btn-success" title="{phrase var='view_more'}">
                {phrase var='view_more'}
            </a>
        </div>
    {/if}
</div>