<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<style>
    .yc_extra_info {
        color: #808080;
        padding: 4px 0;
    }
    select{
        min-width: 50px;
    }
</style>

{/literal}

<div class="main_break">
    {$sCreateJs}
    <form method="post" class="ync_add_edit_form" action="{url link='current'}" id="ync_edit_coupon_form"  enctype="multipart/form-data">
        <div id="js_custom_privacy_input_holder">
        {if $bIsEdit && empty($sModule)}
            {module name='privacy.build' privacy_item_id=$aForms.coupon_id privacy_module_id='coupon'}
        {/if}
        </div>

        <div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
        <div><input type="hidden" name="val[selected_categories]" id="js_selected_categories" value="{value type='input' id='selected_categories'}" /></div>
        <div><input type="hidden" name="val[is_approved]" value="{value type='input' id='is_approved'}" /></div>

        {if !empty($sModule)}
            <div><input type="hidden" name="module" value="{$sModule|htmlspecialchars}" /></div>
        {/if}
        {if !empty($iItem)}
            <div><input type="hidden" name="item" value="{$iItem|htmlspecialchars}" /></div>
        {/if}
        {if $bIsEdit}
            <div><input type="hidden" name="val[coupon_id]" value="{$aForms.coupon_id}" /></div>
            <div><input type="hidden" name="val[status]" value="{$aForms.status}" /></div>
        {/if}

        <div id="js_coupon_block_main" class="js_coupon_block page_section_menu_holder">

            <div class="table form-group">
                <div class="table_left">
                    <label for="title">{required}{phrase var='coupon_name'}: </label>
                </div>
                <div class="table_right">
                    <input type="text" class="ync required ync_coupon_title_max_length form-control" name="val[title]" value="{value type='input' id='title'}" id="title" size="60" />
                </div>
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    <label for="category">{required}{phrase var='category'}:</label>
                </div>
                <div class="table_right">
                    {$sCategories}
                </div>
            </div>
            
            <div class="table form-group">
                <div class="table_left">
                    <label for="site_url">{phrase var='site_url'}:</label>
                </div>
                <div class="table_right">
                    <input type="text" class="form-control" name="val[site_url]" value="{value type='input' id='site_url'}" id="site_url" size="40" maxlength="255" />
                </div>
            </div>

            <div class="table form-group">
                    <label for="description">{phrase var='description'}</label>

                    {editor id='description'}
            </div>

            <div class="table form-group-follow">
                {if !empty($aForms.image_path) && !empty($aForms.coupon_id)}
                    {module name='core.upload-form' type='coupon' current_photo=$aForms.current_image id=$aForms.coupon_id}
                    <input type="hidden" name="val[image_path]" value="{value type='input' id='image_path'}">
                    <input type="hidden" name="val[server_id]" value="{value type='input' id='server_id'}">
                {else}
                    {module name='core.upload-form' type='coupon' current_photo=''}
                {/if}
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    {required}{phrase var='start_date'}:
                </div>
                <div class="table_right">
                    <div class="ync_start_time" style="position: relative;">
                        {select_date prefix='start_time_' id='_start_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
                    </div>
                </div>
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    {required}{phrase var='end_date'}:
                </div>
                <div class="table_right">
                    <div class="ync_end_time" style="position: relative;">
                        {select_date prefix='end_time_' id='_end_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
                    </div>
                </div>
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    {phrase var='expired_date'}
                </div>
                <div class="table_right">
                    <div class="ync_disable" style="position: relative; {if ($bIsEdit && !$aForms.expire_time) || (isset($aForms.unlimit_time) && $aForms.unlimit_time == 1)} display: none;{/if}">
                        {select_date prefix='expire_time_' id='_expire_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
                    </div>
                    <div class="extra_info_custom" style="margin-top:10px; font-size:12px;">
                        <input type="checkbox" name="val[unlimit_time]" onclick="disable($(this));" value="1" id="unlimit_time" class="checkbox v_middle"{if ($bIsEdit && !$aForms.expire_time) || (isset($aForms.unlimit_time) && $aForms.unlimit_time == 1)} checked="checked"{/if} /> {phrase var='set_to_unlimit_time'}
                    </div>
                </div>
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    {phrase var='coupon_type'}
                </div>
                <div class="table_right">
                    <input type="radio" value="discount" name="val[coupon_type]" id="coupon_type" {if isset($aForms.discount_type) || !$bIsEdit } checked='checked' {/if} onClick="yncoupon.changeTypeCoupon('discount')"> <label for="discount">{phrase var='discount'}</label>
                    <input type="radio" value="special_price" name="val[coupon_type]" id="coupon_type" {if isset($aForms.special_price_value) &&  $bIsEdit } checked='checked' {/if} onClick="yncoupon.changeTypeCoupon('special_price')"> <label for="special_price">{phrase var='special_price'}</label>
                </div>
            </div>

            <div class="table form-group-follow discount_block"  {if ( isset($aForms.discount_type) && $aForms.discount_type != 'special_price' ) || !$bIsEdit } style='display:block' {else} style='display:none' {/if}>
                <div class="table_left">
                    <label for="discount_type_select">{required}{phrase var='discount'}</label>
                </div>
                <div class="table_right">
                    <div>
                        <input type="text" name="val[discount_value]" class="ync_positive_number form-control" id="discount_value" value="{value type='input' id='discount_value'}" />
                    </div>
                    <select id="discount_type" class="ync required form-control" name='val[discount_type]' onchange="if ($(this).val()=='price') $('#discount_currency').show(); else $('#discount_currency').hide();">
                        <option value="percentage" {if isset($aForms.discount_type) && $aForms.discount_type == 'percentage'} selected {/if}>{phrase var='percentage'}</option>
                        <option value="price"{if isset($aForms.discount_type) && $aForms.discount_type == 'price'} selected {/if}>{phrase var='price'}</option>
                    </select>
                    {if count($aCurrency)}
                    <select class="form-control" id="discount_currency" name='val[discount_currency]' {if isset($aForms.discount_type) && $aForms.discount_type == 'price'}{else}style="display: none;"{/if}>
                    {foreach from=$aCurrency key=code item=aC}
                        <option value="{$code}"{if isset($aForms.discount_currency)}{if $code==$aForms.discount_currency} selected="selected"{/if}{elseif $code=='USD'} selected="selected"{/if}>{$code} - {$aC.name}</option>
                    {/foreach}
                    </select>
                    {/if}
                </div>
            </div>

            <div class="table form-group special_price_block" {if isset($aForms.special_price_value) &&  $bIsEdit } style='display:block' {else}  style='display:none' {/if}>
                <div class="table_left">
                    <label for="specical_price">{phrase var='special_price'}</label>
                </div>
                <div class="table_right">
                    <div class="ync_disable" style="position: relative;">
                        <input type="text" name="val[special_price_value]" class="ync_positive_number ync_disable form-control" value="{value type='input' id='special_price_value'}" id="special_price_value" />
                    </div>
                    {if count($aCurrency)}
                    <select class="form-control" id="special_price_currency" name='val[special_price_currency]' >
                    {foreach from=$aCurrency key=code item=aC}
                        <option value="{$code}"{if isset($aForms.special_price_currency)}{if $code==$aForms.special_price_currency} selected="selected"{/if}{elseif $code=='USD'} selected="selected"{/if}>{$code} - {$aC.name}</option>
                    {/foreach}
                    </select>
                    {/if}
                </div>
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    {required}<label for="location_venue">{phrase var='location_venue'}:</label>
                </div>
                <div class="table_right">
                    <input type="text" name="val[location_venue]" class="ync required form-control" value="{value type='input' id='location_venue'}" id="location_venue" size="40" maxlength="200" />
                    <div class="yc_extra_info">
                        {if !$bIsEdit}
                        <a href="#" id="js_link_show_add" onclick="$(this).hide(); $('#js_mp_add_city').show(); $('#js_link_hide_add').show(); return false;">{phrase var='add_city_zip'}</a>
                        <a href="#" id="js_link_hide_add" style="display: none;" onclick="$(this).hide(); $('#js_mp_add_city').hide(); $('#js_link_show_add').show(); return false;">{phrase var='hide_add_city_zip'}</a>
                        {/if}
                    </div>
                </div>
            </div>

            <div id="js_mp_add_city" {if !$bIsEdit} style="display:none;"{/if} >

                <div class="table form-group" style="display:none">
                    <div class="table_left">
                        <label for="address">{phrase var='address'}</label>
                    </div>
                    <div class="table_right">
                        <input type="text" name="val[address]" class="form-control" value="{value type='input' id='address'}" id="address" size="30" maxlength="200" />
                    </div>
                </div>

                <div class="table form-group">
                    <div class="table_left">
                        <label for="city">{phrase var='city'}:</label>
                    </div>
                    <div class="table_right">
                        <input type="text" name="val[city]" class="form-control" value="{value type='input' id='city'}" id="city" size="20" maxlength="200" />
                    </div>
                </div>
                <div class="table form-group">
                    <div class="table_left">
                        <label for="postal_code">{phrase var='zip_postal_code'}:</label>
                    </div>
                    <div class="table_right">
                        <input type="text" name="val[postal_code]" class="form-control" value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20" />
                    </div>
                </div>

                <div class="table form-group-follow">
                    <div class="table_left">
                        {required}<label for="country_iso">{phrase var='country'}:</label>
                    </div>
                    <div class="table_right">
                        {select_location}
                        {module name='core.country-child'}
                    </div>
                </div>
            </div>

            <div class="table form-group">
                <div class="table_left">
                    <button id="refresh_map" class="button btn btn-primary btn-sm" type="button" onclick="coupon_inputToMap();">{phrase var='refresh_map'}</button>
                </div>
                <div class="table_right">
                    <input type="hidden" name="val[gmap][latitude]" value="{value type='input' id='input_gmap_latitude'}" id="input_gmap_latitude" />
                    <input type="hidden" name="val[gmap][longitude]" value="{value type='input' id='input_gmap_longitude'}" id="input_gmap_longitude" />
                    <div id="mapHolder" style="width: 400px; height: 400px"></div>
                    <div class="extra_info_custom" style="margin-top:10px; font-size:12px;">
                        <input type="checkbox" name="val[is_show_map]" onclick="disable($(this));" value="1" id="is_show_map" class="checkbox v_middle"{if (!$bIsEdit || $aForms.is_show_map) } checked="checked"{/if} /> {phrase var='show_map'}
                    </div>
                </div>
            </div>

            <div class="table form-group-follow">
                <div class="table_left">
                    <label for="quantity">{required}{phrase var='quantity'}:</label>
                </div>
                <div class="table_right">
                    <div class="ync_disable" style="position: relative;{if ($bIsEdit && !$aForms.quantity) || isset($aForms.unlimit_quantity)} display: none;{/if}">
                        <input type="text" name="val[quantity]" class="ync_positive_number ync_disable form-control" value="{value type='input' id='quantity'}" id="quantity" />
                    </div>
                    
                    <div class="extra_info_custom" style="margin-top:10px; font-size:12px;">
                        <input type="checkbox" name="val[unlimit_quantity]" onclick="disable($(this));" value="1" id="unlimit_quantity" class="checkbox v_middle"{if ($bIsEdit && !$aForms.quantity) || isset($aForms.unlimit_quantity)} checked="checked"{/if} /> {phrase var='set_to_unlimit_quantity'}
                    </div>
                </div>
            </div>



            <div class="table form-group-follow">
                <div class="table_left">
                    <label>{phrase var='coupon_code'}:</label>
                </div>
                <div class="table_right">
                    <div class="ync_disable" style="position: relative;{if isset($bFail)}{if empty($aForms.auto_generate)} display: block;{else} display: none;{/if}{elseif $bIsEdit && !empty($aForms.code_setting)} display: block;{else} display: none;{/if}">
                        <input type="text" name="val[code_setting]" class="ync_disable form-control" value="{value type='input' id='code_setting'}" id="code_setting" /> ({phrase var='1_30_characters'})
                    </div>
                    
                    <div class="extra_info_custom" style="margin-top:10px; font-size:12px;">
                        <input type="checkbox" name="val[auto_generate]" onclick="disable($(this));" value="1" id="auto_generate" class="checkbox v_middle"{if isset($bFail)}{if empty($aForms.auto_generate)}{else} checked="checked"{/if}{elseif $bIsEdit && !empty($aForms.code_setting)}{else} checked="checked"{/if} /> {phrase var='set_auto_generate'}
                    </div>
                     <div class="yc_extra_info extra_info">
                        {phrase var='auto_generate_8_alpha_numeric_characters'}
                    </div>
                </div>
                
            </div>

            <div class="table form-group">
                <div class="table_left">
                    {phrase var='term_conditions'}
                </div>
                <div class="table_right">
                    {editor id='term_condition'}
                </div>
            </div>
            
            <div class="clear"></div>
            <br/>

            <div class="table form-group-follow ync_choosetheme">
                <div class="table_left">
                    {phrase var='print_layout'}:
                </div>
                <div class="table_right">
                    <input type="hidden" name="val[print_option][style]" id="print_option_style" value="{if isset($aForms.print_option)}{$aForms.print_option.style}{else}1{/if}" />
                    <input type="hidden" name="val[print_option][photo]" id="print_option_photo" value="{if isset($aForms.print_option)}{$aForms.print_option.photo}{else}1{/if}" />
                    <input type="hidden" name="val[print_option][site_url]" id="print_option_site_url" value="{if isset($aForms.print_option)}{$aForms.print_option.site_url}{else}1{/if}" />
                    <input type="hidden" name="val[print_option][location]" id="print_option_location" value="{if isset($aForms.print_option)}{$aForms.print_option.location}{else}1{/if}" />
                    <input type="hidden" name="val[print_option][category]" id="print_option_category" value="{if isset($aForms.print_option)}{$aForms.print_option.category}{else}1{/if}" />
                    <button type="button" class="button btn btn-primary btn-sm"
                            onclick="tb_show('{phrase var='customize_themes'}', $.ajaxBox('coupon.blockThemes', 'width=700{if isset($aForms.coupon_id)}&id={$aForms.coupon_id}{/if}'));">
                        {phrase var='customize_themes'}
                    </button>
                </div>
            </div>

            {if count($aFields)}

            {foreach from=$aFields item=aField}

                {template file='coupon.block.custom.form'}
                
            {/foreach}
            {/if}
           

            {if empty($sModule) && Phpfox::isModule('privacy')}
            <div class="table form-group-follow">
                <div class="table_left">
                    {phrase var='privacy'}:
                </div>
                <div class="table_right">
                    {module name='privacy.form' privacy_name='privacy' privacy_info='coupon.control_who_can_see_this_coupon'  default_privacy='coupon.default_privacy_setting'}
                </div>
            </div>
            {/if}


            {if empty($sModule)  && Phpfox::isModule('privacy') && Phpfox::getUserParam('coupon.can_control_claim_coupons')}
            <div class="table form-group-follow">
                <div class="table_left">
                    {phrase var='claims_privacy'}
                </div>
                <div class="table_right">
                    {module name='privacy.form' privacy_name='privacy_claim' privacy_info='coupon.control_who_can_claims_on_this_coupon' privacy_no_custom=true}
                </div>
            </div>
            {/if}

            <div class="table form-group-follow" {if $bIsEdit && $aForms.status == 8} style="display: none;" {/if}>
                <div class="table_left">
                    {phrase var='publishing_fee_multi_currency' publish_fee=$iPublishFee symbol_currency_fee=$symbolCurrencyFee}
                </div>
                <div class="table_right">
                    <input type="checkbox" name="val[feature_coupon]" value="1" id="feature_coupon" class="checkbox v_middle" onclick="Calculate($(this));"{if ($bIsEdit && $aForms.is_featured) || isset($aForms.feature_coupon)} checked="checked"{/if} /> {phrase var='features_this_coupon_multi_currency' feature_fee=$iFeatureFee symbol_currency_fee=$symbolCurrencyFee}
                </div>
                <div class="extra_info_custom total_money" style="margin-top:10px; font-size:12px;">
                    {phrase var='total_fee_multi_currency' total_fee=$iTotalFee symbol_currency_fee=$symbolCurrencyFee }
                </div>
            </div>
            <input type="hidden" name="val[is_publish]" id="ync_is_publish" value="0">
            <div class="table_clear">
                <ul class="table_clear_button">
                    {if $bIsEdit && $aForms.is_draft == 1}
                    <li><input type="submit" name="val[draft_update]" value="{phrase var='update'}" class="button btn btn-primary" /></li>
                    <li><input type="submit" name="val[draft_publish]"
                               onclick="yncoupon.confirmOnAddCoupon(getTotalFee(),'{url link='current'}',true); return false"
                               value="{phrase var='publish'}" class="button btn-default"/></li>
                    {else}
                    <li><input type="submit" name="val[{if $bIsEdit}update{else}publish{/if}]" {if !$bIsEdit}
                               onclick="yncoupon.confirmOnAddCoupon(getTotalFee(),'{url link='current'}'); return false"
                               {/if} value="{if $bIsEdit}{phrase var='update'}{else}{phrase var='publish'}{/if}"
                        class="button btn-primary" />
                    </li>
                    {/if}
                    {if !$bIsEdit}
                    <li><input type="submit" name="val[draft]" value="{phrase var='save_as_draft'}" class="button btn btn-default" /></li>
                    <li><input type="submit" name="val[cancel]" value="{phrase var='cancel'}" class="button btn btn-default" /></li>
                    {/if}

                </ul>
                <div class="clear"></div>
            </div>

            {if Phpfox::getParam('core.display_required')}
            <div class="table_clear">
                {required} {phrase var='core.required_fields'}
            </div>
            {/if}
        </div>
    </form>
</div>

{literal}
<script>
    
    function disable(a) {
        if(a.is(':checked')) {
            a.parent().parent().find('.ync_disable').hide();
       }
        else
            a.parent().parent().find('.ync_disable').show();
    }

    function Calculate(c) {
        publish = {/literal}{$iPublishFee}{literal};
        feature = {/literal}{$iFeatureFee}{literal};

        symbol_currency_fee = '{/literal}{$symbolCurrencyFee}{literal}';

        if(c.is(':checked')){
            $('.total_money').html(oTranslations['coupon.total_fee_multi_currency'].replace('{total_fee}',publish + feature).replace('{symbol_currency_fee}',symbol_currency_fee));
        }
        else{
            $('.total_money').html(oTranslations['coupon.total_fee_multi_currency'].replace('{total_fee}',publish).replace('{symbol_currency_fee}',symbol_currency_fee));
        }
    }

    function getTotalFee() {
        total = {/literal}{$iPublishFee}{literal};

        if($('#feature_coupon').is(':checked'))
            total += {/literal}{$iFeatureFee}{literal};

        status = {/literal}{if $bIsEdit} {$aForms.status} {else} 0 {/if};
        {literal}
        deniedCode = {/literal} {$iDeniedCode} {literal};
        symbol_currency_fee = '{/literal}{$symbolCurrencyFee}{literal}';        

        if(status == deniedCode)
            return oTranslations['coupon.confirm_publish_again'];
        else
            return oTranslations['coupon.confirm_publish_coupon_multi_currency'].replace('{total_fee}', total).replace('{symbol_currency_fee}',symbol_currency_fee);
    }
    
    var oMarker;
    var oGeoCoder;
    var sQueryAddress;
    var oMap;
    var oLatLng;
    var bDoTrigger = false;
    /* This function takes the information from the input fields and moves the map towards that location*/
    function coupon_inputToMap()
    {
        var sQueryAddress = $('#location_venue').val() + ' ' +$('#address').val() + ' ' +  $('#city').val();
        
        if ($('#js_country_child_id_value option:selected').val() > 0)
        {
            sQueryAddress += ' ' + $('#js_country_child_id_value option:selected').text();
    
            //$.ajaxCall('core.getChildre','country_iso=' + $('#country_iso option:selected').val());
        }
        sQueryAddress += ' ' + $('#country_iso option:selected').text();
        //debug ('Searching for: ' + sQueryAddress);
        oGeoCoder.geocode({
            'address': sQueryAddress
            }, function(results, status)
            {
                if (status == google.maps.GeocoderStatus.OK)
                {
                    oLatLng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
                    oMarker.setPosition(oLatLng);
                    oMap.panTo(oLatLng);
                    $('#input_gmap_latitude').val(oMarker.position.lat());
                    $('#input_gmap_longitude').val(oMarker.position.lng());
                }
            }
        );
        if (bDoTrigger)
        {
            google.maps.event.trigger(oMarker, 'dragend');
            bDoTrigger = false;
        }
    }
    
    function coupon_initialize()
    {
        oGeoCoder = new google.maps.Geocoder();
        if(typeof(aInfo)=='undefined')
        {
            aInfo = {latitude:0, longitude:0};
        }
        oLatLng = new google.maps.LatLng(aInfo.latitude, aInfo.longitude);
        
        var myOptions = {
            zoom: 11,
            center: oLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            streetViewControl: false
        };
        oMap = new google.maps.Map(document.getElementById("mapHolder"), myOptions);
        oMarker = new google.maps.Marker({
            draggable: true,
            position: oLatLng,
            map: oMap
        });
    
        
        /* Fake the dragend to populate the city and other input fields */
        google.maps.event.trigger(oMarker, 'dragstart');
        google.maps.event.trigger(oMarker, 'dragend');
        google.maps.event.addListener(oMarker, "dragend", function()
        {
            debug('drag end');
            $('#input_gmap_latitude').val(oMarker.position.lat());
            $('#input_gmap_longitude').val(oMarker.position.lng());
            oLatLng = new google.maps.LatLng(oMarker.position.lat(), oMarker.position.lng());
            oGeoCoder.geocode({
                'latLng': oLatLng
            },
            function(results, status)
            {
                if (status == google.maps.GeocoderStatus.OK)
                {
                    $('#city').val('');
                    $('#postal_code').val('');
                    //debug (results[0]);
                    for (var i in results[0]['address_components'])
                    {
                        if (results[0]['address_components'][i]['types'][0] == 'locality')
                        {
                            $('#city').val(results[0]['address_components'][i]['long_name']);
                        }
                        if (results[0]['address_components'][i]['types'][0] == 'country')
                        {
                            var sCountry = $('#country_iso option:selected').val();
                            $('#js_country_iso_option_'+results[0]['address_components'][i]['short_name']).attr('selected','selected');
                            if (sCountry != $('#country_iso option:selected').val())
                            {
                                $('#country_iso').change();
                            }
                        }
                        if (results[0]['address_components'][i]['types'][0] == 'postal_code')
                        {
                            $('#postal_code').val(results[0]['address_components'][i]['long_name']);
                        }
                        if (results[0]['address_components'][i]['types'][0] == 'street_address')
                        {
                            $('#address').val(results[0]['address_components'][i]['long_name']);
                        }
                        if (isset($('#js_country_child_id_value')) && results[0]['address_components'][i]['types'][0] == 'administrative_area_level_1')
                        {
                            $('#js_country_child_id_value option').each(function(){
                                if ($(this).text() == results[0]['address_components'][i]['long_name'])
                                {
                                    $(this).attr('selected','selected');
                                    bHasChanged = true;
                                }
                            });
                        }                   
                    }
                }
            });
        });
        /* Sets events for when the user inputs info */
        coupon_inputToMap();
    }
    
    function coupon_loadScript()
    {
        var prefix_api = 'https://';
        
        var script = document.createElement('script');
        script.type= 'text/javascript';
        script.src = prefix_api + 'maps.google.com/maps/api/js?sensor=false&key={/literal}{param var="core.google_api_key"}{literal}&callback=coupon_initialize';
        document.body.appendChild(script);
    }
    var loadMap = false;

    $Behavior.loadGoogleMap = function() {
        if(loadMap != true)
        {
           loadMap = true;
            $('#js_country_child_id_value').change(function(){
                debug("Cleaning  city, postal_code and address");
                $('#city').val('');
                $('#postal_code').val('');
                $('#address').val('');
            });
            $('#country_iso, #js_country_child_id_value').change(coupon_inputToMap);
            $('#location_venue, #address, #postal_code, #city').blur(coupon_inputToMap);
            coupon_loadScript();
        }
    };
    </script>
{/literal}