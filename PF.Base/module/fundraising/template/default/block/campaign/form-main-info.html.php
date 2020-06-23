<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_fundraising_block_main" class="js_fundraising_block page_section_menu_holder">
	<div class="form-group">
		<label>{required}{phrase var='category'}:</label>
		{$sCategories}
	</div>
	
	<div class="form-group">
		<label for="title">{required}{phrase var='campaign_name'}: </label>
		<input class="form-control required" type="text" class="ynfr required ynfr_campaign_title_max_length" name="val[title]" value="{value type='input' id='title'}" id="title" size="60" />
		<div class="extra_info">
			{phrase var='you_can_enter_maximum_number_characters', number=255}
		</div>
	</div>

	<div class="form-group">
		<label for="short_description">{required}{phrase var='short_description'}:</label>
		<textarea cols="59" rows="10" name="val[short_description]" class="form-control js_edit_fundraising_form ynfr required ynfr_campaign_short_description_max_length" id="short_description">{value id='short_description' type='textarea'}</textarea>
		<div class="extra_info">
			{phrase var='you_can_enter_maximum_number_characters', number=500}
		</div>
	</div>

	<div class="form-group">
		<label>{phrase var='main_description'}</label>
		{editor id='description'}
	</div>
	{plugin call='fundraising.template_controller_add_textarea_end'}
    <div class="form-group">
        <label>{required}{phrase var='your_paypal_account'}:</label>
        <input type="text" class="form-control ynfr required email" name="val[paypal_account]"  {if $bIsEdit && $aForms.user_id != Phpfox::getUserId()} disabled=true{/if}  value="{value type='input' id='paypal_account'}" id="paypal_account" size="60" />
    </div>
	<div class="form-group">
		<label for="financial_goal">{phrase var='campaign_goal_financial_goal'}:</label>
		{if !$bIsEdit}
			<input type="text" name="val[financial_goal]" class="form-control ynfr required number ynfr_positive_number" value="{$iDefaultFundraising}" id="financial_goal" size="60" />
		{else}
		
		<input type="text" name="val[financial_goal]"class="form-control ynfr required number ynfr_positive_number" value="{value type='input' id='financial_goal'}" id="financial_goal" size="60" />
		{/if}
        <div class="extra_info">
            {phrase var='set_0_for_unlimit_goal'}
        </div>
	</div>
    <div class="form-group">
        <label>{phrase var='currency'}:</label>
        <select id="donation_select_currency" class="form-control ynfr required" {if $bIsEdit && $aForms.is_draft != 1} title="{phrase var='can_not_edit_currency_ongoing_campaign'}"disabled="disabled" {/if} name='val[selected_currency]'>
            {foreach from=$aCurrentCurrencies key=key item=aCurrency}
            <option value="{$aCurrency.currency_id}">
                {$aCurrency.currency_id}
            </option>
            {/foreach}
        </select>
    </div>
	<div class="form-group">
		<label>{phrase var='expired_date'}:</label>
		<div class="extra_info">
			{phrase var='you_can_set_expired_date'}
		</div>
		<div class="ynfr_expired_time" style="position: relative; {if $bIsEdit && ($aForms.unlimit_time == 'checked' || !$aForms.end_time) } display: none; {/if}">
			{select_date prefix='expired_time_' id='_expired_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
		</div>
        <div class="extra_info">
            <input type="checkbox" name="val[unlimit_time]" onclick="disable($(this));" value="1" id="unlimit_time" {if $bIsEdit} {if !$aForms.end_time} checked="checked"{/if} {/if} /> {phrase var='set_to_unlimit_time'}
        </div>
	</div>

	<div class="form-group">
		<label>{phrase var='minimum_donation'}:</label>
		{if !$bIsEdit}
		<input type="text" name="val[minimum_amount]" class="form-control number ynfr_positive_number" value="{$iDefaultMinFundraising}" id="minimum_amount" />
		{else}
		<input type="text" name="val[minimum_amount]" class="form-control number ynfr_positive_number" value="{value type='input' id='minimum_amount'}" id="minimum_amount" />
		{/if}
	</div>
    {literal}
    <script type="text/javascript">
        function disable() {
            if(document.getElementById('unlimit_time').checked)
                $('.ynfr_expired_time').hide();
            else
                $('.ynfr_expired_time').show();
        }
    </script>
    {/literal}
	<div class="form-group form-group-follow">
		<label>{phrase var='list_predefine'}:</label>
		<div class="p_4 predefined_holder" id="">
			{if !$bIsEdit}
				{foreach from=$aTempPredefined key=iKey item=aPredefined}
					{template file='fundraising.block.campaign.predefine-main-info-form'}
				{/foreach}
			{else}
                {foreach from=$aForms.predefined_amount_list key=iKey item=aPredefined}
                {if isset($aPredefined) && !empty($aPredefined)}
                   {template file='fundraising.block.campaign.predefine-main-info-form'}
                {/if}
                {/foreach}
                {if !isset($aForms.predefined_amount_list)}
                    {foreach from=$aTempPredefined key=iKey item=aPredefined}
                        {template file='fundraising.block.campaign.predefine-main-info-form'}
                    {/foreach}
                {/if}
			{/if}
		</div>
		<div class="extra_info">
			{phrase var='enter_up_to_preselect'}
		</div>
	</div>
	
	<div class="form-group form-group-follow">
		<div class="table_right">
			<input value="1" type="checkbox" name="val[allow_anonymous]" id="allow_anonymous" {if $bIsEdit} {$aForms.allow_anonymous} {else} checked="checked"{/if} /> {phrase var='allow_anonymous'}
		</div>
	</div>

	<div class="form-group">
		{required}<label>{phrase var='location_venue'}:</label>
		<div class="extra_info">
			{phrase var='please_fill_your_address_here'}
		</div>
		<input type="text" name="val[location_venue]" class="form-control ynfr required" value="{value type='input' id='location_venue'}" id="location_venue" size="40" maxlength="200" />
		<div class="extra_info">
            {if !$bIsEdit}
            <a href="#" id="js_link_show_add" onclick="$(this).hide(); $('#js_mp_add_city').show(); $('#js_link_hide_add').show(); return false;">{phrase var='add_city_zip'}</a>
            <a href="#" id="js_link_hide_add" style="display: none;" onclick="$(this).hide(); $('#js_mp_add_city').hide(); $('#js_link_show_add').show(); return false;">{phrase var='hide_add_city_zip'}</a>
            {/if}
		</div>
	</div>

	<div id="js_mp_add_city" {if !$bIsEdit} style="display:none;"{/if} >
		 <div class="form-group" style="display:none">
			<label >{phrase var='address'}</label>
			<input class="form-control" type="text" name="val[address]" value="{value type='input' id='address'}" id="address" size="30" maxlength="200" />
		</div>

		<div class="table form-group">
			<label>{phrase var='city'}:</label>
			<input class="form-control" type="text" name="val[city]" value="{value type='input' id='city'}" id="city" size="20" maxlength="200" />
		</div>
		<div class="form-group">
			<label for="postal_code">{phrase var='zip_postal_code'}:</label>
			<input class="form-control" type="text" name="val[postal_code]" value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20" />
		</div>
		<div class="form-group">
			{required}<label for="country_iso">{phrase var='country'}:</label>
			{select_location}
		</div>
	</div>
	<div class="form-group">
		<button type="button" class="btn btn-sm btn-primary" value="{phrase var='refresh_map'}" onclick="ynfundraising_map.inputToMap();">{phrase var='refresh_map'}</button>
		<input type="hidden" name="val[gmap][latitude]" value="{value type='input' id='input_gmap_latitude'}" id="input_gmap_latitude" />
		<input type="hidden" name="val[gmap][longitude]" value="{value type='input' id='input_gmap_longitude'}" id="input_gmap_longitude" />
		<div id="mapHolder" class="mt-1" style="width: 400px; height: 400px"></div>
	</div>

	{if empty($sModule) && Phpfox::isModule('privacy') && Phpfox::getUserParam('fundraising.can_set_allow_list_on_campaigns')}
	<div class="form-group form-group-follow">
		<label>{phrase var='privacy'}:</label>
		{module name='privacy.form' privacy_name='privacy' privacy_info='fundraising.control_who_can_see_this_fundraising'  default_privacy='fundraising.default_privacy_setting'}
	</div>
	{/if}

	{if empty($sModule)  && Phpfox::isModule('privacy') && Phpfox::getUserParam('fundraising.can_control_donate_on_campaigns')}
	<div class="form-group form-group-follow">
		<label>{phrase var='donate_privacy'}</label>
		{module name='privacy.form' privacy_name='privacy_donate' privacy_info='fundraising.control_who_can_donate_on_this_fundraising' privacy_no_custom=true}
	</div>
	{/if}

	<div class="table_clear">
		{if $bIsEdit && $aForms.is_draft == 1}
			<button type="submit" name="val[draft_update]" value="{phrase var='update'}" class="btn btn-sm btn-primary">{phrase var='update'}</button>
			<button type="submit" name="val[draft_publish]" onclick="return confirmBeforeSubmit(this)" value="{phrase var='publish'}" class="btn btn-sm btn-primary">{phrase var='publish'}</button>
		{else}
			<button type="submit" name="val[{if $bIsEdit}update{else}publish{/if}]" {if !$bIsEdit} onclick="return confirmBeforeSubmit(this)" {/if} value="{if $bIsEdit}{phrase var='update'}{else}{phrase var='publish'}{/if}" class="btn btn-sm btn-primary">{if $bIsEdit}{phrase var='update'}{else}{phrase var='publish'}{/if}</button>
		{/if}
		{if !$bIsEdit}<button type="submit" name="val[draft]" value="{phrase var='save_as_draft'}" class="btn btn-sm btn-default">{phrase var='save_as_draft'}</button>{/if}
	</div>
	{if Phpfox::getParam('core.display_required')}
	<div class="table_clear">
		{required} {phrase var='core.required_fields'}
	</div>
	{/if}
</div>

<script type="text/javascript">
function confirmBeforeSubmit(ele) {l}
    $Core.jsConfirm({l}
        message: "{phrase var='confirm_publish_campaign'}"
    {r}, function(){l}
        var oInput = $('<input>',{l}
            type: 'hidden',
            name: $(ele).attr('name'),
            value: $(ele).val()
        {r}).appendTo('#ynfr_edit_campaign_form');
        $('#ynfr_edit_campaign_form').submit();
    {r}, function(){l}{r});

    return false;
{r}
(function(){l}
{if $bIsEdit}
    $Behavior.ynfundraising_setCountry = function()
    {l}
        $("#js_country_iso_option_{$aForms.country_iso}").prop("selected", true);
    {r}
{/if}
$Behavior.initializeValidateCustomClassYnfr = function() {l} 
	function checkCondition(){l}
		if(/undefined/i.test(typeof jQuery.validator))
		{l}
			window.setTimeout(checkCondition, 400);
		{r}
		else
		{l}
			initValidator();
		{r}
	{r}
	window.setTimeout(checkCondition, 400);
	
	function initValidator()
	{l}
		{if isset($aForms.minimum_amount)}
			$.validator.addClassRules("ynfr_sponsor_level_amount", {l}range:[{$aForms.minimum_amount},100000000]{r});
		{/if}
		jQuery.validator.addMethod('greater_than_minimum', function(value, element) {l}
					if(value < parseInt($('#minimum_amount').val()) && value != '')	
					{l}
						return false;
					{r}
	
					return true;
				{r}, '{phrase var='must_greater_than_minimum'}'
			);
	
		jQuery.validator.addClassRules("greater_than_minimum", {l}
				greater_than_minimum: {l}greater_than_minimum: true{r}
		{r});
		jQuery.validator.messages.range = "{phrase var='please_enter_an_amount_greater_or_equal'}" + ' {l}0{r} ' + "" ;
		jQuery.validator.messages.maxlength = "{phrase var='maximum_number_of_characters_for_this_field_is_semicolon'}" + ' {l}0{r} ';
	{r}
{r}
{r})();
</script>

