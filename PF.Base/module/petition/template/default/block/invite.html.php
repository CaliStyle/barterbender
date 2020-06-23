<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script>
 if ($('#div_invitefriend').length)
	  {
var mLoad = setInterval(function() {
		  $('#div_invitefriend img').each(function(){
				$(this).attr('src',$(this).data('src'));
				//console.log("rung");
			});

		}, 3000);
		}
</script>

{/literal}
<form method="post" class="yns_invite_form" action="#" onsubmit="js_box_remove(this); $(this).ajaxCall('petition.inviteFriends'); return false;">
    <input type="hidden" name="val[petition_id]" value="{$aForms.petition_id}"/>
    <input type="hidden" name="val[title]" value="{$aForms.title}"/>


{if Phpfox::isModule('friend')}
<div style="width:100%; position:relative;">
            <div class="yns_invite_frdlist">
              <h3>{phrase var='petition.invite_friends'}</h3>
              <div id="div_invitefriend" style="height:370px;">
                      {if isset($aForms.petition_id)}
                              {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.petition_id friend_module_id='petition'}
                      {/if}
              </div>
            </div>
            {/if}
			{if Phpfox::isModule('friend')}
            <div class="yns_invite_newguest_list">

                      <h3>{phrase var='petition.new_guest_list'}</h3>

                       <div class="label_flow">
                            <div id="js_selected_friends"></div>
                       </div>


            </div>

            <div class="clear"></div>
            {/if}

            <h3>{phrase var='petition.invite_people_via_email'}</h3>
            <div class="p_4">
                    <textarea class="form-control" cols="40" rows="8" name="val[emails]" style="height:60px;"></textarea>
                    <div class="extra_info">
                            {phrase var='petition.separate_multiple_emails_with_a_comma'}
                    </div>
            </div>

            <h3>{phrase var='petition.add_a_personal_message'}</h3>
            <div class="p_4">
                    <textarea class="form-control" cols="40" rows="8" name="val[personal_message]" style="height:120px;">
                        {$sFriendMessageTemplate}
                    </textarea>
            </div>

            <div class="yns_invite_formbtn">
                    <input type="submit" name="val[invite_submit]" value="{phrase var='petition.send_invitations'}" class="btn btn-success btn-sm" />
            </div>

    </div>
</form>
<script type="text/javascript">
    $Behavior.setupInviteLayout = function() {l}
        if(!$('.yns_select_friend_btn').length) {l}
         	$("#js_friend_search_content").append('<div class="yns_select_friend_btn"><button type="button" class="button btn btn-success btn-sm" onclick="ynpetition_selectall_friend();">{phrase var='petition.select_all'}</button> <button class="button btn btn-warning btn-sm" type="button" onclick="ynpetition_unselectall_friend();">{phrase var='petition.un_select_all'}</button></div>');
	        $("#js_friend_search_content").parent().parent().css('height','');
        {r}
    {r}
</script>
