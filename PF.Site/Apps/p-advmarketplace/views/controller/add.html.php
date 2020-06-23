<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($iPage)}
{if $bIsEdit}
{literal}
<script type="text/javascript">
    $Behavior.countryLoad = function () {
        $("#country_iso option[value={/literal}{$aForms.country_iso}{literal}]").prop("selected", true);
    }
</script>
{/literal}
{/if}
{if $bIsEdit && $aForms.view_id == '2'}
    <div class="error_message">
        {phrase var='advancedmarketplace.notice_this_listing_is_marked_as_sold'}
    </div>
    <div class="main_break"></div>
{/if}

{$sCreateJs}
{if $isCreating}
    <div class="p-step-nav-container mb-3">
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
<form method="post" action="{url link='current'}" enctype="multipart/form-data"
      onsubmit="return startProcess({$sGetJsForm}, false);" id="js_advancedmarketplace_form"
      class="p-advmarketplace-container-create">
    {if $isCreating}
        <input type="hidden" name="creating" value="1">
    {/if}
    {if $bIsEdit}
        <div><input type="hidden" name="id" id="ilistingid" value="{$aForms.listing_id}"/></div>
        <input type="hidden" name="type" value="advancedmarketplace">
    {/if}

    <div id="js_custom_privacy_input_holder">
        {if $bIsEdit && empty($sModule) && Phpfox::isModule('privacy')}
            {module name='privacy.build' privacy_item_id=$aForms.listing_id privacy_module_id='advancedmarketplace'}
        {/if}
    </div>

    <div><input type="hidden" name="page_section_menu" value="js_mp_block_{$sActiveTab}" id="page_section_menu_form"/>
    </div>

    <div id="js_mp_block_detail" class="js_mp_block page_section_menu_holder"
         {if !empty($sActiveTab) && $sActiveTab != 'detail'}style="display:none;"{/if}>

        {if empty($sModule) && !empty($aPages) && !$bIsEdit}
            <div class="table form-group">
                <div class="table_left">{_p var='pages'}</div>
                <div class="table_right">
                    <select class="form-control" name="val[page_id]">
                        <option value="0">{_p var='none'}</option>
                        {foreach from=$aPages name=pages item=aPage}
                            <option value="{$aPage.page_id}">{$aPage.title}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}

        <div class="form-group">
            <label for="title">
                {phrase var='advancedmarketplace.what_are_you_selling'}
                <span class="p-text-danger">{required}</span></label>
            <input type="text" class="form-control close_warning" name="val[title]" value="{value type='input' id='title'}" id="title"
                   size="40" maxlength="100" required>
        </div>

        {if empty($aForms.listing_id)}
            {module name='core.upload-form' type='advancedmarketplace_default' }
        {/if}
        <div class="form-group">
            <label for="price">
                {phrase var='advancedmarketplace.price'}
            </label>
            <div class="input-group input-group-dropdown">
                <input type="number" class="form-control w-auto close_warning p-advmarketplace-input-price" name="val[price]"
                       value="{value type='input' id='price'}" placeholder="0.00"
                       id="price" size="10" maxlength="100" min="0" step="0.001" onfocus="this.select();"/>
                <div class="input-group-btn dropdown">
                    <select class="w-auto btn dropdown-toggle" name="val[currency_id]">
                        {foreach from=$aCurrencies key=sCurrency item=aCurrency}
                            <option value="{$sCurrency}"{if $bIsEdit} {value type='select' id='currency_id' default=$sCurrency}{else}{if $aCurrency.is_default} selected="selected"{/if}{/if}>{$sCurrency}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="help-block mt-1">
                {_p var='advancedmarketplace_leave_this_section_blank_if_your_product_is_free_to_buy'}
            </div>
        </div>


        {if Phpfox::getUserParam('advancedmarketplace.can_sell_items_on_advancedmarketplace')}
            <div class="form-group">
                <div class="checkbox p-checkbox-custom">
                    <label>
                        <input id="advancedmarketplace_is_sell" value="1" type="checkbox" class="close_warning"
                               name="val[is_sell]" {value type='checkbox' id='is_sell' default='1'}>
                        <i class="ico ico-square-o mr-1"></i>
                        <div>
                            {_p var='instant_payment'}
                            <div class="help-block">
                                {_p var='if_you_enable_this_option_buyers_can_make_a_payment_to_one_of_the_payment_gateways_you_have_on_file_with_us_to_manage_your_payment_gateways_go_a_href_link_here_a' link=$sUserSettingLink}
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            <div class="form-group" id="advancedmarketplace_payment_methods" style="display: none;">
                <label>
                    {_p var='payment_methods'} <span class="p-text-danger">{required}</span>
                </label>
                <div class="checkbox p-checkbox-custom">
                    {foreach from=$payment_gateways item=payment_gateway}
                        <label>
                            <input value="{$payment_gateway.gateway_id}" type="checkbox" name="val[payment_methods][]"
                                   {if $payment_gateway.checked}checked{/if}><i class="ico ico-square-o mr-1 close_warning"></i>
                            {$payment_gateway.title}
                        </label>
                    {/foreach}
                </div>
            </div>
        {/if}

        {if $bIsEdit && ($aForms.view_id == '0' || $aForms.view_id == '2')}
            <div class="form-group">
                <div class="checkbox p-checkbox-custom">
                    <label>
                        <input id="closed_item_sold" value="2" type="checkbox" name="val[view_id]" {value type='checkbox' id='view_id' default='2'}>
                        <i class="ico ico-square-o mr-1 close_warning"></i>
                        <div>
                            {_p var='closed_item_sold'}
                            <div class="help-block">
                                {_p var='enable_this_option_if_this_item_is_sold_and_this_listing_should_be_closed' link=$sUserSettingLink}
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        {/if}


        <div class="form-group">
            <label for="description">{phrase var='advancedmarketplace.short_description'}</label>
            <textarea class="form-control close_warning" rows="6" name="val[short_description]" maxlength="200"
            placeholder="{_p var='maximum_200_characters'}">{value type='textarea' id='short_description'}</textarea>
        </div>
        <div class="form-group">
            <label for="description">{phrase var='product_information'}</label>
            {editor id='description' rows='12' placeholder='insert_your_product_detail_here'}
        </div>

        <div class="form-group p-advmarketplace-add-country p-advmarketplace-select-form-mini js_core_init_selectize_form_group dont-unbind-children">
            <label>{phrase var='advancedmarketplace.country'} <span class="p-text-danger">{required}</span></label>
            {select_location}
            {module name='core.country-child'}
            {if !$bIsEdit}
                <div class="extra_info">
                    <a href="#" onclick="$(this).parent().hide(); $('#js_mp_add_city').show(); return false;">
                        {phrase var='advancedmarketplace.add_location_detail'}
                    </a>
                </div>
            {/if}
        </div>

        <div id="js_mp_add_city" class="mb-2" {if !$bIsEdit} style="display:none;"{/if}>
            <div class="form-inline row ml--1 mr--1">
                <div class="form-group pl-1 pr-1 col-sm-3">
                    <label for="location">{phrase var='advancedmarketplace.location_venue'}:</label>
                    <div class="d-block">
                        <input type="text" class="form-control w-full" name="val[location]"
                               value="{value type='input' id='location'}" id="location" size="40" maxlength="200"/>
                    </div>
                </div>
                <div class="form-group pl-1 pr-1 col-sm-3">
                    <label for="street_address">{phrase var='advancedmarketplace.address'}</label>
                    <div class="d-block">
                        <input type="text" class="form-control w-full" name="val[address]"
                               value="{value type='input' id='address'}" id="address" size="30" maxlength="200"/>
                    </div>
                </div>

                <div class="form-group pl-1 pr-1 col-sm-3">
                    <label for="city">{phrase var='advancedmarketplace.city'}:</label>
                    <div class="d-block">
                        <input type="text" class="form-control w-full" name="val[city]"
                               value="{value type='input' id='city'}" id="city" size="20" maxlength="200"/>
                    </div>
                </div>
                <div class="form-group pl-1 pr-1 col-sm-3">
                    <label for="postal_code">{phrase var='advancedmarketplace.zip_postal_code'}:</label>
                    <div class="d-block">
                        <input type="text" class="form-control w-full" name="val[postal_code]"
                               value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <input type="hidden" name="val[gmap][latitude]" value="{value type='input' id='input_gmap_latitude'}"
                   id="input_gmap_latitude"/>
            <input type="hidden" name="val[gmap][longitude]" value="{value type='input' id='input_gmap_longitude'}"
                   id="input_gmap_longitude"/>
            <div class="hidden">
                <div id="mapHolder" class="w-full"></div>
            </div>
        </div>

        <div class="form-group p-advmarketplace-select-form-mini js_core_init_selectize_form_group">
            <label for="category">{_p var='category'} <span class="p-text-danger">{required}</span></label>
            <select class="form-control" name="val[category]" id="p-advmarketplace-categories">
                {foreach from=$categories item=category}
                    <option value="{$category.category_id}" {value type='select' id='category' default=$category.category_id}>{$category.name}</option>
                {/foreach}
            </select>
        </div>

        <div id="advmarketplace_js_customfield_form" class="ync-customfield">
            {if count($aCustomFields) > 0}
                {module name="advancedmarketplace.frontend.customfield" aCustomFields=$aCustomFields cfInfors=$cfInfors}
            {/if}
        </div>

        {if Phpfox::isModule('tag')}
            <div class="table form-group">
                <label>
                    {_p var='tags'}
                </label>
                <div>
                    <input type="text" name="val{if $iItemId}[{$iItemId}]{/if}[tag_list]" class="close_warning"
                           value="{value type='input' id='tag_list'}" size="30" placeholder="{_p var='insert_product_brand_model_etc'}">
                    <div class="help-block">
                        {_p var='separate_tags_by_commas'}
                    </div>
                </div>
            </div>
        {/if}
        {if Phpfox::getUserParam('advancedmarketplace.can_sell_items_on_advancedmarketplace')}
            <div class="form-group">
                <div class="checkbox p-checkbox-custom">
                    <label>
                        <input value="1" type="checkbox" class="close_warning"
                               name="val[auto_sell]" {value type='checkbox' id='auto_sell' default='1'}>
                        <i class="ico ico-square-o mr-1"></i>
                        <div>
                            {_p var='auto_sold'}
                            <div class="help-block">
                                {_p var='if_this_is_enabled_and_once_a_successful_purchase_of_this_item_is_made'}
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        {/if}

        <div class="form-group">
            <div class="checkbox p-checkbox-custom">
                <label>
                    <input id="advancedmarketplace_is_expiry_date" value="1" type="checkbox" class="close_warning"
                           name="val[has_expiry]" {value type='checkbox' id='has_expiry' default='1'}>
                    <i class="ico ico-square-o mr-1"></i>
                    <div>
                        {_p var='set_expiry_date'}
                    </div>
                </label>
                <label id="advancedmarketplace_expiry_date" class="p-fevent-label-align mt-1" style="display: none;">
                    <div class="">
                        {select_date prefix='expiry_' id='_expiry' start_year='current_year' end_year='+5' field_separator=' / ' field_order='MDY' default_all=true}
                    </div>
                </label>
            </div>
        </div>

        {if (!isset($sModule) || empty($sModule)) && Phpfox::isModule('privacy')}
            <div class="form-group">
                <label for="">{phrase var='privacy'}</label>
                {module name='privacy.form' privacy_name='privacy' default_privacy='advancedmarketplace.display_on_profile'}
            </div>
        {/if}
        {if $bIsEdit && isset($aForms.post_status)}
            <input type="hidden" name="val[post_status]" value="{$aForms.post_status}"/>
        {/if}
        {if !$isCreating}
            <div class=" p-form-group-btn-container has-top-border">
                <input type="submit"
                       value="{if $bIsEdit}{phrase var='advancedmarketplace.update'}{else}{phrase var='advancedmarketplace.submit'}{/if}"
                       class="btn btn-primary pull-left mr-1"/>
                {if !$bIsEdit}
                    <input type="submit" name="val[draft]"
                           value="{phrase var='advancedmarketplace.save_as_draft'}"
                           class="btn btn-default"/>
                {else}
                    <a id="js_p_advmarketplace_cancel" href="{permalink module='advancedmarketplace/detail' id=$aForms.listing_id title=$aForms.title}" class="btn btn-default">
                        {_p var='cancel_uc'}
                    </a>
                {/if}
                {if $bIsEdit && $aForms.post_status == 2}
                    <input type="submit" name="val[draft_publish]" value="{phrase var='advancedmarketplace.publish'}"
                           class="btn btn-success"/>
                {/if}
            </div>
        {else}
            <div class="p-step-groupaction-container">
                <div class="p-step-groupaction-outer">
                    <div class="item-button-action-container">
                        <input type="submit"
                               value="{if $bIsEdit}{phrase var='advancedmarketplace.update'}{else}{phrase var='publish_and_proceed'}{/if}"
                               class="btn btn-primary pull-left mr-1"/>
                        {if !$bIsEdit}<input type="submit" name="val[draft]"
                                             value="{phrase var='advancedmarketplace.save_as_draft'}"
                                             class="btn btn-default" />{/if}
                        {if $bIsEdit && $aForms.post_status == 2}
                            <input type="submit" name="val[draft_publish]"
                                   value="{phrase var='advancedmarketplace.publish'}" class="btn btn-success"/>
                        {/if}
                    </div>
                    <div class="item-button-step-container">
                        <div class="item-action">
                            {if !$bIsEdit}
                                <a href="{url link='advancedmarketplace'}"
                                   class="item-action-link">{_p var='cancel_uc'}</a>
                            {/if}
                        </div>
                        <div class="item-step">
                            <span class="item-step-number">1/3</span>
                            {if $bIsEdit}
                                <button onclick="appAdvMarketplace.switchStep('js_mp_block_customize');return false;"
                                        class="btn item-nav-button"><i class="ico ico-angle-right"></i></button>
                            {else}
                                <button class="btn item-nav-button disabled"><i class="ico ico-angle-right"></i>
                                </button>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>

    <div id="js_mp_block_customize" class="js_mp_block page_section_menu_holder"
        {if !empty($sActiveTab) && $sActiveTab != 'customize'}style="display:none;"{/if}>
        <div id="js-p-advmarketplace-photos-container">
            {module name='advancedmarketplace.photo'}
        </div>
        {if $isCreating}
            <div class="p-step-groupaction-container">
                <div class="p-step-groupaction-outer">
                    <div class="item-button-action-container">
                        <div id="p_advmarketplace_back_to_manage_container" style="display: none;">
                            <a href="javascript:void(0);" class="btn btn-default" id="p_advmarketplace_back_to_manage"
                            onclick="$Core.advancedmarketplace.toggleUploadSection({$aForms.listing_id}, 0, 1);">
                                {_p var='back_to_manage'}
                            </a>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary" id="p_advmarketplace_confirm_photo">
                            {_p var='next'}
                        </a>
                    </div>
                    <div class="item-button-step-container">
                        <div class="item-action">
                            <a href="{permalink module='advancedmarketplace/detail' id=$aForms.listing_id title=$aForms.title}"
                               class="item-action-link">{_p var='skip_all'}</a>
                        </div>
                        <div class="item-step">
                            <button onclick="appAdvMarketplace.switchStep('js_mp_block_detail');return false;"
                                    class="btn item-nav-button"><i class="ico ico-angle-left"></i></button>
                            <span class="item-step-number">2/3</span>
                            <button onclick="appAdvMarketplace.switchStep('js_mp_block_invite');return false;"
                                    class="btn item-nav-button"><i class="ico ico-angle-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>

    <div id="js_mp_block_invite" class="js_mp_block page_section_menu_holder"
         {if !empty($sActiveTab) && $sActiveTab != 'invite'}style="display:none;"{/if}>
        <div class="block">
            <div class="p-advmarket-invatation-alert mb-2">{_p var='make_sure_your_listings_in_a_published_version_before_sending_any_invitations_to_your_friends'}</div>
            <div class="form-group">
                <label for="js_find_friend">{_p var='invite_friends'}</label>
                {if isset($aForms.listing_id)}
                    <div id="js_selected_friends" class="hide_it"></div>
                    {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.listing_id friend_module_id='advancedmarketplace' }
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
                          class="form-control textarea-auto-scale" placeholder="{_p var='write_message'}"></textarea>
            </div>
            {if !$isCreating}
                <div class="form-group">
                    <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary"
                           name="invite_submit"/>
                </div>
            {/if}
        </div>
        {if $isCreating}
            <div class="p-step-groupaction-container">
                <div class="p-step-groupaction-outer">
                    <div class="item-button-action-container">
                        <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary"
                               name="invite_submit"/>
                    </div>

                    <div class="item-button-step-container">
                        <div class="item-action">
                            <a href="{permalink module='advancedmarketplace/detail' id=$aForms.listing_id title=$aForms.title}"
                               class="btn btn-success btn-icon">
                                <i class="ico ico-check"></i> {_p var='finish'}
                            </a>
                        </div>
                        <div class="item-step">
                            <button onclick="appAdvMarketplace.switchStep('js_mp_block_customize');return false;"
                                    class="btn item-nav-button"><i class="ico ico-angle-left"></i></button>
                            <span class="item-step-number">3/3</span>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>

    {if isset($aForms.listing_id) && $bIsEdit}
        <div id="js_mp_block_manage" class="js_mp_block page_section_menu_holder" style="display:none;">
            {module name='advancedmarketplace.invite-list'}
        </div>
    {/if}

</form>
{/if}

<script type="text/javascript">
    var loadMap = false;
    var photoConfirmLink = "{url link='advancedmarketplace.add' id=$iListingId tab='invite' creating=1}";
    var inviteConfirmLink = "{permalink module='advancedmarketplace/detail' id=$aForms.listing_id title=$aForms.title}";
    oTranslations['it_looks_like_you_havent_upload_any_photo_yet'] = "{_p var='it_looks_like_you_havent_upload_any_photo_yet'}";
    oTranslations['it_looks_like_you_havent_invited_any_people_yet'] = "{_p var='it_looks_like_you_havent_invited_any_people_yet'}";
    oTranslations['finish_photo_uploading'] = "{_p var='finish_photo_uploading'}";
    oTranslations['next'] = "{_p var='next'}";
    var currentEditAdvMarketplaceTab = "{$currentTab}";
    {literal}
    $Behavior.ynadvInitializeGoogleMapLocation = function () {
        if (loadMap === false) {
            loadMap = true;
            $('#js_country_child_id_value').change(function () {
                debug("Cleaning  city, postal_code and address");
                $('#city').val('');
                $('#postal_code').val('');
                $('#address').val('');
            });
            $('#country_iso, #js_country_child_id_value').change(adv_inputToMap);
            $('#location, #address, #postal_code, #city').blur(adv_inputToMap);
            adv_loadScript('{/literal}{param var='core.google_api_key'}{literal}');
        }
        var $photoConfirmBtn = $('#p_advmarketplace_confirm_photo');
        if ($photoConfirmBtn.length) {
            var photoConfirmMessage = oTranslations['it_looks_like_you_havent_upload_any_photo_yet'];
            $photoConfirmBtn.click(function () {
                window.location.href = photoConfirmLink;
            });
        }
        if(!empty(currentEditAdvMarketplaceTab) && $('#js_mp_block_' + currentEditAdvMarketplaceTab).length && !$('#js_mp_block_' + currentEditAdvMarketplaceTab).hasClass('redirect-edit')) {
            let tabId = '#js_mp_block_' + currentEditAdvMarketplaceTab;
            $Core.pageSectionMenuShow(tabId);
            $(tabId).addClass('redirect-edit');
        }
    };
    {/literal}
</script>