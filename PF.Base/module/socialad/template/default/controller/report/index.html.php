
{module name='socialad.sub-menu'}

<form id="js_ynsa_report_form"
	data-ajax-action="socialad.changeReportListFilter"
	data-result-div-id="js_ynsa_report_table_holder"
	data-is-validate="true"
	data-custom-event="ondatachanged">
<div class="table form-group ynsa_report_campaign">
	<div class="table_left">
		{phrase var='campaign'}:
	</div>
	<div class="table_right">
		<select class="form-control ynsaMultipleChosen" name="val[campaign_id]" id="js_ynsa_report_select_campaign">
			<option value="0" >{phrase var='all_campaigns'}</option>
			{foreach from=$aSaCampaigns item=aCampaign}
				<option {if $aCampaign.campaign_id == $iDefaultCampaignId} selected="selected" {/if}  value="{$aCampaign.campaign_id}" >{$aCampaign.campaign_name}</option>
			{/foreach}
		</select>
	</div>
</div>
<div id="js_ynsa_report_select_ad_holder"class="ynsa_report_adsblock">
{if $iDefaultAdId}
	{module name='socialad.select-ad' iCampaignId=$iDefaultCampaignId iDefaultAdId=$iDefaultAdId}
{/if}
</div>

<div class="table form-group ynsa_report_range_time">
	<div class="ynsaAdTime ynsaLFloat" >
		<div class="ynsaAdTimeTitle ynsaLFloat" >{phrase var='start'} : </div>

		<div class="ynsaAdTimeContent ynsaLFloat report">
			 {select_date prefix='start_' id='_begin_time' start_year='-20' end_year='+10' field_separator=' / ' field_order='MDY' }
		</div>
	</div>
	<div class="ynsaAdTime ynsaLFloat" >
		<div class="ynsaAdTimeTitle ynsaLFloat" >{phrase var='end'} : </div>

		<div class="ynsaAdTimeContent ynsaLFloat report">
			 {select_date prefix='end_' id='_begin_time' start_year='-20' end_year='+10' field_separator=' / ' field_order='MDY' }
		</div>
	</div>
	<div class="ynsaAdTime ynsaLFloat" >
		<select class="form-control" name="val[summary]" id="js_ynsa_report_summary">
			{foreach from=$aSummaryOptions item=iSummary}
				<option value="{$iSummary}" >
					{if $iSummary == 0 }
						{phrase var='all_days'}
					{elseif $iSummary  == 1}
						{$iSummary} {phrase var='day_per_row'}
					{else}
						{$iSummary} {phrase var='days_per_row'}
					{/if}

				</option>
			{/foreach}
		</select>
	</div>
</div> <!-- end choose date time -->

<div class="table form-group ynsa_report_export_type">
<select name="val[export_type]" class="form-control ynsaNoAjax" >
	<option value="xls" > {phrase var='export_report'} ({phrase var='dot_xls'}) </option>
	<option value="csv" > {phrase var='export_report'} ({phrase var='dot_csv'}) </option>
</select>
<button class="btn btn-success btn-sm" style="margin-top: 10px;" id="js_ynsa_export_report_btn" data-action-url="{url link='socialad.report.export'}">{phrase var='export'}</button>
</div>

</form> <!-- end report form -->

<div class="table form-group" id="js_ynsa_report_table_holder">
</div>


<script type="text/javascript">
$Behavior.ynsaInitReportListForm = function() {l}
	$("#js_ynsa_report_form").ajaxForm( );
	ynsocialad.report.initReportForm();
{r}
</script>
