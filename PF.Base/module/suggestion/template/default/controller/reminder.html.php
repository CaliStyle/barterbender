<?php
/**
 * @copyright       [YOUNETCO]
 * @author          NghiDV
 * @package         Module_Suggestion
 * @version         $Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="yn_suggestion_list_item">
<form action="" method="post" name="frmIncoming" id="frmIncoming">
    
    <input type="hidden" name="iUserId" id="iUserId" value="<?= Phpfox::getUserId(); ?>" />
    <div id="sKey" style="display:none;">sKey}</div>
    {if count($aRows)>0}   
        {foreach from=$aRows key=iKey item=aItem}
              {if count($aItem)>0}
        
                <h3>{$aItem[0].header_title}</h3>
                <div style="overflow: hidden;">
                <div id="{$iKey}" >
                {foreach from=$aItem item=aRow}
            
                    <div id="ynsuggestion_item_{$aRow.suggestion_id}" class="ynsuggestion_item">
                        
                        <span class="ajaxLoader hide" style="position: absolute; right:120px;"><img src="{$sFullUrl}theme/frontend/default/style/default/image/ajax/add.gif" /></span>

                        <div class="suggestion_image">          
                            {$aRow.avatar}
                        </div> 
                        <div class="suggestion_info">           
                            <div class="user_tooltip_info_user" itemprop="name">                
                                {$aRow.info}
                            </div>
                        </div>   
                        
                        <div class="suggestion_action">

                            {if (isset($aRow.accept)) }
                                <button type="button" class="button btn btn-primary btn-sm" style="margin-left: 10px; " onclick="doProcess(this, 1, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}', '{$aRow.url}'); return false;">{$aRow.accept}</button>
                            {/if}
                            
                            {if (isset($aRow.ignore)) }
                                <button type="   button" class="button btn btn-default btn-sm" style="margin-left: 10px; "  onclick="doProcess(this, 2, {$aRow.friend_user_id}, {$aRow.friend_friend_user_id}, 'process_{$aRow.suggestion_id}','{$aRow.module_id}','{$aRow.url}'); return false;">{$aRow.ignore}</button>
                            {/if}

                            {if (isset($aRow.delete)) }
                                <button type="button" class="button btn btn-danger btn-sm" style="margin-left: 10px; "  onclick="doProcessDelete(this, {$aRow.suggestion_id}); return false;">{$aRow.delete}</button>
                            {/if}

                            {if (isset($aRow.reminder))}
                                <button type="button" class="button btn btn-success btn-sm" style="margin-left: 10px; " onclick="doReminder({$aRow.item_id},'{$aRow.module_id}','{$aRow.url}','{$aRow.title}',{$aRow.friend_user_id}); return false;">{$aRow.reminder}</button>
                            {/if}

                            {if (isset($aRow.delete_reminder))}
                                <button type="button" class="button btn btn-danger btn-sm" style="margin-left: 10px; " value="" onclick="deleteReminder(this, {$aRow.reminder_id}); return false;">{$aRow.delete_reminder}</button>
                            {/if}

                        </div>
                        
                    </div>
                {/foreach}
                </div>
                {if $aItem[0].total > Phpfox::getParam('suggestion.number_item_on_other_block')  }
                <div id='suggestion_view_more_{$iKey}' style="float:right">
                    <a  class='global_view_more no_ajax_link' style='width:100px;cursor:pointer;'
                    onClick="$('#suggestion_view_more_{$iKey}').hide(); $('#view_more_loader').show();  $.ajaxCall('suggestion.loadReminderViewMore','type={$iKey}&iPage_{$iKey}='+$('#iPage_{$iKey}').val());">{phrase var='suggestion.view_more'}
                    </a>
                </div>
                <input type="hidden" id="iPage_{$iKey}" value="0">
               {/if}
                </div>
            {else}
                {if ($sView != 'my' && $sView != 'friends') }
                <div class="message">{phrase var='suggestion.no_new_suggestion_at_this_time'}</div>
                {/if}
            {/if}
        {/foreach}
    {/if}

</form>
</div>

{literal}
<script language="javascript">      
 
function doReminder(iItemId,moduleId,sLinkCallback,sTitle,iUserId){
     suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+iItemId+'&sSuggestionType=suggestion'+'&sModule=suggestion_'+moduleId+'&sLinkCallback='+sLinkCallback+'&sTitle='+sTitle+'&sPrefix=&sExpectUserId='+iUserId));            
}
function deleteReminder(target,iReminderId){
    
    $(target).parent().find('input[class="button"]').hide();
    $(target).parent().parent().find('span[class*="ajaxLoader"]').show();        
    $.ajaxCall('suggestion.deleteReminder','&iReminderId='+iReminderId);
} 

function doProcess(target, iApprove, iFriendId, iItemId, iProcessId, sModule, sUrl){
    console.log($(target).parent().parent().find('span[class*="ajaxLoader"]'));
    $(target).parent().find('input[class="button"]').hide();
    $(target).parent().parent().find('span[class*="ajaxLoader"]').show();        
    $.ajaxCall('suggestion.approve','iApprove='+iApprove+'&iFriendId='+iFriendId+'&iItemId='+iItemId+'&sModule='+sModule+'&iProcessId='+iProcessId+'&sUrl='+sUrl);
}    

function doProcessDelete(target,iSuggestId){
    
    $(target).parent().find('input[class="button"]').hide();
    $(target).parent().parent().find('span[class*="ajaxLoader"]').show();        
    $.ajaxCall('suggestion.delete','&iSuggestId='+iSuggestId);
} 

</script>    
<style>
    .hide{display: none}
    .show{display: block}
    .pager_outer{float:left; width:100%;}    
    .ynsuggestion_item {margin-bottom:10px; overflow: hidden;}
    .suggestion_image {float: left; margin-right: 10px;width: 90px; height: 90px}
    .suggestion_info {float:left;}
    .suggestion_action {float:right;}
</style>  
{/literal}


