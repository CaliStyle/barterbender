<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key={$apiKey}&libraries=places"></script>
{$sCreateJs}
<input type="hidden" id="required_custom_fields"/>
<input type="hidden" id="category_event_id" value="{if $bIsEdit}{$aForms.event_id}{else}0{/if}"/>
{if $bIsEdit && $aForms.event_type == 'repeat'}
	<form method="post" action="{url link='current'}" enctype="multipart/form-data" id="js_event_form">
{else}
	<form method="post" action="{url link='current'}" enctype="multipart/form-data" onsubmit="return custom_js_event_form(this);" id="js_event_form">
{/if}
<input type="hidden" value="{$valToken}" name="core[security_token]">
<input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" />
<div><input type="hidden" name="val[current_tab]" value="{if !empty($sAction)}{$sAction}{/if}" id="current_tab"></div>
{if !empty($sModule)}
	<div><input type="hidden" name="module" value="{$sModule|htmlspecialchars}" /></div>
{/if}
{if !empty($iItem)}
	<div><input type="hidden" name="item" value="{$iItem|htmlspecialchars}" /></div>
{/if}

{if $bIsEdit && $aForms.event_type == 'repeat'}
{template file='fevent.block.applyforrepeatevent'}
{/if}

{if $bIsEdit}
	<div><input type="hidden" name="id" value="{if isset($aForms.event_id)}{$aForms.event_id}{else}0{/if}" /></div>
    {if $aForms.event_type != 'repeat'}
        <div><input type="hidden" id="ynfevent_editconfirmboxoption_value" name="val[ynfevent_editconfirmboxoption_value]" value="only_this_event" /></div>
    {/if}	
{/if}
	<div id="js_event_block_detail" class="js_event_block page_section_menu_holder main_break" {if !empty($sActiveTab) && $sActiveTab != 'detail'}style="display:none;"{/if}>
		<div class="form-group">
			{required}<label for="title">{_p var='fevent.what_are_you_planning'}</label>
			<input type="text" name="val[title]" value="{value type='input' id='title'}" id="title" class="form-control" maxlength="100" />
		</div>

        {if !empty($sCategories)}
	        <div class="form-group">
	            <label for="category">{_p var='fevent.category'}:</label>
	            <div id="categories">{$sCategories}</div>
	        </div>
        {/if}

        <div id="ajax_custom_fields" class="ync-customfield">
	        {if $bIsEdit && isset($aCustomFields)}
	            {module name="fevent.custom" aCustomFields=$aCustomFields}
	        {/if}
        </div>
	
		
		<div class="form-group">
			<label for="description">{_p var='fevent.description'}:</label>
			{editor id='description' rows='6'}
		</div>			

		<input type="hidden" id="bIsEdit" value="{$bIsEdit}"/>
		<input type="hidden" id="eventID" value="{if $bIsEdit}{$aEvent.event_id}{else}-1{/if}"/>
		
		<div class="form-inline ynfevent_event_type mb-2" id="ynfevent_event_type" {if $bIsEdit && $aForms.event_type == 'repeat'}style="display: none;"{/if}>
			<input type="hidden" id="ynfevent_event_type_value" value="{if isset($aForms.event_type)}{$aForms.event_type}{/if}" />
			<div class="form-group mr-3">
				<div class="ynfevent-event-type radio">
					<label for="ynfevent_event_type_radio_one_time" class="pl-0">
						<input id="ynfevent_event_type_radio_one_time" type="radio" name="val[event_type]" {if !$bIsEdit || ($bIsEdit && $aForms.isrepeat == -1)}checked="checked"{/if} value="one_time" /><i class="ico ico-circle-o mr-1"></i><span>{_p var='fevent.one_time_event'}</span>
					</label>
				</div>
			</div>
			<div class="form-group">
				<div class="ynfevent-event-type radio">
					<label for="ynfevent_event_type_radio_repeat" class="pl-0">
						<input id="ynfevent_event_type_radio_repeat" type="radio" name="val[event_type]" {if ($bIsEdit && (int)$aForms.isrepeat > -1)}checked="checked"{/if} value="repeat" /><i class="ico ico-circle-o mr-1"></i><span>{_p var='fevent.repeat_event'}</span>
					</label>
				</div>
			</div>
		</div>			

		<div id="ynfevent_one_time_section" {if $bIsEdit && $aForms.event_type == 'repeat'}style="display: none;"{/if}>
			<div class="form-group">
				<label>{_p var='fevent.start_time'}:</label>
				<div>
					{if $bIsEdit && $canEditStartTime == false}
						<span class="ynfe_view_infor">{$aEvent.start_month}/{$aEvent.start_day}/{$aEvent.start_year} {_p var='fevent.at_lower'} {$aEvent.start_hour}:{$aEvent.start_minute}</span>
						<input type="hidden" id="start_month" name="val[start_month]" value="{$aEvent.start_month}"/>
						<input type="hidden" id="start_day" name="val[start_day]" value="{$aEvent.start_day}"/>
						<input type="hidden" id="start_year" name="val[start_year]" value="{$aEvent.start_year}"/>
						<input type="hidden" id="start_hour" name="val[start_hour]" value="{$aEvent.start_hour}"/>
						<input type="hidden" id="start_minute" name="val[start_minute]" value="{$aEvent.start_minute}"/>
					{else}
						{select_date prefix='start_' id='_start' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator=''}				
					{/if}
				</div>
			</div>	
			
			<div class="table form-group" id="js_event_add_end_time">
				<label for="">{_p var='fevent.end_time'}:</label>
				<div>
					{if $bIsEdit && $canEditEndTime == false}
						<span class="ynfe_view_infor">{$aEvent.end_month}/{$aEvent.end_day}/{$aEvent.end_year} {_p var='fevent.at_lower'} {$aEvent.end_hour}:{$aEvent.end_minute}</span>
						<input type="hidden" id="end_month" name="val[end_month]" value="{$aEvent.end_month}"/>
						<input type="hidden" id="end_day" name="val[end_day]" value="{$aEvent.end_day}"/>
						<input type="hidden" id="end_year" name="val[end_year]" value="{$aEvent.end_year}"/>
						<input type="hidden" id="end_hour" name="val[end_hour]" value="{$aEvent.end_hour}"/>
						<input type="hidden" id="end_minute" name="val[end_minute]" value="{$aEvent.end_minute}"/>						
					{else}
						{select_date prefix='end_' id='_end' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+4' time_separator=''}
					{/if}
				</div>
			</div>
			
            <div>
                <p style="font-style:italic;">{_p var='fevent.changes_to_an_event_s_start_end_date_or_time_will_require_guests'}</p>
            </div>					
		</div>

		<div id="ynfevent_repeat_section">
			<div class="form-group" {if $bIsEdit && $aForms.event_type == 'repeat'}style="display: none;"{/if}>
				<label for="description">{_p var='fevent.repeat'}:</label>
				<select name="val[repeat_section_type]" id="selrepeat" class="form-control w-auto">
					<option value="daily" {if isset($aForms.repeat_section_type) && $aForms.repeat_section_type == 'daily'}selected="selected"{/if}>{_p var='fevent.daily'}</option>
					<option value="weekly" {if isset($aForms.repeat_section_type) && $aForms.repeat_section_type == 'weekly'}selected="selected"{/if}>{_p var='fevent.weekly'}</option>
					<option value="monthly" {if isset($aForms.repeat_section_type) && $aForms.repeat_section_type == 'monthly'}selected="selected"{/if}>{_p var='fevent.monthly'}</option>
				</select>
			</div>			
			<div class="form-group">
				<label>{if $bIsEdit && $aForms.event_type == 'repeat'}{_p var='fevent.start_time'}{else}{_p var='fevent.start_event'}{/if}:</label>
				<div>
					{select_date prefix='repeat_section_start_' id='_repeatstart' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator=''}				
				</div>
			</div>	
			<div class="form-group">
				<label>{if $bIsEdit && $aForms.event_type == 'repeat'}{_p var='fevent.end_time'}{else}{_p var='fevent.end_event'}{/if}:</label><div>{select_date prefix='repeat_section_end_' id='_repeatend' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+4' time_separator=''}
				</div>
			</div>
            <div class="form-group">
                <p class="font-italic mb-0">{_p var='fevent.changes_to_an_event_s_start_end_date_or_time_will_require_guests'}</p>
            </div>					
			<div class="form-inline mb-2" {if $bIsEdit && $aForms.event_type == 'repeat'}style="display: none;"{/if}>
				<label>{_p var='fevent.end_repeat'}:</label>
				<div class="row ml--1 mr--1">
					<div class="form-group w-auto pl-1 pr-1 pull-left col-xs-12">
						<label>
							<input type="radio" name="val[repeat_section_end_repeat]" {if isset($aForms.repeat_section_end_repeat) == false || (isset($aForms.repeat_section_end_repeat) && $aForms.repeat_section_end_repeat == 'after_number_event')}checked="checked"{/if} value="after_number_event">
							{_p var='fevent.after'}
						</label>
						<input type="text" class="form-control d-block w-full" name="val[repeat_section_after_number_event]" value="{if isset($aForms.repeat_section_after_number_event)}{$aForms.repeat_section_after_number_event}{/if}" id="ynfevent_after_number_event" size="5" maxlength="5" />
						<p class="extra_info d-block mb-0 mt-h1">{_p var='fevent.event_s'} ({_p var='fevent.allow_maximum'} {$fevent_max_instance_repeat_event} {_p var='fevent.event_s'})</p>
					</div>
					<div class="form-group w-auto pl-1 pr-1 col-xs-12">
						<label for="">
							<input type="radio" name="val[repeat_section_end_repeat]" {if isset($aForms.repeat_section_end_repeat) && $aForms.repeat_section_end_repeat == 'repeat_until'}checked="checked"{/if} value="repeat_until">
							{_p var='fevent.at_uppercase'}
						</label>
						{select_date prefix='repeat_section_repeatuntil_' id='_repeatuntil' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true time_separator='fevent.time_separator'}
					</div>
				</div>
			</div>		

            {if $bIsEdit && $aForms.event_type == 'repeat'}
                {literal}
                    <script type="text/javascript">
                        ;$Behavior.initWithEdtingRepeatEvent = function()
                        {

                            $('#ynfevent_repeat_section').css('display','block');
                            setTimeout(function(){
                                {/literal}{if !Phpfox::getParam('fevent.allow_change_date_recurrent_event')}{literal}
                                $('.js_datepicker_image').remove();
                                $('.js_datepicker_core_repeatstart').find("*").off();
                                $('.js_datepicker_core_repeatend').find("*").off();
                                $('.js_date_picker.hasDatepicker').attr('readonly', true);
                                {/literal}{/if}{literal}

                                {/literal}{if !Phpfox::getParam('fevent.allow_change_time_recurrent_event')}{literal}
                                $('#repeat_section_start_hour').attr('disabled', 'disabled');
                                $('#repeat_section_start_minute').attr('disabled', 'disabled');
                                $('#repeat_section_end_hour').attr('disabled', 'disabled');
                                $('#repeat_section_end_minute').attr('disabled', 'disabled');
                                {/literal}{/if}{literal}
                            }
                            , 1000);
                        };
                    </script>
                {/literal}
            {/if}			
		</div>

		<div class="table form-group" style="display: none;">
			<div class="table_left">
				<input type="checkbox" class="form-control" {if isset($aEvent.isrepeat) && $aEvent.isrepeat!=-1}checked{/if} onclick="showrepeat(1)" id="cbrepeat"/> {_p var='fevent.a_rce'}<span id="chooserepeat" >{if !isset($aEvent.isrepeat) || $aEvent.isrepeat==-1}{else}: {$content_repeat}{/if}</span><span style="padding-left:3px;"><a href="javascript:void(0)" onclick="showrepeat(2)"><span id="editrepeat">{if isset($aEvent.isrepeat) && $aEvent.isrepeat>=0}{_p var='fevent.edit'}{/if}</span></a></span>
				<input type="hidden" value="{if isset($aEvent.isrepeat)}{$aEvent.isrepeat}{else}-1{/if}" id="txtrepeat" name="val[txtrepeat]"/>
				<input type="hidden" value="{$until}" name="val[daterepeat]" id="daterepeat"/>
				<input type="hidden" value="{if isset($aEvent.isrepeat)}{$aEvent.timerepeat_hour}{/if}" name="val[daterepeat_hour]" id="daterepeat_hour"/>
				<input type="hidden" value="{if isset($aEvent.isrepeat)}{$aEvent.timerepeat_minute}{/if}" name="val[daterepeat_min]" id="daterepeat_min"/>
				<input type="hidden" value="{if isset($aEvent.isrepeat)}{$aEvent.duration_days}{/if}" name="val[daterepeat_dur_day]" id="daterepeat_dur_day"/>
				<input type="hidden" value="{if isset($aEvent.isrepeat)}{$aEvent.duration_hours}{/if}" name="val[daterepeat_dur_hour]" id="daterepeat_dur_hour"/>
			</div>
			<div class="table_right">
				
			</div>
		</div>

		{if Phpfox::getUserParam('fevent.allow_delete_attendees_of_past_repeat_event')}
			<div class="table form-group" id="deleteAllAttendeesBox" style="display: none;">
				<div class="table_left">
					<input type="checkbox" class="form-control" id="deleteAllAttendees" name="val[deleteAllAttendees]" {if isset($aEvent.is_delete_user_past_repeat_event) && $aEvent.is_delete_user_past_repeat_event == 1}checked{/if} /> {_p var='fevent.del_attendees_past_event'}
				</div>
				<div class="table_right">
				</div>
			</div>
		{/if}
		
		<div class="form-group">
			{required}<label for="location">{_p var='fevent.location_venue'}:</label>
			<input type="text" class="form-control" name="val[location]" value="{value type='input' id='location'}" id="location_venue" size="40" maxlength="200" />
			{if !$bIsEdit}
			<div class="help-block">
				<a href="#" onclick="$(this).parent().hide(); $('#js_event_add_country').show(); return false;">{_p var='fevent.add_address_city_zip_country_no_range'}</a>
			</div>
			{/if}				
		</div>
		
		<div id="js_event_add_country" {if !$bIsEdit} style="display:none;"{/if} class="mb-2">
			<div class="form-inline row ml--1 mr--1">
				<div class="form-group pl-1 pr-1 col-sm-3">
					<label for="country_iso">{_p var='fevent.country'}:</label>
					<div class="d-block">
						{select_location}
						{module name='core.country-child'}
					</div>
				</div>				 
				<div class="form-group pl-1 pr-1 col-sm-3">
					<label for="street_address">{_p var='fevent.address'}</label>
					<input type="text" class="form-control d-block w-full" name="val[address]" value="{value type='input' id='address'}" id="address" size="30" maxlength="200" />
				</div>			 			 
				<div class="form-group pl-1 pr-1 col-sm-3">
					<label for="city">{_p var='fevent.city'}:</label>
					<input type="text" class="form-control d-block w-full" name="val[city]" value="{value type='input' id='city'}" id="city" size="20" maxlength="200" />
				</div>		
				<div class="form-group pl-1 pr-1 col-sm-3">
					<label for="postal_code">{_p var='fevent.zip_postal_code'}:</label>
					<input type="text" class="form-control d-block w-full" name="val[postal_code]" value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20" />
				</div>
			</div>
			
			<div class="form-group" style="display: none;">
				<label for="range_value">{_p var='fevent.range'}:</label>
				<input type="text" class="form-control" name="val[range_value]" value="{value type='input' id='range_value'}" id="range_value" size="10" maxlength="20" />
				<select name="val[range_type]" class="form-control">
					<option value="0" {if isset($aEvent.range_type) && $aEvent.range_type==0}selected{/if}>{_p var='fevent.miles'}</option>
					<option value="1" {if isset($aEvent.range_type) && $aEvent.range_type==1}selected{/if}>{_p var='fevent.km'}</option>
				</select>
			</div>		
		</div>
		
		{if $bCanAddMap}
        <div class="form-group">
			<input id="refresh_map" type="button" class="mb-1 btn btn-sm btn-primary" value="{_p var='fevent.refresh_map'}" onclick="inputToMapAdvEvent();"/>
			<input type="hidden" name="val[gmap][latitude]" value="{value type='input' id='input_gmap_latitude'}" id="input_gmap_latitude" />
			<input type="hidden" name="val[gmap][longitude]" value="{value type='input' id='input_gmap_longitude'}" id="input_gmap_longitude" />
			<div id="mapHolder" class="mb-2 mt-1"></div>
        </div>
        {/if}
        
		{if empty($sModule) && Phpfox::isModule('privacy')}
		<div class="form-group-follow">
			<label class="">{_p var='fevent.event_privacy'}:</label>
			{module name='privacy.form' privacy_name='privacy' privacy_info='fevent.control_who_can_see_this_event' privacy_no_custom=true default_privacy='fevent.display_on_profile'}
		</div>

		{/if}
		
		<div class="table_clear">
		{if $bIsEdit}
			<input type="submit" name="val[update_detail]" value="{_p var='fevent.update'}" class="btn btn-primary" />
		{else}	
			<input type="submit" name="val[submit_detail]" value="{_p var='fevent.submit'}" class="btn btn-primary" />
		{/if}
		</div>
		
	</div>

	<div id="js_event_block_customize" class="js_event_block page_section_menu_holder main_break" {if empty($sActiveTab) || $sActiveTab != 'customize'}style="display:none;"{/if}>
        <div id="js_fevent_form_holder" class="uploader-photo-fix-height">
            {if isset($aForms) && isset($aForms.total_image) && ($aForms.total_image < $aForms.image_limit)}
                <div class="alert alert-success" id="js_fevent_succes_message" style="display: none;">{_p var='photo_s_uploaded_successfully'}</div>
                {module name='core.upload-form' type='fevent' params=$aForms.params id=$aForms.event_id}
                <div class="fevent-submit-upload">
                    <a href="{url link='fevent.add' id=$aForms.event_id tab='customize'}" id="js_fevent_done_upload" style="display: none !important;" onclick="$Core.dropzone.instance['fevent'].files = [];" class="text-uppercase"><i class="ico ico-check"></i>&nbsp;{_p var='finish_upload'}</a>
                </div>
            {/if}
        </div>
        <div class="fevent_manage_photos_holder p-1">
            {module name='fevent.photo'}
        </div>
	</div>
	<!--Admins : START -->
	{if $bIsEdit}
	<div id="js_event_block_admins" class="js_event_block page_section_menu_holder main_break" {if empty($sActiveTab) || $sActiveTab != 'admins'}style="display:none;"{/if}>
        <div class="form-group">
            {module name='friend.search-small' input_name='admins' current_values=$aForms.admins}
        </div>
        <div class="form-group">
            <input type="submit" value="{_p var='update'}" class="btn btn-primary"/>
        </div>
	</div>
	{/if}
	<!--Admins : END -->
	<div id="js_event_block_invite" class="js_event_block page_section_menu_holder main_break" {if empty($sActiveTab) || $sActiveTab != 'invite'}style="display:none;"{/if}>
        <div class="block">
            <div class="form-group">
                <label for="js_find_friend">{_p var='invite_friends'}</label>
                {if isset($aForms.event_id)}
                <div id="js_selected_friends" class="hide_it"></div>
                {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.event_id friend_module_id='fevent' }
                {/if}
            </div>
            <div class="form-group invite-friend-by-email">
                <label for="emails">{_p var='invite_people_via_email'}</label>
                <input name="val[emails]" id="emails" class="form-control" data-component="tokenfield" data-type="email" >
                <p class="help-block">{_p var='separate_multiple_emails_with_a_comma'}</p>
            </div>
            <div class="form-group">
                <label for="personal_message">{_p var='add_a_personal_message'}</label>
                <textarea rows="1" name="val[personal_message]" id="personal_message" class="form-control textarea-auto-scale" placeholder="{_p var='write_message'}"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary" name="invite_submit"/>
            </div>
        </div>
	</div>	
	
	{if $bIsEdit}
	<div id="js_event_block_manage" class="js_event_block page_section_menu_holder main_break" {if empty($sActiveTab) || $sActiveTab != 'manage'}style="display:none;"{/if}>
		{module name='fevent.list'}
	</div>
	{/if}
	
	{if $bIsEdit && Phpfox::getUserParam('fevent.can_mass_mail_own_members')}
	<div id="js_event_block_email" class="js_event_block page_section_menu_holder main_break" {if empty($sActiveTab) || $sActiveTab != 'email'}style="display:none;"{/if}>
		<div id="js_send_email"{if !$bCanSendEmails} style="display:none;"{/if}>
			<div class="help-block">
				{_p var='fevent.send_out_an_email_to_all_the_guests_that_are_joining_this_event'}
				{if isset($aForms.mass_email) && $aForms.mass_email}
				<br />
				{_p var='fevent.last_mass_email'}: {$aForms.mass_email|date:'mail.mail_time_stamp'}
				{/if}
			</div>
			
			<div class="form-group">
				<label class="">
					{_p var='fevent.subject'}:
				</label>
				<input type="text" name="val[mass_email_subject]" value="" class="form-control" size="30" id="js_mass_email_subject" />
			</div>
			<div class="form-group">
				{_p var='fevent.text'}:
				<textarea class="form-control" rows="8" name="val[mass_email_text]" id="js_mass_email_text"></textarea>
			</div>		
			
			<div class="table_clear">
				<ul class="table_clear_button">
					<li><input type="button" value="{_p var='fevent.send'}" class="btn btn-sm btn-primary" onclick="$('#js_event_mass_mail_li').show(); $.ajaxCall('fevent.massEmail', 'type=message&amp;id={$aForms.event_id}&amp;subject=' + $('#js_mass_email_subject').val() + '&amp;text=' + $('#js_mass_email_text').val()); return false;" /></li>
					<li id="js_event_mass_mail_li" style="display:none;">{img theme='ajax/add.gif' class='v_middle'} <span id="js_event_mass_mail_send">Sending mass email...</span></li>
				</ul>
				<div class="clear"></div>
			</div>
		</div>
		<div id="js_send_email_fail"{if $bCanSendEmails} style="display:none;"{/if}>
			<div class="help-block">
				{_p var='fevent.you_are_unable_to_send_out_any_mass_emails_at_the_moment'}
				<br />
				{_p var='fevent.please_wait_till'}: <span id="js_time_left">{$iCanSendEmailsTime|date:'mail.mail_time_stamp'}</span>
			</div>			
		</div>
	</div>
	{/if}
</form>
<script type="text/javascript">
    {literal}
        $Behavior.loadCustomField = function() {
            $( document ).ready(function() {
                $('.js_mp_category_list').trigger('change');
            });
        };
    {/literal}

	function showrepeat(value)
    {l}
    	var txtrepeat=$('#txtrepeat').val();
    	var daterepeat=$('#daterepeat').val();

    	var daterepeat_hour = $('#daterepeat_hour').val();
    	var daterepeat_min = $('#daterepeat_min').val();
    	var daterepeat_dur_day = $('#daterepeat_dur_day').val();
    	var daterepeat_dur_hour = $('#daterepeat_dur_hour').val();

    	var check=$('#cbrepeat').attr('checked');
    	if(check)
		{l}
    		$('#extra_info_date').css('display','none');
    		$('#js_event_add_end_time').css('display','none');

    		$('#deleteAllAttendeesBox').css('display','none');

    		tb_show("{_p var='fevent.repeat'}",$.ajaxBox("fevent.repeat","height=300;width=430&value="+value
    				+"&txtrepeat="+txtrepeat
    				+"&daterepeat="+daterepeat 
    				+ "&daterepeat_hour=" + daterepeat_hour 
    				+ "&daterepeat_min=" + daterepeat_min 
    				+ "&daterepeat_dur_day=" + daterepeat_dur_day 
    				+ "&daterepeat_dur_hour=" + daterepeat_dur_hour
    				+ "&eventID=" + $('#eventID').val()
			));
		{r}
    	else
    	{l}
    		var bIsEdit=$('#bIsEdit').val();
    		if(!bIsEdit)
    			$('.extra_info').css('display','block');
    		else
    			$('#js_event_add_end_time').css('display','block');

    		$('#chooserepeat').html("");
    		$('#txtrepeat').val("-1");
    		$('#daterepeat').val("");

    		$('#daterepeat_hour').val("");
    		$('#daterepeat_min').val("");
    		$('#daterepeat_dur_day').val("");
    		$('#daterepeat_dur_hour').val("");

    		$('#editrepeat').html("");

			if ($('#js_event_add_end_time').css('display') == 'none') {l}
			    $('#extra_info_date').css('display','block');
			{r}    		
    		
    		$('#deleteAllAttendeesBox').css('display','none');

    		tb_remove();
    	{r}	
    {r}
</script>

<script type="text/javascript">
var loadMap = false;
{literal}  
 $Behavior.ynfeInitializeGoogleMapLocation = function() {
    if (loadMap === false) {
        loadMap = true; 
        $('#js_country_child_id_value').change(function(){
            debug("Cleaning  city, postal_code and address");
            $('#city').val('');
            $('#postal_code').val('');
            $('#address').val('');
        });
        $('#country_iso, #js_country_child_id_value').change(inputToMapAdvEvent);
        $('#location_venue, #address, #postal_code, #city').blur(inputToMapAdvEvent);
        fevent_loadScript('{/literal}{param var='core.google_api_key'}{literal}');
    }
  };
	function plugin_addFriendToSelectList(sId)
	{

		var ele = $('#js_friend_' + sId + ''),
			imgele = ele.parent().find('img'),
			spanele = ele.parent().find('.no_image_user');
		if(imgele.length){
			imgele.css('margin-right','5px');
			ele.prepend(imgele);
		}
		else{
			ele.css('display','flex').css('align-items','center');
			spanele.css('margin-right','5px');
			ele.prepend(spanele);
		}
		ele.css('max-width','110px');
	}
{/literal}
</script> 