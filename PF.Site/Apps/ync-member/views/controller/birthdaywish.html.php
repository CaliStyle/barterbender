<div class="ynmember_birthday_modal">
    <div class="ynmember_birthday_modal_inner">
        <div class="ynmember_birthday_block_bg" style="background-image: url('{param var='core.path_actual'}PF.Site/Apps/ync-member/assets/image/birthday_bg.png')"></div>
        <div class="ynmember_birthday_send">
            {if count($aSents) == 1}
                <p class="fw-bold">{_p var='You sent birthday wishes to a friend'}</p>
            {/if}
            {if count($aSents) > 1}
                <p class="fw-bold">{_p var='you_sent_birthday_wishes_to_number_friends' number=$aSents|count}</p>
            {/if}
            <ul>
                {foreach from=$aSents name=users item=aUser key=iKey}
                    {if $iKey < 5}
                        <li>
                           <div class="ynmember_avatar">
                                {if $aUser.user_image}
                                    <a href="{url link=$aUser.user_name}" title="{$aUser.full_name}" style="background-image: url('{img user=$aUser suffix='_200_square' return_url=true}');"></a>
                                {else}
                                    {img user=$aUser suffix='_200_square' return_url=true}
                                {/if}
                            </div>
                        </li>
                    {/if}
                {/foreach}
                {if count($aSents) > 6}
                <li>
                    <div class="ynmember_more">{$aSents|count|ynmember_subtract:5}</div>
                </li>
                {/if}
            </ul>
        </div>
        <ul>
            {foreach from=$aUsers name=users item=aUser}
                {if Phpfox::getUserId() != $aUser.user_id}
                    <li>
                        {template file='ynmember.block.entry_birthday_upcoming_popup'}
                    </li>
                {/if}
            {/foreach}
        </ul>
        {if count($aUsers) > 4}
            <a href="{url link='ynmember.birthday'}" class="ynmember_viewmore uppercase active">
                    {_p var='view_more_number_birthdays' number=$aUsers|count|ynmember_subtract:4}
                <i class="fa fa-angle-down" aria-hidden="true"></i>
            </a>
        {/if}
    </div>
</div>