<?php

defined('PHPFOX') or exit('NO DICE!');

?>

{foreach from=$aMostPopularCoupons item=aCoupon name=coupon}
	{template file='coupon.block.coupon-items'}
{/foreach}

<div class="clear"></div>
