<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: search.html.php 3710 2011-12-07 10:02:30Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !$bSearch}
<script type="text/javascript">
	var sPrivacyInputName = '{$sPrivacyInputName}';
	var sConfirmText = "{phrase var='donation.are_you_sure'}";
	var sSearchByValue = '';
{literal}
	$Behavior.searchFriendBlock = function()
	{
		sSearchByValue = $('.js_is_enter').val();		
		
		if ($.browser.mozilla) 
		{
			$('.js_is_enter').keypress(checkForEnter);
		} 
		else 
		{
			$('.js_is_enter').keydown(checkForEnter);
		}		
	};
        
        function deleteUser(iPageId, iDonationId, bIsGuest){
            if (confirm(sConfirmText)){
                showLoader();
                $.ajaxCall('donation.deleteUser', 'iPageId='+iPageId+'&iDonationId='+iDonationId+'&bIsGuest='+bIsGuest);                
            }
        }

	updateCheckBoxes();
	
	function updateFriendsList()
	{		
		updateCheckBoxes();
	}
	
	function removeFromSelectList(sId)
	{
		$('.js_cached_friend_id_' + sId + '').remove();
		$('#js_friends_checkbox_' + sId).attr('checked', false);
		$('#js_friend_input_' + sId).remove();
		$('.js_cached_friend_id_' + sId).remove(); return false;		
		
		return false;
	}
	
	function addFriendToSelectList(oObject, sId)
	{		
		if (oObject.checked)
		{
			iCnt = 0;
			$('.js_cached_friend_name').each(function()
			{			
				iCnt++;
			});			

			if (function_exists('plugin_addFriendToSelectList'))
			{
				plugin_addFriendToSelectList(sId);
			}
			{/literal}
			$('#js_selected_friends').append('<div class="js_cached_friend_name row1 js_cached_friend_id_' + sId + '' + (iCnt ? '' : ' row_first') + '"><span style="display:none;">' + sId + '</span><input type="hidden" name="val[' + sPrivacyInputName + '][]" value="' + sId + '" /><a href="#" onclick="return removeFromSelectList(' + sId + ');">{img theme='misc/delete.gif' class="delete_hover v_middle"}</a> ' + $('#js_friend_' + sId + '').html() + '</div>');			
			{literal}
		}
		else
		{
			if (function_exists('plugin_removeFriendToSelectList'))
			{
				plugin_removeFriendToSelectList(sId);
			}			
			
			$('.js_cached_friend_id_' + sId).remove();
			$('#js_friend_input_' + sId).remove();
		}
	}
	
	function cancelFriendSelection()
	{
		if (function_exists('plugin_cancelFriendSelection'))
		{
			plugin_cancelFriendSelection();
		}			
		
		$('#js_selected_friends').html('');	
		$Core.loadInit(); 
		tb_remove();
	}
	
	function updateCheckBoxes()
	{
		iCnt = 0;
		$('.js_cached_friend_name').each(function()
		{			
			iCnt++;
			$('#js_friends_checkbox_' + $(this).find('span').html()).attr('checked', true);
		});
		
		$('#js_selected_count').html((iCnt / 2));
	}
	
	function showLoader()
	{
		$('#js_friend_search_content').html($.ajaxProcess(oTranslations['friend.loading'], 'large'));
	}	
	
	function checkForEnter(event)
	{
		if (event.keyCode == 13) 
		{
			showLoader(); 
			
			$.ajaxCall('donation.searchAjax', 'find=' + $('#js_find_friend').val() + '&amp;input=' + sPrivacyInputName + '');
		
			return false;	
		}
	}
{/literal}
</script>
<div id="js_friend_loader_info"></div>
<div id="js_friend_loader">
{if $sFriendType != 'mail'}
	<div class="p_4">
		<div class="go_left" style="display:none;">
			{phrase var='friend.view'}:&nbsp;<select class="form-control" name="view" onchange="showLoader(); $(this).ajaxCall('donation.searchAjax', 'input={$sPrivacyInputName}'); return false;">
				<option value="all">{phrase var='friend.all_friends'}</option>
				<option value="online"{if $sView == 'online'} selected="selected"{/if}>{phrase var='friend.online_friends'}</option>
				{if count($aLists)}
				<optgroup label="{phrase var='friend.friends_list'}">
				{foreach from=$aLists item=aList}
					<option value="{$aList.list_id}"{if $sView == $aList.list_id} selected="selected"{/if}>{$aList.name|clean|split:30}</option>
				{/foreach}
				</optgroup>
				{/if}
			</select>
		</div>
		<div class="t_right" style="display:none;">
			<script type="text/javascript">
				sSearchByValue = '{phrase var='friend.search_by_email_full_name_or_user_name'}';
			</script>
			<input type="text" class="js_is_enter v_middle default_value form-control" name="find" value="{phrase var='friend.search_by_email_full_name_or_user_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_friend" size="30" />
			<button type="button" onclick="showLoader(); $.ajaxCall('donation.searchAjax', 'friend_module_id={$sFriendModuleId}&amp;friend_item_id={$sFriendItemId}&amp;find=' + $('#js_find_friend').val() + '&amp;input={$sPrivacyInputName}'); return false;" class="button v_middle btn btn-success btn-sm">{phrase var='friend.find'}</button>
		</div>
		<div class="clear"></div>
	</div>
	
	<div class="main_break"></div>
	
{else}	
			<input type="text" class="js_is_enter v_middle default_value form-control" name="find" value="{phrase var='friend.search_by_email_full_name_or_user_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_friend" size="30" />
			<button type="button" onclick="showLoader(); $.ajaxCall('donation.searchAjax', 'friend_module_id={$sFriendModuleId}&amp;friend_item_id={$sFriendItemId}&amp;find=' + $('#js_find_friend').val() + '&amp;input={$sPrivacyInputName}&amp;type={$sFriendType}'); return false;" class="button v_middle btn btn-success btn-sm">{phrase var='friend.find'}</button>

{/if}
	{$sView}
	<div class="t_center" style="display:none;">
		{foreach from=$aLetters item=sLetter}<span style="padding-right:5px;"><a href="#" onclick="showLoader(); $.ajaxCall('donation.searchAjax', 'letter={$sLetter}&amp;input={$sPrivacyInputName}&amp;type={$sFriendType}&amp;view={$sView}'); return false;"{if $sActualLetter == $sLetter} style="text-decoration:underline;"{/if}>{$sLetter}</a></span>{/foreach}
	</div>

{/if}
	<div id="js_friend_search_content">
		{pager}
		<div class="label_flow" style="height:200px;">
			{foreach from=$aFriends name=friend item=aFriend}
			
			<div style="position: relative" class="row2">
				
				{if $aFriend.not_show_name}	
					<span> <img src='{$sNoProfileImagePath}' height='50' width='50'/> </span>
					<span class ="js_donation" style="font-weight:bold; position: absolute; top:12px; left:55px;" id="js_friend_{$aFriend.user_id}"> {phrase var='donation.anonymous'}</span>
                                <span style="position: absolute; bottom:12px; left:55px;color:#666666">{$aFriend.time_stamp|convert_time}</span>
								
				{elseif $aFriend.is_guest}
					<span> <img src='{$sNoProfileImagePath}' height='50' width='50'/> </span>
					<span class ="js_donation" style="font-weight:bold; position: absolute; top:12px; left:55px;" id="js_friend_{$aFriend.user_id}"> {$aFriend.temp_id}</span>
                                <span style="position: absolute; bottom:12px; left:55px;color:#666666">{$aFriend.time_stamp|convert_time}</span>
				{else}	
					<span>{$aFriend.img}</span>
					<span class ="js_donation" style="font-weight:bold; position: absolute; top:12px; left:55px;" id="js_friend_{$aFriend.user_id}">{$aFriend|user}{if isset($aFriend.is_active)} <em>({$aFriend.is_active})</em>{/if}{if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false} {phrase var='friend.cannot_select_this_user'}{/if}</span>
                                <span style="position: absolute; bottom:12px; left:55px;color:#666666">{$aFriend.time_stamp|convert_time}</span>
				{/if}
				{if !$aFriend.not_show_money}
					<span  style="font-weight:bold; position: absolute; top:12px; left:250px;" id="js_friend_{$aFriend.user_id}"> {phrase var='donation.donated'} {$aFriend.quanlity} {phrase var='donation.currency_type'}</span>
				{else}
					<span  style="font-weight:bold; position: absolute; top:12px; left:250px;" id="js_friend_{$aFriend.user_id}">{phrase var='donation.donated'} {phrase var='donation.generously'}</span>
				{/if}
				{if $bModerator}
					<button onclick="deleteUser({$iPageId},{$aFriend.donation_id}, {$aFriend.is_guest});" style="position: absolute; right:0; top:20px;" type="button" name="deleteUser[]" class="button btn btn-warning btn-sm">{phrase var='donation.remove_from_list'}</button>
				{/if}
				
			</div>
			
			{foreachelse}
			
			<div class="extra_info">
			{if $sFriendType == 'mail'}
				{phrase var='user.sorry_no_members_found'}
			{else}
				{phrase var='donation.there_is_no_donation'}
			{/if}                        
			</div>
			{/foreach}
			
			<div class="extra_info">
				{$iTotalDonors}
			</div>
		</div>                
	</div>
{if !$bSearch}
	{if $bIsForShare}
	
	{else}
	{if $sPrivacyInputName != 'invite'}
	<div class="main_break t_right">		
		<button type="button" name="submit" onclick="{literal}if (function_exists('plugin_selectSearchFriends')) { plugin_selectSearchFriends(); } else { $Core.loadInit(); tb_remove(); }{/literal}" class="button btn btn-primary btn-sm">{phrase var='friend.use_selected'}</button>&nbsp;<button type="button" name="cancel" onclick="{literal}if (function_exists('plugin_cancelSearchFriends')) { plugin_cancelSearchFriends(); } else { cancelFriendSelection(); }{/literal}" class="button btn btn-default btn-sm">{phrase var='friend.cancel'}</button>	
	</div>
	{/if}
	{/if}
</div>
{/if}

