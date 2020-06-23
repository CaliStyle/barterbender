
<div id="js_ynsa_manual_payment"  class="ynsaSection  ynsaManualPaymentInstruction ">
	<div class="ynsaSection ynsaTopSection">
		<div class="table form-group" id="js_ynsa_text_holder">
			<div class="table_left">
				<label for="title">{phrase var='question'}</label>
			</div>
			<div class="table_right">
				<textarea class="form-control" name="" cols="27" rows="8" id="js_ynsa_question" ></textarea>		
			</div>
			<div class="table_right">
				<span id="js_ynsa_question_err" generated="true" class="ynsaError"></span>
			</div>
			<div class="extra_info js_limit_info">
			</div>
		</div> <!-- end text -->
	</div>

{if !$bNoButton}
	<button id="js_ynsa_faq_confirmbtn" onclick="ynsocialad.faq.submitAddNewFAQ(); return false;" name="val[action_review]" class="btn btn-primary btn-sm">{phrase var='confirm'}</button>
{/if}
</div>
