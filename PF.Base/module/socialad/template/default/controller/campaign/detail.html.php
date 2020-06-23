{module name='socialad.sub-menu'}

{if $aSaCampaigns = $aTempCampaigns} {/if}
{if $bSaIsNoPaging = true} {/if}
{template file='socialad.block.campaign.campaign-list'}

<div class="ynsaHeaderDetailBlock" > <span class="ynsaTitle">  {phrase var='ads_list'} </span> </div>
{module name='socialad.ad.ad-filter' iFilterCampaignId=$aSaCampaign.campaign_id}
<div id="js_ynsa_ad_list" class="ynsaLFloat">
{module name='socialad.ad.ad-list' }
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    if (typeof google != 'undefined' && typeof google.load != 'undefined') {l}
	    google.load('visualization', '1.0', {l}'packages':['corechart']{r});
    {r}
</script>

<script>
	$Behavior.ynsaInitSorting = function() {l} 
		$('#js_ynsa_ad_list').clickableTable();
	{r}
</script>

