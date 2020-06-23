<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="form-group">
    <label for="">{required}{_p var='push_notification_title'}</label>
    <input class="form-control" id="title" type="text" name="val[title]" maxlength="30" value="{value id="title" type="input"}" />
    <div class="extra_info">
        {_p var='enter_the_title_of_this_push_notification_max_30_characters'}
    </div>
</div>
<div class="form-group">
    <label for="">{_p var='push_notification_message'}</label>
    <input class="form-control" id="message" type="text" name="val[message]" maxlength="200" value="{value id="message" type="input"}"/>
    <div class="extra_info">
        {_p var='enter_the_message_of_this_push_notification_keep_it_less_than_40_characters_for_the_best_layout'}
    </div>
</div>
<div class="form-group">
    <label id="js_icon_holder">
        {_p var='notification_icon'}
        <br>
        {if !empty($aForms.icon_path)}
        <div style="margin: 5px 0px; border: solid 1px #eee;max-width: 50px">
            {img server_id=$aForms.icon_server_id path='core.url_pic' file=$aForms.icon_path suffix='_100' width='50px' class='icon_image'}
            <input type="hidden" name="val[icon_path]" value="{$aForms.icon_path}">
            <input type="hidden" name="val[icon_server_id]" value="{$aForms.icon_server_id}">
        </div>
        {/if}
    </label>
    <input type="file" class="form-control" id="icon" name="icon"/>
    <div class="extra_info">
        {_p var='choose_a_icon_for_this_push_notification_recommend_dimensions_for_the_image_are_100_100_pixels'}
    </div>
</div>
<div class="form-group">
    <label id="js_photo_holder">
        {_p var='notification_photo'}
        <br>
        {if !empty($aForms.photo_path)}
        <div style="margin: 5px 0px; border: solid 1px #eee; max-width:200px">
            {img server_id=$aForms.photo_server_id path='core.url_pic' file=$aForms.photo_path suffix='_400' width='200px' class='photo_image'}
            <input type="hidden" name="val[photo_path]" value="{$aForms.photo_path}">
            <input type="hidden" name="val[photo_server_id]" value="{$aForms.photo_server_id}">
        </div>
        {/if}
    </label>
    <input type="file" class="form-control" id="photo" name="photo"/>
    <div class="extra_info">
        {_p var='choose_a_photo_to_be_sent_in_this_push_notification'}
    </div>
</div>
<div class="form-group">
    <label for="">{required}{_p var='redirect_url'}</label>
    <input class="form-control" id="redirect_url" type="text" name="val[redirect_url]" value="{value id="redirect_url" type="input"}"/>
    <div class="extra_info">
        {_p var='enter_the_url_on_which_you_want_to_redirect_subscribers_when_they_click_on_this_push_notification_enter_full_url_like'}
    </div>
</div>
