<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<form method="post" enctype="multipart/form-data" id="js_add_package_form" name="js_add_package_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {if $bIsEdit}
                    {phrase var='edit_a_package'}
                {else}
                    {phrase var='add_new_package'}
                {/if}
            </div>
        </div>

        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.package_id}"/></div>
            {/if}
            <div class="form-group">
                <label for="name">{required}{phrase var='package_name'}</label>
                <input type="text" name="val[name]" id="name" class="form-control" value="{value type='input' id='name'}" maxlength="255">
            </div>

            <div class="form-group">
                <label for="">{required}{phrase var='valid_period'}</label>
                <p class="help-block"><i>{phrase var='enter_a_numeric_value'}</i></p>
                <div class="row">
                    <div class="col-md-8">
                        <input class="form-control" type="number" name="val[expire_number]" id="expire_number" value="{value type='input' id='expire_number'}"/>
                    </div>
                    <div class="col-md-4">
                        <select name="val[expire_type]" id="expire_type" class="form-control">
                            <option value="1" {value type='select' id='expire_type' default=1}>{phrase var='day'}</option>
                            <option value="2" {value type='select' id='expire_type' default=2}>{phrase var='week'}</option>
                            <option value="3" {value type='select' id='expire_type' default=3}>{phrase var='month'}</option>
                            <option value="0" {value type='select' id='expire_type' default=0}>{phrase var='never_expired'}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="fee">{required}{phrase var='package_fee'} ({$aCurrentCurrencies.0.symbol})</label>
                <p class="help-block"><i>{phrase var='enter_a_numeric_value_0_if_you_want_this_package_to_be_free'}</i></p>
                <input class="form-control" type="text" name="val[fee]" id="fee" value="{value type='input' id='fee'}">
            </div>

            <div class="form-group">
                <label for="max_cover_photo">{required}{phrase var='maximum_cover_photos_can_be_displayed'}</label>
                <input class="form-control" type="number" name="val[max_cover_photo]" id="max_cover_photo" value="{value type='input' id='max_cover_photo'}"/>
            </div>

            <div class="form-group">
                <label for="themes">{required}{phrase var='available_themes_for_this_package'}</label>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <a class="item-image" href="{$core_path}module/directory/static/image/theme_1.png" target="_blank">
                                <img src="{$core_path}module/directory/static/image/theme_1.png">
                            </a>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="1" name="val[themes][]"
                                {if isset($aForms.themes) && count($aForms.themes) > 0}
                                    {foreach from=$aForms.themes key=Id item=theme}
                                        {if isset($theme.theme_id) && $theme.theme_id == 1}
                                            checked="checked"
                                        {elseif $theme == 1}
                                            checked="checked"
                                        {/if}
                                    {/foreach}
                                {/if}
                                > {_p var='Theme 1'}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <a class="item-image" href="{$core_path}/module/directory/static/image/theme_2.png" target="_blank">
                                <img src="{$core_path}module/directory/static/image/theme_2.png">
                            </a>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" value="2" name="val[themes][]"
                                {if isset($aForms.themes) && count($aForms.themes) > 0}
                                    {foreach from=$aForms.themes key=Id item=theme}
                                        {if isset($theme.theme_id) && $theme.theme_id == 2}
                                            checked="checked"
                                        {elseif $theme == 2}
                                            checked="checked"
                                        {/if}
                                    {/foreach}
                                {/if}
                            > {_p var='Theme 2'}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="">{required}{phrase var='modules_supported'}</label>
                <div class="checkbox">
                    <label><input type="checkbox"  value="1" id='select_all_modules'><i>{phrase var='select_all'}</i></label>
                </div>
                <ul>
                {foreach from=$aModules key=Id item=aModule}
                    {if $aModule.module_type == 'module'}
                    <li class="">
                        <input type="checkbox" name="val[modules][]" class='module_checkbox' value="{$aModule.module_id}"
                            {if isset($aForms.modules) && count($aForms.modules) > 0}
                                {foreach from=$aForms.modules key=Id item=module}
                                    {if isset($module.module_id) && $module.module_id == $aModule.module_id}
                                        checked="checked"
                                    {elseif $module == $aModule.module_id}
                                        checked="checked"
                                    {/if}
                                {/foreach}
                            {elseif !$bIsEdit}
                                checked="checked"
                            {/if}
                        > {$aModule.module_phrase|convert}
                    </li>
                    {/if}
                {/foreach}
                </ul>
            </div>

            <div class="form-group">
                <label>{phrase var='features_supported'}</label>
                <div class="checkbox">
                    <label><input type="checkbox"  value="1" id='select_all_feature_support'><i>{phrase var='select_all'}</i></label>
                </div>
                <ul>
                {foreach from=$aPackageSettings key=Id item=aPackageSetting}
                    <li class="">
                        <input type="checkbox" name="val[settings][]" class='feature_checkbox' value="{$aPackageSetting.setting_id}"
                           {if isset($aForms.settings)}
                                {if count($aForms.settings) > 0 }
                                    {foreach from=$aForms.settings key=Id item=setting}
                                        {if isset($setting.setting_id) && $setting.setting_id == $aPackageSetting.setting_id}
                                            checked="checked"
                                        {elseif $setting == $aPackageSetting.setting_id}
                                            checked="checked"
                                        {/if}
                                    {/foreach}
                                {/if}
                            {elseif !$bIsEdit}
                                checked="checked"
                            {/if}
                        > {$aPackageSetting.setting_phrase|convert}
                    </li>
                {/foreach}
                </ul>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" name="val[submit]" value="{phrase var='save'}" class="btn btn-primary" />
        </div>
    </div>
</form>


{literal}
<script type="text/javascript">
    $Behavior.directory_check_all_package = function () {
        number_module_support = 11;
        number_feature_support = 6;

        if ($('.module_checkbox:checked').length == number_module_support) {
            $('#select_all_modules').prop('checked', true);
        } else {
            $('#select_all_modules').prop('checked', false);
        }


        if ($('.feature_checkbox:checked').length == number_feature_support) {
            $('#select_all_feature_support').prop('checked', true);
        } else {
            $('#select_all_feature_support').prop('checked', false);
        }

        $('.module_checkbox').on('click', function () {
            if ($('.module_checkbox:checked').length == number_module_support) {
                $('#select_all_modules').prop('checked', true);
            }
            else {
                $('#select_all_modules').prop('checked', false);
            }
        });

        $('.feature_checkbox').on('click', function () {
            if ($('.feature_checkbox:checked').length == number_feature_support) {
                $('#select_all_feature_support').prop('checked', true);
            }
            else {
                $('#select_all_feature_support').prop('checked', false);
            }
        });


        $('#select_all_modules').on('click', function () {
            if ($('#select_all_modules').prop('checked')) {
                $('.module_checkbox').prop('checked', true);
            }
            else {
                $('.module_checkbox').prop('checked', false);
            }
        });

        $('#select_all_feature_support').on('click', function () {
            if ($('#select_all_feature_support').prop('checked')) {
                $('.feature_checkbox').prop('checked', true);
            }
            else {
                $('.feature_checkbox').prop('checked', false);
            }
        });
    }
</script>
{/literal}
