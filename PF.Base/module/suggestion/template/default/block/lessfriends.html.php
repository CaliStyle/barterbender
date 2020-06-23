<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aFriends)>0}
<div id="bIsAllowSuggestion" style="display:none;">{$bIsAllowSuggestion}</div>
<div id="suggsetionFriends">
    <div id="iCurrentUserId" style="display:none">{$iCurrentUserId}</div>
    <div id="sTitle" style="display:none">{$sTitle}</div>
<p style="background-color:#BAFC8B; padding:5px;">{phrase var='suggestion.your_friends_have_only_a_few_friends'}</p>



    {for $i = 0; $i < count($aFriends); $i++}
    {if $i == count($aFriends) - 1}
        <p style="position: absolute;">{$aFriends[$i].img}</p>
        <p style="padding: 0 0 15px 60px;">
            <strong>{$aFriends[$i].user_link)}</strong><BR>
            <b>{$aFriends[$i].total_friends}</b> {phrase var='suggestion.friends'}<BR>
            {$aFriends[$i].url}
        </p>
    {else}
        <p style="position: absolute;">{$aFriends[$i].img}</p>
        <p style="padding: 0 0 15px 60px; border-bottom:1px solid #f2f2f2;">
            <strong>{$aFriends[$i].user_link)}</strong><BR>
            <b>{$aFriends[$i].total_friends}</b> {phrase var='suggestion.friends'}<BR>
            {$aFriends[$i].url}
        </p>
    {/if}
    {/for}
</div>
{literal}

<script language="javascript">
function show_suggestfriend(e)
{
    _iFriendId = $(e).attr('rel');
    if (parseInt($('#bIsAllowSuggestion').html()) == 1)
        suggestion_and_recommendation_tb_show($('#sTitle').html(),$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId));
    else
        suggestion_and_recommendation_tb_show($('#sTitle').html(),$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId+'&sSuggestionType=recommendation'));
}
</script>
{/literal}

{literal}
    <style>
        #js_block_border_suggestion_lessfriends{
            background: #FFF !important;
        }

        #js_block_border_suggestion_lessfriends .title,
        #js_block_border_suggestion_lessfriends .content{
            padding-top: 10px !important;
            padding-right: 10px !important;
            padding-bottom: 0px !important;
            padding-left: 10px !important;
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
        #js_block_border_suggestion_lessfriends{
            display:none !important;
        }
    </style>
{/literal}

<p style="display:none;">{phrase var='suggestion.no_friends'}</p>

{/if}