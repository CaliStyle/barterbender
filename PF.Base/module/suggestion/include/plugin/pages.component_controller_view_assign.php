<?php

$iPageId = (int)$this->request()->getInt('req2');

if ($iPageId>0 && Phpfox::isModule('suggestion')  && Phpfox::isUser() ){        
    /*get detail pages*/
    
    $aPages = Phpfox::getService('pages')->getPage($iPageId);
	$sTitle = base64_encode(urlencode($aPages['title']));
    
    $sSuggestToFriends = _p('suggestion.suggest_to_friends_2');
    $sCurrentUrl = Phpfox::getLib('url')->getFullUrl(); 
    $aRequest = Phpfox::getLib('request')->getRequests(); 
    $iUserId = Phpfox::getUserId();
    $html ='<a id=\"btnSuggestPage\" class=\"pages_like_join built\" onclick=\"suggestion_and_recommendation_tb_show(\'...\',$.ajaxBox(\'suggestion.friends\',\'iFriendId='.$iPageId.'&sSuggestionType=suggestion&sModule=suggestion_pages&sLinkCallback='.$sCurrentUrl.'&sTitle='.$sTitle.'&sPrefix=pages_\')); return false;\" href=\"#\">'.$sSuggestToFriends.'</a>';
    if (Phpfox::getUserParam('suggestion.enable_friend_suggestion')){
        if(!isset($aRequest['req3'])  || (isset($aRequest['req3']) && $aRequest['req3'] == "info" ) ) {
            $this->template()->setHeader('<script language="javascript">
            $Behavior.ynSuggestPage = function(){
                $().ready(function(){
                     if ($(\'._is_pages_view\').length)
                        {
                            var add = true
                            if (!$("#btnSuggestPage").length)
                            {
                                 $(\'.pages_header_name\').parent().append("'.$html.'");
                                 add = false;
                            }

                        }
                });
    }
    </script><style>
        #js_is_user_profile #js_is_page #section_menu{min-width: 0px;}
		#section_menu ul{float: right;}
    </style>');
        }
    }
} /*end check module*/?>