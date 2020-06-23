<div id="ynauction_featureinbox">
	<form enctype="multipart/form-data" action="{$sFormUrl}" method="post">
		<div>
			<input type="hidden" name="val[name]" value="{$aEditedAuction.name}" />
			<input type="hidden" value="{$iDefaultFeatureFee}" id="ynauction_defaultfeaturefee">
		</div>
		<div class="table form-group">
				<div class="table_left">
					<label>{phrase var='feature'}:</label>
				</div>
				<div class="table_right">
					{phrase var='feature_this_auction_for'} <input class="form-control" id="ynauction_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10"> {phrase var='day_s_with'} <input class="form-control" id="ynauction_feature_fee_total" type="text" value="0" size="10" readonly /> {$aCurrentCurrencies.0.currency_id}
					<br /> <span>({phrase var='fee_to_feature_auction_feature_fee_currency_id_for_1_day' feature_fee=$iDefaultFeatureFee currency_id=$aCurrentCurrencies.0.currency_id})</span>
					{if $aEditedAuction.featured}
						{if isset($aEditedAuction.expired_date)}
						<div>
								{phrase var='note_this_auction_is_featured_until_expire_date' expire_date=$aEditedAuction.expired_date start_date=$aEditedAuction.start_date}
						</div>
						{/if}
					{/if }
				</div>
		</div>
		<div class="table form-group" style="text-align: right;"><button id="ynauction_submit" type="submit" class="btn btn-primary" value="" name="val[featureinbox]">{phrase var='update'}</button></div>
	</form>
</div>
`
{literal}
<script type="text/javascript">
	$('#ynauction_feature_number_days').on('keyup', ynauction.onChangeFeatureFeeTotal);
</script>
{/literal}