<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<style type="text/css">
    .ync-schedule-time .select_date .js_datepicker_core{
        display: flex;
    }
    .ync-schedule-time .select_date .js_datepicker_selects {
        margin-left: 5px;
    }
    .ync-schedule-time .select_date .select-date-separator {
        margin-left: 5px;
    }
</style>
{/literal}

<div class="panel-body">
        {if !$bIsEdit}
            <textarea style="display: none" name="val[audience]">{$sTypeId}</textarea>
            <input type="hidden" name="val[audience_type]" value="{$sType}">
            <div class="form-group">
                <label>{_p var='choose_from_your_templates'}</label>
                <select class="form-control" name="val[template_id]" id="template_id" onchange="yncwebpush_admin.selectTemplate($(this));">
                    <option value="0">{_p var='select_template'}</option>
                    {if count($aTemplates)}
                        {foreach from=$aTemplates item=aTemplate}
                            <option value="{$aTemplate.template_id}" {if isset($iTemplateId) && $iTemplateId == $aTemplate.template_id}selected{/if}>{$aTemplate.template_name|clean}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
        {/if}
        <div id="js_template_detail">
            {template file='yncwebpush.block.admincp.add-template-info'}
        </div>
        <div id="js_save_template_holder">
            <div class="form-group">
                <label for="js_save_template">
                    <input type="checkbox" name="val[save_template]" class="save_template" value="save_template" data-select-box="" id="js_save_template" /> {_p var='also_save_this_notification_as_new_template_to_use_it_later'}
                </label>
            </div>
            <div class="form-group" id="js_save_template_name" style="display: none;">
                <label>{required}{_p var='template_name'}</label>
                <input type="text" class="form-control" name="val[template_name]" placeholder="{_p var='name_of_template'}"/>
            </div>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-warning disabled" id="preview_notification" disabled onclick="return yncwebpush_admin.previewNotification();">{_p var='preview'}</button>
            <button type="button" class="btn btn-success" id="js_set_schedule">{_p var='schedule'}</button>
            <input type="hidden" name="val[is_schedule]" value="{if $bIsEdit}1{else}0{/if}" id="js_is_schedule">
        </div>

        <div class="form-group ync-schedule-time" id="js_schedule_date_time" {if empty($iSchedule) && !$bIsEdit}style="display: none"{/if}>
            <label>{_p var='schedule_to_send_your_notification_on'}</label>
            <div style="position: relative;" class="">
                {select_date prefix='schedule_' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator='yncwebpush.time_separator'}
            </div>
        </div>

</div>
<div class="panel-footer">
    {if !$bIsEdit}
        <button class="btn btn-primary disabled" disabled name="val[send]" id="js_submit">{_p var='send'}</button>
        {if !$bPopup}
            <a href="{url link='admincp.yncwebpush.send-push-notification'}" class="btn btn-default">{_p var='back'}</a>
        {/if}
    {else}
        <button class="btn btn-primary disabled" disabled name="val[send]" id="js_submit">{_p var='send'}</button>
    {/if}
</div>
