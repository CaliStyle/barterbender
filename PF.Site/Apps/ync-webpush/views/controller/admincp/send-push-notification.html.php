<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" id="js_ync_web_push_send_notification" enctype="multipart/form-data" action="{url link='current'}" name="js_ync_web_push_send_notification" onsubmit="$(this).find('#js_submit').prop('disabled', true); return true;">
    <input type="hidden" name="template" value="{$iTemplateId}">
    <input type="hidden" name="edit_id" value="{if !empty($iEditId)}{$iEditId}{else}0{/if}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <span style="color: {if !$bIsCompose}#000{else}#bbb9b9{/if}">{_p var='audience'}</span> <i class="ico ico-angle-double-right" style="font-size: 8px;"></i> <span style="color: {if $bIsCompose}#000{else}#bbb9b9{/if}">{_p var='message'}</span>
            </div>
        </div>
        {if !$bIsCompose && !$bIsEdit}
            <div class="panel-body">
                <label>{_p var='notification_will_be_sent_to'}</label>
                <div class="form-group">
                    <div class="radio">
                        <label for="js_audience_all"><input type="radio" name="val[audience_type]" class="js_audience_type" value="all" data-select-box="" id="js_audience_all" checked /> {_p var='all_subscribers'}</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="radio">
                        <label for="js_audience_id"><input type="radio" name="val[audience_type]" value="group" class="js_audience_type" data-select-box="#audience_id" id="js_audience_id" /> {_p var='specific_user_group'}</label>
                    </div>
                    <select name="val[audience_id]" id="audience_id" class="form-control js_audience_select" style="display: none;">
                        {foreach from=$aUserGroups key=iKey item=aGroup}
                            <option value="{$aGroup.user_group_id}">{_p var=$aGroup.title}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <div class="radio">
                        <label for="js_audience_title"><input type="radio" name="val[audience_type]" value="browser" class="js_audience_type" data-select-box="#audience_title" id="js_audience_title" /> {_p var='specific_browser'}</label>
                    </div>
                    <select name="val[audience_title]" id="audience_title" class="form-control js_audience_select" style="display: none;">
                        <option value="Chrome">Chrome</option>
                        <option value="Firefox">Firefox</option>
                    </select>
                </div>
                <div class="form-group">

                    <a href="{if !empty($iTemplateId)}{url link='admincp.yncwebpush.manage-subscribers' template=$iTemplateId}{else}{url link='admincp.yncwebpush.manage-subscribers'}{/if}">{_p var='more_specific_subscribers'}</a>
                </div>
            </div>
            <div class="panel-footer">
                <button name="val[select_audience]" value="select_audience" class="btn btn-primary">{_p var='next'}</button>
            </div>
        {else}
            {module name='yncwebpush.admincp.compose-notification' sType=$sType sTypeId=$sTypeId iTemplateId=$iTemplateId}
        {/if}
    </div>
</form>