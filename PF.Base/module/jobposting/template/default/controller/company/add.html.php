<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [YOUNET_COPPYRIGHT]
 * @author           AnNT
 * @package          Module_jobposting
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<!-- 1 Check Load Ajax Load More (find End Load Ajax Load More)-->
{if $iPage <= 1}
<div class="main_break">
{$sCreateJs}
<form method="post" action="{url link='current'}" name="js_jc_form" id="core_js_jobposting_company_form" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
    {if isset($iItem) && isset($sModule)}
    <div><input type="hidden" name="val[module_id]" value="{$sModule|htmlspecialchars}" /></div>
    <div><input type="hidden" name="val[item_id]" value="{$iItem|htmlspecialchars}" /></div>
    {/if}

    {if $bIsEdit}
    <div><input type="hidden" name="val[company_id]" value="{$aForms.company_id}" /></div>
    <div><input type="hidden" name="val[is_sponsor]" value="{$aForms.is_sponsor}" /></div>
    <div id="js_custom_privacy_input_holder">{module name='privacy.build' privacy_item_id=$aForms.company_id privacy_module_id='jobposting'}</div>
    <a id="js_yncjobposting_url" style="display: none;" href="{$sLink}"></a>
    {/if}

    <!--Company Information-->
    <div id="js_jobposting_company_block_info" class="js_jobposting_company_block page_section_menu_holder">
        <div class="form-group">
            <label for="name">{required}{phrase var='company_name'}:</label>
            <input type="text" name="val[name]" value="{value type='input' id='name'}" id="name" size="40" class="form-control" maxlength="255" />
        </div>

        {plugin call='jobposting.template_controller_company_add_textarea_start'}

        <div class="form-group">
            <label for="description">{required}{phrase var='description'}:</label>
            {editor id='description'}
        </div>

        <div class="form-group">
            <label for="location">{required}{phrase var='jobposting_headquarters_location'}:</label>
            <input type="text" name="val[location]" class="form-control" value="{value type='input' id='location'}" id="location" size="40" maxlength="255" />
            <div class="extra_info">
                {if !$bIsEdit}
                <a href="#" id="js_link_show_add" onclick="$(this).hide(); $('#js_mp_add_city').show(); $('#js_link_hide_add').show(); return false;">{phrase var='add_city_zip_country'}</a>
                <a href="#" id="js_link_hide_add" style="display: none;" onclick="$(this).hide(); $('#js_mp_add_city').hide(); $('#js_link_show_add').show(); return false;">{phrase var='hide_city_zip_country'}</a>
                {/if}
            </div>
        </div>

        <div id="js_mp_add_city" {if !$bIsEdit} style="display:none;"{/if} >

            <div class="form-group">
                <label for="city">{phrase var='city'}:</label>
                <input type="text" name="val[city]" class="form-control" value="{value type='input' id='city'}" id="city" size="25" maxlength="255" />
            </div>

            <div class="form-group">
                <label for="postal_code">{phrase var='zip_postal_code'}:</label>
                <input type="text" name="val[postal_code]" class="form-control" value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20" />
            </div>

            <div class="form-group">
                <label for="country_iso">{phrase var='country'}:</label>
                {select_location}
                {module name='core.country-child'}
            </div>
        </div>

        <div class="form-group">
            <input id="refresh_map" type="button" class="btn btn-sm btn-primary" value="{phrase var='refresh_map'}" onclick="ynjobposting_inputToMap();"/>

            <input type="hidden" name="val[gmap][latitude]" value="{value type='input' id='input_gmap_latitude'}" id="input_gmap_latitude" />
            <input type="hidden" name="val[gmap][longitude]" value="{value type='input' id='input_gmap_longitude'}" id="input_gmap_longitude" />
        </div>

        <div class="form-group clearfix">
            <div id="mapHolder" style="width: 100%; height: 400px"></div>
        </div>

        <div class="form-group">
            <label for="website">{phrase var='website'}:</label>
            <input type="text" name="val[website]" class="form-control" value="{value type='input' id='website'}" id="website" size="40" maxlength="255" />
        </div>

        <div class="form-group">
            <label for="size">{phrase var='company_size'}:</label>
            <div class="form-inline">
                <div class="form-group">
                    {phrase var='from'} &nbsp;
                    <input type="text" name="val[size_from]" class="form-control" value="{value type='input' id='size_from'}" id="size_from" size="5" maxlength="10" />
                </div>

                <div class="form-group">
                   &nbsp; {phrase var='to'} &nbsp;
                    <input type="text" name="val[size_to]" class="form-control" value="{value type='input' id='size_to'}" id="size_to" size="5" maxlength="10" />
                </div>
               &nbsp; {phrase var='employees'}
            </div>
        </div>

        <div class="table form-group-follow">
            <div class="table_left">
                <label for="industry">{required}{phrase var='industry'}:</label>
            </div>

            <div class="table_right">
                {$sIndustries}

            <div class="extra_info">{phrase var='you_can_add_up_to_3_industries'}</div>
            </div>
        </div>

        <div class="table form-group">
            <div class="table_left">
                <label for="contact">{phrase var='contact_information'}:</label>
            </div>

            <div class="form-group">
                <label for="contact_name">{required}{phrase var='name'}:</label>
                <input type="text" name="val[contact_name]" value="{value type='input' id='contact_name'}" id="contact_name" class="form-control" maxlength="255" />
            </div>

            <div class="form-group">
                <label for="contact_phone">{required}{phrase var='phone'}:</label>
                <input type="text" name="val[contact_phone]" value="{value type='input' id='contact_phone'}" id="contact_phone" class="form-control" maxlength="255" />
            </div>

            <div class="form-group">
                <label for="contact_email">{required}{phrase var='email'}:</label>
                <input type="text" name="val[contact_email]" value="{value type='input' id='contact_email'}" id="contact_email" class="form-control" maxlength="255" />
            </div>

            <div class="form-group">
                <label for="contact_fax">{phrase var='fax'}:</label>
                <input type="text" name="val[contact_fax]" value="{value type='input' id='contact_fax'}" id="contact_fax" class="form-control" maxlength="255" />
            </div>
        </div>


        {if count($aFields)}
            {foreach from=$aFields item=aField}
                {template file='jobposting.block.custom.form'}
            {/foreach}
        {/if}

        {if empty($sModule) && Phpfox::isModule('privacy')}
        <div class="table form-group-follow">
            <div class="table_left">
                <label>
                    {phrase var='company_privacy'}:
                </label>
            </div>
            <div class="table_right">
                {module name='privacy.form' privacy_name='privacy' privacy_info='jobposting.control_who_can_see_your_company_information' privacy_no_custom=true}
            </div>
        </div>
        {/if}

        <div style="display: none;"><input type="checkbox" name="val[sponsor]" id="js_jc_sponsor_checkbox" /></div>

        <div class="table_clear">
                {if $bIsEdit}
                    {if isset($aForms.post_status) && $aForms.post_status != 1}
                    <input type="submit" name="val[draft_update]" value="{phrase var='updates'}" class="btn btn-sm btn-success" onclick="this.form.action='{url link='jobposting.company.add' id=$aForms.company_id}'" />
                    <input type="submit" name="val[draft_publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_draft_publish_btn" />
                    {else}
                    <input type="submit" name="val[update]" value="{phrase var='updates'}" class="btn btn-sm btn-success" onclick="this.form.action='{url link='jobposting.company.add' id=$aForms.company_id}'" />
                        {if !empty($aForms.is_approved) && $aForms.is_sponsor != 1 && Phpfox::getUserParam('jobposting.can_sponsor_company')}
                        <input type="button" value="{phrase var='sponsor_company'}" class="js_jc_sponsor_btn btn btn-sm btn-default" /><span class="js_jc_add_loading"></span>
                        {/if}
                    {/if}
                {else}
                <input type="submit" name="val[publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_publish_btn" />
                <input type="submit" name="val[draft]" value="{phrase var='save_as_draft'}" class="btn btn-sm btn-default js_jc_draft_btn" />
                {/if}
            <div class="clear"></div>
        </div>
        {if Phpfox::getParam('core.display_required')}
        <div class="table_clear">
            {required} {phrase var='core.required_fields'}
        </div>
        {/if}
    </div>
    <!--//Company Information-->

    <!-- 1 End Load Ajax Load More -->
    {/if}

    {if $bIsEdit}

    <!-- 2 Check Load Ajax Load More-->
    {if $iPage <= 1}

    <!--Photos-->
    <div id="js_jobposting_company_block_photos" class="js_jobposting_company_block page_section_menu_holder" style="display:none;">
        {if ((isset($aCompany.user_id) && $aCompany.user_id == Phpfox::getUserId()) || $add_photo) }
            <div class="jobposting-module manage-photo">
                {if ((isset($aCompany.user_id) && $aCompany.user_id == Phpfox::getUserId()) || $delete_photo) }
                    {module name='jobposting.company.photo'}
                {/if}
            </div>
        {/if}
    </div>
    <!--//Photos-->

    <!--My Bought Packages-->
    <div id="js_jobposting_company_block_packages" class="js_jobposting_company_block page_section_menu_holder" style="display:none;">
        <input type="hidden" id="currency_jobposting" value="{$currency}"/>
        <div class="table form-group-follow">
            <div class="table_left">
                {phrase var='your_existing_packages'}
            </div>
            <div class="table_right table-responsive">
                <table class="table default_table" cellpadding="0" cellspacing="0" id="js_jc_bought_packages">
                    <tr>
                        <th align="left">{phrase var='package_name'}</th>
                        <th>{phrase var='fee'}</th>
                        <th>{phrase var='remaining_job_posts'}</th>
                        <th>{phrase var='valid_time'}</th>
                        <th>{phrase var='payment_status'}</th>
                    </tr>
                    {if isset($aForms.packages)}
                    {foreach from=$aForms.packages name=package item=aPackage}
                    <tr{if is_int($phpfox.iteration.package/2)} class="on"{/if}>
                        <td>{$aPackage.name}</td>
                        <td class="t_center">{$aPackage.fee_text}</td>
                        <td class="t_center">{if $aPackage.post_number==0}{phrase var='unlimited'}{else}{$aPackage.remaining_post}{/if}</td>
                        <td class="t_center">{$aPackage.expire_text}</td>
                        <td class="t_center">{$aPackage.status_text}</td>
                    </tr>
                    {foreachelse}
                    <tr>
                        <td colspan="5">
                            <div class="extra_info">{phrase var='no_package_found'}.</div>
                        </td>
                    </tr>
                    {/foreach}
                    {/if}
                </table>
            </div>
        </div>

        <div class="table form-group-follow">
            <div class="table_left">
               {phrase var='your_can_purchase_additional_packages'}
            </div>
            <div class="table_right">
                {if isset($aForms.tobuy_packages)}
                <ul class="jc_list" id="js_jc_tobuy_packages">
                    {foreach from=$aForms.tobuy_packages name=tbpackage item=aTBPackage}
                    <li><label><input type="checkbox" name="val[packages][]" value="{$aTBPackage.package_id}" id="js_jc_package_{$aTBPackage.package_id}" class="js_jc_package" fee_value="{$aTBPackage.fee}" />{$aTBPackage.name} - {$aTBPackage.fee_text} - {if $aTBPackage.post_number==0}{phrase var='unlimited'}{else}{phrase var='remaining'} {$aTBPackage.post_number} {phrase var='job_posts'}{/if} - {$aTBPackage.expire_text}</label></li>
                    {foreachelse}
                    <li><div class="extra_info">{phrase var='no_package_found'}.</div></li>
                    {/foreach}
                </ul>
                {/if}
            </div>
        </div>

        <div class="table_clear">

            <input type="button" value="{phrase var='pay_packages'}" class="btn btn-sm btn-default js_jc_pay_packages_btn" disabled="disabled" />
            <span class="js_jc_add_loading"></span>

            {if isset($aForms.post_status) && $aForms.post_status == 2}
            <input type="submit" name="val[draft_update]" value="{phrase var='updates'}" class="btn btn-sm btn-primary" onclick="this.form.action='{url link='jobposting.company.add.packages' id=$aForms.company_id}'" />
            <input type="submit" name="val[draft_publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_draft_publish_btn" />
            {else}

                {if !empty($aForms.is_approved) && $aForms.is_sponsor != 1 && Phpfox::getUserParam('jobposting.can_sponsor_company')}
                <input type="button" value="{phrase var='sponsor_company'}" class="btn btn-sm btn-default js_jc_sponsor_btn button_off" />
                {/if}
            {/if}
        </div>
    </div>
    <!--//My Bought Packages-->

    <!--Submission Form-->
    <div id="js_jobposting_company_block_form" class="js_jobposting_company_block page_section_menu_holder" style="display:none;">
        <div class="form-group">
            <label for="form_title">{required}{phrase var='form_title'}:</label>
            <input type="text" name="val[form_title]" value="{if isset($aForms.form_title)}{value type='input' id='form_title'}{else}{$aForms.name}{/if}" id="form_title" class="form-control" size="40" maxlength="255" />
            <div class="extra_info">{phrase var='enter_the_title_for_the_submission_form'}</div>
        </div>

        <div class="form-group">
            <label for="form_description">{phrase var='form_description'}:</label>
            <textarea name="val[form_description]" id="form_description" class="form-control" rows="5">{value type='textarea' id='form_description'}</textarea>
            <div class="extra_info">{phrase var='enter_the_description_for_the_form_this_will_appear_below_the_form_title'}</div>
        </div>

        <div class="form-group">
            <label for="company_logo">{phrase var='company_logo'}:</label>
            <div class="table_right">
                {if isset($aForms.logo_image)}
                <div id="js_jc_logo_holder" style="position: relative; width: 120px; margin: 0 0 2px 2px;" onmouseover="$('#js_jc_remove_button').show()" onmouseout="$('#js_jc_remove_button').hide()">
                    <div id="js_jc_remove_button" style="position: absolute; display: none;">
                        <a href="#" title="{phrase var='delete_this_image'}" onclick="if (confirm('{phrase var='are_you_sure' phpfox_squote=true}')) {l} $('#js_jc_logo_holder').remove(); $.ajaxCall('jobposting.deleteLogo', 'id={$aForms.company_id}'); {r} return false;">{img theme='misc/delete_hover.gif' alt=''}</a>
                    </div>
                    {$aForms.logo_image}
                </div>
                {/if}
                <input type="file" name="company_logo" id="company_logo" />
            </div>
        </div>

        <div class="form-group">
            <label for="">{phrase var='job_title'}:</label>
            <div class="checkbox">
                <label for="job_title_enable">
                    <input type="checkbox" name="val[job_title_enable]" id="job_title_enable" {if array_key_exists('job_title_enable', $aForms) && $aForms.job_title_enable!='0'}checked="checked"{/if} />
                    {phrase var='show_job_title'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="">{phrase var='candidate_name'}:</label>
            <div class="checkbox">
                <label for="candidate_name_enable">
                    <input type="checkbox" name="val[candidate_name_enable]" id="candidate_name_enable" {if array_key_exists('candidate_name_enable', $aForms) && $aForms.candidate_name_enable!='0'}checked="checked"{/if} />
                    {phrase var='enable_your_name_field'}
                </label>

                <label for="candidate_name_require">
                    <input type="checkbox" name="val[candidate_name_require]" id="candidate_name_require" {if array_key_exists('candidate_name_require', $aForms) && $aForms.candidate_name_require!='0'}checked="checked"{/if} />
                    {phrase var='required_field'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="">
                {phrase var='candidate_photo'}:
            </label>
            <div class="checkbox">
                <label for="candidate_photo_enable">
                    <input type="checkbox" name="val[candidate_photo_enable]" id="candidate_photo_enable" {if array_key_exists('candidate_photo_enable', $aForms) && $aForms.candidate_photo_enable!='0'}checked="checked"{/if} />
                    {phrase var='enable_your_photo_field'}
                </label>
            </div>

            <div class="checkbox">
                <label for="candidate_photo_require">
                    <input type="checkbox" name="val[candidate_photo_require]" id="candidate_photo_require" {if array_key_exists('candidate_photo_require', $aForms) && $aForms.candidate_photo_require!='0'}checked="checked"{/if} />
                    {phrase var='required_field'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="">
                {phrase var='candidate_email'}:
            </label>

            <div class="checkbox">
                <label for="candidate_email_enable">
                    <input type="checkbox" name="val[candidate_email_enable]" id="candidate_email_enable" {if array_key_exists('candidate_email_enable', $aForms) && $aForms.candidate_email_enable!='0'}checked="checked"{/if} />
                    {phrase var='enable_your_email_field'}
                </label>
            </div>
            <div class="checkbox">
                <label for="candidate_email_require">
                    <input type="checkbox" name="val[candidate_email_require]" id="candidate_email_require" {if array_key_exists('candidate_email_require', $aForms) && $aForms.candidate_email_require!='0'}checked="checked"{/if} />
                    {phrase var='required_field'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="">{phrase var='candidate_telephone'}:</label>
            <div class="checkbox">
                <label for="candidate_telephone_enable">
                    <input type="checkbox" name="val[candidate_telephone_enable]" id="candidate_telephone_enable" {if array_key_exists('candidate_telephone_enable', $aForms) && $aForms.candidate_telephone_enable!='0'}checked="checked"{/if} />
                    {phrase var='enable_your_telephone_field'}
                </label>
            </div>

            <div class="checkbox">
                <label for="candidate_telephone_require">
                    <input type="checkbox" name="val[candidate_telephone_require]" id="candidate_telephone_require" {if array_key_exists('candidate_telephone_require', $aForms) && $aForms.candidate_telephone_require!='0'}checked="checked"{/if} />
                    {phrase var='required_field'}
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="">
                {phrase var='resume'}:
            </label>
            <div class="checkbox">
                <label for="resume_enable">
                    <input type="checkbox" name="val[resume_enable]" id="resume_enable" {if !Phpfox::isModule('resume')}disabled{/if} {if array_key_exists('resume_enable', $aForms) && $aForms.resume_enable!='0'}checked="checked"{/if} />
                    {phrase var='allow_candidate_to_apply_this_job_by_using_his_her_resume_on_module_resume'}
                </label>
            </div>

            {if !Phpfox::isModule('resume')}
            <span class="extra_info">
                {phrase var='to_use_this_option_please_install_module_a_href_link_resume_a' link="https://phpfox.younetco.com/v4-resume.html"}
            </span>
            {/if}
        </div>

        <div class="form-group">
            <label for="">{phrase var='resume_format'}:</label>
            <span class="extra_info">
                {phrase var='ms_word_pdf_zip_size_maximum' size='500 kb'}
            </span>
        </div>

        <div id="js_custom_field_review_holder">{$sCustomField}</div>

        <div class="ynjp_addField clear">
            <a href="#" onclick="tb_show('{phrase var='add_field_question'}', $.ajaxBox('jobposting.controllerAddField', 'height=300&width=300&action=add&company_id={$aForms.company_id}')); return false;">{phrase var='add_field_question'}</a>
        </div>

        <div class="table_clear">
            {if isset($aForms.post_status) && $aForms.post_status == 2}
            <input type="submit" name="val[draft_update]" value="{phrase var='updates'}" class="btn btn-sm btn-primary" onclick="this.form.action='{url link='jobposting.company.add.form' id=$aForms.company_id}'" />
            <input type="submit" name="val[draft_publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_draft_publish_btn" />
            {else}
            <input type="submit" name="val[update]" value="{phrase var='updates'}" class="btn btn-sm btn-primary" onclick="this.form.action='{url link='jobposting.company.add.form' id=$aForms.company_id}'" />
                {if !empty($aForms.is_approved) && $aForms.is_sponsor != 1 && Phpfox::getUserParam('jobposting.can_sponsor_company')}
                <input type="button" value="{phrase var='sponsor_company'}" class="btn btn-sm btn-default js_jc_sponsor_btn" /><span class="js_jc_add_loading"></span>
                {/if}
            {/if}
        </div>
        {if Phpfox::getParam('core.display_required')}
        <div class="table_clear">
            {required} {phrase var='core.required_fields'}
        </div>
        {/if}
    </div>
    <!--//Submission Form-->

    <!--Manage Job Posted-->
    <div id="js_jobposting_company_block_jobs" class="js_jobposting_company_block page_section_menu_holder" style="display:none;">
        <div class="ynjp_manageJobPosted">
            <div class="clearfix">
                <div class="ynjp_search">
                    <div class="ynjp_searchForm">
                        <div class="form-group">
                            <label>{phrase var='job_title'}</label>
                            <input type="text" name="search_title" value="{value type='input' id='search_title'}" class="form-control" id="search_title" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>{phrase var='posted_from'}</label>
                            {select_date prefix='from_' class="form" id='_from' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true}
                        </div>
                        <div class="form-group">
                            <label>{phrase var='to'}</label>
                            {select_date prefix='to_' id='_to' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true}
                        </div>
                        <div class="form-group">
                            <label>{phrase var='status'}</label>
                            <select class="form-control" name="search_status">
                                <option value="all">{phrase var='all'}</option>
                                <option value="show"{if isset($aForms.search_status) && $aForms.search_status=='show'} selected="selected"{/if}>{phrase var='show'}</option>
                                <option value="hide"{if isset($aForms.search_status) && $aForms.search_status=='hide'} selected="selected"{/if}>{phrase var='hide'}</option>
                            </select>
                        </div>

                        <div class="buttons">
                            <input type="button" value="{phrase var='search'}" class="btn btn-sm btn-success" id="js_jc_search_jobs" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"> </div>

        <table class="table default_table" cellpadding="0" cellspacing="0" id="js_jc_job_posted" style="margin: 20px 0;">
            <tr>
                <th align="center">{phrase var='job_title'}</th>
                <th align="center">{phrase var='posted_date'}</th>
                <th align="center">{phrase var='expired_date'}</th>
                <th align="center">{phrase var='show'}</th>
                <th align="center">{phrase var='status'}</th>
                <th align="center">{phrase var='option'}</th>
            </tr>
    {else}
        <table id="page2" style="display: none">
    {/if}
    <!-- 2 End Load Ajax Load More-->
        {if !empty($aJobs)}
            {foreach from=$aJobs name=job item=aJob}
            <tr{if is_int($phpfox.iteration.job/2)} class="on"{/if} id="js_jp_job_{$aJob.job_id}">
                {template file='jobposting.block.company.posted-job-entry'}
            </tr>
            {/foreach}

        {if !PHPFOX_IS_AJAX}
        {pager}
        {/if}
        {else}
            {if !PHPFOX_IS_AJAX}
            <tr>
                <td colspan="6">
                    <div class="extra_info">{phrase var='no_job_found'}.</div>
                </td>
            </tr>
            {/if}
        {/if}
        </table>
        <!-- 4 Check Load Ajax Load More -->
        {if $iPage <= 1}
        <div class="table_clear">
            {if isset($aForms.post_status) && $aForms.post_status == 2}
            <input type="submit" name="val[draft_update]" value="{phrase var='updates'}" class="btn btn-sm btn-primary" onclick="this.form.action='{url link='jobposting.company.add.jobs' id=$aForms.company_id}'" />
            <input type="submit" name="val[draft_publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_draft_publish_btn" />
            {else}
                {if !empty($aForms.is_approved) && $aForms.is_sponsor != 1 && Phpfox::getUserParam('jobposting.can_sponsor_company')}
                <input type="button" value="{phrase var='sponsor_company'}" class="btn btn-sm btn-default js_jc_sponsor_btn" /><span class="js_jc_add_loading"></span>
                {/if}
            {/if}
        </div>
    </div>
    <!--//Manage Job Posted-->

    <!--Admins-->
    <div id="js_jobposting_company_block_admins" class="js_jobposting_company_block page_section_menu_holder" style="display:none;">
        <div class="go_left">
            <div id="js_custom_search_friend"></div>

        </div>
        <div>
            {if isset($aForms.admins)}
            <div id="js_custom_search_friend_placement">{if count($aForms.admins)}
                <div class="js_custom_search_friend_holder" style="clear:both;" >
                    <ul>
                    {foreach from=$aForms.admins item=aAdmin}
                        <li>
                            <a href="#" class="friend_search_remove" title="Remove" onclick="$(this).parents('li:first').remove(); return false;">{phrase var='pages.remove'}</a>
                            <div class="friend_search_image">
                                {img
                                user=$aAdmin
                                suffix='_120_square'
                                max_width='50'
                                max_height='50'} </div>

                            <span>{$aAdmin.full_name|clean}</span>
                            <div><input type="hidden" name="admins[]" value="{$aAdmin.user_id}" /></div>
                        </li>
                    {/foreach}
                    </ul>
                </div>
                {/if}</div>
            {/if}
        </div>
        <div class="clear"></div>

        <div class="table_clear">
            {if isset($aForms.post_status) && $aForms.post_status == 2}
            <input type="submit" name="val[draft_update]" value="{phrase var='updates'}" class="btn btn-sm btn-primary" onclick="this.form.action='{url link='jobposting.company.add.admins' id=$aForms.company_id}'" />
            <input type="submit" name="val[draft_publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_draft_publish_btn" />
            {else}
            <input type="submit" name="val[update]" value="{phrase var='updates'}" class="btn btn-sm btn-default" onclick="this.form.action='{url link='jobposting.company.add.admins' id=$aForms.company_id}'" />
            {/if}
        </div>

        <script type="text/javascript">
            $Behavior.ynjpSearchFriends = function(){l}
                $Core.searchFriends({l}
                    'id': '#js_custom_search_friend',
                    'placement': '#js_custom_search_friend_placement',
                    'width': '300px',
                    'max_search': 10,
                    'input_name': 'admins',
                    'default_value': '{phrase var='pages.search_friends_by_their_name'}'
                {r});
                $Core.searchFriendsInput.buildFriends = function($oObj){l}
                    var $selected_admins_ele = $('#js_custom_search_friend_placement input[name="admins[]"]');
                    var selected_admins = '';
                    if ($selected_admins_ele.length) {l}
                        $selected_admins_ele.each(function() {l}
                            selected_admins = selected_admins + ',$' + $(this).val() + '$';
                        {r})
                    {r}
                    $.ajaxCall('jobposting.getJobFriends', 'sAdmin=' + selected_admins, 'GET');
                {r};
            {r};
        </script>
    </div>
    <!--Manage Permission-->
    <div id="js_jobposting_company_block_permission" class="js_jobposting_company_block page_section_menu_holder" style="display:none;">

        {if isset($aForms.admins) && count($aForms.admins)}
        <ul class="ynjb_permission_admin">
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label for="">{phrase var='permission_target'}</label>
                    </div>
                    <div class="table_right">
                        <select id="admin_permission" class="form-control" name="val[permission][admin_permission]" >

                            {foreach from=$aForms.admins item=aAdmin key=iKey}
                                {if $iKey == 0}
                                    <option value="{$aAdmin.user_id}" selected="selected" > {$aAdmin.full_name|clean}</option>
                                {else}
                                    <option value="{$aAdmin.user_id}" > {$aAdmin.full_name|clean}</option>
                                {/if}
                            {/foreach}

                        </select>
                        <a id='change_owner' href="javascript:void();" >{phrase var='change_owner'}</a>
                    </div>
                </div>

            </li>
            <li>
                <div class="table form-group">
                     <div class="table_left">
                        <label>{phrase var='can_add_photos'}</label>
                     </div>
                    <div class="table_right">
                        <select id="add_photo" class="form-control" name="val[permission][add_photo]">
                                    <option value="1" {if $aAdminFirst.add_photo} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.add_photo} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label>{phrase var='can_delete_photos'}</label>
                    </div>
                    <div class="table_right">
                        <select id="delete_photo" class="form-control" name="val[permission][delete_photo]">
                                    <option value="1" {if $aAdminFirst.delete_photo} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.delete_photo} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                     <div class="table_left">
                        <label>{phrase var='can_buy_packages'}</label>
                     </div>
                    <div class="table_right">
                        <select id="buy_packages" class="form-control" name="val[permission][buy_packages]">
                                    <option value="1" {if $aAdminFirst.buy_packages} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.buy_packages} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                     <div class="table_left">
                        <label>{phrase var='can_edit_submission_form'}</label>
                     </div>
                    <div class="table_right">
                        <select id="edit_submission_form" class="form-control" name="val[permission][edit_submission_form]">
                                    <option value="1" {if $aAdminFirst.edit_submission_form} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.edit_submission_form} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label>{phrase var='can_add_job'}</label>
                    </div>
                    <div class="table_right">
                        <select id="add_job" class="form-control" name="val[permission][add_job]">
                                    <option value="1" {if $aAdminFirst.add_job} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.add_job} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label>{phrase var='can_edit_job'}</label>
                    </div>
                    <div class="table_right">
                        <select id="edit_job" class="form-control" name="val[permission][edit_job]">
                                    <option value="1" {if $aAdminFirst.edit_job} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.edit_job} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label>{phrase var='can_delete_job'}</label>
                    </div>
                    <div class="table_right">
                        <select id="delete_job" class="form-control" name="val[permission][delete_job]">
                                    <option value="1" {if $aAdminFirst.delete_job} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.delete_job} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label>{phrase var='can_view_application'}</label>
                    </div>
                    <div class="table_right">
                        <select id="view_application" class="form-control" name="val[permission][view_application]">
                                    <option value="1" {if $aAdminFirst.view_application} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.view_application} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>
            <li>
                <div class="table form-group">
                    <div class="table_left">
                        <label>{phrase var='can_download_resumes'}</label>
                    </div>
                    <div class="table_right">
                        <select id="download_resumes" class="form-control" name="val[permission][download_resumes]">
                                    <option value="1" {if $aAdminFirst.download_resumes} selected="selected" {/if}>{phrase var='yes'}</option>
                                    <option value="0" {if !$aAdminFirst.download_resumes} selected="selected" {/if}>{phrase var='no'}</option>
                        </select>
                    </div>
                </div></li>

        </ul>
        <div class="table_clear">
                {if isset($aForms.post_status) && $aForms.post_status == 2}
                <input type="submit" name="val[draft_update]" value="{phrase var='updates'}" class="btn btn-sm btn-success" onclick="this.form.action='{url link='jobposting.company.add.permission' id=$aForms.company_id}'" />
                <input type="submit" name="val[draft_publish]" value="{phrase var='publish'}" class="btn btn-sm btn-primary js_jc_draft_publish_btn" />
                {else}
                <input type="submit" name="val[update]" value="{phrase var='updates'}" class="btn btn-sm btn-success" onclick="this.form.action='{url link='jobposting.company.add.permission' id=$aForms.company_id}'" />
                {/if}
            </div>
        {else}
        {phrase var='your_company_does_not_have_admin'}
            <div class="public_message"   style="display: block;">
                {phrase var='your_company_does_not_have_admin'}</div>
        {/if}

    </div>
    <!--//Admins-->
    <div id="ynjb_loading" style="display: none" >
        <img src="{$core_path}module/jobposting/static/image/default/loading.gif" alt="">

    </div>
        <!--  4 End Load Ajax Load More -->
        {/if}
    {/if}
    <!-- If Edit here-->

    <!-- 5 Check Load Ajax Load More -->
    {if $iPage <= 1}
</form>


</div>
<script type="text/javascript">
{if isset($aCompany.company_id)}
$Behavior.changeOwner = function(){l}

    $('#change_owner').click(function(){l}
        $Core.jsConfirm(
            {l}message: "{phrase var='do_you_want_to_change_owner_company'}"{r},
            function(){l}
                $('#js_jobposting_company_block_admins').hide();
                $('#ynjb_loading').show();

                var admin_id = $('#admin_permission').val();
                $.ajaxCall('jobposting.changeOwner', 'id={$aCompany.company_id}&admin_id='+admin_id);
            {r},
            function(){l}{r}
        );
        return false;
    {r});
{r};

$Behavior.changeAdmin = function()
{l}
    $('#admin_permission').change(function(){l}

        $Core.ajax('jobposting.changeAdminCompany',
        {l}
            params:
            {l}
                id: {$aCompany.company_id},
                user_id: $('#admin_permission').val(),
            {r},
            type: 'POST',
            success: function(response)
            {l}
                var adminPermission = jQuery.parseJSON(response);
                $("#add_photo option[value='"+adminPermission.add_photo+"']").attr("selected","selected");
                $("#delete_photo option[value='"+adminPermission.delete_photo+"']").attr("selected","selected");
                $("#buy_packages option[value='"+adminPermission.buy_packages+"']").attr("selected","selected");
                $("#edit_submission_form option[value='"+adminPermission.edit_submission_form+"']").attr("selected","selected");
                $("#add_job option[value='"+adminPermission.add_job+"']").attr("selected","selected");
                $("#edit_job option[value='"+adminPermission.edit_job+"']").attr("selected","selected");
                $("#delete_job option[value='"+adminPermission.delete_job+"']").attr("selected","selected");
                $("#view_application option[value='"+adminPermission.view_application+"']").attr("selected","selected");
                $("#download_resumes option[value='"+adminPermission.download_resumes+"']").attr("selected","selected");
            {r}
        {r});

        return false;

    {r});

{r};
{/if}

$Behavior.pageSectionMenuRequest = function(){l}
    $Core.pageSectionMenuShow('#js_jobposting_company_block_{$sNewReq}');
    if ($('#page_section_menu_form').length > 0){l}
        $('#page_section_menu_form').val('js_jobposting_company_block_{$sNewReq}');
    {r}
{r};
{if $bCanSponsorPublishedCompany}
$Behavior.ynjpConfirmSponsor = function(){l}

    $('.js_jc_draft_btn').on('click', function()
    {l}
        $(this).prop('disabled',true);
        $('#core_js_jobposting_company_form').submit();
    {r});

    $('.js_jc_publish_btn').on('click', function()
    {l}
        $(this).prop('disabled',true);
        return ynjobposting.company.popupSponsor('{$bCanSponsorPublishedCompany}', '{$iSponsorFee}','1');
    {r});
{r};
{else}
$Behavior.ynjpConfirmSponsor = function(){l}

    $('.js_jc_publish_btn').on('click', function()
    {l}
        $(this).prop('disabled',true);
        $('#core_js_jobposting_company_form').submit();
    {r});

    $('.js_jc_draft_btn').on('click', function()
    {l}
        $(this).prop('disabled',true);
        $('#core_js_jobposting_company_form').submit();
    {r});
{r};
{/if}
{if $bIsEdit}
$Behavior.initPayPackagesBtn = function(){l}
    ynjobposting.company.updatePayPackagesBtn();
{r};

$Behavior.ynjpHandleEvent = function(){l}

    $('.js_jc_draft_publish_btn').on('click', function(){l}
        this.form.action = '{url link='jobposting.company.add' id=$aForms.company_id}';
        return ynjobposting.company.popupSponsor('{$bCanSponsorPublishedCompany}', '{$iSponsorFee}','1');
    {r});

    $('#core_js_jobposting_company_form').on('submit', function(){l}
        return ynjobposting.company.submitForm();
    {r});

    $('.js_jc_sponsor_btn').on('click', function(){l}
        ynjobposting.company.popupSponsor({$aForms.company_id}, '{$iSponsorFee}','0');
    {r});

    $('.js_jc_package').on('click', function(){l}
        ynjobposting.company.updatePayPackagesBtn();
    {r});

    $('.js_jc_pay_packages_btn').on('click', function(){l}
        ynjobposting.company.payPackages({$aForms.company_id});
    {r});

    $('#js_jc_search_jobs').on('click',function(){l}
        ynjobposting.company.searchJobs('{url link='jobposting.company.add.jobs' id=$aForms.company_id}');
    {r});

    $('[rel=js_jobposting_company_block_info]').click(function(){l}
       $Core.loadInit();
    {r});
{r};

$Behavior.clickInitJob = function(){l}
    $('[rel=js_jobposting_company_block_jobs]').click(function(){l}
        var url=$('#js_yncjobposting_url').attr('href');
        window.location = url;
    {r});
{r};

{/if}
</script>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?v=3.exp&key={param var='core.google_api_key'}&libraries=places"></script>
{/if}
<!-- 5 End Load Ajax Load More -->

{literal}
<script type="text/javascript">
    $Behavior.ynjobpostingLoadMoreManageJobPosted = function () {
        if ($('#page2').length > 0 && $('#page2 tbody').length > 0 && $('#js_jc_job_posted tbody').length > 0)
        {
            $('#js_jc_job_posted tbody').append($('#page2 tbody').html());
            $('#page2').remove();
        }
    }
</script>
{/literal}
