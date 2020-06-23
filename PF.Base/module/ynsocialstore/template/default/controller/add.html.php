<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/25/16
 * Time: 10:23
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynsocialstore-hiddenblock">
    <input type="hidden" value="addproduct" id="ynsocialstore_pagename" name="ynsocialstore_pagename">

</div>
{if isset($invoice_id) && (int)$invoice_id > 0}
<div class="ynsocialstore_box_payment">
    <h3>{_p var='ynsocialstore.payment_methods'}</h3>
    {module name='api.gateway.form'}
</div>
{else}
    {if isset($sError) && !empty($sError)}
        {$sError}
    {else}
        <input type="hidden" name="val[feature_fee]" value="{if $aPackage !== null}{$aPackage.feature_product_fee}{else}0{/if}" id="ynsocialstore_defaultfeaturefee">
        <input type="hidden" id="ynsocialstore_corepath" value="{$core_path}">
        {if $bIsEdit}
            <input type="hidden" id="ynstore_product_id" name="val[product_id]" value="{$aForms.product_id}">
            <input type="hidden" id="ynstore_total_quantity" name="" value="{$iTotalAttQuantity}">
            <input type="hidden" id="ynstore_manage_attr_link" name="" value="{$sLinkManageAttr}">
        {/if}

        <div class="ynstore_add_product">
            {$sCreateJs}
            <form enctype="multipart/form-data" id="ynsocialstore_add_product_form" action="{url link='current'}" class="ynsocialstore-add-edit-form" method="post">
                <div id="js_custom_privacy_input_holder">
                    {if $bIsEdit && empty($sModule)}{module name='privacy.build' privacy_item_id=$aForms.product_id privacy_module_id='ynsocialstore_product'}{/if}
                </div>

                <div class="ynstore-product-add-block">
                    <h3>{_p var='ynsocialstore.general_info'}</h3>

                    <div class="form-group">
                        <label for="name">{required}{_p var='ynsocialstore.product_title'}: </label>
                        <input class="form-control" type="text" name="val[name]" id="name" value="{value type='input' id='name'}" id="name" maxlength="200"/>
                    </div>
                    {if !$bIsEdit && $iStoreId == 0}
                        <div class="form-group">
                            <label for="name">{required}{_p var='ynsocialstore.store_name'}: </label>
                            <select name="val[store_id]" class="form-control" id="store_name">
                                <option value>{_p var='ynsocialstore.select'}:</option>
                                {foreach from=$aAllStore key=iKey item=aStore}
                                    <option value="{$aStore.store_id}" {if $aStore.canAddProduct == false}disabled{else}{if count($aAllStore) == 1}selected{/if}{/if}>{$aStore.name|clean}</option>
                                {/foreach}
                            </select>
                        </div>
                    {/if}

                    <input type="hidden" name="val[store_id]" id="ynstore_store_id" value="{if $iStoreId > 0}{$iStoreId}{else}0{/if}">

                    <div class="row">
                        <div class="col-md-6 col-sm-12 ">
                            <div class="form-group">
                                <label for="name">{required}{_p var='ynsocialstore.product_type'}: </label>
                                <select name="val[product_type]" class="form-control" id="product_type">
                                    <option value="physical" {if isset($aForms) && $aForms.product_type == 'physical'}selected{/if}>{_p var='ynsocialstore.physical_product'}</option>
                                    <option value="digital" {if isset($aForms) && $aForms.product_type == 'digital'}selected{/if}>{_p var='ynsocialstore.digital_product'}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 ">
                            {if isset($aUOMs) && count($aUOMs)}
                            <div class="form-group {if isset($aForms) && $aForms.product_type == 'digital'}hide{/if}" id="ynstore_product_uom">
                                <label for="uom">{_p var='ynsocialstore.uom'}: </label>
                                <select name="val[uom]" id="ynstore_uom" class="form-control">
                                    <option value="0">{_p('None')}</option>
                                    {foreach from=$aUOMs item=uom}
                                    <option value="{$uom.uom_id}" {if isset($aForms.uom_id) && $aForms.uom_id == $uom.uom_id }selected{/if}>{$uom.title}</option>
                                    {/foreach}
                                </select>
                            </div>
                            {/if}
                        </div>
                    </div>

                    <div id="ynstore_categorylist" class="form-group">
                        <label for="category">{required}{_p var='ynsocialstore.category'}:</label>
                        <div class="js_ynstore_add_categories">
                            {$sCategories}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{required}{_p var='ynsocialstore.description'}</label>
                        {editor id='description'}
                    </div>
                </div>

                <div class="ynstore-product-add-block">
                    <h3>{_p var='ynsocialstore.price_discounts'}</h3>

                    <div class="row">
                        <div class="col-md-5 col-sm-12 ynstore-paddingright-5">
                            <div class="form-group">
                                <label for="price">{required}{_p var='ynsocialstore.price'} ({$aCurrentCurrencies.0.currency_id}): </label>
                                <input class="form-control" type="text" name="val[product_price]" id="product_price" value="{value type='input' id='product_price'}" size="40" />
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12 ynstore-paddingleft-5">
                            <div class="form-group">
                                <label for="discount">{_p var='ynsocialstore.discount_0_or_blank_mean_no_discount'}: </label>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="val[discount_value]" id="ynstore_product_discount_value" value="{if isset($aForms) && $aForms.discount_type == 'percentage'}{$aForms.discount_percentage}{elseif isset($aForms) && $aForms.discount_type == 'amount'}{$aForms.discount_price}{else}0{/if}"/>

                                    <div class="input-group-addon">
                                        <select name="val[discount_type]" id="ynstore_discount_type">
                                            <option value="amount" {if isset($aForms) && $aForms.discount_type == 'amount'}selected{/if}>{$aCurrentCurrencies.0.currency_id} ({_p var='ynsocialstore.fixed_amount'})</option>
                                            <option value="percentage" {if isset($aForms) && $aForms.discount_type == 'percentage'}selected{/if}>{_p var='ynsocialstore.percentage'}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label for="selling_price">{_p var='ynsocialstore.selling_price'} ({$aCurrentCurrencies.0.currency_id}):</label>
                                <input type="text" class="form-control" name="val[selling_price]" id="selling_price" readonly value="{value type='input' id='selling_price'}">
                            </div>
                        </div>
                    </div>

                    <label for="discount_period">{_p var='ynsocialstore.discount_period'}</label>
                    <div class="row">
                        <div class="{if isset($aForms.discount_timeless) && $aForms.discount_timeless}hide{/if}" id="ynstore_discount_time">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 ynstore-paddingright-5">
                                        <div class="form-group">
                                            <div class="ynstore_start_time" class="form-control">
                                                <div class="input-group">
                                                    <div class="btn input-group-addon">{_p('From')}</div>
                                                    {select_date prefix='start_time_' id='_from' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true}
                                                    <div class="btn input-group-addon js_datepicker_image" onclick="$('.ynstore_start_time .js_date_picker').focus();"><i class="ico ico-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-6 col-xs-12 ynstore-paddingleft-5">
                                        <div class="form-group">
                                            <div class="ynstore_end_time">
                                                <div class="input-group">
                                                    <div class="btn input-group-addon">{_p('To')}</div>
                                                    {select_date prefix='end_time_' id='_end_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true }
                                                    <div class="btn input-group-addon js_datepicker_image" onclick="$('.ynstore_end_time .js_date_picker').focus();"><i class="ico ico-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                           <div class="form-group">
                              <div class="checkbox">
                                 <label>
                                    <input type="checkbox" class="" value="1" name="val[discount_timeless]" onclick="ynsocialstore.checkNoDefineDiscountPeriod($(this));" {if isset($aForms) && $aForms.discount_timeless == 1}checked{/if}>
                                    {_p var='ynsocialstore.no_definition'}
                                 </label>
                              </div>
                           </div>
                         </div>
                    </div>
                </div>

                <div class="ynstore-product-add-block {if isset($aForms) && $aForms.product_type == 'digital'}hide{/if}">
                    <h3>{_p var='ynsocialstore.inventory'}</h3>
                    <div id="ynstore_product_inventory" class="">
                        <div class="form-group">
                            <label for="inventory">{_p var='ynsocialstore.enable_inventory'}</label>

                            <div class="item_is_active_holder ynstore_inventory_enable">
                                {if isset($aForms.enable_inventory) && !$aForms.enable_inventory}
                                <span class="js_item_active item_is_active"><input type="radio" class="checkbox"  name="val[enable_inventory]" value="1"> {_p var='ynsocialstore.yes'}</span>
                                <span class="js_item_active item_is_not_active"><input type="radio" class="checkbox" name="val[enable_inventory]" checked="checked" value="0"> {_p var='ynsocialstore.no'}</span>
                                {else}
                                <span class="js_item_active item_is_active"><input type="radio" class="checkbox" checked="checked" name="val[enable_inventory]" value="1"> {_p var='ynsocialstore.yes'}</span>
                                <span class="js_item_active item_is_not_active"><input type="radio" class="checkbox"  name="val[enable_inventory]" value="0"> {_p var='ynsocialstore.no'}</span>
                                {/if}
                            </div>
                            <br/>
                            <span class="extra_info">{_p var='ynsocialstore.noti_about_inventory_enable'}</span>
                            <br/>
                            <span class="extra_info">{_p var='ynsocialstore.0_or_blank_mean_unlimited'}</span>
                        </div>

                        <div id="ynstore_product_inventory_detail" class="{if isset($aForms.enable_inventory) && !$aForms.enable_inventory}hide{/if}">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 ynstore-paddingright-5">
                                    <div class="form-group">
                                        <label for="min_quantily">{_p var='ynsocialstore.minimum_order_quantity'}</label>
                                        <input type="text" name="val[min_order]" id="min_order" value="{value type='input' id='min_order'}" maxlength="10">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 ynstore-paddingleft-5">
                                    <div class="form-group">
                                        <label for="max_quantily">{_p var='ynsocialstore.maximum_order_quantity_per_user'}</label>
                                        <input type="text" name="val[max_order]" id="max_order" value="{value type='input' id='max_order'}" maxlength="10">
                                    </div>
                                </div>

                            </div>
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="product_quantity">{_p var='ynsocialstore.in_stock_quantity'}</label>
                                    <input type="text" name="val[product_quantity_main]" id="product_quantity_main" value="{value type='input' id='product_quantity_main'}" maxlength="10">
                                 </div>
                              </div>
                           </div>

                            <div class="form-group">
                                <label for="inventory">{_p var='ynsocialstore.auto_temporarily_close_product_when_out_of_stock'}</label>
                                <div class="item_is_active_holder">
                                    {if isset($aForms.auto_close) && !$aForms.auto_close}
                                    <span class="js_item_active item_is_active"><input type="radio" class="checkbox"  name="val[auto_close]" value="1"> {_p var='ynsocialstore.yes'}</span>
                                    <span class="js_item_active item_is_not_active"><input type="radio" class="checkbox" name="val[auto_close]" checked="checked" value="0"> {_p var='ynsocialstore.no'}</span>
                                    {else}
                                    <span class="js_item_active item_is_active"><input type="radio" class="checkbox" checked="checked" name="val[auto_close]" value="1"> {_p var='ynsocialstore.yes'}</span>
                                    <span class="js_item_active item_is_not_active"><input type="radio" class="checkbox"  name="val[auto_close]" value="0"> {_p var='ynsocialstore.no'}</span>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="ynstore-product-add-block {if isset($aForms) && $aForms.product_type == 'physical' || (!isset($aForms))}hide{/if}">
                    <h3>{_p var='ynsocialstore.the_product'}</h3>
                    <div id="ynstore_product_link" class="">

                        <div class="form-group">
                            <label for="product_quantity">{_p var='ynsocialstore.link_download'}</label>
                            <input type="text" name="val[link_download]" id="link_download" value="{if isset($aForms) && !empty($aForms.link)}{$aForms.link}{/if}">
                        </div>
                    </div>
                </div>

                <div id="ynstore_customfield_category">
                </div>

                <div class="ynstore-product-add-block ynstore-lastitem">
                    {if (!$bIsEdit && (Phpfox::getUserParam('ynsocialstore.can_feature_own_product') || Phpfox::getUserParam('ynsocialstore.can_feature_product'))) || ($bIsEdit && Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('ynsocialstore.can_feature_own_product')) || ($bIsEdit && Phpfox::getUserParam('ynsocialstore.can_feature_product'))}
                        <h3>{_p var='ynsocialstore.feature'}</h3>
                        <div class="form-group {if isset($sModule) && $sModule == 'pages'}hide{/if}">
                    {else}
                        <div class="{if isset($sModule) && $sModule == 'pages'}hide{/if}">
                    {/if}
                        {if isset($aForms) && $aForms.feature_end_time > 0}
                        <div class="extra_info">
                            {if isset($aForms.is_unlimited_feature) && $aForms.is_unlimited_feature}
                            {_p var='ynsocialstore.note_this_product_is_featured_unlimited_time'}
                            {else}
                            {_p var='ynsocialstore.note_this_product_is_featured_until_expire_date' expire_date=$aForms.expire_feature_day}
                            {/if}
                        </div>
                        {/if}
                        {if (!$bIsEdit && (Phpfox::getUserParam('ynsocialstore.can_feature_own_product') || Phpfox::getUserParam('ynsocialstore.can_feature_product'))) || ($bIsEdit && Phpfox::getUserId() == $aForms.user_id && Phpfox::getUserParam('ynsocialstore.can_feature_own_product')) || ($bIsEdit && Phpfox::getUserParam('ynsocialstore.can_feature_product'))}
                            <div class="ynstore-product-cal-block">
                                <label id="ynstore_add_product_feature_fee">
                                {if $aPackage !== null}
                                    {_p var='ynsocialstore.feature_this_product_symbol_feature_fee_day' symbol=$aCurrentCurrencies.0.symbol feature_fee=$aPackage.feature_product_fee}
                                {else}
                                    {_p var='ynsocialstore.feature_this_product_symbol_feature_fee_day' symbol=$aCurrentCurrencies.0.symbol feature_fee=0}
                                {/if}
                                </label>

                                <div class="ynstore-product-cal">
                                    <button class="btn btn-default" id="ynstore_minus_day" data-type="minus" onclick="ynsocialstore.changeNumberOfFeatureDays(this);">-</button>

                                    <span class="ynstore-product-cal-combine">
                                        <input class="" id="ynsocialstore_feature_number_days" type="text" name="val[feature_number_days]" value="0">
                                        <span id="ynsocialstore_number_unit">{_p var='ynsocialstore.l_day_s'}</span>
                                    </span>

                                    <button class="btn btn-default" id="ynstore_add_day" data-type="add" onclick="ynsocialstore.changeNumberOfFeatureDays(this);">+</button>

                                    <span class="ynstore-product-result">
                                        =
                                        &nbsp;
                                        <b>{$aCurrentCurrencies.0.symbol}</b>
                                        <label class="" id="ynsocialstore_feature_fee_total"><b>0</b></label>
                                        <div class="help-block hide" id="ynstore_currency_id"><b>{$aCurrentCurrencies.0.symbol}</b></div>
                                    </span>
                                </div>

                            </div>
                        {else}
                        <input class="form-control" id="ynsocialstore_feature_number_days" type="hidden" name="val[feature_number_days]" value="0" size="10">
                        {/if}
                    </div>

                    {if empty($sModule) && Phpfox::isModule('privacy')}
                    <div class="form-group-follow">
                        <label for="">
                            {_p var='ynsocialstore.privacy'}:
                        </label>
                        {module name='privacy.form' privacy_name='privacy' privacy_info='ynsocialstore.control_who_can_see_this_product'}
                    </div>
                    {/if}
                </div>

                <div id="ynsocialstore_submit_buttons">
                    {if !$bIsEdit}
                        <input id="ynsocialstore_submit" type="submit" class="button btn btn-primary" value="{_p var='ynsocialstore.publish'}" name="val[create]"/>
                        <input id="ynsocialstore_submit_draft" type="submit" class="button btn btn-default" value="{_p var='ynsocialstore.save_as_draft'}" name="val[draft]" />
                    {else}
                        {if $bIsEdit && $aForms.product_status == 'draft'}
                            <input id="ynsocialstore_submit" type="submit" class="button btn btn-primary" value="{_p var='ynsocialstore.publish'}" name="val[create]"/>
                        {/if}
                            <input id="ynsocialstore_submit" type="submit" class="button btn btn-primary" value="{_p var='ynsocialstore.update'}" name="val[update]"/>
                        {/if}
                    {if PHPFOX_IS_AJAX}
                        <button id="ynsocialstore_back" type="button" class="btn btn-default _a_back breadcrumbs-list" value="{_p var='ynsocialstore.back'}" name="val[back]">
                            {_p var='ynsocialstore.back'}
                        </button>
                    {else}
                        <a href="{if $bIsEdit}{$detailUrl}{else}{url link='ynsocialstore'}{/if}" class="btn btn-default">{_p var='ynsocialstore.back'}</a>
                    {/if}
                </div>
            </form>
        </div>
    {/if}
{/if}
{if PHPFOX_IS_AJAX_PAGE}
{literal}
<script type="text/javascript">
    $Behavior.globalInit();
    {/literal}
    {if $bIsEdit}
        $Behavior.ynsocialstoreEditCategory();
    {/if}
    {literal}
    ynsocialstore.initAddProduct();
</script>
{/literal}
{/if}
