<div id="yndirectory_edit">
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="edit" id="yndirectory_pagename" name="yndirectory_pagename">
		<input type="hidden" value="{if isset($bIsEdit) && isset($iBusinessid)}{$iBusinessid}{/if}" id="yndirectory_businessid" name="yndirectory_businessid">
	</div>

	{if isset($invoice_id) && (int)$invoice_id > 0}
		<div>
			<h3>{phrase var='payment_methods'}</h3>
			{module name='api.gateway.form'}			
		</div>
	{else}
		<div>
			{$sCreateJs}
			<form enctype="multipart/form-data" id="yndirectory_edit_directory_form" action="{$sFormUrl}" class="yndirectory-add-edit-form" method="post">
				<div class="yndirectory-hiddenblock">
					<input type="hidden" value="{$type}" name="type">
					<input type="hidden" value="{$package_id}" name="package">
					<input type="hidden" value="{$iDefaultFeatureFee}" id="yndirectory_defaultfeaturefee">
				</div>

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
		                    <label>{phrase var='description'}</label>
		                    {editor id='description'}
			            </div>

						<div class="form-group">
							<label>{required}{phrase var='short_description'}:</label>
							<textarea class="form-control" cols="59" rows="10" name="val[short_description]" class="" id="short_description" style="height:70px;">{value id='short_description' type='textarea'}</textarea>
						</div>

						<h3>{phrase var='locations'}</h3>

						<div class="form-group">
							<div id="yndirectory_locationlist" >
									{if $bIsEdit && isset($aEditedBusiness)}
										{if count($aEditedBusiness.locations)}
											{foreach from=$aEditedBusiness.locations key=iLocation item=aLocation}
											<div data-item="{$iLocation}" class="yndirectory-location">
												<div class="">
													<div class="form-group">
														<label>{phrase var='address_title'}</label>
														<input type="text" name="val[location_title][]" value="{$aLocation.location_title}" class="form-control" />
													</div>
													<div class="form-group">
														<label>{phrase var='address'}</label>
														<input id="yndirectory_location_1" type="text" data-inputid="fulladdress" name="val[location_fulladdress][]" value="{$aLocation.location_address}" size="30" class="form-control" placeholder="{phrase var='enter_a_location'}" autocomplete="off">
													</div>
													<div class="extra_info mt-h1">
														<a href="#" onclick="yndirectory.viewMap(this); return false;">{phrase var='view_map'}</a>
													</div>
													<input type="hidden" data-inputid="address" name="val[location_address][]" value="{$aLocation.location_address}" />
													<input type="hidden" data-inputid="city" name="val[location_address_city][]" value="" />
													<input type="hidden" data-inputid="country" name="val[location_address_country][]" value="" />
													<input type="hidden" data-inputid="zipcode" name="val[location_address_zipcode][]" value="" />
													<input type="hidden" data-inputid="lat" name="val[location_address_lat][]" value="{$aLocation.location_latitude}" />
													<input type="hidden" data-inputid="lng" name="val[location_address_lng][]" value="{$aLocation.location_longitude}" />
												</div>
												<div class="extra_info mt-h1">
													<a id="yndirectory_add" {if $iLocation != 0 }style='display:none;'{/if} href="#" onclick="yndirectory.appendPredefinedForEdit(this,'location'); return false;">
														{img theme='misc/add.png' class='v_middle'}
													</a>
													<a id="yndirectory_delete"  {if $iLocation == 0 }style='display:none;'{/if} href="#" onclick="yndirectory.removePredefined(this,'location'); return false;">
														<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
													</a>
												</div>
											</div>
											{/foreach}
										{else}
											<div data-item="1" class="yndirectory-location">
												<div>
													<div class="form-group">
														<label>{phrase var='address_title'}</label>
														<input type="text" name="val[location_title][]" value="" size="30" class="form-control" />
													</div>
													<div class="form-group">
														{required}{phrase var='address'}
														<input id="yndirectory_location_1" type="text" data-inputid="fulladdress" name="val[location_fulladdress][]" value="" class="form-control" placeholder="{phrase var='enter_a_location'}" autocomplete="off">
													</div>
													<div class="extra_info mt-h1">
														<a href="#" onclick="yndirectory.viewMap(this); return false;">{phrase var='view_map'}</a>
													</div>
													<input type="hidden" data-inputid="address" name="val[location_address][]" value="" />
													<input type="hidden" data-inputid="city" name="val[location_address_city][]" value="" />
													<input type="hidden" data-inputid="country" name="val[location_address_country][]" value="" />
													<input type="hidden" data-inputid="zipcode" name="val[location_address_zipcode][]" value="" />
													<input type="hidden" data-inputid="lat" name="val[location_address_lat][]" value="" />
													<input type="hidden" data-inputid="lng" name="val[location_address_lng][]" value="" />
												</div>
												<div class="extra_info mt-h1">
													<a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'location'); return false;">
														{img theme='misc/add.png' class='v_middle'}
													</a>
													<a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'location'); return false;">
														<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
													</a>
												</div>
											</div>
										{/if}
									{/if}
							</div>
						</div>

			            <h3>{phrase var='contact_information'}</h3>
			            <div id="yndirectory_phonelist" class="form-group">
		                    <label for="phone">{required}{phrase var='phone'}: </label>
							{if $bIsEdit && isset($aEditedBusiness)}
		                    	{if count($aEditedBusiness.phones)}
			                	  	{foreach from=$aEditedBusiness.phones key=iPhone item=aPhone}
                                        <div class="phone-wrapper">
                                            <input class="form-control" type="text" name="val[phone][]" value="{$aPhone.phone_number}" size="40" />
                                            <div class="extra_info mt-h1">
                                                <a id="yndirectory_add" href="#" {if $iPhone != 0 }style='display:none;'{/if}   onclick="yndirectory.appendPredefined(this,'phone'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>
                                                <a id="yndirectory_delete" {if $iPhone == 0 }style='display:none;'{/if} href="#" onclick="yndirectory.removePredefined(this,'phone'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            </div>
                                        </div>
								  	{/foreach}
							  	{else}
                                    <div class="phone-wrapper">
                                        <input class="form-control" type="text" name="val[phone][]" value="" size="40" />
                                        <div class="extra_info mt-h1">
                                            <a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'phone'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>

                                            <a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'phone'); return false;">
                                                <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                            </a>
                                        </div>
                                    </div>
								 {/if}
					        {/if}     	 
			            </div>

			            <div id="yndirectory_faxlist" class="form-group">
		                    <label>{phrase var='fax'}: </label>
							{if $bIsEdit && isset($aEditedBusiness)}
		                    	{if count($aEditedBusiness.faxs)}
			                	  	{foreach from=$aEditedBusiness.faxs key=iFax item=aFax}
                                        <div class="fax-wrapper">
                                            <input class="form-control" type="text" name="val[fax][]" value="{$aFax.fax_number}" size="40" />
                                            <div class="extra_info mt-h1">
                                                <a id="yndirectory_add" href="#" {if $iFax != 0 }style='display:none;'{/if} onclick="yndirectory.appendPredefined(this,'fax'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>
                                                <a id="yndirectory_delete" {if $iFax == 0 }style='display:none;'{/if} href="#" onclick="yndirectory.removePredefined(this,'fax'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            </div>
                                        </div>
									{/foreach}
								{else}
                                    <div class="fax-wrapper">
                                        <input class="form-control" type="text" name="val[fax][]" value="" size="40" />
                                        <div class="extra_info mt-h1">
                                            <a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'fax'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>

                                            <a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'fax'); return false;">
                                                <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                            </a>
                                        </div>
                                    </div>
								{/if}
					        {/if} 
			            </div>
			            <div class="form-group">
		                    <label>{required}{phrase var='email'}: </label>
		                    <input class="form-control" type="text" name="val[email]" id="yndirectory_email" value="{value type='input' id='email'}" size="40" />
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='country'}: </label>
							{select_location}
							{module name='core.country-child'}
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='city'}: </label>
		                    <input class="form-control" type="text" name="val[city]" value="{value type='input' id='city'}" size="40" />
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='province'}: </label>
		                    <input class="form-control" type="text" name="val[province]" value="{value type='input' id='province'}" size="40" />
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='zip_code'}: </label>
		                    <input class="form-control" type="text" name="val[zip_code]" value="{value type='input' id='postal_code'}" size="40" />
			            </div>
			            <div id="yndirectory_websitelist" class="form-group">
		                    <label>{phrase var='web_address'}: </label>
		                    {if $bIsEdit && isset($aEditedBusiness)}
		                    	{if count($aEditedBusiness.websites)}
			                	  	{foreach from=$aEditedBusiness.websites key=iWebsite item=aWebsite}
                                        <div class="web_address-wrapper">
                                            <input class="form-control" type="text" name="val[web_address][]" value="{$aWebsite.website_text}" size="40" />
                                            <div class="extra_info mt-h1">
                                                <a id="yndirectory_add" href="#" {if $iWebsite != 0 }style='display:none;'{/if} onclick="yndirectory.appendPredefined(this,'web_address'); return false;">
                                                    {img theme='misc/add.png' class='v_middle'}
                                                </a>

                                                <a id="yndirectory_delete" {if $iWebsite == 0 }style='display:none;'{/if} href="#" onclick="yndirectory.removePredefined(this,'web_address'); return false;">
                                                    <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                                </a>
                                            </div>
                                        </div>
								  {/foreach}
								{else}
                                    <div class="web_address-wrapper">
                                        <input class="form-control" type="text" name="val[web_address][]" value="" size="40" />
                                        <div class="extra_info mt-h1">
                                            <a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'web_address'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>

                                            <a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'web_address'); return false;">
                                                <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                            </a>
                                        </div>
                                    </div>
								{/if}
					        {/if} 
			            </div>		
			            <div class="form-group">
                            {if !empty($aEditedBusiness.current_logo) && !empty($aEditedBusiness.business_id)}
                                {module name='core.upload-form' type='directory_business_logo' current_photo=$aForms.current_logo id=$aForms.business_id}
                                <input type="hidden" name="val[logo_path]" value="{$aEditedBusiness.logo_path}">
                                <input type="hidden" name="val[server_id]" value="{$aEditedBusiness.server_id}">
                            {else}
                                {module name='core.upload-form' type='directory_business_logo' current_photo=''}
                            {/if}
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='business_sizes'}: </label>
							<select class="form-control" id="yndirectory_businesssize" name="val[size]">
		                		{foreach from=$aBusinessSizes key=Id item=aBusinessSize}
		                			<option value="{$aBusinessSize}"  {if $bIsEdit && $aEditedBusiness.size ==  $aBusinessSize }selected{/if}>{$aBusinessSize}</option>
		            	    	{/foreach}
							</select>
			            </div>
			            <div id="yndirectory_visitinghourlist" class="form-group yndirectory_visitinghourlist">
		                    <label for="visiting_hours">{phrase var='visiting_hours'}: </label>
							{if $bIsEdit && isset($aEditedBusiness)}
		                    	{if count($aEditedBusiness.vistinghours)}
			                	  {foreach from=$aEditedBusiness.vistinghours key=iVistingHour item=aVistingHour}
                                    <div class="visiting_hours-wrapper">
                                        <select class="form-control" name="val[visiting_hours_dayofweek_id][]">
                                            {foreach from=$aVisitingHours.dayofweek key=Id item=dayofweekItem}
                                                <option value="{$dayofweekItem.id}" {if $bIsEdit && $aVistingHour.vistinghour_dayofweek ==  $dayofweekItem.id }selected{/if}>{$dayofweekItem.phrase}</option>
                                            {/foreach}
                                        </select>
                                        <select class="form-control" name="val[visiting_hours_hour_starttime][]"  onchange="onChangeStartHourToClosed(this);">
                                                <option></option>
                                                {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                                    <option value="{$hourItem}" {if $bIsEdit && $aVistingHour.vistinghour_starttime ==  $hourItem }selected{/if}>
                                                {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                                    {if $Id == $Ids}{$hourformat}{/if}
                                                {/foreach}
                                                    </option>
                                                {/foreach}
                                        </select>
                                        <select class="form-control" name="val[visiting_hours_hour_endtime][]" onchange="onChangeEndHourToClosed(this);">
                                                    <option></option>
                                                {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                                    <option value="{$hourItem}" {if $bIsEdit && $aVistingHour.vistinghour_endtime ==  $hourItem }selected{/if}>
                                                {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                                        {if $Id == $Ids}{$hourformat}{/if}
                                                {/foreach}
                                                    </option>
                                                {/foreach}
                                        </select>
                                        <div class="extra_info mt-h1">
                                            <a id="yndirectory_add" href="#" {if $iVistingHour != 0 }style='display:none;'{/if} onclick="yndirectory.appendPredefined(this,'visiting_hours'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>

                                            <a id="yndirectory_delete"  href="#" {if $iVistingHour == 0 }style='display:none;'{/if} onclick="yndirectory.removePredefined(this,'visiting_hours'); return false;">
                                                <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                            </a>
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
                                        <select class="form-control" name="val[visiting_hours_hour_starttime][]"  onchange="onChangeStartHourToClosed(this);">
                                            <option></option>
                                            {foreach from=$aVisitingHours.hour key=Id item=hourItem}
                                            <option value="{$hourItem}">
                                                {foreach from=$aVisitingHours.format key=Ids item=hourformat}
                                                    {if $Id == $Ids}{$hourformat}{/if}
                                                {/foreach}
                                            </option>
                                            {/foreach}
                                        </select>
                                        <select class="form-control" name="val[visiting_hours_hour_endtime][]" onchange="onChangeEndHourToClosed(this);">
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
                                            <a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'visiting_hours'); return false;">
                                                {img theme='misc/add.png' class='v_middle'}
                                            </a>

                                            <a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'visiting_hours'); return false;">
                                                <img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
                                            </a>
                                        </div>
                                    </div>
								{/if}
					        {/if}  
			            </div>
			            <div class="form-group">
		                    <label>{phrase var='timezone'}: </label>
							<select class="form-control" name="val[time_zone]" id="time_zone">
								{foreach from=$aTimeZones key=sTimeZoneKey item=sTimeZone}
								<option value="{$sTimeZoneKey}" {if (empty($aForms.time_zone) && $sTimeZoneKey == Phpfox::getParam('core.default_time_zone_offset')) || (!empty($aForms.time_zone) && $aForms.time_zone == $sTimeZoneKey)} selected="selected"{/if}>{$sTimeZone}</option>
								{/foreach}
							</select>
			            </div>
			            <div class="form-group">
			            	<div class="checkbox ync-checkbox-custom"><label><input type="checkbox" name="val[disable_visitinghourtimezone]" {if $bIsEdit && $aEditedBusiness.dst_check }checked="checked"{/if} /><i class="ico ico-square-o mr-1"></i>{phrase var='check_this_to_disable_visiting_hours_and_timezone_fields'}</label></div>
							 
			            </div>
			            <div class="table form-group">
		                    <label>{phrase var='founders'}: </label>
		                    <input class="form-control" id="yndirectory_founder" type="text" name="val[founder]" size="60" value="{value type='input' id='founder'}" />
			            </div>

			            <div id="yndirectory_customfield" class="form-group">
				        	<h3>{phrase var='additional_information'}</h3>
				            <div id="yndirectory_customfield_user">
								{if $bIsEdit && isset($aEditedBusiness)}
			                    	{if count($aEditedBusiness.additioninfo)}
				                	  {foreach from=$aEditedBusiness.additioninfo key=iAdditionalInfo item=aAdditionalInfo }
			            				<div class="yndirectory-customfield-user">
			            					<div class="form-group">
							                    <label>{phrase var='title'}: </label>
							                    <input class="form-control" maxlength="255" type="text" name="val[customfield_user_title][]" size="60" value="{$aAdditionalInfo.usercustomfield_title}" id="yndirectory_customfield_title" />
						                    </div>
						                    <div class="form-group">
							                    <label>{phrase var='content'}: </label>
							                    <input class="form-control" type="text" name="val[customfield_user_content][]" size="60" value="{$aAdditionalInfo.usercustomfield_content}" id="yndirectory_customfield_content"/>
												<div class="extra_info mt-h1">
													<a id="yndirectory_add" href="#" {if $iAdditionalInfo != 0 }style='display:none;'{/if} onclick="yndirectory.appendPredefined(this,'customfield_user'); return false;">
													{img theme='misc/add.png' class='v_middle'}
													</a>
													<a id="yndirectory_delete" {if $iAdditionalInfo == 0 }style='display:none;'{/if}  href="#" onclick="yndirectory.removePredefined(this,'customfield_user'); return false;">
													<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
													</a>
												</div>
						                    </div>
							            </div>
									  {/foreach}
									{else}
						            	<div class="yndirectory-customfield-user">
						            		<div class="form-group">
							                    <label>{phrase var='title'}: </label>
							                    <input class="form-control" maxlength="255" type="text" name="val[customfield_user_title][]" size="60" />
						            		</div>
						            		<div class="form-group">
							                    <label>{phrase var='content'}: </label>
							                    <input class="form-control" type="text" name="val[customfield_user_content][]" size="60" />
												<div class="extra_info mt-h1">
													<a id="yndirectory_add" href="#" onclick="yndirectory.appendPredefined(this,'customfield_user'); return false;">
														{img theme='misc/add.png' class='v_middle'}
													</a>
													<a id="yndirectory_delete" style="display: none;" href="#" onclick="yndirectory.removePredefined(this,'customfield_user'); return false;">
														<img src="{$corepath}module/directory/static/image/delete.png" class="v_middle"/>
													</a>
												</div>
						            		</div>
							            </div>
									{/if}
						        {/if} 
				            </div>
			            </div>

			            <div id="yndirectory_categorylist" class="form-group">
		                    <label>{required}{phrase var='category'}:</label>
                            <div class="category-wrapper">
                                {if $bIsEdit && isset($aEditedBusiness)}
                                    {if count($aEditedBusiness.editCategories)}
                                        {foreach from=$aEditedBusiness.editCategories item=aCategory }
                                        <div class="section_category_{$aCategory} js_category_section_content">
                                            {$sCategories}
                                        </div>
                                        {/foreach}
                                    {/if}
                                {/if}
                            </div>
			            </div>

			            <div class="form-group">
				            <div id="yndirectory_customfield_category">
				            </div>
			            </div>			            

						<div class="form-group">
							<label>{phrase var='tags'}:</label>
							<input class="form-control" type="text" name="val[tag_list]" value="{if isset($aEditedBusiness.tag_list)}{$aEditedBusiness.tag_list}{/if}" size="30">
							<div class="help-block">{phrase var='separate_multiple_topics_with_commas'}</div>
						</div>

						{if $aPackage !== null && $aEditedBusiness.business_status != Phpfox::getService('directory.helper')->getConst('business.status.draft') && Phpfox::getUserParam('directory.can_feature_business')}
							<div class="form-group yndirectory_visitinghourlist">
								<label>{phrase var='feature'}:</label>
								<input class="form-control" id="yndirectory_feature_number_days" type="text" name="val[feature_number_days]" value="0" size="10">
								<div class="help-block">{phrase var='feature_this_business_for'}</div>

								<input class="form-control mt-1" id="yndirectory_feature_fee_total" type="text" value="0" size="10" readonly /> 
								<div class="help-block">
									<p>{phrase var='day_s_with'}</p>
									<p>({phrase var='fee_to_feature_business_feature_fee_currency_id_for_1_day' feature_fee=$iDefaultFeatureFee currency_id=$aCurrentCurrencies.0.currency_id})</p>
								</div>
								{if isset($aEditedBusiness.featured) && $aEditedBusiness.featured}
								<div>
									{if isset($aEditedBusiness.is_unlimited) && $aEditedBusiness.is_unlimited}
										{phrase var='note_this_business_is_featured_unlimited_time'}
									{else}
										{phrase var='note_this_business_is_featured_until_expire_date' expire_date=$aEditedBusiness.expired_date}
									{/if}
								</div>
								{/if}
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
				<input type="hidden" name="val[isClaimingDraft]" value="{$isClaimingDraft}"/>
				<div class="table_clear">
					{if !$bIsEdit}
						<button id="yndirectory_submit" type="submit" class="btn btn-sm btn-primary" value="{phrase var='create_business'}" name="val[create]">{phrase var='create_business'}</button>
					{elseif $isClaimingDraft == 1}
						<button id="yndirectory_save_claim_draft" type="submit" class="btn btn-sm btn-primary" name="val[update]">{phrase var='update_business'}</button>
						<button id="yndirectory_publish_claim_draft" type="submit" class="btn btn-sm btn-primary" value="{phrase var='publish'}" name="val[publish_claim_draft]">{phrase var='publish'}</button>
					{else}
						<button id="yndirectory_submit" type="submit" class="btn btn-sm btn-primary" value="Update Business" name="val[update]">{phrase var='update_business'}</button>
					{/if}
					{if !$bIsEdit}
						<button id="yndirectory_preview" type="button" class="btn btn-sm btn-default" value="{phrase var='preview'}" name="val[preview]">{phrase var='preview'}</button>
					{/if}
					<button id="yndirectory_back" data-url="{$sBackUrl}" type="button" class="btn btn-sm btn-default" value="{phrase var='back'}" name="val[back]">{phrase var='back'}</button>
				</div>
				<div class="clear"></div>
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
	$Behavior.readyYnDirectory = function() {
		yndirectory.init();
	};
</script>
{/literal}