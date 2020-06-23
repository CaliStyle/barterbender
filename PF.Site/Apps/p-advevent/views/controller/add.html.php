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
{if !PHPFOX_IS_AJAX}
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key={$apiKey}&libraries=places"></script>
{$sCreateJs}
<input type="hidden" id="required_custom_fields"/>
<input type="hidden" id="category_event_id" value="{if $bIsEdit}{$aForms.event_id}{else}0{/if}"/>

{if $isCreating}
    <div class="p-step-nav-container mb-1">
        <div class="p-step-nav-button js_p_step_nav_button">
            <div class="nav-prev dont-unbind">
                <i class="ico ico-angle-left"></i>
            </div>
            <div class="nav-next dont-unbind">
                <i class="ico ico-angle-right"></i>
            </div>
        </div>
        <div class="p-step-nav-outer js_p_step_nav_outer_scroll">
            <ul class="p-step-nav">
                {foreach from=$aPageStepMenu key=stepKey item=stepMenu}
                    <li class="p-step-item{if $stepMenu.finished} finished{/if}{if $stepKey == $sActiveTab} active{/if}{if !$stepMenu.enabled} disabled{/if}">
                        {if $stepMenu.enabled}
                            <a href="#{$stepKey}" class="p-step-link" rel="{$sPageStepMenuName}_{$stepKey}">
                                <span class="item-title">{$stepMenu.title}</span>
                                <span class="item-icon"><span class="item-icon-bg"><i class="ico ico-check"></i></span></span>
                            </a>
                        {else}
                            <a href="javascript:void(0);" class="p-step-link">
                                <span class="item-title">{$stepMenu.title}</span>
                                <span class="item-icon"><span class="item-icon-bg"><i class="ico ico-check"></i></span></span>
                            </a>
                        {/if}
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}

<form method="post" action="{url link='current'}" enctype="multipart/form-data" {if $bIsEdit && $aForms.event_type == 'repeat'}onsubmit="return custom_js_event_form(this);"{/if} id="js_event_form" class="p-fevent-container-add-page">
    {if $isCreating}
        <input type="hidden" name="creating" value="1">
    {/if}
    <input type="hidden" value="{$valToken}" name="core[security_token]">
    <input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}"/>
    <input type="hidden" name="val[current_tab]" value="{if !empty($sAction)}{$sAction}{/if}" id="current_tab">
    <input type="hidden" id="bIsEdit" value="{$bIsEdit}"/>
    <input type="hidden" id="eventID" value="{if $bIsEdit}{$aEvent.event_id}{else}-1{/if}"/>

    {if !empty($sModule)}
        <input type="hidden" name="module" value="{$sModule|htmlspecialchars}"/>
    {/if}
    {if !empty($iItem)}
        <input type="hidden" name="item" value="{$iItem|htmlspecialchars}"/>
    {/if}

    {if $bIsEdit}
        <input type="hidden" name="id" value="{if isset($aForms.event_id)}{$aForms.event_id}{else}0{/if}"/>

        {if !$isCreating && $aForms.event_type == 'repeat'}
            {template file='fevent.block.applyforrepeatevent'}
        {else}
            <input type="hidden" id="ynfevent_editconfirmboxoption_value"
                name="val[ynfevent_editconfirmboxoption_value]" value="only_this_event"/>
        {/if}
    {/if}
    <div id="js_event_block_detail" class="js_event_block page_section_menu_holder "
         {if !empty($sActiveTab) && $sActiveTab != 'detail'}style="display:none;"{/if}>
        <div class="form-group">
            <label for="title">{_p var='fevent.what_are_you_planning'}</label> <span class="p-text-danger">{required}</span></label>
            <input type="text" name="val[title]" value="{value type='input' id='title'}" id="title"
                   class="form-control" maxlength="100" placeholder="{_p var='event_title'}" {if !$bIsEdit}required{/if}>
            <p class="help-block">
                {_p var='maximum_number_characters' number=100}
            </p>
        </div>

        <div class="form-group js_core_init_selectize_form_group">
            <label for="category">{_p var='category'}</label>
            <select class="form-control" name="val[category]" id="p-fevent-categories">
                <option value="">{_p var='select'}:</option>
                {foreach from=$categories item=category}
                    <option value="{$category.category_id}" {value type='select' id='category' default=$category.category_id}>{$category.name}</option>
                {/foreach}
            </select>
        </div>

        <div id="ajax_custom_fields" class="ync-customfield">
            {if $bIsEdit && isset($aCustomFields)}
                {module name="fevent.custom" aCustomFields=$aCustomFields}
            {/if}
        </div>

        {if empty($aForms.event_id)}
            {module name='core.upload-form' type='fevent_default'}
        {/if}

        <div class="form-group">
            <label for="description">{_p var='fevent.description'}</label>
            {editor id='description' rows='6'}
        </div>

        <div class="form-group">
            <div class="checkbox p-checkbox-custom">
                <label class="fw-bold p-text-capitalize">
                    <input value="1" type="checkbox" name="val[has_ticket]" id="p_fevent_has_ticket"
                            {value type='checkbox' id='has_ticket' default='1'}>
                    <i class="ico ico-square-o mr-1"></i> {_p var='ticket'}
                </label>
            </div>
        </div>

        <div class="form-group" id="ticket_info_group">
            <select name="val[ticket_type]" id="ticket_type" class="form-control">
                <option value="free"{value type='select' id='ticket_type' default='free'}>{_p var='free'}</option>
                <option value="paid"{value type='select' id='ticket_type' default='paid'}>{_p var='paid'}</option>
            </select>
            <input type="text" name="val[ticket_price]" value="{value type='input' id='ticket_price'}"
                   id="ticket_price" class="form-control" placeholder="{_p var='price_info'} ({_p var='max_number_characters' number=50})" maxlength="50">
            <input type="text" name="val[ticket_url]" value="{value type='input' id='ticket_url'}"
                   class="form-control" placeholder="{_p var='link_to_ticket_info'}">
        </div>
        <div class="p-fevent-one-time-section">
        <div class="form-group">
            <label>{_p var='fevent.start_time'}</label>
            <div class="p-fevent-date-wrapper">
                {select_date prefix='start_' id='_start' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
                <div class="p-picktim-form-group dont-unbind-children" id="start_time">
                    {if $bIsEdit && $aForms.isrepeat > -1 && !Phpfox::getParam('fevent.allow_change_time_recurrent_event')}
                        <input class="form-control" name="val[start_time]" type="text" readonly="readonly" autocomplete="off" value="{$aForms.start_time}">
                    {/if}
                </div>
            </div>
        </div>

        <div class="form-group" id="js_event_add_end_time">
            <label for="">{_p var='fevent.end_time'}</label>
            <div class="p-fevent-date-wrapper">
                {select_date prefix='end_' id='_end' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
                <div class="p-picktim-form-group dont-unbind-children" id="end_time">
                    {if $bIsEdit && $aForms.isrepeat > -1 && !Phpfox::getParam('fevent.allow_change_time_recurrent_event')}
                        <input class="form-control" name="val[end_time]" type="text" readonly="readonly" autocomplete="off" value="{$aForms.end_time}">
                    {/if}
                </div>
            </div>
        </div>

        {if $bIsEdit && $aForms.event_type == 'repeat'}
            <input type="hidden" name="val[isrepeat]" value="{$aForms.isrepeat}">
        {else}
            <div class="form-group">
                <label for="description">{_p var='fevent.repeat'}:</label>
                <select name="val[isrepeat]" id="p_fevent_repeat_select" class="form-control w-auto">
                    <option value="-1" {value type='select' id='isrepeat' default='-1'}>
                        {_p var='no_repeat'}
                    </option>
                    <option value="0" {value type='select' id='isrepeat' default='0'}>
                        {_p var='daily'}
                    </option>
                    <option value="1" {value type='select' id='isrepeat' default='1'}>
                        {_p var='weekly'}
                    </option>
                    <option value="2" {value type='select' id='isrepeat' default='2'}>
                        {_p var='monthly'}
                    </option>
                </select>

            </div>
            <div id="p_event_end_repeat" class="form-inline" style="display: none;">
                <label>{_p var='fevent.end_repeat'}</label>
                <div class="row ml--1 mr--1">
                    <div class="form-group w-auto pl-1 pr-1 pull-left col-xs-12">
                        <label>
                            <input type="radio" name="val[repeat_section_end_repeat]"
                                   {if isset($aForms.repeat_section_end_repeat) == false || (isset($aForms.repeat_section_end_repeat) && $aForms.repeat_section_end_repeat == 'after_number_event')}checked="checked"{/if}
                                   value="after_number_event">
                            {_p var='fevent.after'}
                        </label>
                        <input type="text" class="form-control d-block w-full"
                               name="val[repeat_section_after_number_event]"
                               value="{if isset($aForms.repeat_section_after_number_event)}{$aForms.repeat_section_after_number_event}{/if}"
                               id="ynfevent_after_number_event" size="5" maxlength="5"/>
                        <p class="help-block">
                            {_p var='fevent.event_s'} ({_p var='fevent.allow_maximum'} {$fevent_max_instance_repeat_event} {_p var='fevent.event_s'})
                        </p>
                    </div>
                    <div class="form-group w-auto pl-1 pr-1 col-xs-12">
                        <label>
                            <input type="radio" name="val[repeat_section_end_repeat]"
                                   {if isset($aForms.repeat_section_end_repeat) && $aForms.repeat_section_end_repeat == 'repeat_until'}checked="checked"{/if}
                                   value="repeat_until">
                            {_p var='fevent.at_uppercase'}
                        </label>
                        {select_date prefix='repeat_section_repeatuntil_' id='_repeatuntil' start_year='2010' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true time_separator='fevent.time_separator'}
                    </div>
                </div>
            </div>
        {/if}
        </div>
        <div class="form-group">
            <p class="help-block">{_p var='fevent.changes_to_an_event_s_start_end_date_or_time_will_require_guests'}</p>
        </div>

        <div class="form-group">
            <label for="location">{_p var='location_venue'}</label> <span class="p-text-danger">{required}</span>
            {location_input}
        </div>

        {if $bCanAddMap}
            <div class="form-group" style="display: none;">
                <input id="refresh_map" type="button" class="mb-1 btn btn-sm btn-primary"
                       value="{_p var='fevent.refresh_map'}" onclick="inputToMapAdvEvent();"/>
                <input type="hidden" name="val[gmap][latitude]"
                       value="{value type='input' id='input_gmap_latitude'}" id="input_gmap_latitude"/>
                <input type="hidden" name="val[gmap][longitude]"
                       value="{value type='input' id='input_gmap_longitude'}" id="input_gmap_longitude"/>
                <div id="mapHolder" class="mb-2 mt-1"></div>
            </div>
        {/if}

        <div class="form-group">
            <div class="checkbox p-checkbox-custom">
                <label class="fw-bold">
                    <input value="1" type="checkbox" name="val[has_notification]" id="p_fevent_has_notification"
                            {value type='checkbox' id='has_notification' default='1'}>
                    <i class="ico ico-square-o mr-1"></i> {_p var='notification_reminder'}
                </label>
            </div>
            <p class="help-block">
                {_p var='if_user_set_reminder_send_notification_for_users_who_maybe_attending_attending_to_this_event'}
            </p>
        </div>

        <div id="p_fevent_notification_value" style="display: none">
            <div class="form-group">
                <input type="number" class="form-control" name="val[notification_value]" value="{value type='input' id='notification_value'}" min="0">
                <select name="val[notification_type]" id="notification_type" class="form-control">
                    <option value="minute" {value type='select' id='notification_type' default='minute'}>{_p var='minutes'}</option>
                    <option value="hour" {value type='select' id='notification_type' default='hour'}>{_p var='hours'}</option>
                    <option value="day" {value type='select' id='notification_type' default='day'}>{_p var='days'}</option>
                </select>
            </div>
        </div>

        {if empty($sModule) && Phpfox::isModule('privacy')}
            <div class="form-group">
                <label class="">{_p var='privacy'}:</label>
                {module name='privacy.form' privacy_name='privacy' privacy_no_custom=true default_privacy='fevent.display_on_profile'}
            </div>
        {/if}

        {if $isCreating}
            <div class="p-step-groupaction-container">
                <div class="p-step-groupaction-outer">
                    <div class="item-button-action-container">
                        {if $bIsEdit}
                            <input type="submit" name="val[update_detail]" value="{_p var='update'}" class="btn btn-primary pull-left">
                        {else}
                            <input type="submit" name="val[submit_detail]" value="{_p var='create_event'}" class="btn btn-primary pull-left">
                        {/if}
                    </div>
                    <div class="item-button-step-container">
                        <div class="item-action">
                            {if !$bIsEdit}
                                <a href="{url link='fevent'}" class="item-action-link">
                                    {_p var='cancel'}
                                </a>
                            {/if}
                        </div>
                        <div class="item-step">
                            <span class="item-step-number">1/3</span>
                            {if $bIsEdit}
                                <button class="btn item-nav-button" onclick="ynfeAddPage.switchStep('js_event_block_customize');return false;">
                                    <i class="ico ico-angle-right"></i>
                                </button>
                            {else}
                                <button class="btn item-nav-button disabled">
                                    <i class="ico ico-angle-right"></i>
                                </button>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        {else}
            <div class="p-form-group-btn-container has-top-border">
                <input type="submit" name="val[update_detail]" value="{_p var='fevent.update'}" class="btn btn-primary">
            </div>
        {/if}
    </div>

    <div id="js_event_block_customize" class="js_event_block page_section_menu_holder "
        {if empty($sActiveTab) || $sActiveTab != 'customize'}style="display:none;"{/if}>

        <div id="js-p-fevent-photos-container">
            {module name='fevent.photo'}
        </div>

        {if $isCreating}
            <div class="p-step-groupaction-container">
                <div class="p-step-groupaction-outer">
                    <div class="item-button-action-container">
                        <div id="p_fevent_back_to_manage_container" style="display: none;">
                            <a href="javascript:void(0);" class="btn btn-default" id="p_fevent_back_to_manage"
                               onclick="ynfeAddPage.toggleUploadSection({$aForms.event_id}, 0, 1);">
                                {_p var='back_to_manage'}
                            </a>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary" id="p_fevent_confirm_photo">
                            {_p var='next'}
                        </a>
                    </div>
                    <div class="item-button-step-container">
                        <div class="item-action">
                            <a href="{permalink module='fevent' id=$aForms.event_id title=$aForms.title}"
                               class="item-action-link">{_p var='fevent.skip_all'}</a>
                        </div>
                        <div class="item-step">
                            <button onclick="ynfeAddPage.switchStep('js_event_block_detail');return false;"
                                    class="btn item-nav-button"><i class="ico ico-angle-left"></i></button>
                            <span class="item-step-number">2/3</span>
                            <button onclick="ynfeAddPage.switchStep('js_event_block_invite');return false;"
                                    class="btn item-nav-button"><i class="ico ico-angle-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
    <!--Admins : START -->
    {if $bIsEdit}
        <div id="js_event_block_admins" class="js_event_block page_section_menu_holder "
             {if empty($sActiveTab) || $sActiveTab != 'admins'}style="display:none;"{/if}>
            <div class="form-group">
                {module name='friend.search-small' input_name='admins' current_values=$aForms.admins}
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='update'}" class="btn btn-primary"/>
            </div>
        </div>
    {/if}
    <!--Admins : END -->
    <div id="js_event_block_invite" class="js_event_block page_section_menu_holder "
         {if empty($sActiveTab) || $sActiveTab != 'invite'}style="display:none;"{/if}>
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
                <input name="val[emails]" id="emails" class="form-control" data-component="tokenfield"
                       data-type="email">
                <p class="help-block">{_p var='separate_multiple_emails_with_a_comma'}</p>
            </div>
            <div class="form-group">
                <label for="personal_message">{_p var='add_a_personal_message'}</label>
                <textarea rows="1" name="val[personal_message]" id="personal_message"
                          class="form-control textarea-auto-scale"
                          placeholder="{_p var='write_message'}"></textarea>
            </div>
            {if !$isCreating}
            <div class="form-group">
                <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary" name="invite_submit">
            </div>
            {/if}
        </div>

        {if $isCreating}
            <div class="p-step-groupaction-container">
                <div class="p-step-groupaction-outer">
                    <div class="item-button-action-container">
                        <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary" name="invite_submit">
                    </div>
                    <div class="item-button-step-container">
                        <div class="item-action">
                            {if $aPageStepMenu.invite.finished}
                                <a href="{permalink module='fevent' id=$aForms.event_id title=$aForms.title}" class="btn btn-success btn-icon p-text-capitalize">
                                    <i class="ico ico-check"></i> {_p var='finish'}
                                </a>
                            {else}
                                <a href="javascript:void(0);" class="btn btn-success btn-icon p-text-capitalize" id="p-fevent-confirm-invite">
                                    <i class="ico ico-check"></i> {_p var='finish'}
                                </a>
                            {/if}
                        </div>
                        <div class="item-step">
                            <button onclick="ynfeAddPage.switchStep('js_event_block_customize');return false;"
                                    class="btn item-nav-button"><i class="ico ico-angle-left"></i></button>
                            <span class="item-step-number">3/3</span>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/if}

    {if $bIsEdit}
        {if !PHPFOX_IS_AJAX}
        <div id="js_event_block_manage" class="js_event_block page_section_menu_holder "
             {if empty($sActiveTab) || $sActiveTab != 'manage'}style="display:none;"{/if}>
        {/if}
            {module name='fevent.list'}
        {if !PHPFOX_IS_AJAX}
        </div>
        {/if}
    {/if}
{if !PHPFOX_IS_AJAX}
    {if $bIsEdit && Phpfox::getUserParam('fevent.can_mass_mail_own_members')}
        <div id="js_event_block_email" class="js_event_block page_section_menu_holder "
             {if empty($sActiveTab) || $sActiveTab != 'email'}style="display:none;"{/if}>
            <div id="js_send_email"{if !$bCanSendEmails} style="display:none;"{/if}>
                <div class="help-block">
                    {_p var='fevent.send_out_an_email_to_all_the_guests_that_are_joining_this_event'}
                    {if isset($aForms.mass_email) && $aForms.mass_email}
                        <br/>
                        {_p var='fevent.last_mass_email'}: {$aForms.mass_email|date:'core.global_update_time'}
                    {/if}
                </div>

                <div class="form-group">
                    <label class="">
                        {_p var='fevent.subject'}:
                    </label>
                    <input type="text" name="val[mass_email_subject]" value="" class="form-control"
                           size="30" id="js_mass_email_subject">
                </div>
                <div class="form-group">
                    {_p var='fevent.text'}:
                    <textarea class="form-control" rows="8" name="val[mass_email_text]" id="js_mass_email_text"></textarea>
                </div>

                <div class="table_clear">
                    <ul class="table_clear_button">
                        <li><input type="button" value="{_p var='fevent.send'}" class="btn btn-sm btn-primary"
                                   onclick="$('#js_event_mass_mail_li').show(); $.ajaxCall('fevent.massEmail', 'type=message&amp;id={$aForms.event_id}&amp;subject=' + $('#js_mass_email_subject').val() + '&amp;text=' + $('#js_mass_email_text').val()); return false;"/>
                        </li>
                        <li id="js_event_mass_mail_li" style="display:none;">
                            {img theme='ajax/add.gif' class='v_middle'}
                            <span id="js_event_mass_mail_send">Sending mass email...</span>
                        </li>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="js_send_email_fail"{if $bCanSendEmails} style="display:none;"{/if}>
                <div class="help-block">
                    {_p var='fevent.you_are_unable_to_send_out_any_mass_emails_at_the_moment'}
                    <br/>
                    {_p var='fevent.please_wait_till'}: <span
                            id="js_time_left">{$iCanSendEmailsTime|date:'core.global_update_time'}</span>
                </div>
            </div>
        </div>
    {/if}
</form>
<script type="text/javascript">
    var photoConfirmLink = "{url link='fevent.add' id=$aForms.event_id tab='invite' creating=1}";
    var inviteConfirmLink = "{permalink module='fevent' id=$aForms.event_id title=$aForms.title}";
    oTranslations['it_looks_like_you_havent_upload_any_photo_yet'] = "{_p var='it_looks_like_you_havent_upload_any_photo_yet'}";
    oTranslations['fevent_it_looks_like_you_havent_invited_any_people_yet'] = "{_p var='fevent_it_looks_like_you_havent_invited_any_people_yet'}";
    oTranslations['finish_photo_uploading'] = "{_p var='fevent.finish_photo_uploading'}";
    oTranslations['next'] = "{_p var='next'}";
    var currentEditFEventTab = "{$currentTab}";
    var loadMap = false;
    var start_time = '{$aForms.start_time}';
    var end_time = '{$aForms.end_time}';

    {literal}
    $Behavior.addNewAdvEvent = function () {
        var initTimePicker = {/literal}{if $bIsEdit && $aForms.isrepeat > -1 && !Phpfox::getParam('fevent.allow_change_time_recurrent_event')}0{else}1{/if}{literal};
        ynfeAddPage.init(initTimePicker);
    };

    $Behavior.ynfeInitializeGoogleMapLocation = function () {
        if (loadMap === false) {
            loadMap = true;
            fevent_loadScript('{/literal}{param var='core.google_api_key'}{literal}');
        }
        $('#js_country_child_id_value').change(function () {
            $('#city').val('');
            $('#postal_code').val('');
            $('#address').val('');
        });
        $('#country_iso, #js_country_child_id_value').change(inputToMapAdvEvent);
        $('#location_venue, #address, #postal_code, #city').blur(inputToMapAdvEvent);
    };
    {/literal}
</script>
{/if}

{if $bIsEdit && $aForms.isrepeat > -1}
{literal}
    <script type="text/javascript">
        ;$Behavior.initWithEdtingRepeatEvent = function()
        {
            setTimeout(function(){
                        {/literal}{if !Phpfox::getParam('fevent.allow_change_date_recurrent_event')}{literal}
                console.log('remove');
                    $('.js_datepicker_image').remove();
                    $('.js_datepicker_core_start').find("*").off();
                    $('.js_datepicker_core_end').find("*").off();
                    $('.js_date_picker.hasDatepicker').attr('readonly', true);
                        {/literal}{/if}{literal}
                }
                , 1000);
        };
    </script>
{/literal}
{/if}

