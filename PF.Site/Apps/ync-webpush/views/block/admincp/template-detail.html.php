<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item-container">
    <div class="item-action">
        <a class="ync-action" href="{url link='admincp.yncwebpush.send-push-notification' template=$aItem.template_id}"><i class="ico ico-paperplane-alt-o"></i>&nbsp;{_p var='create_notification'}</a>
        <a class="ync-action" href="{url link='admincp.yncwebpush.add-template' id=$aItem.template_id}"><i class="ico ico-pencilline-o"></i>&nbsp;{_p var='edit_template'}</a>
    </div>
    <div class="item-inner table-responsive">
        <table class="table table-admin">
            <tr>
                <td class="w180">{_p var='notification_title'}</td>
                <td>{$aItem.title|clean}</td>
            </tr>
            <tr>
                <td>{_p var='notification_message'}</td>
                <td>{$aItem.message|clean}</td>
            </tr>
            {if !empty($aItem.icon_path)}
                <tr>
                    <td class="w180">{_p var='notification_icon'}</td>
                    <td>{img server_id=$aItem.icon_server_id path='core.url_pic' file=$aItem.icon_path suffix='_100' width='50px'}</td>
                </tr>
            {/if}
            {if !empty($aItem.photo_path)}
                <tr>
                    <td class="w180">{_p var='notification_photo'}</td>
                    <td>{img server_id=$aItem.photo_server_id path='core.url_pic' file=$aItem.photo_path suffix='_400' width='200px'}</td>
                </tr>
            {/if}
            <tr>
                <td class="w180">{_p var='redirect_url'}</td>
                <td><a href="{$aItem.redirect_url}">{$aItem.redirect_url}</a></td>
            </tr>
        </table>
    </div>
</div>