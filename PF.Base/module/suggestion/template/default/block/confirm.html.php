<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<form action="" name="frmConfirm" id="frmConfirm" method="post">    
    <input type="hidden" name="iFriendId" id="iFriendId" value="{$aFriend.user_id}" />
    <input type="hidden" name="iCurrentUserId" id="iCurrentUserId" value="{$iCurrentUserId}" />
    <input type="hidden" name="sHeaderText" id="sHeaderText" value='{$sYouAreNow}' />
    <input type="hidden" name="sHeaderTextSuggsetion" id="sHeaderTextSuggsetion" value='{$sYouAreNowSuggestion}' />
    <p><input type="radio" name="type" id="recommend" value="recommend" checked="checked" /> 
    {phrase var='suggestion.send_friend_request_to_friend_of'} <a target="_blank" href="{url link={$aFriend.user_name}}">{$aFriend.full_name)}</a></p>
    <p>
        <input type="radio" name="type" id="suggest" value="suggest" /> 
        {phrase var='suggestion.suggest_friends_to'} <a target="_blank" href="{url link={$aFriend.user_name}}">{$aFriend.full_name)}</a>
    </p>
    <p style="margin-top:10px">
        <button type="button" name="continue" id="continue" class="button btn btn-primary">{phrase var='suggestion.continue'}</button>
        <button type="button" name="skip" id="skip"  class="button btn btn-default">{phrase var='suggestion.skip'}</button>
    </p>
</form>
{literal}
<script lang="javascript">
    $('input[name="skip"]').click(function(){
        tb_remove();
    });
    $('input[name="continue"]').click(function(){
        tb_remove();
        
        var _type = $('input[name="type"]:checked').val();
        if (_type == 'recommend')
            suggestion_and_recommendation_tb_show($('#sHeaderText').val(),$.ajaxBox('suggestion.friends','iFriendId='+$('#iFriendId').val()));
        else
            suggestion_and_recommendation_tb_show($('#sHeaderTextSuggsetion').val(),$.ajaxBox('suggestion.friends','iFriendId='+$('#iFriendId').val()+'&iCurrentUserId='+$('#iCurrentUserId').val()));
    });
</script>
{/literal}
