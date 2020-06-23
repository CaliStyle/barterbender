
<div id="js_ynsa_manual_payment"  class="ynsaSection  ynsaManualPaymentInstruction ">
	<div class="ynsaSection ynsaTopSection">

		<div class="ynsaSectionHeading ynsaClearFix">
			<div class="ynsaLFloat">
				<div class="ynsaHeadingText">
					{phrase var='instructions'}
				</div>
			</div>
			<div class="ynsaRFloat">
				<!-- put help link here -->
			</div>
		</div>
		
		<div class="ynsaSectionContentPlain ">	
			<div class="ynsaLeftInnerContent"> 
				{$sManualPaymentInstructions}	
			</div>
		</div>
	</div>

{if !$bNoButton}
<br>
	<button style="margin-bottom: 15px;" onclick="document.location='{url link='socialad.payment.pay' id=$aSaAd.ad_id method='paylater'}';" name="val[action_review]" class="btn btn-primary">{phrase var='confirm'}</button>
	
	<div class="extra_info">
		{phrase var='manual_payment_notice'}
	</div>
{/if}
</div>
