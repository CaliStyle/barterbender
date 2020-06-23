{literal}
<script type="text/javascript">
	function submitForm(){
		$('#tmp_end_month').val($('#end_month').val());
		$('#tmp_end_day').val($('#end_day').val());
		$('#tmp_end_year').val($('#end_year').val());
		$('#jobposting_adv_search_form').submit();
	};
</script>
{/literal}

<div class="yns adv-search-block" id ="jobposting_adv_search" {if !$bIsAdvSearch }style="display:none; margin-bottom: 10px;"{else}style="display:block; margin-bottom: 10px;"{/if}>
	<form method="post" action="{$core_path}jobposting/?bIsAdvSearch=1" name="jobposting_adv_search_form" id="jobposting_adv_search_form" >
		<input type="hidden" id="flag_advancedsearch" {if !$bIsAdvSearch }value="0"{else}value="1"{/if} name="search[flag_advancedsearch]"/>
		<input type="hidden" value="{if isset($aForms.end_month)}$aForms.end_month{else}0{/if}" id="tmp_end_month" name="search[end_month]"/>
		<input type="hidden" value="{if isset($aForms.end_year)}$aForms.end_year{else}0{/if}" id="tmp_end_year" name="search[end_year]"/>
		<input type="hidden" value="{if isset($aForms.end_day)}$aForms.end_day{else}0{/if}" id="tmp_end_day" name="search[end_day]"/>
		<!-- Keywords -->
		<div class="form-group">
			<label>{phrase var='keyword'}</label>:
			<div class="table_right">
				<input type="text" name="search[keywords]" class="form-control" value="{if isset($aForms.keywords)}{$aForms.keywords}{/if}" id="keywords" size="22" maxlength="200" />
			</div>
		</div>

		<div class="form-group">
			<label>{phrase var='company'}</label>:
			<div class="table_right">
				<input type="text" name="search[company]" class="form-control" value="{if isset($aForms.company)}{$aForms.company}{/if}" id="company" size="22" maxlength="200" />
			</div>
		</div>

		<div class="form-group">
			<label>{phrase var='country'}</label>:
			<div class="table_right">
	            {$sCountries}
	            {module name='core.country-child'}
			</div>
        </div>

        <div class="form-group">
			<label>{phrase var='city'}</label>:
			<div class="table_right">
				<input type="text" name="search[city]" class="form-control" value="{if isset($aForms.city)}{$aForms.city}{/if}" id="city" size="22" maxlength="200" />
			</div>
		</div>
        
		<div class="form-group">
			<label>{phrase var='catjob_cat'}</label>:
			<div class="table_right">
				{$aCategoriesBlock}
			</div>
		</div>

		<div class="form-group">
			<label>{phrase var='language_preference'}</label>:
			<div class="table_right">
				<input type="text" name="search[language_prefer]" class="form-control" value="{if isset($aForms.language_prefer)}{$aForms.language_prefer}{/if}" id="language_prefer" size="22" maxlength="200" />
			</div>
		</div>

		<div class="form-group">
			<label>{phrase var='education_preference'}</label>:
			<div class="table_right">
				<input type="text" name="search[education_prefer]" class="form-control" value="{if isset($aForms.education_prefer)}{$aForms.education_prefer}{/if}" id="education_prefer" size="22" maxlength="200" />
			</div>
		</div>

		<div class="form-group">
			<label>{phrase var='working_place'}</label>:
			<div class="table_right">
				<input type="text" name="search[working_place]" class="form-control" value="{if isset($aForms.working_place)}{$aForms.working_place}{/if}" id="working_place" size="22" maxlength="200" />
			</div>
		</div>

		<div class="form-group">
			<label>{phrase var='expire_before'}</label>:
			<div class="table_right">
				<div class="js_event_select">	
						{select_date
							prefix='end_'
							id='_end'
							start_year='current_year'
							end_year='+1'
							field_separator=' / ' field_order='MDY'
							default_all=true
							time_separator='event.time_separator'}
				</div>
			</div>
		</div>		

		<div class="form-group">
			<input type="button" onclick="submitForm();return false;" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-sm btn-success"/>
		</div>
	</form>
</div>

