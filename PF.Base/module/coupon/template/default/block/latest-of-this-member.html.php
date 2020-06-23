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

<!-- Suggestion block listing space --> 
{foreach from=$aCoupons item = aCoupon}
	{template file='coupon.block.coupon-items'}
{/foreach}
