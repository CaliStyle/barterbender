<div id="yndirectory_businesstype">
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="businesstype" id="yndirectory_pagename" name="yndirectory_pagename">
	</div>
	<div class="yndirectory_box_type">
		<p>{phrase var='reason_for_creating_new_business'}</p>
		<br>
		<div id="yndirectory_type">
			{if $bCanCreateBusiness && $bCanCreateBusinessForClaiming}
				<div class="radio ync-radio-custom"><label><input type="radio" name="type" value="business" checked="checked"><i class="ico ico-circle-o mr-1"></i>{phrase var='for_my_personal_purpose'}</label></div>
				<div class="radio ync-radio-custom"><label><input type="radio" name="type" value="claiming"><i class="ico ico-circle-o mr-1"></i>{phrase var='for_claiming'}</label></div>
			{elseif $bCanCreateBusiness}
				<div class="radio ync-radio-custom"><label><input type="radio" name="type" value="business" checked="checked"><i class="ico ico-circle-o mr-1"></i>{phrase var='for_my_personal_purpose'}</label></div>
			{else}
				<div class="radio ync-radio-custom"><label><input type="radio" name="type" value="claiming" checked="checked"><i class="ico ico-circle-o mr-1"></i>{phrase var='for_claiming'}</label></div>
			{/if}
		</div>
		<div class="main_break"><button class="btn-sm btn-primary" id="yndirectory_okforclaiming" data-url="{$sNextUrl}" {if $bCanCreateBusiness}style="display: none;"{/if}>{phrase var='next'}</button></div>
	</div>

	{if $bCanCreateBusiness}
		<div class="yndirectory-packages" id="yndirectory_package">
			<div class="yndirectory-package-title">{phrase var='create_a_business_step_1_select_package'}</div>
			<div class="yndirectory-package-items" style="display: none;">
				{if count($aPackages) > 0}
					<div class="content row clearfix">	            
	                    <ul>
		                    {foreach from=$aPackages key=Id item=aPackageSlide}
		                        <li class="">
		                        	<div class="yndirectory-package-item">
		                        		<div class="yndirectory-package-item-action">
		                        			<button class="yndirectory-createabusiness btn btn-default" data-packageid="{$aPackageSlide.package_id}" data-url="{$sNextUrl}" data-module="{$sModule}" data-item="{$iItem}">{phrase var='create_a_business'}</button>
	                        			</div>					
		                        		<div class="yndirectory-package-item-name">{$aPackageSlide.name}</div>
		                        		<div class="yndirectory-package-item-price">{phrase var='package_price'}: <span>{$aPackageSlide.fee_display}</span></div>
		                        		<div class="yndirectory-package-item-description">
		                        			<ul>
											{for $i = 0; $i < 3; $i++}
												{if isset($aPackageSlide.modules[$i])}
													<li>{ _p var=$aPackageSlide.modules[$i].module_phrase}</li>
												{/if}
											{/for}
											</ul>
		                        		</div>
		                        		
		                        	</div>
		                        </li>
		            		{/foreach}
	                    </ul>
					</div>
				{else}
					{phrase var='nothing_item_s'}
				{/if}
			</div>

			<div class="yndirectory-package-title">{phrase var='all_packages'}</div>
			<div class="yndirectory-package-items">
				{foreach from=$aPackages key=Id item=aPackage}
					<div class="yndirectory-package-item">
						<div class="yndirectory-package-item-action">
							<div><button class="yndirectory-createabusiness btn btn-default" data-packageid="{$aPackage.package_id}" data-url="{$sNextUrl}" data-module="{$sModule}" data-item="{$iItem}">{phrase var='create_a_business'}</button></div>
						</div>

						<div class="yndirectory-package-item-name">{$aPackage.name}</div>
						<div class="yndirectory-package-item-price">
							<div>{phrase var='price'}: <span>{$aPackage.fee_display}</span></div>
							<div>{phrase var='duration'}:
								{if $aPackage.expire_type == 0}
									{phrase var='never_expired'}
								{elseif $aPackage.expire_type == 1}
									{$aPackage.expire_number} {phrase var='day_s'}
								{elseif $aPackage.expire_type == 2}
									{$aPackage.expire_number} {phrase var='week_s'}
								{elseif $aPackage.expire_type == 3}
									{$aPackage.expire_number} {phrase var='month_s'}
								{/if}
							</div>
						</div>
						<div class="yndirectory-package-item-features">
							<div>{phrase var='features_available'}: </div>
							<div>
								<ul>
									{foreach from=$aPackage.settings key=Id item=setting}
										<li>
											{$setting.setting_phrase|convert}
										</li>
									{foreachelse}
										{phrase var='nothing_item_s'}
									{/foreach}
								</ul>
							</div>					
						</div>
						<div class="yndirectory-package-item-description">
							<div>{phrase var='modules_available'}: </div>
							<div>
								<ul>
									{foreach from=$aPackage.modules key=Id item=module}
										<li>
											{$module.module_phrase|convert}
										</li>
									{foreachelse}
										{phrase var='nothing_item_s'}
									{/foreach}
								</ul>
							</div>					
						</div>
						
					</div>
				{foreachelse}
					{phrase var='nothing_item_s'}
				{/foreach}
			</div>
		</div>
	{/if}
</div>