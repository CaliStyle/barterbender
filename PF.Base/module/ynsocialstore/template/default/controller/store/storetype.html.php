<div id="ynsocialstore_storetype">
	<div class="ynsocialstore-hiddenblock">
		<input type="hidden" value="storetype" id="ynsocialstore_pagename" name="ynsocialstore_pagename">
	</div>
{if !empty($sError)}
	{$sError}
{else}
	{if $bCanCreateStore}
		<div class="ynstore-packages-block" id="ynsocialstore_package">
			<div class="ynstore-title">{_p var='ynsocialstore.open_new_store_step_1_choose_package'}</div>

			{if count($aPackages) > 0}
			<!--div class="ynstore-title">{_p var='ynsocialstore.all_packages'}</div-->

			<div class="ynstore-package-items">
				{foreach from=$aPackages key=Id item=aPackage}
					<div class="ynstore-package-item">
						<div class="ynstore-choose-btn ynsocialstore-createastore" data-packageid="{$aPackage.package_id}" data-module="{$sModule}" data-item="{$iItem}" data-url="{$sNextUrl}">
							<span>
								{_p var='ynsocialstore.choose_this_package'}
								<i class="ico ico-angle-right"></i>
							</span>
						</div>

						<div class="ynstore-package-left">
							<div class="ynstore-name">{$aPackage.name}</div>
							<div class="ynstore-price">
								{$aPackage.fee_display}
							</div>
							<div class="ynstore-duration">
								{if $aPackage.expire_number == 0}
									{_p var='ynsocialstore.never_expired'}
								{else}
									{$aPackage.expire_number} {_p var='ynsocialstore.day_s'}
								{/if}
							</div>
						</div>

						<div class="ynstore-package-right">
							<ul>
								<li>
								<i class="ico ico-angle-right"></i>
								{if $aPackage.max_products > 0}
									{$aPackage.max_products} {_p var='ynsocialstore.products_can_be_created'}
								{elseif $aPackage.max_products == 0}
									{_p var='ynsocialstore.number_of_product_is_unlimited'}
								{/if}
								</li>
								<li>
								<i class="ico ico-angle-right"></i>
								{if $aPackage.max_photo_per_product > 0}
									{$aPackage.max_photo_per_product} {_p var='ynsocialstore.photos_can_add_to_each_product'}
								{elseif $aPackage.max_photo_per_product == 0}
									{_p var='ynsocialstore.number_of_photo_can_add_to_each_product_is_unlimited'}
								{/if}
								</li>

								{if $aPackage.theme_editable}
								<li>
									<i class="ico ico-angle-right"></i>
									{_p var='ynsocialstore.support_changing_store_theme'}
								</li>
								{/if}

								{if $aPackage.enable_attribute}
								<li>
									<i class="ico ico-angle-right"></i>
									{_p var='ynsocialstore.support_attribute_to_product_in_store'}
								</li>
								{/if}

								<li>
									<i class="ico ico-angle-right"></i>
									{_p var='ynsocialstore.fee_for_featuring_store'}: <b>{$aPackage.feature_store_fee_display}/{_p var='ynsocialstore.day'}</b>
								</li>

								<li>
									<i class="ico ico-angle-right"></i>
									{_p var='ynsocialstore.fee_for_featuring_products_in_store'}:
									<b>{$aPackage.feature_product_fee_display}/{_p var='ynsocialstore.product'}/{_p var='ynsocialstore.day'}</b>
								</li>
							</ul>
						</div>
					</div>
				{foreachelse}
					{_p var='ynsocialstore.nothing_item_s'}
				{/foreach}
			</div>
		{else}
			{_p var='ynsocialstore.no_packages_found'}
		{/if}
		</div>   
	{elseif !empty($sError)}
		{$sError}	       
	{/if}
{/if}
</div>