
<div class="sub_section_menu subauction_section_menu">
	<ul>
		{if isset($aModuleView.overview) && $aModuleView.overview.is_show}
			<li {if $aModuleView.overview.active} class='active' {/if}>
				<a href="{$aModuleView.overview.link}" style="background-image: url({$core_path}module/auction/static/image/icon-detail-overview.png)">
					{$aModuleView.overview.module_phrase}
				</a>
			</li>
		{/if}

        {if ($aAuction.product_status != 'draft')
			&& isset($aModuleView.shipping) && $aModuleView.shipping.is_show}
			<li {if $aModuleView.shipping.active} class='active' {/if}>
				<a href="{$aModuleView.shipping.link}" style="background-image: url({$core_path}module/auction/static/image/icon-detail-about-us.png)">
					{$aModuleView.shipping.module_phrase}
				</a>
			</li>
		{/if}
        
        {if ($aAuction.product_status != 'draft')
			&& isset($aModuleView.bidhistory) && $aModuleView.bidhistory.is_show}
			<li {if $aModuleView.bidhistory.active} class='active' {/if}>
				<a href="{$aModuleView.bidhistory.link}" style="background-image: url({$core_path}module/auction/static/image/icon-1.jpg)">
					{$aModuleView.bidhistory.module_phrase}
				</a>
			</li>
		{/if}
        
        {if ($aAuction.product_status != 'draft') && ($aAuction.user_id == Phpfox::getUserId()) 
			&& isset($aModuleView.offerhistory) && $aModuleView.offerhistory.is_show}
			<li {if $aModuleView.offerhistory.active} class='active' {/if}>
				<a href="{$aModuleView.offerhistory.link}" style="background-image: url({$core_path}module/auction/static/image/icon-2.jpg)">
					{$aModuleView.offerhistory.module_phrase}
				</a>
			</li>
		{/if}
        
        {if ($aAuction.product_status != 'draft')
			&& isset($aModuleView.chart) && $aModuleView.chart.is_show}
			<li {if $aModuleView.chart.active} class='active' {/if}>
				<a href="{$aModuleView.chart.link}" style="background-image: url({$core_path}module/auction/static/image/icon-3.jpg)">
					{$aModuleView.chart.module_phrase}
				</a>
			</li>
		{/if}
        
		{if ($aAuction.product_status != 'draft')
			&& isset($aModuleView.activities) && $aModuleView.activities.is_show}
			<li {if $aModuleView.activities.active} class='active' {/if}>
				<a href="{$aModuleView.activities.link}" style="background-image: url({$core_path}module/auction/static/image/icon-detail-activities.png);">
					{$aModuleView.activities.module_phrase}
				</a>
			</li>
		{/if}

		{foreach from=$aPagesModule item=aPage}
			{if ($aAuction.product_status != 'draft')}
				<li {if $aPage.active} class='active' {/if}>
					<a href="{$aPage.link}" style="background-image: url({$core_path}module/auction/static/image/icon-detail-overview.png)">
						{$aPage.module_phrase}
					</a>				
				</li>
			{/if}
		{/foreach}
	</ul>
</div>


{if Phpfox::isUser() && ($aAuction.product_status != 'draft')}
<div class='ynauction-like'>
	{if $isLiked}
		<a href="javascript:void(0)" class="btn btn-sm btn-primary"  onclick="$(this).parent().hide();$.ajaxCall('auction.deleteLike', 'type_id=auction&amp;item_id={$aAuction.product_id}'); return false;">
			{phrase var='unlike'}
		</a>
	{else}
		<a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="$(this).parent().hide();$.ajaxCall('auction.addLike', 'type_id=auction&amp;item_id={$aAuction.product_id}'); return false;">
			{phrase var='like'}
		</a>
	{/if}
</div>
{/if}