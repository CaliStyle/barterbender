
<div class="ynsaSection section_float_left">

	<div class="ynsaSectionHeading ynsaClearFix">
		<div class="ynsaLFloat">
			<div class="ynsaHeadingText">
				{phrase var='your_audience'}
			</div>
		</div>
		<div class="ynsaRFloat">
			<!-- put help link here -->
		</div>
	</div>

	<div class="ynsaSectionContent ynsaSectionFixOverflowPlacement min_height">

		<div class="ynsaLeftInnerContent">
			<div class=" form-group">
				<label>
					{phrase var='location'}:
				</label>
				<select class="form-control ynsaAudience" multiple="multiple" data-placeholder="{phrase var='all_locations'}" name="val[audience_location][]" id="ynsa_location">
					{foreach from=$aAllCountries key=sIso item=aCountry}
						<option value="{$sIso}"
						{if isset($aForms) && isset($aForms.audience_location) && $aForms.audience_location}
							{foreach from=$aForms.audience_location item=sAudienceLocation}
								{if $sIso == $sAudienceLocation} selected="selected" {/if}
							{/foreach}
						{/if}

						> {$aCountry.name}
					{/foreach}
				</select>
				<div class="clear"></div>
			</div>

			<div class="form-group">
				<label>
					{phrase var='gender'}:
				</label>
				<select class="form-control ynsaAudience" name="val[audience_gender]"  id="ynsa_gender">
					<option value="0">{phrase var='core.any'}
					{foreach from=$aGenders key=iId item=sGender}
						<option value="{$iId}" {if isset($aForms) && $aForms.audience_gender == $iId} selected="selected" {/if} > {$sGender} </option>
					{/foreach}
				</select>
				<div class="clear"></div>
			</div>
			<div class="table form-group ynsa_age_range">
				<label>
					{phrase var='age_between'}:
				</label>
				<div class="table_right">
					<select class="form-control ynsaAudience" name="val[audience_age_min]" id="age_min">
						<option value="0">{phrase var='core.any'}</option>
						{foreach from=$aAge item=iAge}
							<option value="{$iAge}" {if isset($aForms) && $aForms.audience_age_min == $iAge} selected="selected" {/if} >{$iAge}</option>
						{/foreach}
					</select>

					<span id="js_age_to">
						<span class="ynsa_middleselect_text">{phrase var='and'}</span>
						<span class="age_max_wrapper"><select class="form-control ynsaAudience" name="val[audience_age_max]" id="age_max">
						<option value="200">{phrase var='ad.any'}</option>
						{foreach from=$aAge item=iAge}
							<option value="{$iAge}" {if isset($aForms) && $aForms.audience_age_max == $iAge} selected="selected" {/if}>{$iAge}</option>
						{/foreach}
						</select></span>
					</span>
				</div>
			</div>
		</div> <!-- end right section -->

		<div class="ynsaExtraSectionInfo ynsaExtraSectionExtraWide">
			<div class="ynsaExtraSectionInfoTitle ">
				{phrase var='affected_audiences'}
			</div>
			<div class="ynsaExtraSectionInfoContent">
				<div class="ynsaHighlightExtraContent">
					<span id="js_ynsa_number_affected_audience">  </span> <span class="ynsaPeople" > {phrase var='people'} </span>
				</div>
			</div>
		</div>
        <input type="hidden" name="val[is_show_guest]" value=" {if isset($aForms) && $aForms.is_show_guest}{$aForms.is_show_guest}{else}0{/if}">
	</div> <!-- end audience section content -->
</div> <!-- end audience section -->
