<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<script type="text/javascript">
{literal}
    $('#messages').focus(function(){            
        if($(this).val() == $('#sText').val()){
            $(this).val('');
        }    
    });

    $('#messages').blur(function(){
        if($(this).val() == ''){
            $(this).val($('#sText').val());
        }    
    });
        
	function shareFriendContinue()
	{
		var iCnt = 0;
		$('.js_cached_friend_name').each(function(){
			iCnt++;
		});
		
		if (!iCnt)
		{
{/literal}
			alert('{phrase var='suggestion.need_to_select_some_friends_before_we_try_to_send_the_message' phpfox_squote=true}');
{literal}
			return false;
		}
                
        if ($('#messages').val() == $('#sText').val()) {
{/literal}
            $('#messages').val('');
{literal}                    
        }	
                
        if (parseInt($('#messages').val().length) > parseInt($('#iMaxChars').val())){
{/literal}
			alert($('#sMaxChars').val());
{literal}
            return false;
        }	                              
        show();		
		return true;
	}

function shareFriendContinue1()
	{
		var iCnt = 0;
		$('.js_cached_friend_name').each(function(){
			iCnt++;
		});

		if (!iCnt)
		{
{/literal}
			alert('{phrase var='suggestion.need_to_select_some_friends_before_we_try_to_send_the_message' phpfox_squote=true}');
            return true;
{literal}
		}
		else {
            {/literal}
                $Core.reloadPage();
            {literal}
        }

        if ($('#messages').val() == $('#sText').val()) {
{/literal}
            $('#messages').val('');
{literal}
        }

        if (parseInt($('#messages').val().length) > parseInt($('#iMaxChars').val())){
{/literal}
			alert($('#sMaxChars').val());
{literal}
            return false;
        }
        show();
		return true;
	}
{/literal}
</script>
<div class="label_flow p_4 suggestion-popup">	
    <div id="js_friend_search">            
            {module name='suggestion.search' 
            friend_share=true 
            input='to'}
    </div>
    <div id="js_selected_friends" style="display: none;"></div>
    <div id="js_friend_mail">
        <form method="post" action="#" id="frmPost">
                <input type="hidden" id="sText" name="sText" value="{$sMessage}" />
                <input type="hidden" id="iMaxChars" name="iMaxChars" value="{$iMaxChars}" />
                <input type="hidden" id="sModule" name="sModule" value="{$sModule}" />
                <input type="hidden" id="sLink" name="sLink" value="{$sLink}" />
                <input type="hidden" id="sTitle" name="sTitle" value="{$sTitle}" />
                <input type="hidden" id="sSuggestionType" name="sSuggestionType" value="{$sSuggestionType}" />
                <input type="hidden" id="sMaxChars" name="sMaxChars" value="{$sMaxChars}" />
                <input type="hidden" id="sCompleted" name="sCompleted" value="{phrase var='suggestion.completed_request'}" />
                <input type="hidden" id="iSuggestion" name="iSuggestion" value="{$iSuggestion}" />
                <input type="hidden" id="iFriendId" name="iFriendId" value="{$iFriendId}" />
                <input type="hidden" id="sSuggestionText" name="sSuggestionText" value="{phrase var='suggestion.suggestion'}" />                
                <div id="js_selected_friends" style="display:none;"></div>
                <div class="p_4" style="clear: both;">
                        <div class="table form-group">
                            <div class="table_left">
                                    {phrase var='suggestion.message'}:
                            </div>
                            <div class="table_right">
                                <textarea class="form-control" id="messages" cols="30" rows="5" name="val[message]" style="width:95%;">{$sMessage}</textarea>
                                    <p>{$sMaxChars}</p>
                            </div>
                        </div>
                        <div class="table_clear">

                            {if $iSuggestionPages}
                                <button type="button" id="btnConfirm" class="button btn btn-primary btn-sm" onclick="if(shareFriendContinue1()) js_box_remove(this);">{$sContinue}</button>
                                <button type="button" id="btnSkip" class="button button_off btn btn-default btn-sm" onclick="js_box_remove(this);{if $sSuggestionType=='recommendation'}{else} recommendation();{/if} $Core.reloadPage();">{phrase var='suggestion.skip'}</button>
                            {else}
                                <button type="button" id="btnConfirm" class="button btn btn-primary btn-sm" onclick="if(shareFriendContinue()) js_box_remove(this);">{$sContinue}</button>
                                <button type="button" id="btnSkip" class="button button_off btn btn-default btn-sm" onclick="js_box_remove(this);{if $sSuggestionType=='recommendation'}{else} recommendation();{/if}">{phrase var='suggestion.skip'}</button>
                            {/if}
                            {if !$bDontAskMeAgain}
                                <div style="margin-top: 10px;">
                                    <input type="checkbox" onclick="dontAskMeAgain(document.getElementById('dont_ask_me_again').value);" id="dont_ask_me_again" name="bDontAskMeAgain" {if $bDontAskMeAgain} checked="checked" value="0" {else} value="1" {/if}> <span>{phrase var='suggestion.don_t_ask_me_again'}</span>
                                </div>
                            {/if}
                        </div>
                </div>    
            </form>
    </div>
    <div id="completed"></div>
</div>        

{literal}
<script lang="javascript">
    
    $('#js_selected_friends').remove();
    $('#content_load_data').append('<style>.row_moderate{display:none;}</style>'); 
    
    function dontAskMeAgain(iDontAskMeAgain)
    {
        document.getElementById('dont_ask_me_again').value = (iDontAskMeAgain == 1 ? 0 : 1);
        $.ajaxCall('suggestion.dontAskMeAgain', 'bDontAskMeAgain=' + iDontAskMeAgain, 'GET');
    }
    
    function show(){                
        $('#frmPost').append($('#js_selected_friends').html());
        var _params = ($('#frmPost').serialize());        
        
        /*remove all selected user*/
        $('#js_selected_friends').remove();
        $.ajaxCall('suggestion.addRequest',_params);
        $('#js_friend_mail').slideUp(200);
        $('#js_friend_loader').slideUp(200);               
    }
    function recommendation(){
        if ($('#iSuggestion').val()==1){                
            $('#js_selected_friends').remove();
            $.ajaxCall('suggestion.friends','skip=true&iFriendId='+$('#iFriendId').val()+'&sSuggestionType=recommendation');
        }else{
{/literal}
            {if $bSocialPublishers}
                suggestion_and_recommendation_tb_show('...',$.ajaxBox('suggestion.showSocialPublishers'));
            {/if}
{literal}
        }        
    }
    
</script>
<style>
    .suggestion_and_recommendation_js_box_close{height: 0px; overflow: hidden}
</style>
{/literal}