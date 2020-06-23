
<div id="js_ynsa_manual_payment"  class="ynsaManualPaymentInstruction ">
	<div class="ynsaSection ynsaTopSection" style="margin-top: 0;">
		<div class="table form-inline">
			<div class="table_left" style="position: relative;">
				<label for="title">{phrase var='amount'}</label>
			</div>
			<div class="table_right" style="margin-left: 0;">
				<input type="text" name="" value="" class="form-control" id="js_ynsa_addrequest_amount" /> {$aCurrentCurrency.currency_id}
			</div>
			<div class="table_right" style="margin-left: 0;">
				<span id="js_ynsa_addrequest_amount_err" generated="true" class="ynsaError"></span>
			</div>
			<div class="extra_info js_limit_info">
			</div>
		</div>

		<div class="table form-group" id="js_ynsa_text_holder">
			<div class="table_left" style="position: relative;">
				<label for="title">{phrase var='description'}</label>
			</div>
			<div class="table_right" style="margin-left: 0;">
				<textarea class="form-control" name="" cols="27" rows="8" id="js_ynsa_addrequest_reason" ></textarea>		
			</div>
			<div class="table_right" style="margin-left: 0;">
				<span id="js_ynsa_addrequest_reason_err" generated="true" class="ynsaError"></span>
			</div>
			<div class="extra_info js_limit_info">
			</div>
		</div> <!-- end text -->
	</div>

{if !$bNoButton}
	<button id="js_ynsa_addrequest_confirmbtn"  onclick="ynsocialad.addrequest.submitAddRequest(1); return false;" name="val[action_review]" class="btn btn-primary btn-sm" style="margin-top: 10px;">{phrase var='confirm'}</button>
{/if}
</div>
