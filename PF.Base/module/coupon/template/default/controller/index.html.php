<?php

defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsHomepage}

	{if !count($aItems)}
			<div class="extra_info">
				{phrase var='no_coupons_found'}
			</div>
	{else}

		<div class="yncoupon-homepage">
			{module name='coupon.featured-slideshow'}

			{module name='coupon.most-claimed'}

			{module name='coupon.most-comment'}
		</div>

	{/if}
{else}

	{if $sView == 'faq'}
		{module name='coupon.faq'}
	{else}

		{if !count($aItems)}
            {if !PHPFOX_IS_AJAX}
			<div class="extra_info">
				{phrase var='no_coupons_found'}
			</div>
            {/if}
		{else}
            <div class="ync_grid_my_coupon clearfix">
                <div class="ync_grid_most_block clearfix">
                {foreach from=$aItems  name=coupon item=aCoupon}
                    { template file='coupon.block.entry'}
                {/foreach}
                </div>
            </div>
            <div class="clear"></div>

            {if $bShowModerator }
                {moderation}
            {/if}

            {pager}

		{/if}

	{/if}
{/if}

{if !empty($sAdvSearchContent)}
    {$sAdvSearchContent}
{/if}