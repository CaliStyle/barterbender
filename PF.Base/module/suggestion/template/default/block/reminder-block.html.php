{if count($aReminders) > 0}
<div class="block">
		<div class="title" >{phrase var='suggestion.suggestion_reminder'}</div>
	<div class="content">
{foreach from=$aReminders key=iKey item=aReminder}
<p style="line-height:5px;">&nbsp;</p>
{if isset($aReminder.info)}
<p style="position: absolute;"><span class="thumb">{$aReminder.avatar}</span></p>

<p style="padding: 0 0 15px 60px; border-bottom:1px solid #f2f2f2;">    
        <strong>{$aReminder.info}</strong><BR>
            <a href="#" onclick="doReminder({$aReminder.item_id},'{$aReminder.module_id}','{$aReminder.url}','{$aReminder.title}',{$aReminder.friend_user_id}); return false;" >{phrase var='suggestion.suggest_friends'}</a><br>
            <a href="#" onclick="deleteReminder(this, {$aReminder.reminder_id}); return false;" >{phrase var='suggestion.delete'}</a>
</p>
{/if}
{/foreach}
</br>
<div class="bottom">
		<ul>
			<li id="js_block_bottom_1" class="first text-center">
					<a href="{$viewMoreUrl}" id="js_block_bottom_link_1">{phrase var='suggestion.view_more'}</a>
			</li>
		</ul>
</div>

</div>

</div>
{literal}
<style type='text/css'>
.thumb  img {
	width: 50px;
	height: 50px;
}
</style>
<script language="javascript">      
 
function doReminder(iItemId,moduleId,sLinkCallback,sTitle,iUserId){
     suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+iItemId+'&sSuggestionType=suggestion'+'&sModule=suggestion_'+moduleId+'&sLinkCallback='+sLinkCallback+'&sTitle='+sTitle+'&sPrefix=&sExpectUserId='+iUserId));            
}

function deleteReminder(target,iReminderId){    
    $.ajaxCall('suggestion.deleteReminder','&iReminderId='+iReminderId);
} 


</script>

{/literal}
{/if}