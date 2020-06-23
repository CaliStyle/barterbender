<div class="yns subscribe-block" id ="jobposting_subscribe">
	<form method="post" action="{if $req3=='add'}{url link='jobposting.subscribe.add'}{else}{url link='jobposting.subscribe.edit'}{/if}" name="jobposting_subscribe_form" id="jobposting_subscribe_form">
		<!-- Keywords -->
		<div class="ynjp_subscribeJ_holder">
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='keyword'}</span>:
				<div class="p_4">
					<input type="text" name="val[keywords]" value="{if isset($aForms.keywords)}{$aForms.keywords}{/if}" class="form-control" id="keywords" size="22" maxlength="200" />
				</div>
			</div>
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='company'}</span>:
				<div class="p_4">
					<input type="text" name="val[company]" value="{if isset($aForms.company)}{$aForms.company}{/if}" class="form-control" id="company" size="22" maxlength="200" />
				</div>
			</div>
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='location'}</span>:
				<div class="p_4">
					<input type="text" name="val[location]" value="{if isset($aForms.location)}{$aForms.location}{/if}" class="form-control" id="location" size="22" maxlength="200" />
				</div>
			</div>
		</div>
		<div class="ynjp_subscribeJ_holder">
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='industry'}</span>:
				<div class="p_4">
					{$aIndustryBlock1}
				</div>
			</div>
		</div>
		<div class="ynjp_subscribeJ_holder">
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='language_preference'}</span>:
				<div class="p_4">
					<input type="text" name="val[language_prefer]" value="{if isset($aForms.language_prefer)}{$aForms.language_prefer}{/if}" class="form-control" id="language_prefer" size="22" maxlength="200" />
				</div>
			</div>
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='education_preference'}</span>:
				<div class="p_4">
					<input type="text" name="val[education_prefer]" value="{if isset($aForms.education_prefer)}{$aForms.education_prefer}{/if}" class="form-control" id="education_prefer" size="22" maxlength="200" />
				</div>
			</div>
			<div class="p_top_4">
				<span class="ynjp_browse_title">{phrase var='working_place'}</span>:
				<div class="p_4">
					<input type="text" name="val[working_place]" value="{if isset($aForms.working_place)}{$aForms.working_place}{/if}" class="form-control" id="working_place" size="22" maxlength="200" />
				</div>
			</div>
		</div>
		<div class="ynjp_subscribeJ_holder">
			<div class="col-sm-6" style="padding-left:0">
				<span class="ynjp_browse_title">{phrase var='expire_before'}</span>:

				<div class="input-group input-group ynjp_searchDatePicker">
					<span class="ynjp_search_DatePicker_holder">
                        <div class="js_from_select">
                            {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
					</span>
				</div>
			</div>		

		</div>
		<div class="p_top_8">
			<input type="submit" id="filter_submit" name="val[submit]" value="{phrase var='save'}" class="btn btn-sm btn-primary" />
		</div>
	</form>
</div>

{literal}
<style>
    #ui-datepicker-div {
        z-index: 9999 !important;
    }
</style>
<script type="text/javascript">
	popup = 1;
	$Core.loadInit();
</script>
{/literal}