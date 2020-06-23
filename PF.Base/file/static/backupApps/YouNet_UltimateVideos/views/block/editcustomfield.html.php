<div id="ajax-response-custom">
</div>
<form onsubmit="return onSubmitValid(this);" action="" method="post" id="js_add_group_name_form"
      name="js_add_group_name_form">

    <div class="panel panel-default">
        <div class="panel-heading">
            {if isset($bIsEditGroup) && $bIsEditGroup }
                {_p('edit_custom_field_groups')}
            {else}
                {_p('add_custom_field_groups')}
            {/if}
        </div>

        <div class="panel-body">
            <div class="form-group">
                {if isset($bIsEditGroup) && $bIsEditGroup }
                    <input type="hidden" name="val[group_id]" value="{$aGroup.group_id}"/>
                {/if}
                <label for="">
                    {required}{_p('group_name')}
                </label>
                {foreach from=$aLanguages item=aLanguage}
                    <input class="form-control" type="text" id="ynuv_group_name_edit"
                           name="val[group_name]{if isset($aLanguage.phrase_var_name)}[{$aLanguage.phrase_var_name}]{/if}[{$aLanguage.language_id}]{if isset($sMode)}[{$sMode}]{/if}"
                           value="{$aLanguage.post_value|htmlspecialchars}"/>
                    <div class="extra_info">{$aLanguage.title}</div>
                {/foreach}
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            {if isset($bIsEditGroup) && $bIsEditGroup }
            <div class="form-group">
                <label for="">
                    {_p('mapping_categories')}
                </label>
                <div id="mapping_categories">
                    {foreach from=$aCategories key=iKeyCate item=aCategory}
                        <div>
                            {if (($iKeyCate    ) % 3 == 0 ) }
                            <div>
                                {/if}
                                <input type="checkbox" name="val[categories][]" value="{$aCategory.category_id}"
                                        {if isset($bIsEditGroup) && $bIsEditGroup}
                                            {if in_array($aCategory.category_id,$aGroup.categories)}
                                                checked="checked"
                                            {/if}
                                        {/if}
                                > {softPhrase var=$aCategory.title}
                                {if (($iKeyCate + 1) % 3 == 0 || ($iKeyCate + 1) == $totalCategory) }
                            </div>
                            {/if}
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>

    {if $aGroup.customfield}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('custom_fields')}
            </div>
        </div>

        <div class="panel-body">
            <div class="table-responsive flex-sortable">
                <table class="table table-bordered">
                    <tr>
                        <th>{_p('custom_field_name')}</th>
                        <th class="t_center w120">{_p('Option')}</th>
                    </tr>
                    {foreach from=$aGroup.customfield key=iKey item=iField}
                        <tr>
                            <input type="hidden" name="val[customfield][]" value="{$iField.field_id}">
                            <td>{phrase var=$iField.phrase_var_name}</td>
                            <td class="t_center w120">
                                <a href="javascript:void(0)"
                                   onclick="editCustomField({$iField.field_id});">{_p('Edit')}</a>
                                /
                                <a href="javascript:void(0)"
                                   onclick="deleteCustomField({$iField.field_id},{$aGroup.group_id});">{_p('Delete')}</a>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
        {/if}

        <div class="js_mp_parent_holder panel-footer" id="js_mp_holder">
            {if isset($bIsEditGroup) && $bIsEditGroup}
                <input type="button" class="btn btn-primary"
                       onclick="tb_show('{_p('add_custom_field')}', $.ajaxBox('ultimatevideo.AdminAddCustomFieldBackEnd', 'height=300&width=300&action=add&iGroupId={$aGroup.group_id}')); return false;"
                       value="{_p('add_custom_field')}">
            {/if}
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary pull-right"/>
        </div>
        {/if}
    </div>
</form>

{literal}
<script type="text/javascript">
    function deleteCustomField(iCustomFieldId, iGroupId) {
        var message = oTranslations['are_you_sure'];
        $Core.jsConfirm({message: message}, function () {
            $.ajaxCall('ultimatevideo.AdminDeleteCustomField', 'iFieldId=' + iCustomFieldId + '&iGroupId=' + iGroupId, 'post');
        }, function () {
        });
        return false;
    }

    function editCustomField(iCustomFieldId) {
        tb_show('Edit Custom Field', $.ajaxBox('ultimatevideo.AdminAddCustomFieldBackEnd', 'height=300&amp;width=300&action=edit&id=' + iCustomFieldId + '&iGroupId=' +{/literal}{$aGroup.group_id}{literal}));
    }

    var MissingAddName = "{/literal}{_p('group_name_cannot_be_empty')}{literal}";

    function onSubmitValid(obj) {
        var error = false;
        $(".ynuv_add_group_name").each(function () {
            if ($(this).val() == "") {
                error = true;
            }
        })
        if (error == true) {
            $('#ajax-response-custom').html('<div class="error_message">' + MissingAddName + ' </div>');
            return false;
        } else {
            $.ajaxCall('ultimatevideo.AdminAddCustomFieldGroup', $(obj).serialize(), 'post');
            return false;
        }
        return true;
    }

</script>
{/literal}