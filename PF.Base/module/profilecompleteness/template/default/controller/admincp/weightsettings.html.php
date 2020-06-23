<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
{if $bSuccess}
    {literal}
    <style>
        .mode-edit {
            display: none;
        }
    </style>
    {/literal}
{else}
    {literal}
    <style>
        .mode-view {
            display: none;
        }
    </style>
    {/literal}
{/if}

<form method="post" action="{url link='admincp.profilecompleteness.weightsettings'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='admin_menu_weight_settings'}
            </div>
        </div>
        <div class="panel-body">
            <div class="alert alert-info">
                {phrase var='profilecompleteness.admin_message_weight_settings'}
            </div>
            <div class="profile-section">
                <h3>
                    {phrase var='profilecompleteness.profile_photo'}
                </h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td>{phrase var='profilecompleteness.profile_photo'}</td>
                                <td class="w220 mode-view"><b> (+{$aPhoto.user_image})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="user_image" name="user_image" value="{$aPhoto.user_image}"/></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="profile-section">
                <h3>
                    {phrase var='profilecompleteness.basic_information'}
                </h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td>{phrase var='profilecompleteness.location'}</td>
                                <td class="w220 mode-view"><b>(+{$aForms.country_iso})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="country_iso" name="val[country_iso]" value="{value type='input' id='country_iso'}"/></td>
                            </tr>
                            <tr>
                                <td>{phrase var='profilecompleteness.city'}</td>
                                <td class="w220 mode-view"><b>(+{$aForms.city_location})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="city_location" name="val[city_location]" value="{value type='input' id='city_location'}"/></td>
                            </tr>
                            <tr>
                                <td>{phrase var='profilecompleteness.zip_postal_code'}</td>
                                <td class="w220 mode-view"><b>(+{$aForms.postal_code})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="postal_code" name="val[postal_code]" value="{value type='input' id='postal_code'}"/></td>
                            </tr>
                            {if $settingdefault.cf_birthday}
                            <tr>
                                <td>{phrase var='profilecompleteness.date_of_birth'}</td>
                                <td class="w220 mode-view"><b>(+{$aForms.birthday})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="birthday" name="val[birthday]" value="{value type='input' id='birthday'}"/></td>
                            </tr>
                            {/if}
                            {if $settingdefault.cf_gender}
                            <tr>
                                <td>{phrase var='profilecompleteness.gender'}</td>
                                <td class="w220 mode-view"><b>(+{$aForms.gender})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="gender" name="val[gender]" value="{value type='input' id='gender'}"/></td>
                            </tr>
                            {/if}
                            {if $settingdefault.enable_relationship_status}
                            <tr>
                                <td>{phrase var='profilecompleteness.relationship_status'}</td>
                                <td class="w220 mode-view"><b>(+{$aForms.cf_relationship_status})</b></td>
                                <td class="w220 mode-edit"><input class="form-control" type="text" id="cf_relationship_status" name="val[cf_relationship_status]" value="{value type='input' id='cf_relationship_status'}"/></td>
                            </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
            {foreach from=$ListCustom key=KeyName item=CustomField}
                {if isset($CustomField.child) && count($CustomField.child) > 0 && $CustomField.child[0].group_id != 0 && $CustomField.is_active}
                <div class="profile-section">
                    <h3>
                        {phrase var=$CustomField.phrase_var_name}
                    </h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                            {foreach from=$CustomField.child item=child}
                                {if $child.is_active !=0}
                                    <tr>
                                        <td>{phrase var=$child.phrase_var_name}</td>
                                        <td class="w220 mode-view"><b>(+{$child.weight})</b></td>
                                        <td class="w220 mode-edit"><input class="form-control" type="text" id="{$child.field_name}" name="val[cf_{$child.field_name}]" value="{$child.weight}"/></td>
                                    </tr>
                                {/if}
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                {/if}
            {/foreach}
        </div>
        <div class="panel-footer">
            <button type="button" class="btn btn-primary mode-view" id="js_btn_edit">{phrase var='profilecompleteness.edit_weight_of_profile_fields'}</button>
            <button type="submit" class="btn btn-primary mode-edit">{_p var='submit'}</button>
            <button type="button" class="btn btn-danger mode-edit" id="js_btn_cancel">{_p var='cancel'}</button>
        </div>
    </div>
</form>

{literal}
<script>
    $Behavior.profile_completeness_init_settings = function () {
        $('#js_btn_edit').on('click', function () {
            $('.mode-edit').show();
            $('.mode-view').hide();
            return false;
        });
        $('#js_btn_cancel').on('click', function () {
            $('.mode-edit').hide();
            $('.mode-view').show();
            return false;
        })
    }
</script>
{/literal}