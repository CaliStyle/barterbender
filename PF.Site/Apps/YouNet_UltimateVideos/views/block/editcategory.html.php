<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="ajax-response-custom">
</div>
<form method="post" action="" onsubmit="return onSubmitValid(this);">
    {if $bIsEdit}
        <div><input type="hidden" id="ynuv_category_id" name="val[category_id]" value="{$aForms.category_id}"/></div>
        <div><input type="hidden" id="ynuv_parent_category_id" name="parent_id" value="{$aForms.parent_id}"/></div>
        <div><input type="hidden" name="val[name]" value="{$aForms.title}"/></div>
    {/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('ultimate_videos_category_detail')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{_p('parent_category')}:</label>
                <br>
                {if empty($selectBox)}
                    <select name="val[parent_id]">
                        <option value="">{_p('Select')}:</option>
                        {$sOptions}
                    </select>
                {else}
                    {$selectBox}
                {/if}
            </div>
            {foreach from=$aLanguages item=aLanguage}
                <div class="form-group">
                    {if $aLanguage.is_default}
                        <label>{_p('name_in')}&nbsp;<strong>{$aLanguage.title}</strong>:</label>
                        {assign var='value_name' value="name_"$aLanguage.language_id}
                        <input class="form-control" type="text" name="val[name_{$aLanguage.language_id}]"
                               value="{value id=$value_name type='input'}" size="30"/>
                    {else}
                        {assign var='value_name1' value="name_"$aLanguage.language_id}
                        <div class="clearfix collapse-placeholder">
                            <a role="button" data-cmd="core.toggle_placeholder">Name in other languages</a>
                            <div class="inner">
                                <p class="help-block">If the category is empty then its value will have the same value
                                    as default language.</p>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="val[name_{$aLanguage.language_id}]"
                                           value="{value id=$value_name1 type='input'}" size="30"
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            {/foreach}

        </div>

        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary"/>
        </div>
    </div>
</form>
{literal}
<script type="text/javascript">
    if ($('#ynuv_parent_category_id').length != 0) {
        var parentId = $('#ynuv_parent_category_id').val();
        var cateId = $('#ynuv_category_id').val();
        $('#js_mp_category_item_' + parentId).attr('selected', true);
        $("#js_mp_category_item_" + cateId).remove();
    }
    var MissingAddName = "{/literal}{_p('provide_a_category_name')}{literal}";

    function onSubmitValid(obj) {
        $.ajaxCall('ultimatevideo.AdminAddCategory', $(obj).serialize(), 'post');
        return false;
    }
</script>
{/literal}