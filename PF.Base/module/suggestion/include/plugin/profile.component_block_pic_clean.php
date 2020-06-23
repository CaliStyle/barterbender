<?php 

if (Phpfox::isModule('suggestion') && Phpfox::isUser()){
    

$sSuggestionLink = Phpfox::permalink('suggestion', null);
$sSuggestion = _p('suggestion.suggestion');
$iTotalSuggestion = (int)Phpfox::getService('suggestion')->getTotalIncomingSuggestion(Phpfox::getUserId());

$sUserName = $this->request()->get('req1');

if ($sUserName == Phpfox::getUserBy('user_name')){   

/*display total suggestion from 0*/
$sSuggestion .= '<span class="badge_number">'.$iTotalSuggestion.'</span>';

$sBg = Phpfox::getParam('core.path')."module/suggestion/static/image/suggestion.png";
?>
<script language="javascript">

    $Behavior.loadPluginSuggestionBlockPic = function(){
        $().ready(function(){
            if (!$Core.exists('#suggestion_leftbar')){
                $('#page_profile_index .profiles_menu > ul.container-fluid').find('li.dropdown > ul').eq(0).append('<li class="" id="suggestion_leftbar"><a href="<?php  echo $sSuggestionLink?>"><?php  echo $sSuggestion?></a></li>');
                $('#page_profile_index .timeline_main_menu').find('ul').eq(0).append('<li class="suggestion" id="suggestion_leftbar"><a href="<?php  echo $sSuggestionLink?>"><?php  echo $sSuggestion?></a></li>');
             }
        });    
    }
</script>
<style>
    .suggestion:hover{background-color:#EFF9FF}
</style>
<?php }?>

<?php } /*end check module*/?>


<?php 
// this code from  "profile.component_block_header_process.php"; 

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
	
	if ($aUser['user_id'] == Phpfox::getUserId())
	{
		   $bIsFriend = false;
	}
	?>
	<script language="javascript">
	<?php
    if ($bIsFriend){
    ?> 
		var bIsFriend = true;
	<?php }else
		{?>
			var bIsFriend = false;
			<?php }
	?>
	</script>
	<?php
    if ($bIsFriend){
    ?> 
    <script language="javascript">
    
    $Behavior.loadProfileHeaderSuggestion = function(){
    	if (bIsFriend){
    		console.log(bIsFriend);
		        if($('#suggestion_profile_btn').length <= 0){
		            <?php if (Phpfox::getUserParam('suggestion.enable_friend_suggestion')){?>
		                $('.profiles_action').find('ul').eq(0).prepend('<li id="suggestion_profile_btn"><a onclick="suggestion_and_recommendation_tb_show(\'...\',$.ajaxBox(\'suggestion.friends\',\'iFriendId=<?php  echo $iFriendId;?>&sSuggestionType=suggestion&sModule=suggestion_friend\')); return false;" href="#"><i class="fa fa-users"></i><?php //  echo $sSuggestToFriends?></a></li>');
		            <?php }elseif(Phpfox::getUserParam('suggestion.enable_friend_recommend')){?>
		                $('.profiles_action').find('ul').eq(0).prepend('<li id="suggestion_profile_btn"><a onclick="suggestion_and_recommendation_tb_show(\'...\',$.ajaxBox(\'suggestion.friends\',\'iFriendId=<?php  echo $iFriendId;?>&sSuggestionType=recommendation\')); return false;" href="#"><i class="fa fa-users"></i><?php // echo $sSuggestToFriends?></a></li>');
		            <?php }?>            
		        }
    	}else
    	{
    		$('#suggestion_profile_btn').remove();
    	}
    	
    };
    </script>
    <?php }?>

<?php } /*end check module*/?>