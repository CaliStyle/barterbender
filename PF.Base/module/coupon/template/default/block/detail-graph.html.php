<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
 ?>
 
<div class = "block coupon_detail">
	<div class = "ync_discount">
		<span class = "ync_left_arrow"></span>
		{if isset($aCoupon.discount) }	
		<span class = "ync_label">{phrase var='discount'}</span>
		<span class = "ync_value">{$aCoupon.discount}</span> 
		{/if}
        {if isset($aCoupon.special_price) }
		<span class = "ync_label">{phrase var='special_price'}</span>
		<span class = "ync_value">{$aCoupon.special_price}</span>  
        {/if}

	</div>
	<div class = "ync_claim">
		<div class = "ync_claim_sum">
			<!-- Claimed -->
			<span class = "ync_label">{phrase var='claimed'}</span>
			<span class = "ync_value">{$aCoupon.total_claim}</span>
			<!-- Claimed Bar -->
			<div class = "ync_claim_total">
				<div class = "ync_claim_active" style = "width:{$iPercent}%;">
				</div>
			</div>
			<!-- Claims Remain -->
			<p class = "ync_claim_remain">
				{$sRemain}
			</p>
			<!-- Remain Time -->
			<div class = "ync_claim_remain_time">
				{$sRemainTime}
			</div>
		</div>
	</div>
</div>