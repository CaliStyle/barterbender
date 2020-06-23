<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aRows)>0}
<div id="eventsBlock">  
    <input type="hidden" value="{$iUserId}" id="userlogin"/>
    {foreach from=$aRows item=aRow}
    	{if $aRow.hasUser}
              
        <div style="position: absolute">
        	
        	{$aRow.img}
        	
        </div>
        <p style="padding: 0 0 5px 60px; border-bottom:1px solid #CCCCCC; margin-bottom: 10px;">
            <span class="l13">                
                <a style="font-weight:bold;" rel="{$aRow.is_right}" id="{$aRow.link}" href="{$aRow.link}" target="_blank" class="suggestion-join-events">{$aRow.title}
                    <span style="display:none">{$aRow.encode_link}</span>
                    <span class="divIUserId" style="display:none;">{$aRow.user_id}</span>
                    <span class="title" style="display:none;">{$aRow.title}</span>
                </a>
            </span><br />                        
            <span class="l13 suser">{phrase var='suggestion.created_by'} {$aRow.user_link}</span><br />
            <span style="color:#808080">{$aRow.time_stamp|convert_time}</span><br />
            
            
            <span class="l13">
                {if $aRow.isAllowSuggestion}
                    <a id="{$aRow.event_id}" class="suggest-event" href="#" rel="">{phrase var='suggestion.suggest_to_friends_2'}</a>
                {/if}
                {if ($aRow.isAllowSuggestion && $aRow.display_join_link)} - {/if}
                {if $aRow.display_join_link}
                    <a rel="{$aRow.is_right}" id="{$aRow.link}" href="{$aRow.link}" target="_blank" class="suggestion-join-events">{phrase var='suggestion.join_event'}
                        <span style="display:none">{$aRow.encode_link}</span>
                        <span class="divIUserId" style="display:none;">{$aRow.user_id}</span>
                        <span class="title" style="display:none;">{$aRow.title}</span>
                    </a>
                {else}
                <a href="#" style="display:none;"><span class="divIUserId" style="display:none;">{$aRow.user_id}</span></a>
                {/if}
            </span><br />
            
        </p>
        <p id="suggestion-event-{$aRow.event_id}" style="display:none">{$aRow.event_id}++{$aRow.link}++<?php echo base64_encode($this->_aVars['aRow']['title']); ?></p>
        {/if}
    {/foreach}
</div>
{literal}
<script language="javascript">
        $Behavior.eventsClick = function(){
            $('.suggest-event').click(function(e){
                e.preventDefault();
                
                var _iId = $(this).attr('id');               
                
                var _sExpectUserId = $(this).next().find('span[class="divIUserId"]').eq(0).html();
                if (_sExpectUserId != '')                    
                    _sExpectUserId = parseInt(_sExpectUserId);
                
                var _aParams = $('#suggestion-event-'+_iId).html().split('++'); 
                var user_id=$('#userlogin').val();
                if(user_id==0)
                {
                    suggestion_and_recommendation_tb_show('Login', $.ajaxBox('user.login', 'height=250&width=400'));$('body').css('cursor', 'auto');
                }
                else
                {
                    suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+_aParams[0]+'&sSuggestionType=suggestion'+'&sModule=suggestion_event&sLinkCallback='+_aParams[1]+'&sTitle='+_aParams[2]+'&sPrefix='+'&sExpectUserId='+_sExpectUserId));
                }
            });
            
            $('.suggestion-join-events').click(function(e){
                e.preventDefault();
                var _bIsRight = $(this).attr('rel');
                if (_bIsRight == '1'){
                    var pop_window = window.open($(this).attr('id'),'pop_window','toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes');
                    if (window.focus) {pop_window.focus();}                        
                }else{
                    var _iUserId = $(this).find('.divIUserId').eq(0).html();
                    var user_id_page=$('#userlogin').val();
                    var _sTitle = $(this).find('.title').html();
                    if(user_id_page==0)
                    {
                         suggestion_and_recommendation_tb_show('Login', $.ajaxBox('user.login', 'height=250&width=400'));$('body').css('cursor', 'auto');  
                    }
                    else
                    {
                        suggestion_and_recommendation_tb_show('', $.ajaxBox('suggestion.compose', 'height=300&width=500&id=' + _iUserId + '&link=' + _sTitle + '&no_remove_box=true'));
                    }
                }
            });
        };
</script>
<style>
    .l13{line-height: 1.5em}
    .suser{color:#808080}
    .suser a{color:#4F4F4F}

        #js_block_border_suggestion_events{
            background: #FFF !important;
        }

        #js_block_border_suggestion_events .title,
        #js_block_border_suggestion_events .content{
            padding: 10px !important;
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: auto !important;
        }

</style>
{/literal}
{else}
{literal}
<style>
    #js_block_border_suggestion_events{display:none;}
</style>
{/literal}
{/if}