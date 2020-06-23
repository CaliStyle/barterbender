<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aInvites)}
<form method="post" action="{url link='current'}" id="js_form" class="yncotact_form_invitations">
    <div class="main_break">
        {foreach from=$aInvites name=invite item=aInvite}
        <div id="js_invite_{$aInvite.invite_id}" class="js_selector_class_{$aInvite.invite_id} {if is_int($phpfox.iteration.invite/2)}row1{else}row2{/if}{if $phpfox.iteration.invite == 1} row_first{/if}">
            <div class=" go_left t_center" >
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aInvite.invite_id}" id="check{$aInvite.invite_id}" />
                </label>
            </div>
            <div class="t_right">
                {literal}
                <script type="text/javascript">
                    function reSendInvitation(id)
                    {
                        $.ajaxCall('contactimporter.reSendInvitation','invite_id='+id);                                                
                    }
                </script>
                {/literal}
                <ul id = "resend_{$aInvite.invite_id}" class="yncontact_invitations_action">
                	<li>
	                    <a title="{_p var='delete_invitation'}" href="{url link='current' del=$aInvite.invite_id}" class="text-danger"><i class="ico ico-close"></i></a>
                	</li>
                	<li>
                		{if $aInvite.canResendMail && $aInvite.is_resend == 0}
                        <a class="inlinePopup"  title="{_p var='invitation_message'}"  border="0" href="#?call=contactimporter.reSendInvitation&invite_id={$aInvite.invite_id}&width=300&height=200"><img alt="{_p var='resend_invitation'}" title="{_p var='resend_invitation'}" border="0" width="15" height="15" src="{$core_url}module/contactimporter/static/image/send_mail.png"></a>
                        {/if}
                	</li>
                </ul>
            </div>
            <div class="t_content" style="overflow: hidden;">                                         
                {if isset($aInvite.invited_name) && $aInvite.invited_name}
                    {$aInvite.count}. {$aInvite.invited_name} ({$aInvite.email|shorten:30:'...'})
                {else}
                    {$aInvite.count}. {$aInvite.email|shorten:50:'...'}
                {/if}
            </div>
            <div class="clear"></div>
        </div>
        {/foreach}
    </div>
</form>

{pager}
{moderation}
{else}
{if $iPage == 0 }
    <div class="extra_info">
        {phrase var='invite.there_are_no_pending_invitations'}
        <ul class="action">
            <li><a href="{url link='contactimporter'}">{phrase var='invite.invite_your_friends'}</a></li>
        </ul>
    </div>
{/if}
{/if}