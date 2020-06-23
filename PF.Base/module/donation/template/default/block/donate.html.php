{if $iPageId == -1}
	{if !$bNeedToBeConfig}
	<p style="text-align:center; padding: 10px; background: #FFF;">
		<a href="#" onclick="showDonationIndex('{phrase var='donation.page_donation_title_homepage'}', {$iPageId}, '{$sUrl}'); return false;">
			<img src='{$sImg}' />
		</a>
	</p>
	{else}
	<p style="text-align:center; padding: 10px; background: #FFF;">
		<a class="no_ajax" href="{url link='admincp.donation'}">
			<img src='{$sImg}' />
		</a>
	</p>
	<p style="text-align:center; padding: 10px; background: #FFF;">
		<a class="no_ajax" href="{url link='admincp.donation'}" style="text-transform:uppercase; color: #8E8E8E">
			{phrase var='donation.donation_setting'}
		</a>
	</p>
	{/if}
	
{else} 
	{if $iDonation > 0 and Phpfox::isModule('donation')}				
		{if $iUserId == Phpfox::getUserId() && Phpfox::getUserParam('donation.can_add_donation_on_own_page')} 
			<p style="text-align:center; padding: 10px; background: #FFF;">			
				<a onclick="showDonationIndex('{$sDonation}',{$iPageId},'{$sUrl}'); return false;" href="#">
					<img src="{$sImg}"/>			
				</a><br/>
				<a href="{$urlSetting}" style="text-transform:uppercase; color: #8E8E8E"> 
					{phrase var='donation.donation_setting'}
				</a>
			</p>
		{else} 
			<p style="text-align:center; padding: 10px; background: #FFF;">				
				<a onclick="showDonationIndex('{$sDonation}',{$iPageId},'{$sUrl}'); return false;" href="#">
					<img src="{$sImg}"/>			
				</a>
			</p>
		{/if}
	{/if}	  

{/if}
{literal}
<script type="text/javascript" language="javascript">
		function showDonationIndex(title, iPageId, sUrl){  
	tb_show(title,$.ajaxBox('donation.detail','iPageId=' + iPageId + '&sUrl=' + sUrl));
}
</script>
{/literal}