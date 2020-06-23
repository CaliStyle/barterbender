<script type="text/javascript" src="{$sCorePath}module/socialad/static/jscript/ynsocialad.js"></script>
{module name='socialad.sub-menu'}
{module name='socialad.reminder' iRemindAdId=$aSaDetailAd.ad_id}
<div class="ynsaDetailAd">
	<div class="ynsa_table">
		<table class="ynsaTable ynsaLFloat" cellpadding="0" cellspacing="0">
			<tr>
				<th class="first"></th>
				<th class="second">{phrase var='name'}</th>

				<th>{phrase var='status'}
					{module name='socialad.tooltip' sTooltipName='ad_status'}
				</th>

				<th>{phrase var='campaign'}</th>

				<th>{phrase var='start_date'}
					{module name='socialad.tooltip' sTooltipName='ad_start_date'}
				</th>

				<th>{phrase var='end_date'}
					{module name='socialad.tooltip' sTooltipName='ad_end_date'}
				</th>

				<th>{phrase var='clicks'}
					{module name='socialad.tooltip' sTooltipName='click'}
				</th>

				<th>{phrase var='unique_clicks'}
				</th>

				<th>{phrase var='impressions'}
					{module name='socialad.tooltip' sTooltipName='impression'}
				</th>

				<th>{phrase var='reaches'}
					{module name='socialad.tooltip' sTooltipName='reach'}
				</th>

				<th>{phrase var='ctr'}
					{module name='socialad.tooltip' sTooltipName='click_through_rate'}
				</th>

				{*
				<th>{phrase var='days'}
					{module name='socialad.tooltip' sTooltipName='running_day'}
				</th>
				*}

				<th>{phrase var='remaining'}
					{module name='socialad.tooltip' sTooltipName='remaining'}
				</th>

				<th>{phrase var='type'}
					{module name='socialad.tooltip' sTooltipName='ad_type'}
				</th>
			</tr>
			<tr>
				<td class="t_center ynsaFirstColumn">
					{if $aActionAd=$aSaDetailAd} {/if}
					{template file='socialad.block.ad.action'}
				 </td>
				<td title="{phrase var='name'}" class="t_center ">
					<a href="#" onclick="$('#js_ynsa_review_holder').toggle();">{$aSaDetailAd.ad_title|clean}</a>
				</td>
				<td title="{phrase var='status'}" class="t_center">{$aSaDetailAd.ad_status_phrase}</td>
				<td title="{phrase var='campaign'}" class="t_center">
					{if Phpfox::getUserId() == (int)$aSaDetailAd.ad_user_id}
						<a href={url link='socialad.campaign.detail' id=$aSaDetailAd.ad_campaign_id} > {$aSaDetailAd.campaign_name} </a>
					{else}
						{$aSaDetailAd.campaign_name}
					{/if}
				</td>
				<td title="{phrase var='start_date'}" class="t_center">{$aSaDetailAd.ad_start_time_phrase}</td>
				<td title="{phrase var='end_date'}" class="t_center">{$aSaDetailAd.ad_end_time_phrase}</td>
				<td title="{phrase var='clicks'}" class="t_center">{$aSaDetailAd.ad_total_click}</td>
				<td title="{phrase var='unique_clicks'}" class="t_center">{$aSaDetailAd.ad_total_unique_click}</td>
				<td title="{phrase var='impressions'}" class="t_center">{$aSaDetailAd.ad_total_impression}</td>
				<td title="{phrase var='reaches'}" class="t_center">{$aSaDetailAd.ad_total_reach}</td>
				<td title="{phrase var='ctr'}" class="t_center">{$aSaDetailAd.ad_ctr_phrase}</td>
				{*
				<td title="{phrase var='days'}" class="t_center">{$aSaDetailAd.ad_total_running_day}</td>
				*}
				<td title="{phrase var='remaining'}" class="t_center">{$aSaDetailAd.ad_remaining_phrase}</td>
				<td title="{phrase var='type'}" class="t_center ynsaLastColumn">{$aSaDetailAd.ad_type_phrase}</td>
			</tr>
		</table>
	</div>


	<div class="ynsaFloatBlock ynsaPreviewInDetail">
		<div class="ynsaHeaderDetailBlock">
			<a  class="ynsaTitle" href="#" onclick="$('#js_ynsa_review_holder').toggle(); return false;">
				{phrase var='preview'}
			</a>

			{*<a class="ynsaAction ynsaRFloat edit" href="{url link='socialad.ad.add' id=$aSaDetailAd.ad_id}" > {phrase var='edit'} </a>*}
		</div>
		<div class="ynsaReview ynsaLFloat ynsaClearFix " id="js_ynsa_review_holder">
			{module name='socialad.ad.preview.preview' aPreviewAd=$aSaDetailAd}
		</div>

		<div class="ynsaFooterDetailBlock">
			<a class="ynsaAction button btn btn-success btn-sm" href="{url link='socialad.ad.action' actionname='createsimilar' id=$aSaDetailAd.ad_id}" > {phrase var='create_a_similar_ad'} </a>
		</div>
	</div>

	<div class="ynsaFloatBlock ynsaStatisticsInDetail">

		<div class="ynsaHeaderDetailBlock">
			<span class="ynsaTitle" > {phrase var='statistics'} </span>
			<a class="ynsaAction ynsaRFloat" href="{url link='socialad.report.ad' id=$aSaDetailAd.ad_id}"> {phrase var='view_full_report'}</a>

		 </div>
		<div class="ynsaStatistic ynsaLFloat ">
			{module name='socialad.report.short-list' iShortListAdId=$aSaDetailAd.ad_id}
		</div>

	</div>
	<div class="ynsaChart ynsaLFloat">
	{module name='socialad.chart' iChartAdId=$aSaDetailAd.ad_id}
	</div>
</div>

