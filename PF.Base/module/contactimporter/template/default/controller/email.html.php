<script type="text/javascript">{literal}var total_allow_select ={/literal}{$max_invitation}{literal} ;</script>{/literal}
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
                {$aUser.email} - {$aUser|user}
                {/if}
            </div>
            {/foreach}
        </div>
    </div>
    {/if}
    <h3>{_p var='your_contacts'}</h3>
    {if count($aInviteLists) == 0}
    {if count($aJoineds) == 0}
    <div class="error_message">{_p var='there_is_no_contact_in_your_account'}</div>
    {else}
    {_p var='you_have_sent_invitations_to_all_of_your_friends'}
    {/if}
    {else}
    <p class="description" style="margin-bottom: 5px;">{_p var='are_not_joined_yet'}.</p>
    {if  ($sProvider == 'yahoo' || $sProvider == 'gmail' || $sProvider == 'hotmail')}
	    <div class="extra_info"> * {_p var='notice_manual_select_amount'} </div>
    {/if}
    {*<p class="description" style="margin-bottom:5px;">{_p var='you_can_send_max_invitations_per_time' max=$max_invitation}</p>*}
    <br />
    <div style='display:none' id="error">
        <ul class="form-errors"><li><ul class="errors"><li id='error_content'></li></ul></li></ul>
    </div>
    <div class="clear"></div> 
    <div class="table wrapper-list table-contactimporter">
        <div class="contactimporter-title thTableOddRow">
            <div class="table-col"><input class="contact_checkall" type='checkbox' onclick="checkAll('items[]', this.checked ? 1 : 0)" title='Select/Deselect all'></div>
            <div class="table-col">{_p var='name'}</div>
            <div class="table-col">{phrase var='user.email'}</div>
        </div>
        <div id="div_list_view" class="content">
            {php}$counter=0;{/php}
            {foreach from=$aInviteLists key=letter item=aInviteList}
                {if $aInviteList} 
                    <div class="table-row yncontact_email_letter">
                        <div id="letter_{$letter}">{$letter}</div>
                    </div>                    

                    {foreach from=$aInviteList key=i item=aInvite}
                    <div class="thTableOddRow yncontact_email_contact" id="row_{php}echo ++$counter;{/php}">
                        <div class="table-col"><input id="check_{php}echo $counter;{/php}" class="contact_item" type="checkbox" name="items[]" value="{$aInvite.email}" data-yncontactimportername="{$aInvite.name}" /></div>
                        <div class="table-col" onclick='check_toggle({php}echo $counter;{/php},document.getElementById("row_{php}echo $counter;{/php}"),true);'>{$aInvite.name}</div>
                        <div class="table-col name">&lt;{$aInvite.email}&gt;</div>        
                    </div>                    
                    {/foreach}
                {/if}
            {/foreach}
        </div>        
    </div>
    <div class="clear"></div>
    <div class="clear"></div>
    <form method="post" action="{url link='contactimporter.invite'}" class="global_form yncontact_emal_invite" name='openinviterform' id="openinviterform" enctype="application/x-www-form-urlencoded" onsubmit="return check_select_invite();">
        <div class="form-wrapper" id="message-wrapper" style="clear:both;margin-top:5px">
            {if phpfox::getUserParam('contactimporter.hide_the_custom_invittation_message') == false}
            <div class="form-label" id="message-label"><br/>
                <p>
                	<label class="optional" for="message" style="margin-top:5px">{_p var='custom_message_title'}</label>
                </p>
                <p><textarea class="form-control" rows="6" cols="45" id="message" name="message">{_p var='default_invite_message_text'}</textarea></p>
            </div>
            {else}
            <input type="hidden" id="message" name="message" style="display: none;" value="{_p var='default_invite_message_text'}"/>
            {/if}
        </div>
        <div class="yncontact_custom_message">
            <button id="send-button" class="btn btn-sm btn-primary" type="button" value="{_p var='send_invites'}">{_p var='send_invites'}</button>
            {if  ($sProvider == 'yahoo' || $sProvider == 'gmail' || $sProvider == 'hotmail')}
            <button id="sendall-button" class="btn btn-sm btn-primary" type='button' name='send' value="{_p var='send_all' contacts=$iCnt}">{_p var='send_all' contacts=$iCnt}</button>
            {/if}
            <button id="skip-button" class="btn btn-sm btn-default" type='button' value ="{_p var='skip'} &gt;&gt;" onclick="location.href='{url link='contactimporter'}';">{_p var='skip'} &gt;&gt;</button>
        </div>
        { if Phpfox::getUserId() > 0}
        <h3>{phrase var='invite.send_a_custom_invitation_link'}</h3>
        {phrase var='invite.send_friends_your_custom_invitation_link_by_copy_and_pasting_it_into_your_own_email_application'}:
        <div class="main_break">
            <input type="text" name="null" value="{$sIniviteLink}" id="js_custom_link" size="40" style="width:75%;" onfocus="this.select();" onkeypress="return false;" />
        </div>
        {/if}
    </form>
    {/if}
</div>