<?php 

if (Phpfox::isModule('suggestion') && Phpfox::isUser()){
    
    /*
     * get current user 
     */
    
    $aUser = Phpfox::getService('suggestion')->getUserBy('user_name','admin');
    if (count($aUser)>0)
        $iAdminUserId = $aUser['user_id'];
    else
        $iAdminUserId = 1;
?>
<script lang="javascript">
    $Behavior.friendIncomingAcceptSuggestion = function(){
        
        $('.js_drop_data_button').each(function(){
            
            /*confirm button click*/
            $(this).find('li').eq(0).find('input').click(function(){
                var _html = $(this).attr('onclick');
                
                _iFriendId = /user_id=[\d]+/i.exec(_html)+"";
                _iFriendId = _iFriendId.replace(/user_id=/g,"");
                
                if (_iFriendId != ''){
                    /*approve accept*/  
                    $.ajaxCall('suggestion.approve','iItemId='+<?php  echo Phpfox::getUserId();?>+'&iApprove=1&sModule=suggestion_friend&iFriendId='+_iFriendId+'&bAddFriend=0');
                    <?php if (Phpfox::getService('suggestion')->isAllowSuggestionPopup() && Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_friend_suggestion_popup')){?>
                         if(_iFriendId!="null"){suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId+'&sSuggestionType=suggestion'+'&sModule=suggestion_friend'));}
                    <?php }elseif(Phpfox::getService('suggestion')->isAllowRecommendationPopup() && Phpfox::getUserParam('suggestion.enable_friend_recommend')){?>
                         suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId+'&sSuggestionType=recommendation'));
                    <?php }?>
                }
                return true;
            });
            
            /*deny button click*/      
            $(this).find('li').eq(1).find('input').click(function(e){
                var _html = $(this).attr('onclick');
                
                _iFriendId = /user_id=[\d]+/i.exec(_html)+"";
                _iFriendId = _iFriendId.replace(/user_id=/g,"");
                
                if (_iFriendId != ''){
                    
                    setTimeout(function(){
                        $.ajaxCall('suggestion.approve','iItemId='+<?php  echo Phpfox::getUserId();?>+'&iApprove=2&sModule=suggestion_friend&iFriendId='+_iFriendId+'&bAddFriend=0');
                    },100);
                    
                }
                return true;
            }); 
        });        
    }
</script>
<?php }/*end check module*/?>