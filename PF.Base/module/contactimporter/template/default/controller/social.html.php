<script type="text/javascript">{literal}var total_allow_select ={/literal}{$yn_max_invitation}{literal} ;</script>{/literal}
<input type='hidden' id='provider' value='{$sProvider}'>
<input type='hidden' id='friends_count' value='{$iCnt}'>
<input type="hidden" id="contacts" name="contacts" value="" />
<div id="openinviter">
    {if count($aJoineds)}
    {phrase var='friend.the_following_users_are_already_a_member_of_our_community'}:
    <div class="p_4">
        <div class="label_flow" style="padding-bottom:5px; max-height: 100px;">
            {foreach from=$aJoineds name=users item=aUser}
            <div class="{if is_int($phpfox.iteration.users/2)}row1{else}row2{/if} {if $phpfox.iteration.users == 1} row_first{/if}" id="js_invite_user_{$aUser.user_id}">
                {if $aUser.user_id == Phpfox::getUserId()}
                {$aUser.email} - {phrase var='friend.that_s_you'}
                {else}
                {$aUser.email} - {$aUser|user}{if !isset($aUser.friend_id) || !$aUser.friend_id} - <a href="#?call=friend.request&amp;user_id={$aUser.user_id}&amp;width=420&amp;height=250&amp;invite=true" class="inlinePopup" title="{phrase var='profile.add_to_friends'}">{phrase var='friend.add_to_friends'}</a>{/if}
                {/if}
            </div>
            {/foreach}
        </div>
    </div>
    {/if}
    <h3>{_p var='your_contacts'}</h3>
    <p class="description" style="margin-bottom: 5px;">{_p var='are_not_joined_yet'}.</p>
	<div class="extra_info"> * {_p var='notice_manual_select_amount'} </div>
    {*<p class="description" style="margin-bottom:5px;">{_p var='you_can_send_max_invitations_per_time' max=$yn_max_invitation}</p>*}
    <br />
    <div style='display:none' id="error">
        <ul class="form-errors"><li><ul class="errors"><li id='error_content'></li></ul></li></ul>
    </div>
    <div class="clear"></div>
    <div class="table wrapper-list table-contactimporter">
        <div class='contactimporter-title thTableOddRow'>
            <div class="table-col">
            	<input class="contact_checkall" type='checkbox' onclick="checkAll('items[]', this.checked ? 1 : 0)" title='Select/Deselect all'>
            </div>
            <div class="table-col">
            	{_p var='name'}
            </div>
            <div class="table-col">
            	{phrase var='photo.item_phrase'}
            </div>
        </div>
        <div id="div_list_view" class="content">
                {php}$counter=0;{/php}
                {foreach from=$aInviteLists key=letter item=aInviteList}
                {if $aInviteList}
                <div class="table-row yncontact_email_letter">
                    <td colspan="3" class="label"><div id="letter_{$letter}" style="padding-left:5px;">{$letter}</div></td>
                </div>
                {foreach from=$aInviteList key=i item=aInvite}
                <div class='thTableOddRow yncontact_email_contact' id="row_{php}echo ++$counter;{/php}">
                	<div class="table-col">
                		<input id="check_{php}echo $counter;{/php}" class="contact_item" type="checkbox" name="items[]" value="{$aInvite.id}" data-yncontactimportername="{$aInvite.name}" />
                	</div>
                	<div class="table-col" onclick='check_toggle({php}echo $counter;{/php},document.getElementById("row_{php}echo $counter;{/php}"),true);'>
                		{$aInvite.name}
                	</div>
                	<div class="table-col">
                		{if $aInvite.pic eq ''}
                        <img height='30px' width='30px' src="{$sCoreUrl}module/contactimporter/static/image/nophoto_user_thumb_icon.png">
                        {else}
                        <img height='30px' width='30px' src='{$aInvite.pic}'>
                        {/if}
                	</div>
                </div>
                {/foreach}
                {else}
				{/if}
                {/foreach}
        </div>
    </div>
    <div class="clear"></div>
    {template file='contactimporter.block.pager'}
    <div class="clear"></div>
    <form method="post" action="{url link='contactimporter.invite'}" class="global_form yncontact_emal_invite" name='openinviterform' id="openinviterform" enctype="application/x-www-form-urlencoded" onsubmit="return check_select_invite();">
        <div class="form-wrapper" id="message-wrapper" style="clear:both;margin-top:5px">
            {if phpfox::getUserParam('contactimporter.hide_the_custom_invittation_message') == false}
            <div class="form-label" id="message-label"><br/>
                <p>
                	<label class="optional" for="message">
                    {_p var='custom_message_title'}
                	</label>
                </p>
                <p>
                    <textarea class="form-control" rows="6" cols="45" id="message" name="message">{_p var='default_invite_message_text'}</textarea>
                </p>
                
            </div>
            {else}
            <input type="hidden" id="message" name="message" style="display: none;" value="{_p var='default_invite_message_text'}"/>
            {/if}
        </div>
        <div class='extra_info' style='margin-top:20px;margin-bottom:8px'>
        	<ul>
            	<li>{_p var='notice_quota_sending'} </li>
            	<li>{_p var='maximum_invitation_per_day' number=$iQuota} </li>
            	<li>{_p var='maximum_invitation_per_day_of_provider_number' provider=$sProviderName number=$iProviderQuota}</li>
            </ul>
        </div>
        <div class="yncontact_custom_message">
            <button id="send-button" class="btn btn-sm btn-primary" type="button" value="{_p var='send_invites'}">{_p var='send_invites'}</button>
            {if ($sProvider == 'twitter' || $sProvider == 'linkedin')}
            <button id="sendall-button" class="btn btn-sm btn-primary" type='button' name='send' value="{_p var='send_all' contacts=$iCnt}">{_p var='send_all' contacts=$iCnt}</button>
            {/if}
            <button id="skip-button" class="btn btn-sm btn-default" type='button' value ="{_p var='skip'} &gt;&gt;" onclick="document.getElementById('skip').submit();">{_p var='skip'} &gt;&gt;</button>
        </div>

        {if Phpfox::getUserId() }
        <h3>{phrase var='invite.send_a_custom_invitation_link'}</h3>
        {phrase var='invite.send_friends_your_custom_invitation_link_by_copy_and_pasting_it_into_your_own_email_application'}:
        <div class="main_break">
            <input type="text" name="null" value="{$sIniviteLink}" id="js_custom_link" size="40" style="width:75%;" onfocus="this.select();" onkeypress="return false;" />
        </div>
        {/if}
    </form>
</div>

<form method="post" action="{url link='contactimporter'}" id="skip">
    <input type="hidden" value="skip" name="task" />
</form>