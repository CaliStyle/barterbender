<?php
    defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
?>
{if !empty($aBirthdays)}
    <div class="p-fevent-birthday-container {$todayCustomClass}">
        <div class="p-fevent-birthday-container-outer">
            {if !empty($aBirthdays.today)}
            <div class="item-wrapper-today">
                <div class="item-bg-container">
                    <span style="background-image: url('{if !empty($backgroundImage)}{img server_id=$backgroundImage.image_server_id path='event.url_image' file=$backgroundImage.image_path suffix='' return_url=true}{else}{param var='core.path_actual'}PF.Site/Apps/p-advevent/assets/image/bg-default-birthday.png{/if}');"></span>
                </div>
                <div class="item-time-today">
                    <div class="item-today-outer">
                        <i class="ico ico-gift"></i>
                        <span class="item-today-text">{_p var='today'}</span>
                        {if $isSideLocation}({/if}<span class="item-time-text">{$shortTodayText}</span>
                        <span class="item-time-text">{$todayNumber}, {$shortMonthText}</span>{if $isSideLocation}){/if}
                    </div>
                </div>
                <div class="item-listing-today {if count($aBirthdays.today) >= 3 && !$isSideLocation}owl-carousel{/if} {if $isSideLocation}p-listing-container{/if}" {if $isSideLocation}data-mode-view="list"{/if}>
                    {foreach from=$aBirthdays.today item=todayBirthday}
                    <div class="p-item p-fevent-birthday-item item">
                        <div class="item-outer">
                            <div class="p-item-media-wrapper p-margin-default p-fevent-item-media ">
                                {img user=$todayBirthday suffix='_200_square'}
                            </div>
                            <div class="item-inner">
                                <h4 class="p-item-title p-fevent-birthday-title">
                                    {$todayBirthday|user}
                                </h4>
                                <div class="p-item-minor-info p-fevent-birthday-info p-seperate-dot-wrapper">
                                    {if !empty($todayBirthday.gender_text)}
                                    <span class="p-seperate-dot-item">{$todayBirthday.gender_text}</span>
                                    {/if}
                                    {if !empty($todayBirthday.age_text)}
                                    <span class="p-seperate-dot-item">{$todayBirthday.age_text}</span>
                                    {/if}
                                </div>
                                {if empty($todayBirthday.no_permission_to_send_wish) && !isset($todayBirthday.is_sent_message_wish)}
                                <div class="item-send-wish">
                                    <button class="btn btn-xs btn-default" onclick="tb_show('', $.ajaxBox('fevent.openSendWish'));">{_p var='fevent_send_your_wish'}</button>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
            {/if}
            {if !empty($aBirthdays.others)}
            <div class="p-fevent-birthday-listing-other-container {if $isSideLocation}p-listing-container{/if}" {if $isSideLocation}data-mode-view="list"{/if}>
                {foreach from=$aBirthdays.others item=otherBirthday}
                <div class="p-item p-fevent-birthday-item">
                    <div class="item-outer">
                        <div class="p-item-media-wrapper p-margin-default p-fevent-item-media ">
                            {img user=$otherBirthday suffix='_200_square'}
                        </div>
                        <div class="item-inner">
                            <h4 class="p-item-title p-fevent-birthday-title">
                                {$otherBirthday|user}
                            </h4>
                            {if !empty($otherBirthday.birthdate_text)}
                            <div class="p-item-minor-info p-fevent-birthday-info">
                                {$otherBirthday.birthdate_text}
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
            {/if}
        </div>
    </div>
{/if}