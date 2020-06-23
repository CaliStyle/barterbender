<?php 
$aPage = Phpfox::getService('suggestion')->isPage($this->request()->get('req1'));
if ($aPage !== false) {
    $iPageId = (int)$aPage['page_id'];
    
    if ($iPageId>0 && Phpfox::isModule('suggestion')  && Phpfox::isUser() ){        
        /*get detail pages*/
    
        $aPages = Phpfox::getService('pages')->getPage($iPageId);
        $sTitle = base64_encode(urlencode($aPages['title']));

        $sSuggestToFriends = _p('suggestion.suggest_to_friends_2');
        $sCurrentUrl = Phpfox::getLib('url')->getFullUrl(); 
        $iUserId = Phpfox::getUserId();
        ?>
              <script language="javascript">
            
        $Behavior.loadProfileIndexSuggestion = function(){
        	if($('#suggestion_page_btn').length <= 0){
        	
                <?php if (Phpfox::getUserParam('suggestion.enable_friend_suggestion')){?>                    
                    $('#section_menu').eq(0).append('<ul><li id="suggestion_page_btn"><a onclick="suggestion_and_recommendation_tb_show(\'...\',$.ajaxBox(\'suggestion.friends\',\'iFriendId=<?php  echo $iPageId;?>&sSuggestionType=suggestion&sModule=suggestion_pages&sLinkCallback=<?php  echo $sCurrentUrl;?>&sTitle=<?php  echo $sTitle;?>&sPrefix=pages_\')); return false;" href="#"><?php  echo $sSuggestToFriends?></a></li></ul>');                    
                <?php }?>
                
        	}                    
        };
        
        </script>     
        <style>
            #js_is_user_profile #js_is_page #section_menu{min-width: 0px;}
            #js_is_user_profile #js_is_page #section_menu ul{float:right;}
        </style>
    <?php  }    
}
?>