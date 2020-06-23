<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');


?>

<!-- <h2 class="ync-title-block"><span>{phrase var='most_claimed'}</span></h2> -->
{if count($aMostClaimedCoupons)}
<div class="ync_grid_most_block clearfix">
{foreach from=$aMostClaimedCoupons item=aCoupon name=coupon}
	{ template file='coupon.block.entry'}
{/foreach}
</div>
<div class="clear"></div>
{else}
    <div>{phrase var='no_coupons_found'}</div>
{/if}

