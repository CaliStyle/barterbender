<div id="yndirectory_add">
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="add" id="yndirectory_pagename" name="yndirectory_pagename">
	</div>
	{if isset($invoice_id) && (int)$invoice_id > 0}
		<div class="yndirectory_box_payment">
			<h3>{phrase var='payment_methods'}</h3>
			{module name='api.gateway.form'}
		</div>
	{else}
		<div>
			{$sCreateJs}
			<form enctype="multipart/form-data" id="yndirectory_edit_directory_form" action="{$sFormUrl}" class="yndirectory-add-edit-form" method="post" onsubmit="onPreventDuplicateSubmit();">
				<div class="yndirectory-hiddenblock">
					<input type="hidden" value="{$type}" name="type">
					<input type="hidden" value="{$package_id}" name="package">
					<input type="hidden" value="{$iDefaultFeatureFee}" id="yndirectory_defaultfeaturefee">
				</div>
				{if $aPackage !== null}
					<div class="yndirectory-theme">
						<div class="mb-1">{phrase var='create_a_business_step_2_select_theme'}</div>
						<div class="mb-1">{phrase var='please_select_one_theme_for_your_business_note_that_if_this_package_allows_multiple_themes_you_can_re_select_theme_any_time_you_want_at_dashboard'}</div>
						<div id="yndirectory_theme">
							{foreach from=$aPackage.themes key=Id item=theme}
								<div class="yndirectory-theme-item">
									<div>
										<img
											src="
											{if $theme.theme_id == 1}
												{$core_path}module/directory/static/image/theme_1.png
											{elseif $theme.theme_id == 2}
												{$core_path}module/directory/static/image/theme_2.png
											{/if}
											" />
									</div>        	                
                                    <div class="radio ync-radio-custom"><label><input type="radio" name="val[theme]" value="{$theme.theme_id}"
                                        {if isset($aForms) && isset($aForms.theme)}
                                            {if ($aForms.theme == $theme.theme_id)}
                                                checked="checked"
                                            {/if}
                                        {else}
                                            {if isset($aGlobalSetting) && ($aGlobalSetting.default_theme_id == $theme.theme_id)}
                                                checked="checked"
                                            {/if}
                                        {/if}
                                    /><i class="ico ico-circle-o"></i></label></div>
									
								</div>
							{/foreach}
						</div>
					</div>
				{/if}
				<div>
			        <div id="js_custom_privacy_input_holder">
			        {if $bIsEdit && empty($sModule)}
			            {module name='privacy.build' privacy_item_id=$aForms.business_id privacy_module_id='directory'}
			        {/if}
			        </div>

			        <div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
			        <div><input type="hidden" name="val[selected_categories]" id="js_selected_categories" value="{value type='input' id='selected_categories'}" /></div>
			        {if Phpfox::getParam('core.force_https_secure_pages')}
			            <div><input id="force_https_secure_pages" type="hidden" name="force_https_secure_pages" value="https" /></div>
			        {else}
			            <div><input id="force_https_secure_pages" type="hidden" name="force_https_secure_pages" value="http" /></div>
			        {/if}
			        {if !empty($sModule)}
			            <div><input type="hidden" name="module" value="{$sModule|htmlspecialchars}" /></div>
			        {/if}
			        {if !empty($iItem)}
			            <div><input type="hidden" name="item" value="{$iItem|htmlspecialchars}" /></div>
			        {/if}
			        {if $bIsEdit}
			            <div><input type="hidden" name="val[business_id]" value="{$aForms.business_id}" /></div>
			            <div><input type="hidden" name="val[business_status]" value="{$aForms.business_status}" /></div>
			        {/if}

			        <div id="js_directory_block_main" class="js_directory_block page_section_menu_holder">
			        	<h3>{phrase var='general_info'}</h3>
			            <div class="form-group">
		                    <label>{required}{phrase var='business_name'}: </label>
			                <input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="60" />
			            </div>
						<div class="form-group">
							<label>{required}{phrase var='short_description'}: </label>
							<textarea class="form-control" cols="59" rows="10" name="val[short_description]" class="" id="short_description" style="height:70px;">{value id='short_description' type='textarea'}</textarea>
						</div>
                        <div class="form-group">
                            <label>{phrase var='description'}: </label>
                            {editor id='description'}
                        </div>
			            <h3>{phrase var='locations'}</h3>
			            <div class="form-group">
                            <div id="yndirectory_locationlist">
                                {if isset($aForms) && isset($aForms.all_location) && count($aForms.all_location)}
                                    {foreach from=$aForms.all_location key=keyall_location item=itemall_location}
                                        <div data-item="{$keyall_location}" class="yndirectory-location">
                                            <div>
                                                <div class="form-group">
                                                    <label>{phrase var='address_title'}: </label>
                                                    <input class="form-control" type="text" name="val[location_title][]" value="{$itemall_location.location_title}"/>
                                                </div>
                                                <div>
                                                    <label>{required}{phrase var='address'}: </label>
                                                        <input class="form-control" id="yndirectory_location_{if $keyall_location == 0}99999{else}{$keyall_location}{/if}" type="text" data-inputid="fulladdress" name="val[location_fulladdress][]" value="{$itemall_location.location_fulladdress}" size="30" placeholder="{phrase var='enter_a_location'}" autocomplete="off">
                                                    <div class="extra_info mt-h1">
                                                        <a href="javascript:void(0)" onclick="yndirectory.viewMap(this); return false;">{phrase var='view_map'}</a>
                                                    </div>
                                                </div>

                                                <input type="hidden" data-inputid="address" name="val[location_address][]" value="{$itemall_location.location_address}" />
                                                <input type="hidden" data-inputid="city" name="val[location_address_city][]" value="{$itemall_location.location_address_city}" />
                                                <input type="hidden" data-inputid="country" name="val[location_address_country][]" value="{$itemall_location.location_address_country}" />
                                                <input type="hidden" data-inputid="zipcode" name="val[location_address_zipcode][]" value="{$itemall_location.location_address_zipcode}" />
                                                <input type="hidden" data-inputid="lat" name="val[location_address_lat][]" value="{$itemall_location.location_address_lat}" />
                                                <input type="hidden" data-inputid="lng" name="val[location_address_lng][]" value="{$itemall_location.location_address_lng}" />
                                            </div>
                                            <div class="extra_info mt-h1">
                                            {if $keyall_location == 0}
                                                <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'location'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>
                                                <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'location'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {else}
                                                <a id="yndirectory_delete" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'location'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {/if}
                                            </div>
                                        </div>
                                    {/foreach}
                                {else}
                                    <div data-item="1" class="yndirectory-location">
                                        <div>
                                            <div class="form-group">
                                                <label>{phrase var='address_title'}: </label>
                                                <input class="form-control" type="text" name="val[location_title][]" value=""/>
                                            </div>
                                            <div>
                                                <label>{phrase var='address'}: </label>
                                                <input class="form-control" id="yndirectory_location_99999" type="text" data-inputid="fulladdress" name="val[location_fulladdress][]" value="" size="30" placeholder="{phrase var='enter_a_location'}" autocomplete="off">
                                                <div class="extra_info mt-h1">
                                                    <a href="javascript:void(0)" onclick="yndirectory.viewMap(this); return false;">{phrase var='view_map'}</a><input type="hidden" data-inputid="address" name="val[location_address][]" value="" />
                                                </div>
                                            </div>
                                            <input type="hidden" data-inputid="city" name="val[location_address_city][]" value="" />
                                            <input type="hidden" data-inputid="country" name="val[location_address_country][]" value="" />
                                            <input type="hidden" data-inputid="zipcode" name="val[location_address_zipcode][]" value="" />
                                            <input type="hidden" data-inputid="lat" name="val[location_address_lat][]" value="" />
                                            <input type="hidden" data-inputid="lng" name="val[location_address_lng][]" value="" />
                                        </div>
                                        <div class="extra_info mt-h1">
                                            <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'location'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>
                                            <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'location'); return false;">
                                                <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                            </a>
                                        </div>
                                    </div>
                                {/if}
                            </div>
			            </div>

			            <h3>{phrase var='contact_information'}</h3>
			            <div id="yndirectory_phonelist" class="form-group">
		                    <label>{required}{phrase var='phone'}: </label>
						 	{if isset($aForms) && isset($aForms.phone)}
			                    {foreach from=$aForms.phone key=keyphone item=itemphone}
                                    <div class="phone-wrapper">
                                        <input class="form-control" type="text" name="val[phone][]" value="{$itemphone}" size="40" />
                                        <div class="extra_info mt-h1">
                                            {if $keyphone == 0}
                                                <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'phone'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>

                                                <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'phone'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {else}
                                                <a id="yndirectory_delete" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'phone'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
			                    {/foreach}
			                {else}
                                <div class="phone-wrapper">
                                    <input class="form-control" type="text" name="val[phone][]" value="" size="40" />
                                    <div class="extra_info mt-h1">
                                        <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'phone'); return false;">
                                            {img theme='misc/add.png' class='v_middle'}
                                        </a>

                                        <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'phone'); return false;">
                                            <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                        </a>
                                    </div>
                                </div>
			                {/if}
			            </div>

			            <div id="yndirectory_faxlist" class="form-group">
		                    <label>{phrase var='fax'}: </label>
			                {if isset($aForms) && isset($aForms.fax)}
			                	{foreach from=$aForms.fax key=keyfax item=itemfax}
                                    <div class="fax-wrapper">
                                        <input class="form-control" type="text" name="val[fax][]" value="{$itemfax}" size="40" />
                                        <div class="extra_info mt-h1">
                                            {if $keyfax == 0}
                                                <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'fax'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>

                                                <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'fax'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {else}
                                                <a id="yndirectory_delete" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'fax'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
			                	{/foreach}
			                {else}
                                <div class="fax-wrapper">
                                    <input class="form-control" type="text" name="val[fax][]" value="" size="40" />
                                    <div class="extra_info mt-h1">
                                        <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'fax'); return false;">
                                            {img theme='misc/add.png' class='v_middle'}
                                        </a>

                                        <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'fax'); return false;">
                                            <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                        </a>
                                    </div>
                                </div>
			                {/if}
			            </div>

			            <div class="form-group">
		                    <label>{required}{phrase var='email'}: </label>
		                    <input class="form-control" type="text" name="val[email]" id="yndirectory_email" value="{if isset($aForms) && isset($aForms.email)}{$aForms.email}{/if}" size="40" />
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='country'}: </label>
							{select_location}
							{module name='core.country-child'}
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='city'}: </label>
		                    <input class="form-control" type="text" name="val[city]" value="{if isset($aForms) && isset($aForms.city)}{$aForms.city}{/if}" size="40" />
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='province'}: </label>
		                    <input class="form-control" type="text" name="val[province]" value="{if isset($aForms) && isset($aForms.province)}{$aForms.province}{/if}" size="40" />
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='zip_code'}: </label>
		                    <input class="form-control" type="text" name="val[zip_code]" value="{if isset($aForms) && isset($aForms.zip_code)}{$aForms.zip_code}{/if}" size="40" />
			            </div>

			            <div id="yndirectory_websitelist" class="form-group">
		                    <label>{phrase var='web_address'}: </label>
			                {if isset($aForms) && isset($aForms.web_address)}
			                	{foreach from=$aForms.web_address key=keyweb_address item=itemweb_address}
                                    <div class="web_address-wrapper">
                                        <input class="form-control" type="text" name="val[web_address][]" value="{$itemweb_address}" size="40" />
                                        <div class="extra_info mt-h1">
                                            {if $keyweb_address == 0}
                                                <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'web_address'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>

                                                <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'web_address'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {else}
                                                <a id="yndirectory_delete" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'web_address'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
			                	{/foreach}
			                {else}
                                <div class="web_address-wrapper">
                                    <input class="form-control" type="text" name="val[web_address][]" value="" size="40" />
                                    <div class="extra_info mt-h1">
                                        <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'web_address'); return false;">
                                            {img theme='misc/add.png' class='v_middle'}
                                        </a>

                                        <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'web_address'); return false;">
                                            <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                        </a>
                                    </div>
                                </div>
			                {/if}
			            </div>	

			            <div class="form-group">
                            {module name='core.upload-form' type='directory_business_logo' current_photo=''}
			            </div>

			            <div class="form-group">
		                    <label>{phrase var='business_sizes'}: </label>
							<select class="form-control" id="yndirectory_businesssize" name="val[size]">
		                		{foreach from=$aBusinessSizes key=Id item=aBusinessSize}
		                			<option value="{$aBusinessSize}" {if isset($aForms) && isset($aForms.size) && $aForms.size == $aBusinessSize}selected="selected"{/if}>{$aBusinessSize}</option>
		            	    	{/foreach}
							</select>
			            </div>
			            <div id="yndirectory_visitinghourlist" class="form-group">
		                    <label>{phrase var='visiting_hours'}: </label>
			                {if isset($aForms) && isset($aForms.all_visiting_hours)}
			                	{foreach from=$aForms.all_visiting_hours key=keyall_visiting_hours item=itemall_visiting_hours}
                                    <div class="visiting_hours-wrapper">
                                        <select class="form-control" name="val[visiting_hours_dayofweek_id][]">
                                            {foreach from=$aVisitingHours.dayofweek key=Id item=dayofweekItem}
                                                <option value="{$dayofweekItem.id}" {if $itemall_visiting_hours.visiting_hours_dayofweek_id == $dayofweekItem.id}selected="selected"{/if}>{$dayofweekItem.phrase}</option>
                                            {/foreach}
                                        </select>
                                        <select  class="form-control mt-h1" name="val[visiting_hours_hour_starttime][]" onchange="onChangeStartHourToClosed(this);">
                                                <option></option>
                                                {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                                    <option value="{$hourItem}" {if $itemall_visiting_hours.visiting_hours_hour_starttime == $hourItem}selected="selected"{/if}>
                                                {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                                        {if $Id == $Ids}{$hourformat}{/if}
                                                {/foreach}
                                                </option>
                                            {/foreach}
                                        </select>
                                        <select  class="form-control mt-h1" name="val[visiting_hours_hour_endtime][]" onchange="onChangeEndHourToClosed(this);">
                                                    <option></option>
                                                {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                                    <option value="{$hourItem}" {if $itemall_visiting_hours.visiting_hours_hour_endtime == $hourItem}selected="selected"{/if}>
                                                {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                                    {if $Id == $Ids}{$hourformat}{/if}
                                                {/foreach}
                                                    </option>
                                                {/foreach}
                                        </select>
                                        <div class="extra_info mt-h1">
                                            {if $keyall_visiting_hours == 0}
                                                <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'visiting_hours'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>

                                                <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'visiting_hours'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {else}
                                                <a id="yndirectory_delete" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'visiting_hours'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
			                	{/foreach}
			                {else}
                                <div class="visiting_hours-wrapper">
                                    <select class="form-control" name="val[visiting_hours_dayofweek_id][]">
                                        {foreach from=$aVisitingHours.dayofweek key=Id item=dayofweekItem}
                                            <option value="{$dayofweekItem.id}">{$dayofweekItem.phrase}</option>
                                        {/foreach}
                                    </select>
                                    <select class="form-control mt-h1" name="val[visiting_hours_hour_starttime][]" onchange="onChangeStartHourToClosed(this);">
                                        <option></option>
                                        {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                        <option value="{$hourItem}">
                                            {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                            {if $Id == $Ids}{$hourformat}{/if}
                                            {/foreach}
                                        </option>
                                        {/foreach}
                                    </select>
                                    <select class="form-control mt-h1" name="val[visiting_hours_hour_endtime][]" onchange="onChangeEndHourToClosed(this);">
                                        <option></option>
                                        {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                        <option value="{$hourItem}">
                                            {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                            {if $Id == $Ids}{$hourformat}{/if}
                                            {/foreach}
                                        </option>
                                        {/foreach}
                                    </select>
                                    <div class="extra_info mt-h1">
                                        <a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'visiting_hours'); return false;">
                                            {img theme='misc/add.png' class='v_middle'}
                                        </a>

                                        <a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'visiting_hours'); return false;">
                                            <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                        </a>
                                    </div>
                                </div>
			                {/if}
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='timezone'}: </label>
							<select class="form-control" name="val[time_zone]" id="time_zone">
								{foreach from=$aTimeZones key=sTimeZoneKey item=sTimeZone}
								<option value="{$sTimeZoneKey}" {if (empty($aForms.time_zone) && $sTimeZoneKey == Phpfox::getParam('core.default_time_zone_offset')) || (!empty($aForms.time_zone) && $aForms.time_zone == $sTimeZoneKey)} selected="selected"{/if}>{$sTimeZone}</option>
								{/foreach}
							</select>
                            <div class="checkbox ync-checkbox-custom"><label><input class="" type="checkbox" name="val[disable_visitinghourtimezone]" {if isset($aForms) && isset($aForms.disable_visitinghourtimezone) && $aForms.disable_visitinghourtimezone}checked="checked"{/if} /><i class="ico ico-square-o mr-1"></i>{phrase var='check_this_to_disable_visiting_hours_and_timezone_fields'}</label></div>
							 
			            </div>
			            <div class="form-group">
		                    <label for="founder">{phrase var='founders'}: </label>
		                    <input class="form-control" id="yndirectory_founder" type="text" name="val[founder]" size="60" value="{if isset($aForms) && isset($aForms.founder)}{$aForms.founder}{/if}" />
			            </div>

			            <div id="yndirectory_customfield" class="form-group">
				        	<h3>{phrase var='additional_information'}</h3>
				            <div id="yndirectory_customfield_user">
				                {if isset($aForms) && isset($aForms.all_customfield_user)}
				                	{foreach from=$aForms.all_customfield_user key=keyall_customfield_user item=itemall_customfield_user}
						            	<div class="yndirectory-customfield-user">
						            		<div class="form-group">
								                <label>{phrase var='title'}: </label>
							                    <input class="form-control" maxlength="255" type="text" name="val[customfield_user_title][]" size="60" value="{$itemall_customfield_user.customfield_user_title}" />
						                    </div>
											<div class="form-group">
						                    	<label>{phrase var='content'}: </label>
						                    	<input class="form-control" type="text" name="val[customfield_user_content][]" size="60" value="{$itemall_customfield_user.customfield_user_content}" />
												<div class="extra_info mt-h1">
													{if $keyall_customfield_user == 0}
													<a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'customfield_user'); return false;">
														{img theme='misc/add.png' class='v_middle'}
													</a>
													<a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'customfield_user'); return false;">
														<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
													</a>
													{else}
													<a id="yndirectory_delete" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'customfield_user'); return false;">
														<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
													</a>
													{/if}
												</div>
											</div>
						            	</div>
				                	{/foreach}
				                {else}
					            	<div class="yndirectory-customfield-user">
					            		<div class="form-group">
					                  	 	<label>{phrase var='title'}: </label>
					                    	<input class="form-control" maxlength="255" type="text" name="val[customfield_user_title][]" size="60"  id="yndirectory_customfield_title" />
					                    </div>
					                    <div class="form-group">
					                    	<label>{phrase var='content'}: </label>
					                    	<input class="form-control" type="text" name="val[customfield_user_content][]" size="60" id="yndirectory_customfield_content"/>
											<div class="extra_info mt-h1">
												<a id="yndirectory_add" href="javascript:void(0)" onclick="yndirectory.appendPredefined(this,'customfield_user'); return false;">
													{img theme='misc/add.png' class='v_middle'}
												</a>
												<a id="yndirectory_delete" style="display: none;" href="javascript:void(0)" onclick="yndirectory.removePredefined(this,'customfield_user'); return false;">
													<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
												</a>
											</div>
										</div>
					            	</div>
				                {/if}
				            </div>
			            </div>

			            <div id="yndirectory_categorylist" class="form-group">
			                <label>{required}{phrase var='category'}: </label>
                            <div class="category-wrapper">
                                {if isset($aForms) && isset($aForms.all_customfield_user)}
                                    {if !empty($aForms.editCategories)}
                                        {foreach from=$aForms.editCategories item=aCategory }
                                            <div class="section_category_{$aCategory}">
                                                {$sCategories}
                                            </div>
                                        {/foreach}
                                    {else}
                                        {$sCategories}
                                    {/if}
                                {else}
                                    {$sCategories}
                                {/if}
                            </div>
			            </div>

			            <div class="form-group">
				            <div id="yndirectory_customfield_category">
				            </div>
			            </div>

						<div class="form-group">
							<label>{phrase var='tags'}:</label>
							<input class="form-control" type="text" name="val[tag_list]" value="{if isset($aForms.tag_list)}{$aForms.tag_list}{/if}" size="30">
							<div class="help-block">{phrase var='separate_multiple_topics_with_commas'}</div>
						</div>

						{if $aPackage !== null && Phpfox::getUserParam('directory.can_feature_business')}
							<div class="form-group">
								<div>
									<label>{phrase var='feature'}:</label>
									<div class="help-block">{phrase var='feature_this_business_for'}</div>
									<input class="form-control" id="yndirectory_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10">
								</div>
								<div class="mt-1">
									<p class="help-block">{phrase var='day_s_with'}</p>
									<input class="form-control" id="yndirectory_feature_fee_total" type="text" value="0" size="10" readonly />
									<p class="help-block">{$aCurrentCurrencies.0.currency_id} ({phrase var='fee_to_feature_business_feature_fee_currency_id_for_1_day' feature_fee=$iDefaultFeatureFee currency_id=$aCurrentCurrencies.0.currency_id})</p>
								</div>

							</div>
						{/if}

						{if empty($sModule) && Phpfox::isModule('privacy')}
							<div class="form-group">
								<label>{phrase var='business_privacy'}:</label>
								{module name='privacy.form' privacy_name='privacy' privacy_info='directory.control_who_can_see_this_business' privacy_no_custom=true}
							</div>
						{/if}
			        </div>		        
				</div>
				<div class="table_clear" id="yndirectory_submit_buttons">
					<input id="yndirectory_submit" type="submit" class="button btn btn-sm btn-primary" value="{phrase var='create_business'}" name="val[create]"/>
					{if !$bIsEdit}
					<button id="yndirectory_preview" type="button" class="btn btn-sm btn-info" value="{phrase var='preview'}" name="val[preview]">{phrase var='preview'}</button>
					<input id="yndirectory_submit_draft" type="submit" class="button btn btn-sm btn-default" value="{phrase var='save_as_draft'}" name="val[draft]" />
					{/if}
					<button id="yndirectory_back" data-url="{$sBackUrl}" type="button" class="btn btn-sm btn-default" value="{phrase var='back'}" name="val[back]">{phrase var='back'}</button>
				</div>
			</form>
		</div>
		{if Phpfox::getParam('core.display_required')}
		<div class="table_clear">
			{required} {phrase var='core.required_fields'}
		</div>
		{/if}
	{/if}
</div>

{if !isset($invoice_id)}
	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places"></script>
{/if}
{literal}
<script type="text/javascript">
    function onChangeStartHourToClosed(obj){
        if($(obj).val() == "Closed")
            $(obj).next().val("Closed");
    }
    function onChangeEndHourToClosed(obj){
        if($(obj).val() == "Closed")
            $(obj).prev().val("Closed");
    }
	function onPreventDuplicateSubmit(){
		$('#yndirectory_submit_buttons').hide();
		setTimeout(function(){
		    if(!$('#yndirectory_edit_directory_form').valid())
            {
                $('#yndirectory_submit_buttons').show();
            }
        },500);
	}
	$Behavior.readyYnDirectory = function() {
		yndirectory.init();
	};
</script>
{/literal}
