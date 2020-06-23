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
{foreach from=$aFriends name=friend item=aFriend}

<div style="position: relative" class="row2" id = 'yn_donor_{$aFriend.donation_id}'>
	
	{if $aFriend.not_show_name}
	<span class="yndonate_img clearfix"><img src='{$sNoProfileImagePath}' height='50' width='50'/> </span>
	<span class ="js_donation" id="js_friend_{$aFriend.user_id}"> {phrase var='donation.anonymous'}</span>
	<span>{$aFriend.time_stamp|convert_time}</span>
	
	{elseif $aFriend.is_guest}
	<span class="yndonate_img clearfix"> <img src='{$sNoProfileImagePath}' height='50' width='50'/> </span>
	<span class ="js_donation" id="js_friend_{$aFriend.user_id}"> {$aFriend.temp_id|clean|shorten:20:'...'|split:20}</span>
	<span>{$aFriend.time_stamp|convert_time}</span>
	{else}
	<span class="yndonate_img clearfix">{$aFriend.img}</span>
	<span class ="js_donation" id="js_friend_{$aFriend.user_id}">{$aFriend|user}{if isset($aFriend.is_active)} <em>({$aFriend.is_active})</em>{/if}{if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false} {phrase var='friend.cannot_select_this_user'}{/if}</span>
	<span>{$aFriend.time_stamp|convert_time}</span>
	{/if}
	{if !$aFriend.not_show_money}
	<span id="js_friend_{$aFriend.user_id}" class="yndonate_price"> {phrase var='donation.donated'} {$aFriend.quanlity} {phrase var='donation.currency_type'}</span>
	{else}
	<span id="js_friend_{$aFriend.user_id}" class="yndonate_price">{phrase var='donation.donated'} {phrase var='donation.generously'}</span>
	{/if}
	{if $bModerator}
	<button onclick="deleteUser({$iPageId},{$aFriend.donation_id}, {$aFriend.is_guest});" type="button" name="deleteUser[]" class="btn btn-danger btn-sm">{phrase var='donation.remove_from_list'}</button>
	{/if}
	
</div>

{/foreach}
               
{literal}
<script type="text/javascript">
   $Core.loadInit();
</script>
{/literal}


