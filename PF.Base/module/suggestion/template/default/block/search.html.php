<?php
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: search.html.php 2860 2011-08-20 19:17:52Z Raymond_Benc $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bSearch}

<div id="searchBlock">
    <div id="js_friend_loader_info"></div>
    <div id="iUserId" style="display:none">{$iFriendId}</div>
    <div id="js_friend_loader">
        {if $sFriendType != 'mail'}
        <div>
            <div>
                <span class="ynsug_span">{phrase var='friend.view'}:</span>
                <select class="form-control" style="height: 40px;padding-bottom: 0px;" name="view" onchange="showLoader(); if ($('#js_find_friend').val()=='<?php echo _p('suggestion.search_by_email_full_name'); ?>') $(this).ajaxCall('suggestion.searchAjax', 'input={$sPrivacyInputName}'); else $(this).ajaxCall('suggestion.searchAjax', 'input={$sPrivacyInputName}&find='+$('#js_find_friend').val()); return false;">
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

            <div class="t_right">
                <input type="text" class="js_suggestion_is_enter v_middle default_value form-control" name="find" value="{phrase var='suggestion.search_by_email_full_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_friend" size="30" />
                <button type="button"  onclick="showLoader(); $.ajaxCall('suggestion.searchAjax', 'friend_module_id={$sFriendModuleId}&amp;friend_item_id={$sFriendItemId}&amp;find=' + $('#js_find_friend').val() + '&amp;input={$sPrivacyInputName}'); return false;" class="button v_middle btn btn-success btn-sm" id="btnFind">{phrase var='friend.find'}</button>
            </div>
            <div class="clear"></div>
        </div>

        <div class="main_break"></div>
        <div class="separate"></div>

        {else}	
        <input type="text" class="js_suggestion_is_enter v_middle default_value form-control" name="find" value="{phrase var='suggestion.search_by_email_full_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_friend" size="30" />
        <button type="button" onclick="showLoader(); $.ajaxCall('suggestion.searchAjax', 'friend_module_id={$sFriendModuleId}&amp;friend_item_id={$sFriendItemId}&amp;find=' + $('#js_find_friend').val() + '&amp;input={$sPrivacyInputName}&amp;type={$sFriendType}'); return false;" class="button v_middle btn btn-success btn-sm">{phrase var='friend.find'}</button>	

        <div class="main_break"></div>
        <div class="separate"></div>
        {/if}

        <div class="t_center">
            {foreach from=$aLetters item=sLetter}<span style="padding-right:5px;"><a href="#" onclick="showLoader(); $.ajaxCall('suggestion.searchAjax', 'letter={$sLetter}&amp;input={$sPrivacyInputName}&amp;type={$sFriendType}'); return false;"{if $sActualLetter == $sLetter} style="text-decoration:underline;"{/if}>{$sLetter}</a></span>{/foreach}
        </div>
        <div class="main_break"></div>
        <div class="separate"></div>
        {/if}
        
        <div id="js_friend_search_content">
            <div class="main_break"></div>
            <div class="label_flow" style="height:180px;">
	
                {foreach from=$aFriends name=friend item=aFriend}
                <div style='width:160px; height:55px; border:0; float:left; padding:0; margin:0; position: relative; padding: 3px;' class="{if is_int($phpfox.iteration.friend/2)}row1{else}row2{/if}{if $phpfox.iteration.friend == 1} row_first{/if}{if isset($aFriend.is_active)} row_moderate{/if}">

                    {if !isset($aFriend.is_active)}
                    <span class="friend_checkbox"><input type="checkbox" class="checkbox" name="friend[]" class="js_friends_checkbox" id="js_friends_checkbox_{$aFriend.user_id}" value="{$aFriend.user_id}" {if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false}DISABLED {else} onclick="addFriendToSelectList(this, '{$aFriend.user_id}');"{/if} style="vertical-align:middle;" /></span>
                    {/if}
                    <span id="js_friend_{$aFriend.user_id}"><span class="friend_name">{$aFriend|user}{if isset($aFriend.is_active)} <em>({$aFriend.is_active})</em>{/if}{if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false} {phrase var='friend.cannot_select_this_user'}{/if}</span></span>
                </div>
                {foreachelse}
                <div class="extra_info">
                    {if $sFriendType == 'mail'}
                    {phrase var='user.sorry_no_members_found'}
                    {else}
                    {phrase var='friend.sorry_no_friends_were_found'}
                    {/if}
                </div>
                {/foreach}
            </div>
            <br/>
            <p>
                <input type="checkbox" value="" name="selectAll" id="selectAll" style="float: left;" onclick="selectAllFriends(document.getElementById('selectAll'));" />&nbsp;
                <label for="selectAll" style="word-wrap: break-word; line-height: 56px;padding-left: 5px; float: left;">{phrase var='suggestion.select_all'}</label>
                <span id="total" style="word-wrap: break-word; line-height: 56px;float:right;">{phrase var='suggestion.selected';} (<span id="totalChecked">0</span>)</span>
            </p>
        </div>

        {if !$bSearch}
        {if $bIsForShare}

        {else}
        {if $sPrivacyInputName != 'invite'}
        <div class="main_break t_right">		
            <button type="button" name="submit" onclick="{literal}if (function_exists('plugin_selectSearchFriends')) { plugin_selectSearchFriends(); } else { $Core.loadInit(); tb_remove(); }{/literal}" class="button btn btn-success btn-sm">{phrase var='friend.use_selected'}</button>&nbsp;<button type="button" name="cancel" onclick="{literal}if (function_exists('plugin_cancelSearchFriends')) { plugin_cancelSearchFriends(); } else { cancelFriendSelection(); }{/literal}" class="button btn btn-default btn-sm">{phrase var='friend.cancel'}</button>
        </div>
        {/if}
        {/if}
    </div>
</div>
<script type="text/javascript">
    
    var sPrivacyInputName = '{$sPrivacyInputName}';
    var sSearchByValue = '';
    {if $bDisabled}
    {literal}
    /*check if has no friends in list, disabled function related*/
    
    function disabledItem(){
        /*disabled select all check box*/
        $('#selectAll').attr('disabled','disabled');
        /*disabled messages*/
        $('#messages').attr('disabled','disabled');
        /*disabled button confirm*/
        $('#btnConfirm').attr('disabled','disabled');
        $('#btnConfirm').attr('class','button');        
        $('#btnConfirm').css('background','0');        
        $('#btnConfirm').css('background-color','#CCCCCC');        
        $('#btnConfirm').css('color','#999999');        
        $('#btnConfirm').css('border','1px solid #666666');                
    }
    disabledItem();
    {/literal}
    {/if}
    {literal}
    
    
    $Behavior.searchFriendBlock = function()
    {            
        sSearchByValue = $('.js_suggestion_is_enter').val();		

        if ($.browser.mozilla) 
        {
            $('.js_suggestion_is_enter').keypress(checkForEnter);
        } 
        else 
        {
            $('.js_suggestion_is_enter').keydown(checkForEnter);
        }		
    };

    
    function resetAllSelect(){
        $('.js_cached_friend_name').each(function(){
            $(this).remove();            
        });
        $('#totalChecked').html('0');
        updateCheckBoxes();
    }
    
    updateCheckBoxes();
    $('#totalChecked').html('0');
    function updateFriendsList()
    {		
        updateCheckBoxes();     
                
    }
	 
    function countTotalCheckedFriends(){
        var _iTotalChecked=$('#totalChecked').html();
        
        $('input[id^="js_friends_checkbox"]:checked').each(function(){
            _iTotalChecked++;
        });
        $('#totalChecked').html(_iTotalChecked);
        return parseInt(_iTotalChecked);
    }       
        
        /*
         * increase total checked friend to selected
         */
    function addCheckedFriends(){
        var _iTotalChecked=$('#totalChecked').html();
        _iTotalChecked++;        
        $('#totalChecked').html(_iTotalChecked);        
    }       
        
        /*
         * decrease total checked friend to selected
         */
    function removeCheckedFriends(){
        var _iTotalChecked= $('#totalChecked').html();
        if (_iTotalChecked > 0)
            _iTotalChecked--;        
        $('#totalChecked').html(_iTotalChecked);        
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
    	var checks = jQuery('input[class=checkbox]');
    	var flag = true;
    	for(var i = 0 ; i < checks.length ; i++)
    	{
    		if(checks[i].checked == false)
    		{
    			flag = false;	
    		}
    	}
    	
    	if(flag)
    	{
    		$('#selectAll')[0].checked = true
    	}
    	
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
            addCheckedFriends();
        }
        else
        {
            if (function_exists('plugin_removeFriendToSelectList'))
            {
                plugin_removeFriendToSelectList(sId);
            }			
			
            $('.js_cached_friend_id_' + sId).remove();
            $('#js_friend_input_' + sId).remove();
            removeCheckedFriends();
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
            $('#js_friends_checkbox_' + $(this).find('span').html()).prop('checked', true);
        });
        
        $('#totalChecked').html($('#js_friend_search_content .label_flow input:checked').length);
        $('#js_selected_count').html((iCnt / 2));
                
        iTotal = 0;
        var _aUserId = new Array();
        $('span[id^="js_friend_"]').each(function(){        
            var _id = $(this).attr('id').split("_");
            if (_id.length>0){
                _iUserId = _id[2];                                    
                _aUserId[iTotal++] = _iUserId;
            }
        });
       
      $.ajaxCall('suggestion.append_user_image','sUserId='+_aUserId.join(","));
      
       setTimeout(function(){ 
       	
            $('.image_deferred:not(.built)').each(function() {
            var t = $(this),
                src = t.data('src'),
                i = new Image();

            t.addClass('built');
            if (!src) {
                t.addClass('no_image');
                return;
            }

            t.addClass('has_image');
            i.onerror = function(e, u) {
                t.replaceWith('');
            };
            i.onload = function(e) {
                t.attr('src', src);
            };
            i.src = src;
            });

        }, 2000);
    }
	
    function showLoader()
    {                                
        $('#js_friend_search_content').html($.ajaxProcess(oTranslations['loading'], 'large'));
    }	
	
    function checkForEnter(event)
    {
        if (event.keyCode == 13) 
        {
            showLoader(); 
			
            $.ajaxCall('suggestion.searchAjax', 'find=' + $('#js_find_friend').val() + '&amp;input=' + sPrivacyInputName + '');
		
            return false;	
        }
    }
        
    $('#js_find_friend').click(function(){
		
		var abc="{/literal}<?php echo _p('suggestion.search_by_email_full_name'); ?>{literal}";
		
        if ($(this).val() == abc)
            $(this).val('');
            $(this).addClass('bold');
    });
    $('#js_find_friend').blur(function(){
	var def="{/literal}<?php echo _p('suggestion.search_by_email_full_name'); ?>{literal}";
        if ($(this).val() == ''){                    
            $(this).val(def);
            $(this).removeClass('bold');
        }
    });
    
    $('#js_find_friend').keypress(function(e){
        if(e.which == 13){
            $('#btnFind').click();
        }
    });
    
    function selectAllFriends(obj){
        _sId = obj.getAttribute('id');
        console.log($('#'+_sId));
        
        if ($('#'+_sId).is(":checked"))
        {
        	console.log("checked");
        }
        
        //current not checked all
        if ($('#'+_sId).is(":checked"))
        {
        
            $('#js_friend_search_content').find('input[id^="js_friends_checkbox"]').each(function(){
                
                sId = $(this).val();
                if(!$(this).is(":checked")){
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
                    addCheckedFriends();
                    $(this).prop('checked', true);
                }   
                
            });      
        }else{ //current checked all
            $('#js_friend_search_content').find('input[id^="js_friends_checkbox"]').each(function(){
                sId = $(this).val();
                if($(this).is(":checked")){
                    removeCheckedFriends();
                    if (function_exists('plugin_removeFriendToSelectList'))
                    {
                        plugin_removeFriendToSelectList(sId);                        
                    }	
                    $('.js_cached_friend_id_' + sId).remove();
                    $('#js_friend_input_' + sId).remove();
                    $(this).prop('checked', false);                    
                }
            });
        }
    }        
    {/literal}
</script>

{/if}
{literal}
<style>
    .friend_checkbox{position: absolute; left:60px; bottom:5px;}
    .friend_name{
        position: absolute; 
        left:60px; top:20px;
        white-space: nowrap;
        height:30px; overflow: hidden; line-height: 1.3em;}
    .bold{color:#000000 ! important;}

    

    .ynsug_span{
        float: left;
        margin-right: 10px;
        width: 40px;
        margin-top: 8px;
    }

    .ynsug_span + select{
        width: calc(100% - 50px);
        float: left;

    }
    .label_flow.suggestion-popup{
        padding: 10px;
    }
    .suggestion-popup span.friend_name
    {
        height: auto;
    }

    .suggestion-popup input:not([type="button"]),
    select,
    textarea{
        box-sizing:border-box;
        padding: 10px !important;
        background: #F4F4F4 !important;
        margin-bottom: 10px;
        margin-top: 0 !important;
    }

    .suggestion-popup textarea{
        width: 100% !important;
    }

     .suggestion-popup input[type="button"]{
        padding: 12px 20px !important;
        font-size: 15px;
        font-weight: 300;
        letter-spacing: 1px;
        text-transform: uppercase;
        box-shadow: none;
        display: block;
        text-align: center;
        background: transparent !important;
        color: #595959 !important;
        border: 1px #595959 solid !important;
        margin-bottom: 10px;
     }

     .suggestion-popup input[type="button"]:hover{
        background: #595959;
        color: #e5e5e5;
     }

    .suggestion-popup input[type="checkbox"],
    .suggestion-popup input[type="radio"]{
        width: auto !important;
    }

    .suggestion-popup .t_center{
        margin-bottom: 10px;
        margin-top: 10px;
        width: 100%;
        overflow: hidden;
        word-break: break-word;
        word-wrap: break-word;
    }
    .suggestion-popup  .table .table_right{
        padding: 0;
        border: none;
    }

    .suggestion-popup input.button{
        margin-bottom: 0 !important;
    }
    .suggestion-popup input.button_off{
        border: none !important;
        background: transparent !important;
        color: #9a9a9a !important;
        margin-top: 0 !important;
    }
    
    .suggestion-popup input.button_off:hover{
        background: #595959 !important;
        color: #e5e5e5 !important;
    }

    #js_friend_loader .label_flow {
        border-top: 1px solid #c8c8c8 !important ;
    }

    #js_friend_loader input {
        border-bottom: 1px solid #c8c8c8 !important ;
    }

</style>    
{/literal}