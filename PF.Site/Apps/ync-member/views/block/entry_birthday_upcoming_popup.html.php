<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="ynmember_birthday_wish_{$aUser.user_id}">
    <div class="ynmember_title">
        <div>
            {$aUser|user}
            <span class="ynmember_old">({_p var='n_years_old' years_old=$aUser.new_age})</span>
        </div>
        <span class="ynmember_sent{if $aUser.is_sent_birthday_wish} active{/if}"><i class="fa fa-check" aria-hidden="true"></i>Sent</span>
    </div>
    <div class="ynmember_modal_inner">
        <form id="ynmember_birthday_wish_form_{$aUser.user_id}">
            <input type="hidden" name="val[user_id]" value="{$aUser.user_id}">
            <input class="form-control js_ynmember_birthday_wish" {if $aUser.is_sent_birthday_wish}disabled{/if} type="text" name="val[message]" placeholder="{_p var='send_your_best_wishes_to_subject' subject=$aUser|ynmember_gender}" value="{$aUser.birthday_message}" >
        </form>
        <div>
            {if !$aUser.is_sent_birthday_wish}
                <a id="ynmember_send_bw_btn_{$aUser.user_id}" href="javascript:void(0)" class="button_sent" onclick="return ynmember.sendBirthdayWish('{$aUser.user_id}')"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>
            {/if}
            <div class="ynmember_avatar">
                {if $aUser.user_image}
                    <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
                {else}
                    {img user=$aUser suffix='_200_square' return_url=true}
                {/if}
            </div>
        </div>
    </div>
</div>