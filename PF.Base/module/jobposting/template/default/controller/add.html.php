
<div class="main_break">
	{$sCreateJs}
	<form method="post" class="ync_add_edit_form" action="{url link='jobposting.add'}{if $bIsEdit}{$job_id}/ {else}company_{$company_id}/{/if}
	   " id="ync_edit_jobposting_form" name='ync_edit_jobposting_form' enctype="multipart/form-data">
		<div>
			<input type="hidden" name="val[attachment]" class="js_attachment"
					value="{value type='input' id='attachment'}"/>
		</div>
		<div>
			<input type="hidden" id="popup_packages" name="val[packages]" value"0"></div>
		<div>
			<input type="hidden" id="popup_publish" name="val[publish]" value"0"></div>
		<div>
			<input type="hidden" id="popup_paypal" name="val[paypal]" value"0"></div>
		<div>
			<input type="hidden" id="popup_feature" name="val[feature]" value"0"></div>
		<div>
			<input type="hidden" id="company_id" name="val[company_id]" value="{$company_id}"></div>
		{plugin call='jobposting.template_controller_add_hiddenfield'}
		<div id="js_jobposting_block_main" class="js_jobposting_block">

			<div class="table form-group">
				<div class="table_left">
					<label for="title">{required}{phrase var='company_title'}:</label>
				</div>
				<div class="table_right">
					<select id="company_title" class="form-control" {if $bIsEdit} disabled="disabled" {/if} onchange="return changeCompany(this)" >
						{foreach from=$aCompanies item=aCompany key=iKey}
					{if $company_id == $aCompany.company_id}
						<option value="{$aCompany.company_id}" selected="selected">{$aCompany.name}</option>
						{else}
						<option value="{$aCompany.company_id}">{$aCompany.name}</option>
						{/if}
					{/foreach}
					</select>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					<label for="title">{required}{phrase var='job_title'}:</label>
				</div>
				<div class="table_right">
					<input type="text" class="form-control" name="val[title]" value="{value type='input' id='title'}" id="title"
					   size="60"/>
				</div>
			</div>

			<div class="table form-group">
				<div class="table_left">
					<label for="">
						{required}{phrase var='desired_skills_experience'}:
					</label></div>
					<div class="table_right">{editor id='skills'}</div>

				</div>
				<div class="table form-group">
					<div class="table_left">
						<label for="description">{required}{phrase var='job_description'}:</label></div>
						<div class="table_right">{editor id='description'}</div>
					</div>

					<div class="table form-group">
						<div class="table_left">
							<label for="language_prefer">{phrase var='language_preference'}:</label>
						</div>
						<div class="table_right">
							<input type="text" class="form-control" name="val[language_prefer]"
				   value="{value type='input' id='language_prefer'}" id="language_prefer" size="60"/>
						</div>
					</div>

					<div class="table form-group">
						<div class="table_left">
							<label for="education_prefer">{phrase var='education_preference'}:</label>
						</div>
						<div class="table_right">
							<input type="text" class="form-control" name="val[education_prefer]" value="{value type='input' id='education_prefer'}" id="education_prefer" size="60"/>
						</div>
					</div>

					<div class="table form-group">
						<div class="table_left">
							<label for="working_place">{phrase var='working_place'}:</label>
						</div>
						<div class="table_right">
							<input type="text" class="form-control" name="val[working_place]"
					   value="{value type='input' id='working_place'}" id="working_place" size="60"/>

							<div class="extra_info">
								{if !$bIsEdit}
								<a href="#" id="js_link_show_add"
					   onclick="$(this).hide(); $('#js_mp_add_city').show(); $('#js_link_hide_add').show(); return false;">
									{phrase	var='add_city_zip_country'}
								</a>
								<a href="#" id="js_link_hide_add" style="display: none;"
					   onclick="$(this).hide(); $('#js_mp_add_city').hide(); $('#js_link_show_add').show(); return false;">
									{phrase	var='hide_city_zip_country'}
								</a>
								{/if}
							</div>
						</div>
					</div>
					<div id="js_mp_add_city" {if !$bIsEdit} style="display:none;" {
		/if} >
						<div class="table form-group">
							<div class="table_left">
								<label for="city">{phrase var='city'}:</label>
							</div>
							<div class="table_right">
								<input type="text" class="form-control" name="val[city]" value="{value type='input' id='city'}" id="city" size="25"
				   maxlength="255"/>
							</div>
						</div>

						<div class="table form-group">
							<div class="table_left">
								<label for="postal_code">{phrase var='zip_postal_code'}:</label>
							</div>
							<div class="table_right">
								<input type="text" class="form-control" name="val[postal_code]" value="{value type='input' id='postal_code'}"
				   id="postal_code" size="10" maxlength="20"/>
							</div>
						</div>

						<div class="table form-group">
							<div class="table_left">
								<label for="country_iso">{phrase var='country'}:</label>
							</div>
							<div class="table_right">
								{select_location}
				{module name='core.country-child'}
							</div>
						</div>
					</div>

					<div class="table form-group">
						<div class="table_right">
							<button id="refresh_map" type="button" class="btn btn-sm btn-primary"
			   onclick="ynjobposting_inputToMap();">{phrase var='refresh_map'}</button>
							<input type="hidden" name="val[gmap][latitude]" value="{value type='input' id='input_gmap_latitude'}"
			   id="input_gmap_latitude"/>
							<input type="hidden" name="val[gmap][longitude]" value="{value type='input' id='input_gmap_longitude'}"
			   id="input_gmap_longitude"/>
						</div>
					</div>
					<div class="table form-group">
						<div class="table_left">
							<div id="mapHolder" style="width: 100%; height: 400px;float:none;"></div>
						</div>
					</div>

					<div class="table form-group">
						<div class="table_left"><label for="working_time">{phrase var='time'}:</label></div>
						<div class="table_right"><input type="text" class="form-control" name="val[working_time]" value="{value type='input' id='working_time'}" id="working_time" size="60"/></div>
					</div>

					<div class="table form-group">
						<div class="table_left"><label for="">{phrase var='expire_on'}:</label></div>
						<div class="table_right">
							<div class="ync_disable"
			 style="position: relative; {if $bIsEdit} {if !$aForms.time_expire}  display: none; {/if} {/if}">
							{select_date prefix='time_expire_' id='_time_expire' start_year='current_year' end_year='+10'
			field_separator=' / ' field_order='MDY' default_all=true}
							</div>
						</div>
					</div>

					<div class="table form-group-follow">
						<div class="table_left">
							<label for="industry">{phrase var='catjob_cat'}:</label>
						</div>
						<div class="table_right">
							{$sCategories}
							<div class="extra_info">{phrase var='you_can_add_up_to_3_catjob'}</div>
						</div>
					</div>

                    {if count($aFields)}
                        {foreach from=$aFields item=aField}
                        {template file='jobposting.block.custom.form'}
                        {/foreach}
                    {/if}

                     <div class="table form-group-follow">
						<div class="table_left">
							<label for="">{phrase var='job_privacy'}:</label>
						</div>

						<div class="table_right">
							{module name='privacy.form' privacy_name='privacy' privacy_info='Control who can see your job'
		privacy_no_custom=true}
						</div>
					</div>
					{if $draft}
					<div class="table form-group-follow">
						<div class="table_left">{phrase var='select_your_existing_packages'}</div>
						<div class="table_right">
							{foreach from=$aPackages name=package item=aPackage}
							<div class="radio">
								<label>
									<input rel="0" value="{$aPackage.data_id}" type="radio" name="radio_package" {if
						   $phpfox.iteration.package==1}checked="true" {/if}/>
									{$aPackage.name} - {$aPackage.fee_text} - {if $aPackage.post_number==0}{phrase
					var='unlimited'}{else}{phrase var='remaining'} {$aPackage.remaining_post}
					{phrase var='job_posts'}{/if} - {$aPackage.expire_text_2}
								</label>
							</div>
							{foreachelse}
			{phrase var='no_package_found'}
			{/foreach}
						</div>
					</div>
					{if ($buy_packages)}
					<div class="table form-group-follow">
						<div class="table_left">
							{phrase var='or_select_the_one_of_following_packages'}
						</div>
						<div class="table_right">
							{foreach from=$aTobuyPackages name=tbpackage item=aTBPackage}
							<div class="radio">
								<label for="radio_package">
									<input rel="1" value="{$aTBPackage.package_id}" type="radio" name="radio_package"/>
									{$aTBPackage.name} - {$aTBPackage.fee_text} - {if $aTBPackage.post_number==0}{phrase
					var='unlimited'}{else}{phrase var='remaining'}{$aTBPackage.post_number}{phrase var='job_posts'}{/if} - {$aTBPackage.expire_text}
								</label>
							</div>
							{foreachelse}
			{phrase var='no_package_found'}
			{/foreach}
						</div>
					</div>
					{/if}
	{/if}

	{if $bCanFeature && $iFeature}
					<div class="table_right">
						<label>
							<input type="checkbox" name="feature" value="1"/>
							{phrase
			var='feature_this_job_with_featurefee' featurefee=$featurefee}
						</label>
					</div>
					{/if}
					<div class="table_clear">
						<input type="submit"
			   value="{if $bIsEdit && $aForms.post_status==1}{phrase var='update'}{else}{phrase var='save_as_draft'}{/if}"
			   class="btn btn-sm btn-success">

						<input type="button" value="{phrase var='publish'}"
			   class="btn btn-sm btn-primary {if $bIsEdit && $aForms.post_status==1}button_off{/if}" {if $bIsEdit &&
			   $aForms.post_status==1}disabled{/if} onclick="publishJob();"></div>
					{if Phpfox::getParam('core.display_required')}
					<div class="table_clear">{required} {phrase var='core.required_fields'}</div>
					{/if}
				</div>
			</form>
		</div>
		{literal}
<script type="text/javascript">
	function changeCompany(obj) {
		window.location.href = '{/literal}{url link='jobposting.add'}{literal}' + 'company_' + obj.value;
	}

	if (typeof showNotice == 'undefined') {
        function showNotice(title, message) {
            window.parent.sCustomMessageString = message;
            tb_show(title, $.ajaxBox('core.message', 'height=150&width=300'));
        }
	}
</script>
		{/literal}

{if empty($job_id)}
{literal}
		<script type="text/javascript">
	function publishJob() {
		var packages = $('[name=radio_package]:checked').val();
		if (packages > 0) {
			$('#popup_packages').val(packages);
		} else {
            showNotice(oTranslations['notice'], oTranslations['please_select_a_package_to_publish_this_job']);
			return false;
		}
		var rel = $('[name=radio_package]:checked').attr('rel');
		$('#popup_paypal').val(rel);
		$('#popup_publish').val(1);
		var feature = $('[name=feature]').is(':checked');
		if (feature) {
			$('#popup_feature').val(1);
		}
		$('#ync_edit_jobposting_form').submit();
	}
</script>
{/literal}
{else}
{literal}
<script type="text/javascript">
	function publishJob() {
	var param = {/literal}'id={$job_id}'{literal};
	//console.log(param);
	var packages = $('[name=radio_package]:checked').val();
	if (packages > 0) {
		param += '&package=' + packages;
	}
	else
	{
        showNotice(oTranslations['notice'], oTranslations['please_select_a_package_to_publish_this_job']);
		return false;
	}
	param += '&paypal=' + $('[name=radio_package]:checked').attr('rel');
	var feature = $('[name=feature]').is(':checked');
	if (feature) {
		param += '&feature=1';
	}
	else
	{
		param += '&feature=0';
	}
		$('#js_job_publish_btn').attr('disabled', true);
		$('#js_job_publish_loading').html($.ajaxProcess(oTranslations['jobposting.processing'])).show();
		$.ajaxCall('jobposting.publishJob', param);
		return false;
	}
</script>
{/literal}
		{/if}

		<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key={param var='core.google_api_key'}&libraries=places"></script>