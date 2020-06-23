
<div id="js_ynsa_manual_payment"  class="ynsaSection  ynsaManualPaymentInstruction ">
	<div class="ynsaSection ynsaTopSection">

		<div class="table form-inline">
			<div class="table_left">
				<label for="title">{phrase var='amount'}</label>
			</div>
			<div class="table_right">
				<input type="text" name="" value="" class="form-control" id="js_ynsa_addrequest_amount" /> {$aCurrentCurrency.currency_id}
			</div>
			<div class="table_right">
				<span id="js_ynsa_addrequest_amount_err" generated="true" class="ynsaError"></span>
			</div>
			<div class="extra_info js_limit_info">
			</div>
		</div>

		<div class="table form-group" id="js_ynsa_text_holder">
			<div class="table_left">
				<label for="title">{phrase var='description'}</label>
			</div>
			<div class="table_right">
				<textarea class="form-control" name="" cols="27" rows="8" id="js_ynsa_addrequest_reason" ></textarea>		
			</div>
			<div class="table_right">
				<span id="js_ynsa_addrequest_reason_err" generated="true" class="ynsaError"></span>
			</div>
			<div class="extra_info js_limit_info">
			</div>
		</div> <!-- end text -->
	</div>

{if !$bNoButton}
	<button id="js_ynsa_addrequest_confirmbtn" onclick="ynsocialad.addrequest.submitAddRequest(0); return false;" name="val[action_review]" class="btn btn-primary btn-sm">{phrase var='confirm'}</button>
{/if}
</div>
