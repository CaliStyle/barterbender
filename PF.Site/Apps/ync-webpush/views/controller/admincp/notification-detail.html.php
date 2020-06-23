<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='notification_details'}
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-7">
                <div class="item-container ync-item-notification-detail">
                    <div class="item item-id">
                        <strong>{_p var='notification_id'}:</strong> {$aItem.notification_id}
                    </div>
                    <div class="item item-title">
                        <strong>{_p var='notification_title'}:</strong> {$aItem.title|clean}
                    </div>
                    <div class="item item-message">
                        <strong>{_p var='notification_message'}:</strong> {$aItem.message|clean}
                    </div>
                    <div class="item item-url">
                        <strong>{_p var='redirect_url'}:</strong> {$aItem.redirect_url}
                    </div>
                    <div class="item item-sent-on">
                        <strong>{_p var='notification_sent_on'}:</strong> {$aItem.schedule_time|date:'yncwebpush.sent_time_stamp'}
                    </div>
                    <div class="item item-status">
                        <strong>{_p var='notification_status'}:</strong> {_p var=$aItem.status."_u"}
                    </div>
                    <div class="item item-sent-to">
                        <strong>{_p var='notification_sent_to'}:</strong>
                        {if $aItem.audience_type == 'subscriber'}
                            <a href="#" onclick="tb_show('{_p var='particular_subscribers'}', $.ajaxBox('yncwebpush.getSubscriberOfNotification', 'height=500&amp;width=400&amp;id={$aItem.notification_id}')); return false;">{$aItem.audience_key}</a>
                        {else}
                            {$aItem.audience_key} {if !empty($aItem.audience)}({$aItem.audience}){/if}
                        {/if}
                    </div>
                    <div class="item item-total-send">
                        <strong>{_p var='total_number_of_notification_sent'}:</strong> {$aItem.total_send}
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="mb-1"><strong>{_p var='preview'}:</strong></div>
                <div class="ync-item-notification-preview">
                    <div class="item-container">
                        {if !empty($aItem.icon_path)}
                            <div class="item-icon">
                                {img server_id=$aItem.icon_server_id path='core.url_pic' file=$aItem.icon_path suffix='_100' class='icon_image'}
                            </div>
                        {/if}
                        <div class="item-content">
                            <div class="item-title">
                                <strong>
                                    {$aItem.title|clean}
                                </strong>
                            </div>
                            <div class="item-message">
                                {$aItem.message|clean}
                            </div>
                            <div class="item-host">
                                {$sHostName}
                            </div>
                        </div>
                    </div>
                    {if !empty($aItem.photo_path)}
                        <div class="item-image">
                            <span style="background-image: url('{img server_id=$aItem.photo_server_id path='core.url_pic' file=$aItem.photo_path suffix='_400' class='photo_image' return_url=true}')"></span>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        {if $aItem.status == 'scheduled'}
            <a href="{url link='admincp.yncwebpush.send-push-notification' edit_id=$aItem.notification_id}" class="popup btn btn-primary">{_p var='edit_notification'}</a>
        {/if}
        <a href="{url link='admincp.app' id='YNC_WebPush'}" class="btn btn-default">{_p var='back'}</a>
    </div>
</div>
