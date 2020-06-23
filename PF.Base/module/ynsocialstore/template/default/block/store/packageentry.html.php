<div class="ynsocialstore-package-item">
	<div class="ynsocialstore-package-item-action">
		<div><button class="ynsocialstore-createastore button" data-packageid="{$aPackage.package_id}" data-module="{$sModule}" data-item="{if isset($iItem)}{$iItem}{/if}" data-url="{$sNextUrl}">{_p var='ynsocialstore.choose_this_package'}</button></div>
	</div>

	<div class="ynsocialstore-package-item-name">{$aPackage.name}</div>
	<div class="ynsocialstore-package-item-price">
		<div>{_p var='ynsocialstore.price'}: <span>{$aPackage.fee_display}</span></div>
		<div>{_p var='ynsocialstore.duration'}:
			{if $aPackage.expire_number == 0}
				{_p var='ynsocialstore.never_expired'}
			{else}
				{$aPackage.expire_number} {_p var='ynsocialstore.day_s'}
			{/if}
		</div>
	</div>
	<div class="ynsocialstore-package-item-description">
		<div>{_p var='ynsocialstore.package_support'}:</div>
		<div>
			<ul>
				<li>
				{if $aPackage.max_products > 0}
					{$aPackage.max_products} {_p var='ynsocialstore.products_can_be_created'}
				
				{elseif $aPackage.max_products == 0}
					{_p var='ynsocialstore.number_of_product_is_unlimited'}
				{/if}
				
				</li>
				{if $aPackage.theme_editable}
				<li>
					{_p var='ynsocialstore.support_changing_store_theme'}
				</li>
				{/if}
				{if $aPackage.enable_attribute}
				<li>
					{_p var='ynsocialstore.support_attribute_to_product_in_store'}
				</li>
				{/if}
				<li>
					{_p var='ynsocialstore.fee_for_featuring_store'}: {$aPackage.feature_store_fee_display} / {_p var='ynsocialstore.day'}</li>
				<li>
					{_p var='ynsocialstore.fee_for_featuring_products_in_store'}: {$aPackage.feature_product_fee_display} / {_p var='ynsocialstore.product'} / {_p var='ynsocialstore.day'}
				</li>
			</ul>
		</div>
	</div>
	
</div>