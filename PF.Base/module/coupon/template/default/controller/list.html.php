<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script language="JavaScript" type="text/javascript">
	$Behavior.yncInitializeStatisticJs = function(){
		$("#js_from_date_listing").datepicker({
			dateFormat: '{/literal}{$sFormatDatePicker}{literal}',
			onSelect: function(dateText, inst) {
				var $dateTo = $("#js_to_date_listing").datepicker("getDate");
				var $dateFrom = $("#js_from_date_listing").datepicker("getDate");
				if($dateTo)
				{
					$dateTo.setHours(0);
					$dateTo.setMilliseconds(0);
					$dateTo.setMinutes(0);
					$dateTo.setSeconds(0);
				}

				if($dateFrom)
				{
					$dateFrom.setHours(0);
					$dateFrom.setMilliseconds(0);
					$dateFrom.setMinutes(0);
					$dateFrom.setSeconds(0);
				}

				if($dateTo && $dateFrom && $dateTo < $dateFrom) {
					tmp = $("#js_to_date_listing").val();
					$("#js_to_date_listing").val($("#js_from_date_listing").val());
					$("#js_from_date_listing").val(tmp);
				}
				return false;
			}
		});
$("#js_to_date_listing").datepicker({
	dateFormat: '{/literal}{$sFormatDatePicker}{literal}',
	onSelect: function(dateText, inst) {
		var $dateTo = $("#js_to_date_listing").datepicker("getDate");
		var $dateFrom = $("#js_from_date_listing").datepicker("getDate");

		if($dateTo)
		{
			$dateTo.setHours(0);
			$dateTo.setMilliseconds(0);
			$dateTo.setMinutes(0);
			$dateTo.setSeconds(0);
		}

		if($dateFrom)
		{
			$dateFrom.setHours(0);
			$dateFrom.setMilliseconds(0);
			$dateFrom.setMinutes(0);
			$dateFrom.setSeconds(0);
		}

		if($dateTo && $dateFrom && $dateTo < $dateFrom) {
			tmp = $("#js_to_date_listing").val();
			$("#js_to_date_listing").val($("#js_from_date_listing").val());
			$("#js_from_date_listing").val(tmp);
		}
		return false;
	}
});

$("#js_from_date_listing_anchor").click(function() {
	$("#js_from_date_listing").focus();
	return false;
});

$("#js_to_date_listing_anchor").click(function() {
	$("#js_to_date_listing").focus();
	return false;
});
};
</script>
{/literal}

{if $iPage == 0}
<div class="ync-list-block">
	<!-- Search form -->
	<form class="ync" method="get" action="">
		<div class="statistic-left">
			<div class="table form-group">
				<div class="table_left">
					{phrase var="claimer"}:
				</div>
				<div class="table_right">
					{$aFilters.keyword_claimer}
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="table form-group">
				<div class="table_left">
					{phrase var="coupon_code"}:
				</div>
				<div class="table_right">
					{$aFilters.keyword_couponcode}
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="table form-group">
				<div class="table_left">
					{phrase var="from"}:
				</div>
				<div class="table_right">
					<input name="search[fromdate]" id="js_from_date_listing" class="form-control" type="text" value="{if $sFromDate}{$sFromDate}{/if}" />
					<a href="#" id="js_from_date_listing_anchor">
						<img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
					</a>
				</div>
				<div class="clear"></div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					{phrase var="to"}:
				</div>
				<div class="table_right">
					<input name="search[todate]" class="form-control" id="js_to_date_listing" type="text" value="{if $sToDate}{$sToDate}{/if}" />
					<a href="#" id="js_to_date_listing_anchor">
						<img src="<?php echo Phpfox::getLib('template')->getStyle('image', 'jquery/calendar.gif'); ?>" />
					</a>
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="table_clear">
				<input type="submit" name="search[submit]" value="{phrase var='submit'}" class="button btn btn-primary btn-sm" />
				<input type="submit" name="search[submit]" value="{phrase var='reset'}" class="button btn btn-default btn-sm" />
			</div>
		</div>
	</form>
	<div class="clear"></div>
	<!-- Claims Listing Space -->
	<br>
	<div>
		<b>{phrase var="total"}:</b> {if $aCoupon.quantity > 0}{$aCoupon.quantity}{else}{phrase var="unlimited"}{/if}
		<div class="ync_statictis">-</div>
		<div class="ync_statictis2"><b>{phrase var="claimed"}:</b> {$aCoupon.total_claim}
		-
		<b>{phrase var="left_upper"}:</b> {$sRemain} </div>
	</div>
	<br>

	{/if}
	{if $aTransactions && isset($aTransactions) && count($aTransactions)}
	{if $iPage == 0}
	<div class="clear"></div>
	<div class="yncoupon_table_simple">
	<div class="table-header">
		<span>{phrase var="claim_s_time"}</span>
		<span>{phrase var="claimer"}</span>
		<span>{phrase var="coupon_code"}</span>
	</div>
	{/if}
	{foreach from=$aTransactions key=iKey item=aTransaction}
	<div id="js_row{$aTransaction.claim_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
		<span>
			{$aTransaction.time_stamp|date}
		</span>
		<span>
			{$aTransaction|user}
		</span>
		<span>
			{$aTransaction.code}
		</span>                    
	</div>
	{/foreach}
	{pager}
	</div>
{elseif $iPage == 0}
<div>{phrase var="there_are_no_claims_yet"}</div>
{/if}
{if $iPage == 0}
</div>
{/if}
