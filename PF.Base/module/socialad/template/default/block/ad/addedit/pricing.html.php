

<div class="ynsaSection section_float_left">

	<div class="ynsaSectionHeading ynsaClearFix">
		<div class="ynsaLFloat">
			<div class="ynsaHeadingText">
				{phrase var='pricing'}
			</div>
		</div>
		<div class="ynsaRFloat">
			<!-- put help link here -->
		</div>
	</div>
	
	<div class="ynsaSectionContent min_height">	
		<div class="ynsaLeftInnerContent"> 

			<div class="form-group">
				<label>
					{phrase var='how_many_package_s_do_you_want'}
				</label>
					
				<div class="
				{if $aPricingPackage.package_is_unlimited}
					ynsaDisplayNone	
				{/if}"
				 >
						<input class="form-control" data-price-per-package="{$aPricingPackage.package_price}"
							data-benefit-per-package="{$aPricingPackage.package_benefit_number}" 
							id="js_ynsa_number_of_package" 
							type="text" 
							style="width:64px" 
							name="val[ad_number_of_package]" 
							value="{if !isset($aForms)}1{else}{$aForms.ad_number_of_package}{/if}"
							{if isset($aForms) && $aForms.ad_status != 1 && $aForms.ad_status != 2}readonly{/if}
							/>
				</div>
				<div class="mt-1 extra_info 
					{if $aPricingPackage.package_is_unlimited}
						ynsaDisplayNone	
					{/if}"
				>
					{phrase var='total'} {$aPricingPackage.package_benefit_type_text}: <span id="js_ynsa_total_number_of_benefit"> </span> {$aPricingPackage.package_benefit_type_text}
				</div>
				<div class="extra_info">
					{phrase var='pricing_notice' price_text=$aPricingPackage.package_price_text benefit_text=$aPricingPackage.package_benefit_text benefit_number=$aPricingPackage.package_benefit_number}
				</div>
			</div>
		</div>


		<div class="ynsaExtraSectionInfo"> 
			<div class="ynsaExtraSectionInfoTitle">
				{phrase var='price'}
			</div>
			<div class="ynsaExtraSectionInfoContent">
				<div class="ynsaHighlightExtraContent">
					<span id="js_ynsa_price"> {$aPricingPackage.package_price} </span> {$aPricingPackage.package_currency}
				</div>
			</div>
		</div>

	</div> <!-- end audience section content -->
</div> <!-- end audience section -->
