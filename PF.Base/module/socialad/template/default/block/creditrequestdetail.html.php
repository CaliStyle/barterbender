
<div id="js_ynsa_manual_payment"  class="ynsaSection  ynsaManualPaymentInstruction ">
	<div class="ynsaSection ynsaTopSection" style="margin-top: 0;">

		<div class="table form-inline" style="border-bottom: none;">
			<div class="table_left" style="position: relative;">
				<label for="title">{phrase var='amount'}</label>
			</div>
			<div class="table_right" style="margin-left: 0;">
				<input class="form-control" readonly type="text" name="" value="{$creditMoneyRequest.creditmoneyrequest_amount}" id="js_ynsa_addrequest_amount" /> {$aCurrentCurrency.currency_id}
			</div>
		</div>

		<div class="table form-group"  style="border-bottom: none;" id="js_ynsa_text_holder">
			<div class="table_left" style="position: relative;">
				<label for="title">{phrase var='description'}</label>
			</div>
			<div class="table_right" style="margin-left: 0;">
				<textarea class="form-control" readonly name="" cols="27" rows="8" id="js_ynsa_addrequest_reason" >{$creditMoneyRequest.creditmoneyrequest_reason}</textarea>		
			</div>
		</div> <!-- end text -->
	</div>
</div>
