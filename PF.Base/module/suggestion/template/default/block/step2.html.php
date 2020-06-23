<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if ($bShow==0)}
    <form id="frmContinue" name="frmContinue" method="POST">        
        <input type="hidden" name="iFriendId" id="iFriendId" value="{$aFriend.user_id}" />
        <input type="hidden" name="iCurrentUserId" id="iCurrentUserId" value="{$iCurrentUserId}" />
        <input type="hidden" name="sHeaderText" id="sHeaderText" value='{$sYouAreNow}' />
        <input type="hidden" name="sHeaderTextSuggsetion" id="sHeaderTextSuggsetion" value='{$sYouAreNowSuggestion}' />
        {if ($type == '')}
        <p>            
            {phrase var='suggestion.would_you_like_to_suggest_friend_to'} <a target="_blank" href="{url link={$aFriend.user_name}">{$aFriend.full_name)}</a> ?
        </p>
        {/if}
        {if ($type == 'suggestion')}
        <p>            
            {phrase var='suggestion.send_friend_request_to_friend_of'} <a target="_blank" href="{url link={$aFriend.user_name}}">{$aFriend.full_name)}</a> ?
        </p>
        {/if}
        
        <p>
            <input type="checkbox" name="show" id="show" /> {phrase var='suggestion.do_not_show_this'}
        </p>
        
        <p style="margin-top:10px">            
            <button type="button" name="continue" id="continue2" class="button btn btn-primary btn-sm">{phrase var='suggestion.agree'}</button>
            <button type="button" name="skip" id="skip2" class="button btn btn-default btn-sm" onclick="return js_box_remove(this);">{phrase var='suggestion.skip'}</button>
        </p>
    </form>
{/if}
{if ($type=='')}
{literal}
<script language="javascript">
    $('#continue2').click(function(){
       js_box_remove(this);       
       suggestion_and_recommendation_tb_show($('#sHeaderTextSuggsetion').val(),$.ajaxBox('suggestion.friends','iFriendId='+$('#iFriendId').val()+'&iCurrentUserId='+$('#iCurrentUserId').val()));        
    });
</script>
{/literal}
{/if}
{if ($type=='suggestion')}
{literal}
<script language="javascript">
    $('#continue2').click(function(){
       js_box_remove(this);       
       suggestion_and_recommendation_tb_show($('#sHeaderText').val(),$.ajaxBox('suggestion.friends','iFriendId='+$('#iFriendId').val()));        
    });
</script>
{/literal}
{/if}

{literal}
<script language="javascript">
    $('#show').click(function(){
        if($(this).attr('checked')){
            _checked = 1;
        }else{
            _checked = 0;   
        }
        $.ajaxCall('suggestion.changeShow','checked='+_checked);
    });
</script>
{/literal}
