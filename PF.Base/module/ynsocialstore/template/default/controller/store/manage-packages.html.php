<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 10:40 AM
 */
?>
{if !empty($sError)}
{$sError}
{else}
<div id="manage_packages_page">
    <div id="ynsocialstore_current_packages" class="ynstore-packages-block">
        <div class="ynstore-package-items">
            <div class="ynstore-package-item">
                <div class="ynstore-package-left">
                    <div class="ynstore-name">{$aPackageStore.name}</div>
                    <div class="ynstore-price">
                        {$aPackageStore.fee|currency:$defaultCurrency}
                    </div>

                    <div class="ynstore-duration">
                        {if $aPackageStore.expire_number == 0}
                            {_p var='ynsocialstore.never_expired'}
                        {else}
                            {$aPackageStore.expire_number} {_p var='ynsocialstore.day_s'}
                        {/if}
                    </div>
                </div>

                <div class="ynstore-package-right">
                    <ul>
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {if $aPackageStore.max_products > 0}
                            {$aPackageStore.max_products} {_p var='ynsocialstore.products_can_be_created'}

                            {elseif $aPackageStore.max_products == 0}
                            {_p var='ynsocialstore.number_of_product_is_unlimited'}
                            {/if}

                        </li>
                        {if $aPackageStore.theme_editable}
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {_p var='ynsocialstore.support_changing_store_theme'}
                        </li>
                        {/if}
                        {if $aPackageStore.enable_attribute}
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {_p var='ynsocialstore.support_attribute_to_product_in_store'}
                        </li>
                        {/if}
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {_p var='ynsocialstore.fee_for_featuring_store'}:
                            <b>{$aPackageStore.feature_store_fee|currency:$defaultCurrency}/{_p var='ynsocialstore.day'}</b>
                        </li>
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {_p var='ynsocialstore.fee_for_featuring_products_in_store'}:
                            <b>{$aPackageStore.feature_product_fee|currency:$defaultCurrency}/{_p var='ynsocialstore.product'}/{_p var='ynsocialstore.day'}</b>
                        </li>
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {_p var='ynsocialstore.package_applied_at'}:
                            <b>{$aStore.time_stamp|date:'core.global_update_time'}</b>
                        </li>
                        {if $aStore.expire_time != '4294967295'}
                        <li>
                            <i class="ico ico-angle-right"></i>
                            {_p var='ynsocialstore.package_expired_at'}:
                            <b>{$aStore.expire_time|date:'core.global_update_time'}</b>
                        </li>
                        {/if}
                    </ul>
                    {if $aStore.expire_time != '4294967295'}
                    <div class="ynstore-description">
                        <span>
                            {_p var='ynsocialstore.receive_renewal_notification_before_the_exp'}:
                        </span>
                        <select class="form-control" id="renewal_notification" onchange="ynsocialstore.changeReNewBefore(this, {$aStore.store_id})" name="val[renewal_notification]">
                            <option value="1" {if isset($aStore.renew_before) && $aStore.renew_before==1}selected{/if}>{_p var='ynsocialstore.1_day'}</option>
                            <option value="7" {if isset($aStore.renew_before) && $aStore.renew_before==7}selected{/if}>{_p var='ynsocialstore.1_week'}</option>
                            <option value="30" {if isset($aStore.renew_before) && $aStore.renew_before==30}selected{/if}>{_p var='ynsocialstore.1_month'}</option>
                            <option value="0" {if isset($aStore.renew_before) && $aStore.renew_before==0}selected{/if}>{_p var='ynsocialstore.don_t_receive'}</option>
                        </select>
                    </div>
                    {/if}
                </div>

            </div>
        </div>
    </div>

    <div class="ynstore-package-btn-buy clearfix">
        {if $aStore.status == 'draft'}<button onclick="ynsocialstore.confirmChooseRenewPackage(0,{$aStore.store_id},{$aPackageStore.package_id})" data-packageid="{$aPackageStore.package_id}" class="btn btn-primary">{_p var='ynsocialstore.buy_current_packages_and_publish'}</button>{/if}
        <button onclick="ynstoreToggleBuyNewPackages(this)" class="btn btn-primary pull-right" >{_p var='ynsocialstore.buy_new_package'}</button>
    </div>

    <div id="ynsocialstore_storetype" class="ynstore-packages-block" style="display: none">
        <div class="ynsocialstore-hiddenblock">
            <input type="hidden" value="storetype" id="ynsocialstore_pagename" name="ynsocialstore_pagename">
        </div>

        <div class="ynsocialstore-packages" id="ynsocialstore_package">

            {if count($aPackages) > 0}
            <div class="ynstore-title">{_p var='ynsocialstore.all_packages'}</div>
            <div class="ynstore-package-items">
                {foreach from=$aPackages key=Id item=aPackage}
                <div class="ynstore-package-item">
                    <div class="ynstore-choose-btn" onclick="ynsocialstore.confirmChooseRenewPackage({if $aPackage.is_different}{$aPackage.is_different}{else}0{/if},{$aStore.store_id},{$aPackage.package_id})" class="btn btn-primary" data-packageid="{$aPackage.package_id}">
                        <span>
                            {_p var='ynsocialstore.choose_this_package'}
                            <i class="ico ico-angle-right"></i>
                        </span>
                    </div>
                    
                    <div class="ynstore-package-left">
                        <div class="ynstore-name">{$aPackage.name}</div>
    
                        <div class="ynstore-price">
                            {$aPackage.fee_display}
                        </div>

                        <div class="ynstore-duration">
                            {if $aPackage.expire_number == 0}
                            {_p var='ynsocialstore.never_expired'}
                            {else}
                            {$aPackage.expire_number} {_p var='ynsocialstore.day_s'}
                            {/if}
                        </div>
                    </div>

                    <div class="ynstore-package-right">
                        <ul>
                            <li>
                                <i class="ico ico-angle-right"></i>
                                {if $aPackage.max_products > 0}
                                {$aPackage.max_products} {_p var='ynsocialstore.products_can_be_created'}

                                {elseif $aPackage.max_products == 0}
                                {_p var='ynsocialstore.number_of_product_is_unlimited'}
                                {/if}

                            </li>
                            {if $aPackage.theme_editable}
                            <li>
                                <i class="ico ico-angle-right"></i>
                                {_p var='ynsocialstore.support_changing_store_theme'}
                            </li>
                            {/if}
                            {if $aPackage.enable_attribute}
                            <li>
                                <i class="ico ico-angle-right"></i>
                                {_p var='ynsocialstore.support_attribute_to_product_in_store'}
                            </li>
                            {/if}
                            <li>
                                <i class="ico ico-angle-right"></i>
                                {_p var='ynsocialstore.fee_for_featuring_store'}:
                                <b>{$aPackage.feature_store_fee_display}/{_p var='ynsocialstore.day'}</b>
                            </li>
                            <li>
                                <i class="ico ico-angle-right"></i>
                                {_p var='ynsocialstore.fee_for_featuring_products_in_store'}:
                                <b>{$aPackage.feature_product_fee_display} / {_p var='ynsocialstore.product'}/{_p var='ynsocialstore.day'}</b>
                            </li>
                        </ul>
                    </div>
                </div>
                {foreachelse}
                {_p var='ynsocialstore.no_packages_found'}
                {/foreach}
            </div>
            {else}
            {_p var='ynsocialstore.no_packages_found'}
            {/if}
        </div>
        {if !empty($sError)}
        {$sError}
        {/if}
    </div>
</div>

{literal}
<script type="text/javascript">
    function ynstoreToggleBuyNewPackages(ele) {
        if($(ele).hasClass('showAllPackages'))
        {
            $('#ynsocialstore_storetype').hide();
        } else {
            $('#ynsocialstore_storetype').show();
        }

        $(ele).toggleClass('showAllPackages');
    }
</script>
{/literal}
{/if}