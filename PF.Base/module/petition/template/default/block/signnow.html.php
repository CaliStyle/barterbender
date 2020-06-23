<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="petition_maininfo"
{if $aItem.is_approved == 0}

{/if}
>
		<div class="signature_goal">
				<div class="sig_goal_tit"><span>{phrase var='petition.signature_goal'}</span></div>
				<div class="sig_goal_cont">
						<span class="text1"><strong class="total_sign">{$aItem.total_sign|number_format}</strong>{phrase var='petition.out_of'} <strong> {$aItem.signature_goal|number_format}</strong></span>
						<span class="text2">Signatures</span>
				</div>
		</div>
		<!-- <div class="error_message">Deadline has been reached</div> -->


{if $aItem.petition_status == 1}
<div class="error_message" style="text-align: center">{phrase var='petition.deadline_has_been_reached'}</div>
{elseif $aItem.petition_status == 2 && $aItem.is_approved == '1' }
{if $aItem.can_sign == 1}

    <div class="sign_now" id="sign_nowdiv">
			
            <a href="#"  class="btn btn-success btn-sm dont-unbind" onclick="$Core.box('petition.sign',400,'&id={$aItem.petition_id}'); return false;">{phrase var='petition.sign_now'}</a>
            
         

    </div>
	 

{/if}
<div id="signed_{$aItem.petition_id}" {if $aItem.can_sign != 2} style="display: none" {/if}>
<div class="signed">
        {phrase var='petition.signed'}
</div>
</div>
{/if}
{if Phpfox::isAdmin() || $aItem.user_id == Phpfox::getUserId() || ($aItem.module_id == 'pages' && Phpfox::getService('pages')->isAdmin('' . $aItem.item_id . ''))}
            <div class="petition_morelink"> 
               <ul>
                  <li><a href="#"   onclick="$Core.box('petition.inviteBlock',800,'&id={$aItem.petition_id}'); return false;">{phrase var='petition.invite_friends'}</a></li>
                  {if !empty($aItem.target_email)}
                  <li><a href="#"
                        onclick="if( confirm('{phrase var='petition.are_you_sure_you_want_to_sent_petition_letter_to_target'}')) $(this).ajaxCall('petition.sentToTarget','id={$aItem.petition_id}'); return false;">{phrase var='petition.send_petition_to_target'}</a></li>
                  {/if}
               </ul>
            </div>
         {/if}

</div>
 <p align="center">
        



