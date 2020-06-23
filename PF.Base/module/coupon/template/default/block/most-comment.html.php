<?php

defined('PHPFOX') or exit('NO DICE!');
?>

<!-- <h2 class="ync-title-block"><span>{phrase var='most_comment'}</span></h2> -->
{if $aMostCommentCoupons}
<div class="ync_grid_most_block clearfix">
{foreach from=$aMostCommentCoupons item=aCoupon name=coupon}
	{ template file='coupon.block.entry'}
{/foreach}
</div>
<div class="clear"></div>

{else}
<div>{phrase var='no_coupons_found'}</div>
{/if}