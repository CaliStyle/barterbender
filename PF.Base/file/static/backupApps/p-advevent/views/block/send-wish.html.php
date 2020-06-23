<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if empty($loadContent)}
<div class="p-fevent-popup-send-wish-container">
    <div class="item-header-wrapper">
        <div class="item-bg-container">
            <div class="item-info">
                <div class="item-text">
                    {_p var='fevent_send_your_wishes_to_friends_have_birthday_today'}
                </div>
                <div class="item-time">
                    {$dateFormat}
                </div>
            </div>
            <div class="item-media">
                <div class="item-media-bg">
                    <span style="background-image: url('{param var='core.path_actual'}PF.Site/Apps/p-advevent/assets/image/bg-default-birthday.png');"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="item-content-wrapper js_send_wish_content">
{/if}
        <div class="item-member-listing">
            {foreach from=$todayBirthdays item=todayBirthday}
            <div class="item-member js_send_wish_item" data-id="{$todayBirthday.user_id}">
                <div class="item-outer">
                    <div class="item-title">
                        {$todayBirthday|user}
                    </div>
                    <div class="item-inner">
                        <div class="item-wish-wrapper {if $todayBirthday.is_sent_message_wish}has-text{/if}">
                            {if !empty($todayBirthday.message_wish_text)}
                            <div class="form-control">
                                <div class="item-message-text">
                                    {$todayBirthday.message_wish_text}
                                </div>
                            </div>
                            {else}
                            <input type="text" class="form-control js_send_wish_message" placeholder="{_p var='fevent_write_your_wish_here'}">
                            {/if}
                            <div class="item-avatar">
                                {img user=$todayBirthday suffix='_200_square'}
                            </div>
                        </div>
                        {if !empty($todayBirthday.is_sent_message_wish)}
                        <div class="item-status">
                            <i class="ico ico-check"></i> {_p var='sent'}
                        </div>
                        {else}
                        <div class="item-action">
                            <button class="btn btn-sm btn-primary" onclick="P_AdvEvent.sendYourWish(this); return false;">{_p var='fevent_send_ucfirst'}</button>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
{if empty($loadContent)}
    </div>
</div>
{/if}
