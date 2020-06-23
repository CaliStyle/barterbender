<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="#" onsubmit="js_box_remove(this); $(this).ajaxCall('fundraising.inviteFriends'); return false;">
    <input type="hidden" name="val[campaign_id]" value="{$aForms.campaign_id}"/>
    <input type="hidden" name="val[title]" value="{$aForms.title}"/>
    <div style="width:100%; position:relative;">	
    	 	{if Phpfox::isModule('friend')}			
            <h3 style="margin-top:0px; padding-top:0px;">{phrase var='invite_friends'}</h3>
            <div style="height:370px;">			
                {if isset($aForms.campaign_id)}
                        {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.campaign_id friend_module_id='fundraising'}
                {/if}
            </div>
            {/if}
            <h3>{phrase var='invite_people_via_email'}</h3>
            <div class="p_4">
                    <textarea cols="40" rows="8" name="val[emails]" style="width:98%; height:60px;"></textarea>
                    <div class="extra_info">
                            {phrase var='separate_multiple_emails_with_a_comma'}
                    </div>
            </div>
            
            <h3>{phrase var='add_a_personal_message'}</h3>
            <div class="p_4">
                    <textarea cols="40" rows="8" name="val[personal_message]" style="width:98%; height:120px;">
                        {$sFriendMessageTemplate}
                    </textarea>					
            </div>				
            
            <div class="p_top_8">
                    <input type="submit" name="val[invite]" value="{phrase var='send_invitations'}" class="button" />
            </div>
            {if Phpfox::isModule('friend')}
            <div style="display: none">
                 <div class="block">
                      <div class="title">{phrase var='new_guest_list'}</div>
                      <div class="content">
                           <div class="label_flow" style="height:330px;">
                                <div id="js_selected_friends"></div>
                           </div>
                      </div>
                 </div>
            </div>		
            
            <div class="clear"></div>		
            {/if}
    </div>
</form>
<script type="text/javascript">
    $Behavior.setupInviteLayout = function() {l}
         $("#js_friend_search_content").append('<div class="clear" style="padding:5px 0px 10px 0px;"><input type="button" onclick="$(\'input.checkbox\').attr(\'checked\', \'checked\');" value="{phrase var='select_all'}" /> <input type="button" onclick="$(\'input.checkbox\').removeAttr(\'checked\');" value="{phrase var='un_select_all'}"/></div>');
         $("#js_friend_search_content").parent().parent().css('height','');
    {r}
</script>