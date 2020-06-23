<div id="ynstore_featureinbox">
	<form enctype="multipart/form-data" action="{$sFormUrl}" method="post">
		<div>
			<input type="hidden" name="val[name]" value="{$aEditedStore.name}" />
			<input type="hidden" value="{$iDefaultFeatureFee}" id="ynsocialstore_defaultfeaturefee">
		</div>
		<div class="table form-group">
			<div class="table_left">
				<label>{_p var='ynsocialstore.feature'}:</label>
			</div>
			<div class="table_right">
				{_p var='ynsocialstore.feature_this_store_for'} <input class="form-control" id="ynsocialstore_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10"> {_p var='ynsocialstore.day_s_with'} <input class="form-control" id="ynsocialstore_feature_fee_total" type="text" value="0" size="10" readonly /> {$aCurrentCurrencies.0.currency_id}
				<br /> <span>({_p var='ynsocialstore.fee_to_feature_store_feature_fee_currency_id_for_1_day' feature_fee=$iDefaultFeatureFee currency_id=$aCurrentCurrencies.0.currency_id})</span>
				{if isset($aEditedStore.is_featured) && $aEditedStore.is_featured}
				<div>
					{if isset($aEditedStore.is_unlimited) && $aEditedStore.is_unlimited}
						{_p var='ynsocialstore.note_this_store_is_featured_unlimited_time'}
					{elseif !empty($aEditedStore.expired_date)}
						{_p var='ynsocialstore.note_this_store_is_featured_until_expire_date' expire_date=$aEditedStore.expired_date}
					{/if}
				</div>
				{/if}
			</div>
		</div>
		<div class="table form-group" style="text-align: right;"><button id="ynstore_submit" type="submit" class="btn btn-sm btn-primary" value="{_p var='ynsocialstore.update'}" name="val[featureinbox]">{_p var='ynsocialstore.update'}</button></div>
	</form>
</div>

{literal}
<script type="text/javascript">
    $Behavior.initFeatureInbox = function(){
        let objectThis = $('#ynstore_featureinbox').find('#ynsocialstore_feature_number_days');
        objectThis.off('keyup').on('input', ynsocialstore.onChangeFeatureFeeTotal);
    }
</script>
{/literal}