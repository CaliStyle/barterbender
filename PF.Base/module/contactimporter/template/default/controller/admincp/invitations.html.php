<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author          Miguel Espinoza
 * @package          Module_Contact
 * @version         $Id: index.html.php 1424 2010-01-25 13:34:36Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/css">
    .table_right input{
      width:200px;
    }
</script>
{/literal}
<form method="get" action="{url link='admincp.contactimporter.invitations'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='admincp.search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>
                    {_p var='keywords'}:
                </label>
                {$aFilters.title}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" name="search[submit]" value="{phrase var='core.submit'}" class="btn btn-primary" />
            <input type="button" name="search[reset]" onclick="window.location.href='{url link='admincp.contactimporter.invitations'}'" value="{phrase var='core.reset'}" class="btn btn-default" />
        </div>
    </div>
</form>
{if count($items) > 0}
<form action="{url link='admincp.contactimporter.invitations'}" method="post" onsubmit="return getsubmit();" >
    <div class="panel panel-default">
        <div class="table-responsive">
            <table class="table table-admin">
                <thead>
                    <tr>
                        <th width="10px"><input type="checkbox" value="" id = "js_check_box_all" name="checkAll" class="main_checkbox"/></th>
                        <th>{_p var='inviter'}</th>
                        <th>{_p var='full_name'}</th>
                        <th>{_p var='email'}</th>
                        <th>{_p var='options'}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$items key=iKey item=inviter}
                    <tr id="{$inviter.invite_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td style="width:10px">
                            <input type="checkbox" class="checkbox" value="{$inviter.invite_id}" name="is_selected"/>
                        </td>
                        <td>{$inviter.user_name|clean}</td>
                        <td>{$inviter.full_name|clean}</td>
                        <td>
                            {if isset($inviter.invited_name) && $inviter.invited_name}
                                {$inviter.invited_name} ({$inviter.receive_email|shorten:30:'...'})
                            {else}
                                {$inviter.receive_email}
                            {/if}
                        </td>
                        <td width="40px">
                            <div id = "resend_{if isset($aInvite)}{$aInvite.invite_id}{/if}" align="center">
                                <span style="float:left;width:10px;margin-left: 4px;">
                                    {if $inviter.canResendMail && $inviter.is_resend == 0}
                                    <a class="inlinePopup"  title="{_p var='invitation_message'}"  border="0" href="#?call=contactimporter.reSendInvitation&invite_id={$inviter.invite_id}&width=300&height=200"><img alt="{_p var='resend_invitation'}" title="{_p var='resend_invitation'}" border="0" width="15" height="15" src="{$core_url}module/contactimporter/static/image/send_mail.png"></a>
                                    {/if}
                                </span>
                                {if isset($inviter)}
                                <a title="{_p var='delete_invitation'}" class="sJsConfirm" href="{url link='admincp.contactimporter.invitations.page_'.$iPage del=$inviter.invite_id }">{img theme='misc/delete.png' alt='' class='go_right'}</a>
                                {/if}
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <input type="hidden" value="" name="arr_selected" id="arr_selected"/>
            <input type="hidden" value="" name="feed_selected" id="feed_selected"/>
            <input type="submit" id="delete_selected" name="deleteselect" value="{_p var='delete_selected'}" class="delete btn btn-danger sJsCheckBoxButton disabled" disabled="true" onclick="return setValue(this);"/>
        </div>
    </div>
</form>
{pager}
{else}
<div class="extra_info p-4">
    {phrase var='invite.there_are_no_pending_invitations'}
</div>
{/if}
<script type="text/javascript">
    <!--
    {literal}
    function reSendInvitation(id)
    {
        {/literal}                         
        $.ajaxCall('contactimporter.reSendInvitation','invite_id='+id);    
        {literal}    
    }
    {/literal}
    -->
</script>