<?php

if (Phpfox::isModule('suggestion') && Phpfox::isUser()){
    
    $sTypeId = $aFeed['type_id'];
    $aTypeId = explode('_', $sTypeId);

    $iUserId = $aFeed['user_id'];
    $sModule = 'suggestion_' . $aTypeId[0];
    $iItemId = $aFeed['item_id'];

    /*delete each suggestion notification detail*/
    $aSuggestionList = Phpfox::getService('suggestion')->getSuggestionListByUserId($iUserId, $iItemId, $sModule);
    if (count($aSuggestionList)>0){
        foreach($aSuggestionList as $aSuggestion){
            $iSuggestionId = $aSuggestion['suggestion_id'];
            Phpfox::getService('suggestion.process')->deleteNotification($sModule, $iItemId, $iSuggestionId);
        }
    }
    Phpfox::getService('suggestion.process')->delete($iUserId, $sModule, $iItemId);
    
    
} /*end check module*/
?>