<div id="yndirectory_featureinbox">
	<form enctype="multipart/form-data" action="{$sFormUrl}" method="post">
		<div>
			<input type="hidden" name="val[name]" value="{$aEditedBusiness.name}" />
			<input type="hidden" value="{$iDefaultFeatureFee}" id="yndirectory_defaultfeaturefee">
		</div>
		<div class="form-group">
			<label>{phrase var='feature'}:</label>
			<div>
				<div class="form-group">
					<label>{phrase var='feature_this_business_for'}</label>
					<input class="form-control" id="yndirectory_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10">
				</div>
				<div class="form-group">
					<label>{phrase var='day_s_with'}</label>
					<input class="form-control" id="yndirectory_feature_fee_total" type="text" value="0" size="10" readonly />
				</div>
				{$aCurrentCurrencies.0.currency_id}
				<span>({phrase var='fee_to_feature_business_feature_fee_currency_id_for_1_day' feature_fee=$iDefaultFeatureFee currency_id=$aCurrentCurrencies.0.currency_id})</span>
				{if isset($aEditedBusiness.featured) && $aEditedBusiness.featured}
				<div>
					{if isset($aEditedBusiness.is_unlimited) && $aEditedBusiness.is_unlimited}
						{phrase var='note_this_business_is_featured_unlimited_time'}
					{else}
						{phrase var='note_this_business_is_featured_until_expire_date' expire_date=$aEditedBusiness.expired_date}
					{/if}
				</div>
				{/if}
			</div>
		</div>
		<div class="text-right"><button id="yndirectory_submit" type="submit" class="btn btn-sm btn-primary" value="{phrase var='update'}" name="val[featureinbox]">{phrase var='update'}</button></div>
	</form>
</div>

{literal}
	<script type="text/javascript">
		$('#yndirectory_feature_number_days').on('keyup', yndirectory.onChangeFeatureFeeTotal);
	</script>
{/literal}