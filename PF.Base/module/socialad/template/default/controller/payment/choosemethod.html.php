
{module name='socialad.sub-menu'}

<div class="ynsaSection ynsaLFloat" >
	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='ad'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			<a href="{url link='socialad.ad.detail' id=$aPlaceorderAd.ad_id}"> {$aPlaceorderAd.ad_title|clean} </a>
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='ad_preview'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			<div >
				 <a href="{url link='socialad.ad.add' id=$aPlaceorderAd.ad_id}">{phrase var='edit'} </a>
			</div>
			<div class="clear"></div>
			{module name='socialad.ad.preview.preview' aPreviewAd=$aPlaceorderAd }
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='campaign'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{$aPlaceorderAd.campaign_name}
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='package'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			<a href="#" onclick="tb_show('{phrase var='package'}', $.ajaxBox('socialad.showPackagePopup', 'height=400&width=900&package_id={$aSaPackage.package_id}')); return false;">{$aSaPackage.package_name} </a>
		</div>
		{*
		<div class="ynsaRightColumn ynsaColumn">
			{$aSaPackage.package_name}
		</div>
		*}
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='audiences'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{$iSaAffectedAudience} {phrase var='people'}
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='price'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{$aPlaceorderAd.ad_total_price|currency:$aSaPackage.package_currency:2}
		</div>
	</div>

	<div class="ynsaSectionRow2Column ynsaClearFix">
		<div class="ynsaLeftColumn ynsaColumn">
			<label for="title">{phrase var='benefit'}</label>
		</div>

		<div class="ynsaRightColumn ynsaColumn">
			{$aPlaceorderAd.ad_total_benefit} {$aSaPackage.package_benefit_type_text}
		</div>
	</div>
</div>
<div class="ynsaSection ynsaLFloat ynsa_paypal_method">
{if $bNoPaymentMethodActive}
	<div class="ynsaAdExternalUrl">
		{phrase var='no_available_payment_methods'}
	</div>
{else}
	{module name='api.gateway.form'}
	{if $bIsHavePayLater}
		<div class="ynsaPayButton ynsaLFloat">
		<a class="ynsaButton btn btn-sm btn-success" href="#" onclick="tb_show('{phrase var='pay_later_request'}', $.ajaxBox('socialad.showPayLaterPopup', 'height=400&width=700&ad_id={$aPlaceorderAd.ad_id}')); return false;"> {phrase var="pay_later"} </a>
		</div>
	{/if}

	{if isset($bIsHavePayByCredit) && $bIsHavePayByCredit}
		<div class="ynsaPayButton ynsaLFloat">
		<a class="ynsaButton btn btn-sm btn-success" href="{url link='socialad.payment.pay' id=$aPlaceorderAd.ad_id method='paybycredit'}"> {phrase var='pay_by_your_credit'} </a>
		</div>
	{/if}
{/if}


</div>
