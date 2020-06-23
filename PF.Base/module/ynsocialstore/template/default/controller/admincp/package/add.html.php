<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 9/30/16
 * Time: 6:44 PM
 */

?>
{$sCreateJs}

<form method="POST" enctype="multipart/form-data" id="js_add_package_form" name="js_add_package_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $bIsEdit}
                    {_p var='ynsocialstore.edit_a_package'}
                {else}
                    {_p var='ynsocialstore.add_new_package'}
                {/if}
            </div>
        </div>

        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.package_id}"></div>
            {/if}
            <div class="form-group">
                <label>{required}{_p var='ynsocialstore.package_name'}</label>
                <input class="form-control" type="text" name="val[name]" id="name" value="{value type='input' id='name'}" />
            </div>


            <div class="form-group">
                <label>{required}{_p var='ynsocialstore.valid_period'}</label>
                <div class="row">
                    <div class="col-md-3 col-xs-8">
                        <input class="form-control" type="number" name="val[expire_number]" id="expire_number" value="{value type='input' id='expire_number'}">
                    </div>
                    <div class="col-md-1 col-xs-4">
                        <p class="help-block">
                            {_p var='ynsocialstore.day_s'}
                        </p>
                    </div>
                </div>
                <p class="help-block"><i>{_p var='ynsocialstore.enter_a_numeric_value_0_means_never_expired'}</i></p>
            </div>

            <div class="form-group">
                <label>{required}{_p var='ynsocialstore.package_fee'}</label>
                <div class="row">
                    <div class="col-md-3 col-xs-8">
                        <input class="form-control" type="text" name="val[fee]" id="fee" value="{value type='input' id='fee'}">
                    </div>
                    <div class="col-md-1 col-xs-4">
                        {$sDefaultSymbol}
                    </div>
                </div>
                <p class="help-block"><i>{_p var='ynsocialstore.enter_a_numeric_value_0_if_you_want_this_package_to_be_free'}</i></p>
            </div>

            <div class="form-group">
                <label>{required}{_p var='ynsocialstore.available_themes_for_this_package'}</label>
                <div class="row">
                    <div class="col-md-4">
                        <div>
                            <a class="item-image" href="{$core_path}module/ynsocialstore/static/image/theme_1.png" target="_blank">
                                <img src="{$core_path}module/ynsocialstore/static/image/theme_1.png">
                            </a>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="1" name="val[themes][]"
                                {if isset($aForms.themes) && is_array($aForms.themes) && in_array('1', $aForms.themes)}
                                    checked="checked"
                                {elseif isset($aForms.themes) && is_string($aForms.themes) && strpos($aForms.themes, '1')}
                                    checked="checked"
                                {/if}> {_p var='Theme 1'}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <a class="item-image" href="{$core_path}/module/ynsocialstore/static/image/theme_2.png" target="_blank">
                                <img src="{$core_path}module/ynsocialstore/static/image/theme_2.png">
                            </a>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="2" name="val[themes][]"
                                {if isset($aForms.themes) && is_array($aForms.themes) && in_array('2', $aForms.themes)}
                                    checked="checked"
                                {elseif isset($aForms.themes) && is_string($aForms.themes) && strpos($aForms.themes, '2')}
                                    checked="checked"
                                {/if}> {_p var='Theme 2'}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{required}{_p var='ynsocialstore.maximum_number_of_product_allowed_to_create'}</label>
                <input class="form-control" type="number" name="val[max_products]" id="max_products" value="{value type='input' id='max_products'}">
                <p class="help-block"><i>{_p var='ynsocialstore.if_blank_or_set_to_0_the_number_of_product_is_unlimited'}</i></p>
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.maximum_number_of_photo_can_add_to_each_product'}</label>
                <input class="form-control" type="number" name="val[max_photo_per_product]" id="max_photo_per_product" value="{if isset($aForms.max_photo_per_product)}{$aForms.max_photo_per_product}{/if}"/>
                <p class="help-block"><i>{_p var='ynsocialstore.if_blank_or_set_to_0_the_number_of_photos_is_unlimited'}</i></p>
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.fee_for_feature_store'}</label>
                <div class="row">
                    <div class="col-md-3">
                        <input class="form-control" type="text" name="val[feature_store_fee]" id="feature_store_fee" value="{value type='input' id='feature_store_fee' default='1.00'}">
                    </div>
                    <div class="col-md-1">
                        <p class="help-block">{$sDefaultSymbol}/{_p var='ynsocialstore.day'}</p>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.fee_for_featuring_products_in_store'}</label>
                <div class="row">
                    <div class="col-md-3">
                        <input class="form-control" type="text" name="val[feature_product_fee]" id="feature_product_fee" value="{value type='input' id='feature_product_fee' default='1.00'}">
                    </div>
                    <div class="col-md-1">
                        <p class="help-block">{$sDefaultSymbol}/{_p var='ynsocialstore.product'}/{_p var='ynsocialstore.day'}</p>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.allow_changing_store_theme'}</label>
                <div class="item_is_active_holder {if !empty($aForms.theme_editable)} item_selection_active {else} item_selection_not_active {/if}">
                    <span class="js_item_active item_is_active">
                        <input type="radio" value="1" name="val[theme_editable]" {if !empty($aForms.theme_editable)} checked="checked" {/if}> {_p var='Yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" value="0" name="val[theme_editable]" {if empty($aForms.theme_editable)} checked="checked" {/if}> {_p var='No'}
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.allow_add_attribute_to_products_in_store'}</label>
                <div class="item_is_active_holder {if !empty($aForms.enable_attribute)} item_selection_active {else} item_selection_not_active {/if}">
                    <span class="js_item_active item_is_active">
                        <input type="radio" value="1" name="val[enable_attribute]" {if !empty($aForms.enable_attribute)} checked="checked" {/if}> {_p var='Yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" value="0" name="val[enable_attribute]" {if empty($aForms.enable_attribute)} checked="checked" {/if}> {_p var='No'}
                    </span>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{_p var='ynsocialstore.save'}</button>
        </div>
    </div>
</form>
