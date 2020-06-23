<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="_privacy_holder_table" class="block">
    <form method="post" class="form" action="{url link='push-notification'}">
        <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>
        <h4>{_p var='see_your_notifications_right_on_your_screen_even_the_website_is_closed'}</h4>
        <div id="js_push_notification_block_notifications" class="js_push_notification_block page_section_menu_holder" {if !empty($sActiveTab) && $sActiveTab != 'notifications'}style="display:none;"{/if}>
            <h3>{_p var='choose_system_notifications_you_want_to_receive_as_web_push_notification'}</h3>
            <div class="privacy-block-content">
                {foreach from=$aPrivacyNotifications item=aModules}
                    {foreach from=$aModules key=sNotification item=aNotification}
                    <div class="item-outer">
                        <div class="form-group">
                            <label>{$aNotification.phrase}</label>
                            <div class="item_is_active_holder">
                                <span class="js_item_active item_is_active">
                                    <input type="radio" value="0" name="val[notification][{$sNotification}]" {if $aNotification.default} checked="checked"{/if} class="checkbox" /> {_p var='yes'}
                                </span>
                                <span class="js_item_active item_is_not_active">
                                    <input type="radio" value="1" name="val[notification][{$sNotification}]" {if !$aNotification.default} checked="checked"{/if} class="checkbox" /> {_p var='no'}
                                </span>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                {/foreach}
            </div>
        </div>
        <div id="js_push_notification_block_subscribe" class="js_push_notification_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'subscribe'}style="display:none;"{/if}>
            <div class="privacy-block-content">
                <div class="item-outer">
                    <div class="form-group">
                        <label>{_p var='subscribe_to_receive_notifications_from_this_site'}</label>
                        <div class="item_is_active_holder">
                            <span class="js_item_active item_is_active">
                                <input type="radio" value="0" name="val[subscribe_setting]" {if !$aForms.subscribe_setting}checked="checked"{/if} class="checkbox" /> {_p var='yes'}
                            </span>
                            <span class="js_item_active item_is_not_active">
                                <input type="radio" value="1" name="val[subscribe_setting]" {if $aForms.subscribe_setting}checked="checked"{/if} class="checkbox" /> {_p var='no'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group-button mt-1">
            <input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
        </div>
    </form>
</div>
