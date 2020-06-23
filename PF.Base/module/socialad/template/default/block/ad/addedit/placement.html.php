<div class="ynsaSection section_float_left first">

	<div class="ynsaSectionHeading ynsaClearFix">
		<div class="ynsaLFloat">
			<div class="ynsaHeadingText">
				{phrase var='campaign_schedule_and_placement'}
			</div>
		</div>
		<div class="ynsaRFloat">
			<!-- put help link here -->
		</div>
	</div>

	<div class="ynsaSectionContent ynsaSectionFixOverflowPlacement min_height">

		<div class="form-group">
				<label>
				{phrase var='select_campaign'}:
				</label>
				<select class="form-control" name="val[campaign_id]" id="js_select_campaign">
					{foreach from=$aSaCampaigns item=aCampaign}
						<option value="{$aCampaign.campaign_id}"
							{if isset($aForms) && $aForms.ad_campaign_id == $aCampaign.campaign_id}
							selected="selected"
							{/if}
						>{$aCampaign.campaign_name}</option>
					{/foreach}
				</select>
				<div class="clear"></div>
		</div>

		<div class="form-group" id="js_ynsa_campaign_name_holder">
				<label for="title">{phrase var='campaign_name'}</label>
				<input class="form-control" type="text" name="val[campaign_name]" value="{value type='input' id='campaign_name'}" id="js_campaign_name" />
		</div>

		<div class="form-group  ynsa_schedule"> <!-- schedule -->
			<label>
				{phrase var='schedule'}:
				</label>
				<ul class="checklist_grp">
					<li>
				<label><input type="radio" checked="checked" name="val[is_continuous]" value="1" onclick="$('#js_ynsa_ad_time').hide();" value="" /> {phrase var='run_ad_continously_when_approved'}</label></li>
				<li>
				<label><input type="radio" {if isset($aForms) && !$aForms.is_continuous} checked="checked" {/if} name="val[is_continuous]"  value="0"  onclick="$('#js_ynsa_ad_time').show();" > {phrase var='specify_start_and_end_date'}</label></li>
				</ul>
				<div class="ynsaStartEndTime" id="js_ynsa_ad_time"
					{if !isset($aForms) || (isset($aForms) && $aForms.is_continuous)} style="display:none" {/if} >
					<div class="ynsaAdTime ynsaClearFix" >
						<div class="ynsaAdTimeTitle ynsaLFloat" >{phrase var='start'} : </div>

						<div class="ynsaAdTimeContent ynsaLFloat">
							 {select_date prefix='ad_expect_start_time_' id='_begin_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' time_separator=' / ' default_all=true add_time=true }
						</div>
					</div>
					<div class="clear"> </div>
					<div class="ynsaAdTime" >
						<div class="ynsaAdTimeTitle ynsaLFloat" >{phrase var='end'} : </div>

						<div class="ynsaAdTimeContent ynsaLFloat">
							 {select_date prefix='ad_expect_end_time_' id='_begin_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' time_separator=' / ' default_all=true add_time=true }
						</div>
					</div>

				<input type="hidden" id="js_ynsa_start_stop_time_fake"/>

			</div>
				<div class="clear"></div>
		</div> <! -- end schedule -->

		<div class="form-group" id="js_ynsa_select_module">
			<label>
				{phrase var='module_placement'}:
			</label>
			<select class="form-control" multiple="multiple" data-placeholder="{phrase var='all_modules'}" name="val[placement_module_id][]" id="js_ynsa_choose_module">
				{foreach from=$aSaModules key=sModuleId item=sModuleName}
					<option value="{$sModuleId}"
					{if isset($aForms) && isset($aForms.placement_module) && $aForms.placement_module}
						{foreach from=$aForms.placement_module item=sPlacementModuleId}
							{if $sModuleId == $sPlacementModuleId} selected="selected" {/if}
						{/foreach}
					{/if}> {$sModuleName}
				{/foreach}
			</select>
				<div class="clear"></div>
		</div>


	</div> <!-- end section content -->
</div> <!-- end campaign and placement section -->
