<?php 
// in phpfox4 move to "profile.component_block_pic_clean.php" this plug in not work
if (Phpfox::isModule('suggestion') && Phpfox::isUser()){
    $sSuggestToFriends = _p('suggestion.suggest_to_friends_2');
    $sUserName = Phpfox::getUserBy('user_name');
    $sUserName = $this->request()->get('req1');
    $aUser = Phpfox::getService('suggestion')->getUserBy('user_name',$sUserName);
    
    if(is_array($aUser) && count($aUser)>0){
        $iFriendId = $aUser['user_id'];
        $bIsFriend = Phpfox::getService('suggestion')->isMyFriend($iFriendId);        
    }else{
        $iFriendId = Phpfox::getUserId();
        $bIsFriend = false;
    }
    if ($bIsFriend){
    ?> 
    <script language="javascript">
    $Behavior.loadProfileHeaderSuggestion = function(){
        if($('#suggestion_profile_btn').length <= 0){
            <?php if (Phpfox::getUserParam('suggestion.enable_friend_suggestion')){?>
                $('.profiles_action').find('ul').eq(0).prepend('<li id="suggestion_profile_btn"><a onclick="suggestion_and_recommendation_tb_show(\'...\',$.ajaxBox(\'suggestion.friends\',\'iFriendId=<?php  echo $iFriendId;?>&sSuggestionType=suggestion&sModule=suggestion_friend\')); return false;" href="#"><?php  echo $sSuggestToFriends?></a></li>');
            <?php }elseif(Phpfox::getUserParam('suggestion.enable_friend_recommend')){?>
                $('.profiles_action').find('ul').eq(0).prepend('<li id="suggestion_profile_btn"><a onclick="suggestion_and_recommendation_tb_show(\'...\',$.ajaxBox(\'suggestion.friends\',\'iFriendId=<?php  echo $iFriendId;?>&sSuggestionType=recommendation\')); return false;" href="#"><?php  echo $sSuggestToFriends?></a></li>');
            <?php }?>            
        }
    };
    </script>
    <?php }?>

<?php } /*end check module*/?>