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

{if isset($aBirthdays) && is_array($aBirthdays) && count($aBirthdays)}
    {foreach from=$aBirthdays key=sDaysLeft item=aBirthDatas name=birthdays}
        <div class="fevent-birthays-block">
            <p class="fevent-birthays-block__title fz-12 text-gray-dark">
                {if $sDaysLeft == 1}
                    {_p var='friend.tomorrow'}
                {elseif $sDaysLeft == 2}
                    {_p var='friend.after_tomorrow'}
                {elseif $sDaysLeft < 1}
                    {_p var='friend.today_normal'}
                {else}
                    {_p var='friend.days_left_days' days_left=$sDaysLeft}
                {/if}
            </p>
            <div class="fevent-birthays-block__wapper">
                {foreach from=$aBirthDatas item=aBirthday name=userbirthdays}
                    <div class="fevent-birthays-block__item">
                        <div class="fevent-birthays-block__media">
                            {img user=$aBirthday suffix='_50_square'}
                        </div>
                        <div class="fevent-birthays-block__body">
                            {$aBirthday|user}
                            {if $aBirthday.show_age}
                                <p class="fevent-birthays-block__age fz-12 text-gray-dark">{_p var='years_years_old' years=$aBirthday.new_age}</p>
                            {/if}
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    {/foreach}
{else}
    <div class="extra_info">
        {_p var='friend.no_birthdays_coming_up'}
    </div>
{/if}