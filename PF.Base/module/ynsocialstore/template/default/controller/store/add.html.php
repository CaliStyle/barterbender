<div class="ynsocialstore-hiddenblock">
	<input type="hidden" value="addstore" id="ynsocialstore_pagename" name="ynsocialstore_pagename">

</div>	
{if isset($invoice_id) && (int)$invoice_id > 0}
	<div class="ynsocialstore_box_payment">
		<h3>{_p var='ynsocialstore.payment_methods'}</h3>
		{module name='api.gateway.form'}			
	</div>
{else}
	{if isset($sError) && !empty($sError)}
		{$sError}
	{else}
	<input type="hidden" value="{if $aPackage !== null}{$aPackage.feature_store_fee}{else}0{/if}" id="ynsocialstore_defaultfeaturefee">
	<input type="hidden" id="ynsocialstore_corepath" value="{$core_path}">

	<div id="ynsocialstore_add" class="ynstore-store-form-block">
		{$sCreateJs}
		{if !$bIsEdit}
		<h4>{_p var='ynsocialstore.open_new_store_step_2_input_information'}</h4>
		{/if}
		<form enctype="multipart/form-data" id="ynsocialstore_edit_store_form" action="{url link='current'}" class="ynsocialstore-add-edit-form" method="post">
			{if $aPackage !== null}
				{if (!$bIsEdit) || ($aPackage.theme_editable == 1)}
				<p class="description">{_p var='ynsocialstore.please_select_one_theme_for_your_store_note_that_if_this_package_supports_multiple_and_changeable_themes_you_can_re_select_theme_any_time_you_want_at_dashboard'}</p>

				<div id="ynsocialstore_theme" class="ynstore-choose-themes">
					{foreach from=$aPackage.themes key=Id item=theme}
						<div class="yndirectory-theme-item">
							<div>
								<img
									src="
									{if $theme == 1}
										{$core_path}module/ynsocialstore/static/image/theme_1.png
									{elseif $theme == 2}
										{$core_path}module/ynsocialstore/static/image/theme_2.png
									{/if}" />
							</div>
							<div>
	        	                <input type="radio" name="val[theme]" value="{$theme}"
	        	                	{if isset($aForms) && isset($aForms.theme_id)}
		        	                	{if ($aForms.theme_id == $theme)}
	        	                			checked="checked"
		        	                	{/if}
	        	                	{else}
		        	                	{if $Id == 0}
	        	                			checked="checked"
		        	                	{/if}
	        	                	{/if}
	        	                />
							</div>
						</div>
					{/foreach}
				</div>
                {elseif $bIsEdit && empty($aPackage.theme_editable)}
                <input type="hidden" name="val[theme]" value="{$aForms.theme_id}">
				{/if}
			{/if}
				<div class="">
					<div id="js_custom_privacy_input_holder">
				        {if $bIsEdit && (empty($sModule) || $sModule == 'ynsocialstore')}
				            {module name='privacy.build' privacy_item_id=$aForms.store_id privacy_module_id='ynsocialstore_store'}
				        {/if}
			        </div>
			       	<div id="js_ynsocialstore_block_main" class="js_ynsocialstore_block page_section_menu_holder">
			       		<h2>{_p var='ynsocialstore.general_info'}</h2>
			       		<div class="form-group">
		                    <label for="name">{required}{_p var='ynsocialstore.store_name'}: </label>
		                    <input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" maxlength="200"/>
			            </div>

			            <div class="form-group">
		                    <label for="description">{_p var='ynsocialstore.description'}:</label>
		                    {editor id='description'}
			            </div>

			            <div class="form-group">
							<label for="name">{required}{_p var='ynsocialstore.short_description'}: </label>
							<textarea class="form-control" cols="50" rows="5" name="val[short_description]" class="" id="short_description">{value id='short_description' type='textarea'}</textarea>
							<div class="extra_info">{_p var='ynsocialstore.max_characters'}: 500</div>
						</div>

						<div class="form-group">
							<label for="name">{_p var='ynsocialstore.business_type'}:
							</label>
							<select name="val[business_type]" class="form-control" >
								{foreach from=$aBusinessType key=iKey item=aType}
									<option value="{$iKey}" {if $bIsEdit && $aForms.business_type == $iKey}checked{/if}>{$aType}</option>
								{/foreach}
							</select>
						</div>

						<div class="form-group">
							<label for="name">{_p var='ynsocialstore.year_established'}:
							</label>
							<input class="form-control" type="text" name="val[established_year]" id="established_year" value="{value type='input' id='established_year'}" />
						</div>

						<div class="form-group">
							<label for="name">{required}{_p var='ynsocialstore.main_categories'}:
							</label>
							<select class="form-control" multiple="multiple" data-placeholder="{_p var='ynsocialstore.select'}" name="val[category][]" id="categories">
								{foreach from=$aAllCategories key=iId item=aCategory}
                                    {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aCategory.title|convert}
                                    {/if}
									<option value="{$aCategory.category_id}"
									{if isset($aForms) && isset($aForms.categories) && $aForms.categories}
										{foreach from=$aForms.categories item=aSelectedCategory}
											{if $aCategory.category_id == $aSelectedCategory} selected="selected" {/if}
										{/foreach}
									{/if}

									> {$value_name} </option>
									{if isset($aCategory.sub_1) && count($aCategory.sub_1)}
										{foreach from=$aCategory.sub_1 key=iIdSub1 item=aSub1}
                                            {if Phpfox::isPhrase($this->_aVars['aSub1']['title'])}
                                                <?php $this->_aVars['value_name'] = _p($this->_aVars['aSub1']['title']) ?>
                                            {else}
                                                {assign var='value_name' value=$aSub1.title|convert}
                                            {/if}
											<option value="{$aSub1.category_id}"
											{if isset($aForms) && isset($aForms.categories) && $aForms.categories}
												{foreach from=$aForms.categories item=aCategorySub1}
													{if $aSub1.category_id == $aCategorySub1} selected="selected" {/if}
												{/foreach}
											{/if}>
												&nbsp;&nbsp;&nbsp;{$value_name}
											</option>
										{/foreach}
									{/if}
									{if isset($aCategory.sub_2) && count($aCategory.sub_2)}
										{foreach from=$aCategory.sub_2 key=iIdSub2 item=aSub2}
                                            {if Phpfox::isPhrase($this->_aVars['aSub2']['title'])}
                                                <?php $this->_aVars['value_name'] = _p($this->_aVars['aSub2']['title']) ?>
                                            {else}
                                                {assign var='value_name' value=$aSub2.title|convert}
                                            {/if}
											<option value="{$aSub2.category_id}"
											{if isset($aForms) && isset($aForms.categories) && $aForms.categories}
												{foreach from=$aForms.categories item=aCategorySub2}
													{if $aSub2.category_id == $aCategorySub2} selected="selected" {/if}
												{/foreach}
											{/if}>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$value_name}
											</option>
										{/foreach}
									{/if}
								{/foreach}
							</select>
						</div>

						<div class="form-group">
							<label for="name">{required}{_p var='ynsocialstore.shipping_and_payment_infomation'}:
							</label>
							<textarea class="form-control" cols="50" rows="5" name="val[ship_payment_info]" class="" id="ship_payment_info">{value id='ship_payment_info' type='textarea'}</textarea>
						</div>

						<div class="form-group">
							<label for="name">{required}{_p var='ynsocialstore.return_policy'}:
							</label>
							<textarea class="form-control" cols="50" rows="5" name="val[return_policy]" class="" id="return_policy">{value id='return_policy' type='textarea'}</textarea>
						</div>

						<div class="form-group">
							<label for="name">{required}{_p var='ynsocialstore.buyer_protection'}:
							</label>
							<textarea class="form-control" cols="50" rows="5" name="val[buyer_protection]" class="" id="buyer_protection">{value id='buyer_protection' type='textarea'}</textarea>
						</div>

						<div class="form-group">
							<label for="name">{_p var='ynsocialstore.tax'} (%):
							</label>
							<input class="form-control" type="text" id="tax" name="val[tax]" value="{value type='input' id='tax'}" maxlength="9"/>
						</div>

						<h2>{_p var='ynsocialstore.branches'}</h2>

						<div id="ynsocialstore_locationlist">
						 	{if isset($aForms) && isset($aForms.location) && count($aForms.location)}
			                    {foreach from=$aForms.location key=keyall_location item=itemall_location}
					            	<div data-item="{$keyall_location}" class="ynsocialstore-location">
					                	<div class="form-group">
											<label for="">
												{_p var='ynsocialstore.branch_name'}
											</label>
											<input class="form-control" type="text" name="val[location_title][]" value="{$itemall_location.title}" maxlength="150"/>
										</div>

                                        <div class="form-group">
                                            <label for="">
                                                {_p var='ynsocialstore.location'}
                                            </label>
                                            <input class="form-control" type="text" name="val[location][]" value="{$itemall_location.location}" maxlength="255"/>
                                        </div>

										<div class="form-group">
											<label for="">
												{_p var='ynsocialstore.address'}
											</label>
											<input class="form-control" id="ynsocialstore_location_{if $keyall_location == 0}99999{else}1{/if}" type="text" data-inputid="fulladdress" name="val[location_fulladdress][]" value="{$itemall_location.address}" size="30" placeholder="{_p var='ynsocialstore.enter_a_location'}" autocomplete="off">
											<P class="help-block">
												<a href="javascript:void(0)" onclick="ynsocialstore.viewMap(this); return false;">{_p var='ynsocialstore.view_map'}</a>
											</P>
										</div>
											<input type="hidden" data-inputid="address" name="val[location_address][]" value="{$itemall_location.address}" />
											<input type="hidden" data-inputid="city" name="val[location_address_city][]" value="" />
					                    	<input type="hidden" data-inputid="country" name="val[location_address_country][]" value="" />
					                    	<input type="hidden" data-inputid="zipcode" name="val[location_address_zipcode][]" value="" />
					                    	<input type="hidden" data-inputid="lat" name="val[location_address_lat][]" value="{$itemall_location.latitude}" />
					                    	<input type="hidden" data-inputid="lng" name="val[location_address_lng][]" value="{$itemall_location.longitude}" />

										<P class="help-block">
						                   	{if $keyall_location == 0}
							                    <a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'location'); return false;">
							                    	{img theme='misc/add.png' class='v_middle'}
							                    </a>
							                    <a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'location'); return false;">
							                    	<i class="ico ico-close-circle text-danger"></i>
							                    </a>
						                   	{else}
							                    <a id="ynsocialstore_delete" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'location'); return false;">
							                    	<i class="ico ico-close-circle text-danger"></i>
							                    </a>
						                   	{/if}
										</P>
					                </div>
			                    {/foreach}
						 	{else}
				            	<div data-item="1" class="ynsocialstore-location">
			                		<div class="form-group">
										<label for="">
											{_p var='ynsocialstore.branch_name'}
										</label>
										<input class="form-control" type="text" name="val[location_title][]" value="" maxlength="150"/>
									</div>

                                    <div class="form-group">
                                        <label for="val[location][]">
                                            {_p var='ynsocialstore.location'}
                                        </label>
                                        <input class="form-control" type="text" name="val[location][]" value="" maxlength="255"/>
                                    </div>

									<div class="form-group">
										<label for="">
											{_p var='ynsocialstore.address'}
										</label>
			                    		<input class="form-control" id="ynsocialstore_location_99999" type="text" data-inputid="fulladdress" name="val[location_fulladdress][]" value="" size="30" placeholder="{_p var='ynsocialstore.enter_a_location'}" autocomplete="off">
				                    	<p class="help-block">
											<a href="javascript:void(0)" onclick="ynsocialstore.viewMap(this); return false;">{_p var='ynsocialstore.view_map'}</a>
				                    	</p>
									</div>

			                    	<input type="hidden" data-inputid="address" name="val[location_address][]" value="" />
									<input type="hidden" data-inputid="city" name="val[location_address_city][]" value="" />
			                    	<input type="hidden" data-inputid="country" name="val[location_address_country][]" value="" />
			                    	<input type="hidden" data-inputid="zipcode" name="val[location_address_zipcode][]" value="" />
			                    	<input type="hidden" data-inputid="lat" name="val[location_address_lat][]" value="" />
			                    	<input type="hidden" data-inputid="lng" name="val[location_address_lng][]" value="" />

									<div class="extra_info mt-1">
										<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'location'); return false;">
											{img theme='misc/add.png' class='v_middle'}
										</a>
										<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'location'); return false;">
											<i class="ico ico-close-circle text-danger"></i>
										</a>
									</div>
				                </div>
						 	{/if}
						</div>

			            <h2>{_p var='ynsocialstore.contact_information'}</h2>
			            <!--Phone list-->
			            <div id="ynsocialstore_phonelist" class="form-group">
		                    <label for="phone">{required}{_p var='ynsocialstore.phone'}: </label>
						 	{if isset($aForms) && isset($aForms.phone)}
			                    {foreach from=$aForms.phone key=keyphone item=itemphone}
									<div class="ynstore_store-phone">
										<input class="form-control" type="text" name="val[phone][]" value="{$itemphone.info}" size="40" maxlength="150"/>
										<div class="extra_info mt-1">
											{if $keyphone == 0}
												<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'phone'); return false;">
													{img theme='misc/add.png' class='v_middle'}
												</a>

												<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'phone'); return false;">
													<i class="ico ico-close-circle text-danger"></i>
												</a>
											{else}
												<a id="ynsocialstore_delete" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'phone'); return false;">
													<i class="ico ico-close-circle text-danger"></i>
												</a>
											{/if}
										</div>
									</div>
			                    {/foreach}
			                {else}
								<div class="ynstore_store-phone">
									<input class="form-control" type="text" name="val[phone][]" value="" size="40" maxlength="150"/>
									<div class="extra_info mt-1">
										<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'phone'); return false;">
											{img theme='misc/add.png' class='v_middle'}
										</a>

										<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'phone'); return false;">
											<i class="ico ico-close-circle text-danger"></i>
										</a>
									</div>
								</div>
			                {/if}
			            </div>
			            <!--Fax list-->
			            <div id="ynsocialstore_faxlist" class="form-group">
		                    <label for="fax">{_p var='ynsocialstore.fax'}: </label>
			                {if isset($aForms) && isset($aForms.fax)}
			                	{foreach from=$aForms.fax key=keyfax item=itemfax}
									<div class="ynstore_store-fax">
										<input class="form-control" type="text" name="val[fax][]" value="{$itemfax.info}" size="40" maxlength="150"/>
										<div class="extra_info mt-1">
											{if $keyfax == 0}
												<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'fax'); return false;">
													{img theme='misc/add.png' class='v_middle'}
												</a>

												<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'fax'); return false;">
													<i class="ico ico-close-circle text-danger"></i>
												</a>
											{else}
												<a id="ynsocialstore_delete" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'fax'); return false;">
													<i class="ico ico-close-circle text-danger"></i>
												</a>
											{/if}
										</div>
									</div>
			                	{/foreach}
			                {else}
								<div class="ynstore_store-fax">
									<input class="form-control" type="text" name="val[fax][]" value="" size="40" maxlength="150"/>
									<div class="extra_info mt-1">
										<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'fax'); return false;">
											{img theme='misc/add.png' class='v_middle'}
										</a>

										<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'fax'); return false;">
											<i class="ico ico-close-circle text-danger"></i>
										</a>
									</div>
								</div>
			                {/if}
			            </div>
			            <!--Email-->
			            <div class="form-group">
		                    <label for="fax">{required}{_p var='ynsocialstore.email'}: </label>
		                    <input class="form-control" type="text" name="val[email]" id="ynsocialstore_email" value="{if isset($aForms) && isset($aForms.email)}{$aForms.email}{/if}" size="40" />
			            </div>
			            <!--Country-->
			            <div class="form-group">
		                    <label for="country">{_p var='ynsocialstore.country'}: </label>
							{select_location}
							{module name='core.country-child'}
			            </div>
			            <!-- City/Zipcode -->
			            <div class="form-group">
		                    <label for="city">{_p var='ynsocialstore.city'}: </label>
		                    <input class="form-control" type="text" name="val[city]" value="{if isset($aForms) && isset($aForms.city)}{$aForms.city}{/if}" size="40" maxlength="150"/>
			            </div>
			            <div class="form-group">
		                    <label for="zip_code">{_p var='ynsocialstore.zip_code'}: </label>
		                    <input class="form-control" type="text" name="val[zip_code]" value="{if isset($aForms) && isset($aForms.postal_code)}{$aForms.postal_code}{/if}" size="40" maxlength="150"/>
			            </div>
			            <!-- Weblist -->
			            <div id="ynsocialstore_websitelist" class="form-group">
			                    <label for="web_address">{_p var='ynsocialstore.web_address'}: </label>
			                {if isset($aForms) && isset($aForms.website)}
			                	{foreach from=$aForms.website key=keyweb_address item=itemweb_address}
				                    <div class="ynstore_store-website">
										<input class="form-control" type="text" name="val[web_address][]" value="{$itemweb_address.info}" size="40" maxlength="150"/>
										<div class="extra_info mt-1">
											{if $keyweb_address == 0}
												<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'web_address'); return false;">
													{img theme='misc/add.png' class='v_middle'}
												</a>

												<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'web_address'); return false;">
													<i class="ico ico-close-circle text-danger"></i>
												</a>
											{else}
												<a id="ynsocialstore_delete" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'web_address'); return false;">
													<i class="ico ico-close-circle text-danger"></i>
												</a>
											{/if}
										</div>
									</div>
			                	{/foreach}
			                {else}
								<div class="ynstore_store-website">
									<input class="form-control" type="text" name="val[web_address][]" value="" size="40" maxlength="150"/>
									<div class="extra_info mt-1">
										<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'web_address'); return false;">
											{img theme='misc/add.png' class='v_middle'}
										</a>

										<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'web_address'); return false;">
											<i class="ico ico-close-circle text-danger"></i>
										</a>
									</div>
								</div>
			                {/if}
			            </div>
			            <!--Logo-->
			            <div class="form-group">
                            {if !empty($aForms.current_logo) && !empty($aForms.store_id)}
                                {module name='core.upload-form' type='ynsocialstore_store_logo' current_photo=$aForms.current_logo id=$aForms.store_id}
                                <input type="hidden" name="val[logo_path]" value="{value type='input' id='logo_path'}">
                                <input type="hidden" name="val[server_id]" value="{value type='input' id='server_id'}">
                            {else}
                                {module name='core.upload-form' type='ynsocialstore_store_logo' current_photo=''}
                            {/if}
			            </div>
			            <!--Cover-->
			            <div class="form-group">
                            {if !empty($aForms.current_cover) && !empty($aForms.store_id)}
                                {module name='core.upload-form' type='ynsocialstore_store_cover' current_photo=$aForms.current_cover id=$aForms.store_id}
                                <input type="hidden" name="val[cover_path]" value="{value type='input' id='cover_path'}">
                                <input type="hidden" name="val[cover_server_id]" value="{value type='input' id='cover_server_id'}">
                            {else}
                                {module name='core.upload-form' type='ynsocialstore_store_cover' current_photo=''}
                            {/if}
			            </div>
			            <h2>{_p var='ynsocialstore.additional_information'}</h2>
			            <!--Additional info-->
			            <div class="form-group">
							<div id="ynsocialstore_addinfolist">
							 	{if isset($aForms) && isset($aForms.addinfo)}
				                    {foreach from=$aForms.addinfo key=keyall_addinfo item=itemall_addinfo}
						            	<div data-item="{$keyall_addinfo}" class="ynsocialstore-addinfo">
												<div class="form-group">
													<label for="">
														{_p var='ynsocialstore.title'}
													</label>
													<input class="form-control" id="ynsocialstore_addinfo_title" type="text" data-inputid="addinfo" name="val[addinfo_title][]" value="{$itemall_addinfo.title}"/>
												</div>

												<div class="form-group">
													<label>
														{_p var='ynsocialstore.content'}
													</label>
													<input class="form-control" id="ynsocialstore_addinfo_content" type="text" data-inputid="addinfo" name="val[addinfo_content][]" value="{$itemall_addinfo.title}">
												</div>
											<div class="extra_info mt-1"><div>{_p var='ynsocialstore.max_characters'}: 150</div>
						                   	{if $keyall_addinfo == 0}
							                    <a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'addinfo'); return false;">
							                    	{img theme='misc/add.png' class='v_middle'}
							                    </a>
							                    <a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'addinfo'); return false;">
							                    	<i class="ico ico-close-circle text-danger"></i>
							                    </a>
						                   	{else}
							                    <a id="ynsocialstore_delete" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'addinfo'); return false;">
							                    	<i class="ico ico-close-circle text-danger"></i>
							                    </a>
						                   	{/if}
											</div>
						                </div>
				                    {/foreach}
							 	{else}
					            	<div data-item="1" class="ynsocialstore-addinfo">
					            		<div class="form-group">
											<label for="">
												{_p var='ynsocialstore.title'}
											</label>
											<input class="form-control" id="ynsocialstore_addinfo_title" type="text" data-inputid="addinfo" name="val[addinfo_title][]" value=""/>
					            		</div>

					            		<div class="form-group">
						            		<label for="">
												{_p var='ynsocialstore.content'}
						            		</label>
				                    		<input class="form-control" id="ynsocialstore_addinfo_content" type="text" data-inputid="addinfo" name="val[addinfo_content][]" value="">
											<div class="extra_info">{_p var='ynsocialstore.max_characters'}: 150</div>
										</div>

										<p class="help-block">
											<a id="ynsocialstore_add" href="javascript:void(0)" onclick="ynsocialstore.appendPredefined(this,'addinfo'); return false;">
												{img theme='misc/add.png' class='v_middle'}
											</a>
											<a id="ynsocialstore_delete" style="display: none;" href="javascript:void(0)" onclick="ynsocialstore.removePredefined(this,'addinfo'); return false;">
												<i class="ico ico-close-circle text-danger"></i>
											</a>
										</p>
					                </div>
							 	{/if}
							</div>
			            </div>

						{if $aPackage !== null}
							{if !$bIsEdit || ($bIsEdit && $aForms.status == 'draft')}
			            	<div class="">{_p var='ynsocialstore.publish_fee'}: {$aPackage.fee} {$aCurrentCurrencies.0.currency_id}</div>
			            	({_p var='ynsocialstore.store_publishing_fee_depend_on_what_package_you_choose_at_step_1'})
							{/if}
							<div class="form-group {if isset($sModule) && $sModule == 'pages'}hide{/if}">
								{if (!$bIsEdit && (Phpfox::getUserParam('ynsocialstore.can_feature_own_store') || Phpfox::getUserParam('ynsocialstore.can_feature_store'))) || ($bIsEdit && Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('ynsocialstore.can_feature_own_store')) || ($bIsEdit && Phpfox::getUserParam('ynsocialstore.can_feature_store'))}
								<label>{_p var='ynsocialstore.feature'}:</label>
								{/if}
								{if isset($aForms) && $aForms.is_featured}
								<p class="help-block">
										{if isset($aForms.is_unlimited_feature) && $aForms.is_unlimited_feature}
											{_p var='ynsocialstore.note_this_store_is_featured_unlimited_time'}
										{else}
											{_p var='ynsocialstore.note_this_store_is_featured_until_expire_date' expire_date=$aForms.expire_feature_day}
										{/if}

								</p>
								{/if}
								{if (!$bIsEdit && (Phpfox::getUserParam('ynsocialstore.can_feature_own_store') || Phpfox::getUserParam('ynsocialstore.can_feature_store'))) || ($bIsEdit && Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('ynsocialstore.can_feature_own_store')) || ($bIsEdit && Phpfox::getUserParam('ynsocialstore.can_feature_store'))}
									<div class="help-block">{_p var='ynsocialstore.feature_this_store_for'}</div>
									<input class="form-control" id="ynsocialstore_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10">
									<div class="help-block">{_p var='ynsocialstore.day_s_with'}</div>
									<input class="form-control" id="ynsocialstore_feature_fee_total" type="text" value="0" size="10" readonly />
									<div class="help-block">{$aCurrentCurrencies.0.currency_id}</div>
									<span class="help-block">({_p var='ynsocialstore.fee_to_feature_store_feature_fee_currency_id_for_1_day' feature_fee=$aPackage.feature_store_fee currency_id=$aCurrentCurrencies.0.currency_id})</span>
								{else}
									<input class="form-control" id="ynsocialstore_feature_number_days" type="hidden" name="val[feature_number_days]" value="0" size="10">
								{/if}

							</div>
						{/if}

						{if (empty($sModule) || $sModule == 'ynsocialstore') && Phpfox::isModule('privacy')}
						<div class="form-group">
							<label for="">
								{_p var='ynsocialstore.privacy'}:
							</label>
							{module name='privacy.form' privacy_name='privacy' privacy_info='ynsocialstore.control_who_can_see_this_store'}
						</div>
						{/if}

						<div class="table_clear" id="ynsocialstore_submit_buttons">
							{if !$bIsEdit}
							<input id="ynsocialstore_submit" type="submit" class="button btn btn-primary" value="{_p var='ynsocialstore.open_store'}" name="val[create]"/>
							<input id="ynsocialstore_submit_draft" type="submit" class="button btn btn-default" value="{_p var='ynsocialstore.save_as_draft'}" name="val[draft]" />
							{else}
							{if $bIsEdit && $aForms.status == 'draft'}
							<input id="ynsocialstore_submit" type="submit" class="button btn btn-primary" value="{_p var='ynsocialstore.open_store'}" name="val[create]"/>
							{/if}
							<input id="ynsocialstore_submit" type="submit" class="button btn btn-primary" value="{_p var='ynsocialstore.update_store'}" name="val[update]"/>
							{/if}
							<a href="{$sBackUrl}" class="btn btn-default" value="{_p var='ynsocialstore.back'}" name="val[back]">{_p var='ynsocialstore.back'}</a>
						</div>
			       	</div>
				</div>
		</form>
	</div>
	{/if}
{/if}
{if !isset($invoice_id)}
	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places"></script>
{/if}
{if PHPFOX_IS_AJAX_PAGE}
{literal}
<script type="text/javascript">
	$Behavior.globalInit();
	ynstoreReInitAddStore = setInterval(function(){
		ynsocialstore.initAddStore();
		if($('#ynsocialstore_email').validate())
		{
			clearInterval(ynstoreReInitAddStore);
		}
		
	},1000)
</script>
{/literal}
{/if}