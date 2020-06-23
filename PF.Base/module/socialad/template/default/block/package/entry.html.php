	<div class="ynsaPackageEntry">
		<div class="ynsaName ynsaLFloat">
			{$aSaPackage.package_name}
		</div>
		<div class="clear"></div>
		<div class="ynsaPriceAndBenefit ynsaLFloat">
			<span class="ynsaPrice"> {$aSaPackage.package_price_text} </span> {phrase var='for_lower'} <span class="ynsaBenefit">{$aSaPackage.package_benefit_text}</span>
		</div>
		<div class="ynsaAttributes ynsaLFloat">
			<div class="ynsaItemType">
				{phrase var='you_can_advertise'} <strong>{$aSaPackage.package_allow_item_type_text}</strong> {phrase var='as'} :
			</div>
			 <ul>
				{foreach from=$aSaPackage.package_allow_ad_type_custom_text_list item=sCustomText}
					<li> {$sCustomText} </li>
				{/foreach}
			</ul>

		</div> <!-- attribute -->

		{if isset($bNoCreateBtn) && $bNoCreateBtn}
		{else}
		<div class="ynsaCreateAdButton ynsaLFloat">
			<a class="ynsaButton btn btn-success btn-sm" href="{if $iSimilar}{url link='socialad.ad.add' package=$aSaPackage.package_id createsimilar=$iSimilar}{else}{url link='socialad.ad.add' package=$aSaPackage.package_id}{/if}"> {phrase var='create_new_ads'}</a>
		</div>
		{/if}
		<div class="clear"></div>
		<div class="ynsaDescription">
			{$aSaPackage.package_description}
		</div>
	</div>
