<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form action="{url link='admincp.yncwebpush.manage-notifications'}" method="get">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='manage_notifications'}
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="">{_p var='notification_title'}</label>
                    <input type="text" name="val[title]"  class="form-control" value="{value type='input' id='title'}"/>
                </div>
                <div class="form-group col-sm-6">
                    <label for="">{_p var='notification_status'}</label>
                    <select name="val[status]" id="" class="form-control">
                        <option value="">{_p var='any'}</option>
                        <option value="sent" {if isset($aForms.status) && $aForms.status == 'sent'}selected{/if}>{_p var='sent_u'}</option>
                        <option value="sending" {if isset($aForms.status) && $aForms.status == 'sending'}selected{/if}>{_p var='sending_u'}</option>
                        <option value="scheduled" {if isset($aForms.status) && $aForms.status == 'scheduled'}selected{/if}>{_p var='scheduled_u'}</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="">{_p var='view_notifications_sent_from'}</label>
                    {select_date prefix='sent_from_' start_year='-1' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true}
                </div>
                <div class="form-group col-sm-3">
                    <label for="">{_p var='to'}</label>
                    {select_date prefix='sent_to_' start_year='-1' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true}
                </div>
                <div class="form-group col-sm-6">
                    <label for="">{_p var='view_notification_sent_to'}</label>
                    <select name="val[audience_type]" id="" class="form-control">
                        <option value="">{_p var='any'}</option>
                        <option value="all" {if isset($aForms.audience_type) && $aForms.audience_type == 'all'}selected{/if}>{_p var='all_subscribers'}</option>
                        <option value="group" {if isset($aForms.audience_type) && $aForms.audience_type == 'group'}selected{/if}>{_p var='specific_user_group'}</option>
                        <option value="browser" {if isset($aForms.audience_type) && $aForms.audience_type == 'browser'}selected{/if}>{_p var='specific_browser'}</option>
                        <option value="subscriber" {if isset($aForms.audience_type) && $aForms.audience_type == 'subscriber'}selected{/if}>{_p var='particular_subscribers'}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button class="btn btn-success">{_p var='search'}</button>
            <a class="btn btn-default" href="{url link='admincp.app' id='YNC_WebPush'}">{_p var='reset'}</a>
        </div>
    </div>

<div class="panel panel-default">
    {if count($aNotifications)}
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <th class="w20 js_checkbox">
                    <input type="checkbox" name="val[ids]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                </th>
                <th class="w80 t_center">{_p var='id'}</th>
                <th class="">{_p var='title'}</th>
                <th class="w220">{_p var='sent_time'}</th>
                <th class="w200">{_p var='sent_to'}</th>
                <th class="w140">{_p var='status'}</th>
                <th class="w220">{_p var='action'}</th>
            </thead>
            <tbody>
                {foreach from=$aNotifications item=aNotification}
                    <tr>
                        <td class="t_center js_checkbox">
                            {if $aNotification.status != 'sending'}
                                <input type="checkbox" name="ids[]" class="checkbox" value="{$aNotification.notification_id}" id="js_id_row{$aNotification.notification_id}" />
                            {/if}
                        </td>
                        <td class="w80 t_center"><a href="{url link='admincp.yncwebpush.notification-detail' id=$aNotification.notification_id}">{$aNotification.notification_id}</a></td>
                        <td>{$aNotification.title|clean}</td>
                        <td class="w220">{$aNotification.schedule_time|date:'yncwebpush.sent_time_stamp'}</td>
                        <td class="w200">
                            {if $aNotification.audience_type == 'group'}
                                {_p var='specific_user_group'}
                            {elseif $aNotification.audience_type == 'browser'}
                                {_p var='specific_browser'}
                            {elseif $aNotification.audience_type == 'all'}
                                {_p var='all_subscribers'}
                            {elseif $aNotification.audience_type == 'subscriber'}
                                {_p var='particular_subscribers'}
                            {/if}
                        </td>
                        <td class="w140">{_p var=$aNotification.status."_u"}</td>
                        <td class="w220">
                            {if $aNotification.status == 'sent'}
                                <a href="{url link='admincp.yncwebpush.manage-notifications' resend=$aNotification.notification_id}" data-message="{_p var='are_you_sure_this_action_will_create_a_new_notification_have_same_info_with_this_notification_and_send_it_immediately'}" class="sJsConfirm btn btn-primary btn-xs">{_p var='resend_u'}</a>
                                <a href="{url link='admincp.yncwebpush.manage-notifications' delete=$aNotification.notification_id}" data-message="{_p var='are_you_sure_you_want_to_delete_this_notification'}" class="sJsConfirm btn btn-danger btn-xs">{_p var='delete'}</a>
                            {elseif $aNotification.status == 'sending'}
                                <a href="{url link='admincp.yncwebpush.manage-notifications' stop=$aNotification.notification_id}" class="sJsConfirm btn btn-warning btn-xs">{_p var='stop_u'}</a>
                            {elseif $aNotification.status == 'scheduled'}
                                <a href="{url link='admincp.yncwebpush.manage-notifications' now=$aNotification.notification_id}" class="sJsConfirm btn btn-success btn-xs">{_p var='send_now_u'}</a>
                            <a href="{url link='admincp.yncwebpush.send-push-notification' edit_id=$aNotification.notification_id}" class="popup btn btn-info btn-xs">{_p var='edit'}</a>
                                <a href="{url link='admincp.yncwebpush.manage-notifications' delete=$aNotification.notification_id}" data-message="{_p var='are_you_sure_you_want_to_delete_this_notification'}" class="sJsConfirm btn btn-danger btn-xs">{_p var='delete'}</a>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <input type="submit" name="val[delete_selected]" id="delete_selected" data-message="{_p var='are_you_sure_you_want_to_delete_selected_notification_s'}" disabled value="{_p('delete_selected')}" class="sJsConfirm sJsCheckBoxButton btn btn-danger disabled"/>
    </div>
    {else}
    <div class="panel-body">
        {if $bIsSearch}
            <div class="extra_info">
                {_p var='no_notification_found'}
            </div>
        {else}
            <div class="extra_info" style="min-height: 300px;align-items: center;text-align: center;padding-top: 50px;">
                <div>
                    <i class="ico ico-paperplane" style="font-size: 100px"></i>
                </div>
                {_p var='there_are_no_notifications_here_yet'}
                <br/>
                {_p var='create_your_first_notification_now_to_enjoy_a_great_tool_for_announcing'}
                <div style="margin-top: 10px">
                <a href="{url link='admincp.yncwebpush.send-push-notification'}" class="btn btn-primary">{_p var='create_notification'}</a>
                </div>
            </div>
        {/if}
    </div>
    {/if}
</div>
{if count($aNotifications)}
    {pager}
{/if}
</form>
{literal}
    <script type="text/javascript">
        $Behavior.onLoadManageNotification = function(){
            $('.toolbar-top').find('a:eq(2)').addClass('active');
            if (!$('input[name="ids[]"]').length) {
                $('#js_check_box_all').remove();
                $('.js_checkbox').remove();
            }
        }
    </script>
{/literal}
