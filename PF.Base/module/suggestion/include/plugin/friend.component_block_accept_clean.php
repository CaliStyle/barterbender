<?php
	//Phpfox::getBlock('suggestion.advanced-menu');
	//echo Phpfox::getLib('ajax')->getContent(false);

/*get all incomming suggestion friend*/
$aSuggestFriends1 = Phpfox::getService('suggestion')->getSuggestionFriendList(Phpfox::getUserId());

shuffle($aSuggestFriends1);

$limit = count($aSuggestFriends1) < Phpfox::getParam('suggestion.number_friend_suggestion_header_bar') ? count($aSuggestFriends1) : Phpfox::getParam('suggestion.number_friend_suggestion_header_bar');

$aSuggestFriends = array();

for ($i=0; $i < $limit; $i++) { 
       
      $aSuggestFriends[]= array_shift($aSuggestFriends1);
}
/*echo '<pre>';
print_r($aSuggestFriends);*/
$PhraseSuggestFriend = _p('suggestion.friend_suggestions');
$PhrasePeopleMayYouKnow = _p('suggestion.people_may_you_know');
$no_new_suggest_friend = _p('suggestion.no_new_suggest_friend');
$no_recommend_to_you = _p('suggestion.no_recommend_to_you');
$add = _p('suggestion.add_header_block');
$addfriend = _p('suggestion.add_friend_header_block');
$ignore = _p('suggestion.ignore');
$suggested_by =  _p('suggestion.suggested_by');
$findFriends =  _p('suggestion.find_friends');
$findFriendLink = Phpfox::getLib('url')->makeUrl('suggestion', array('view' => 'friendsfriend'));
if(count($aSuggestFriends) > 0 ) {

?>
<div class="holder_notify_drop_title"><?php echo $PhraseSuggestFriend; ?></div>
<div class="clear"></div> 
<div class="all_list_suggest_friend">
<ul class"suggestion_friend" id="js_new_friend_holder_drop_1" >
<?php foreach ($aSuggestFriends as $key_suggest => $aSuggestFriend) {
$paramImage = array();
$paramImage['user'] = $aSuggestFriend['info_suggestion_friend'];
$paramImage['path'] = 'core.url_user';
$paramImage['file'] = $aSuggestFriend['info_suggestion_friend']['user_image'];
$paramImage['suffix'] = '_50_square';
$paramImage['max_width'] = '50';
$paramImage['max_height'] = '50';
$imageHTML = Phpfox::getLib('phpfox.image.helper')->display($paramImage);
$sLinkFriendSuggestion = Phpfox::getLib('url')->makeUrl('profile', $paramImage['user']['user_name']);
$sLinkWhoSuggested = Phpfox::getLib('url')->makeUrl('profile', $aSuggestFriend['user_suggest']['user_name']);
if($aSuggestFriend['number_mutal_friend'] == 1) {
    $PhraseMutualFriends =  _p('suggestion.mutual_friend',array('total' => $aSuggestFriend['number_mutal_friend'] )); 
}
else
{
$PhraseMutualFriends =  _p('suggestion.mutual_friends',array('total' => $aSuggestFriend['number_mutal_friend'] )); 
}
 ?>
    <li style=" padding-top: 5px; padding-bottom: 16px;" id="js_new_friend_request_<?php echo $key_suggest;?>" class="holder_notify_drop_data with_padding <?php if($key_suggest = '0') { echo 'first'; } ?> js_friend_request_1">
            <div class="drop_data_image">
                <?php echo $imageHTML;?>          
            </div>
    <div class="drop_data_content">
           <div class="drop_data_user">
                    <div class="drop_data_action" style="position: relative; float: right; margin-left: 10px;">
                        <div class="js_drop_data_add" style="display:none; padding-right:5px;">
                            <img src="<?php echo Phpfox::getParam('core.path');?>theme/frontend/default/style/default/image/ajax/add.gif"  alt="" />                  
                        </div>                       
                        <div class="js_drop_data_button" id="drop_down_1">
                            <ul class="table_clear_button_suggest_friend">
                                <li style="float: left; padding-right:5px; margin-bottom: 5px;"><button type="button" name="" class="button btn-primary btn-sm" onclick="return $Core.addAsFriend('<?php echo $aSuggestFriend['info_suggestion_friend']['user_id'];?>'); "><?php echo $add;?></button></li>
                                <li style="float: left;"><button style="width: 100%;" type="button" name="" class="button button_off btn btn-default btn-sm" onclick="ignoreSuggestFriend(<?php echo $aSuggestFriend['suggest_id'];?>,<?php echo $aSuggestFriend['user_suggest']['user_id']?>,<?php echo $aSuggestFriend['info_suggestion_friend']['user_id'];?>);"><?php echo $ignore;?></button>
                                </li>
                            </ul>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div style="line-height: 1.6">
                        <span class="user_profile_link_span" id="js_user_name_link_<?php echo $aSuggestFriend['info_suggestion_friend']['user_name'];?>"><a href="<?php echo $sLinkFriendSuggestion;?>"><?php echo $aSuggestFriend['info_suggestion_friend']['full_name'];?></a></span>                        
                        <br /><span style='color: #4F4F4F;'><?php echo $PhraseMutualFriends; ?></span>
                        <br /><span style='color: #4F4F4F;'><?php echo $suggested_by;?></span> <span class="user_profile_link_span"><a href="<?php echo $sLinkWhoSuggested;?>"><?php echo $aSuggestFriend['user_suggest']['full_name']?></a></span>
                        
                    </div>                  
        </div>              
    </div>
    <div class="clear"></div>       
    </li>
<?php } ?>
</ul>
<?php
}
else{
?>
<div class="holder_notify_drop_title"><?php echo $PhraseSuggestFriend; ?></div>
<div class="clear"></div> 
<div class="holder_notify_drop_data"><div class="drop_data_empty">
<?php echo $no_new_suggest_friend;?></div>
<div class="clear"></div> 
</div>
 <?php
}
?>
</div>


<?php
/*get all people may you know*/
$aPeopleYouMayKnows = Phpfox::getService('suggestion')->getPeopleYouMayKnow(Phpfox::getUserId());

if(count($aPeopleYouMayKnows) > 0 ) {
?>
<div class="holder_notify_drop_title"><?php echo $PhrasePeopleMayYouKnow; ?></div>
<div class="clear"></div> 
<div class="all_list_people_you_may_know">
<?php foreach ($aPeopleYouMayKnows as $key_suggest => $aPeopleYouMayKnow) {
$paramImage = array();
$paramImage['user'] = $aPeopleYouMayKnow['info_suggestion_friend'];
$paramImage['path'] = 'core.url_user';
$paramImage['file'] = $aPeopleYouMayKnow['info_suggestion_friend']['user_image'];
$paramImage['suffix'] = '_50_square';
$paramImage['max_width'] = '50';
$paramImage['max_height'] = '50';
$imageHTML = Phpfox::getLib('phpfox.image.helper')->display($paramImage);
$sLink = Phpfox::getLib('url')->makeUrl('profile', $paramImage['user']['user_name']);

if($aPeopleYouMayKnow['number_mutal_friend'] == 1) {
    $PhraseMutualFriends =  _p('suggestion.mutual_friend',array('total' => $aPeopleYouMayKnow['number_mutal_friend'] )); 
}
else
{
    $PhraseMutualFriends =  _p('suggestion.mutual_friends',array('total' => $aPeopleYouMayKnow['number_mutal_friend'] )); 
}

?>
<ul class"people_you_may_know"  >
    <li style=" padding-top: 5px; padding-bottom: 16px;" id="js_new_friend_request__<?php echo $key_suggest;?>" class="holder_notify_drop_data with_padding <?php if($key_suggest = 0) echo 'first';?> js_friend_request_1">
            <div class="drop_data_image">
            <?php echo $imageHTML;?>
            </div>
    <div class="drop_data_content">
           <div class="drop_data_user">
                    <div class="drop_data_action">
                        <div class="js_drop_data_add" style="display:none; padding-right:5px;">
                            <img src="<?php echo Phpfox::getParam('core.path');?>theme/frontend/default/style/default/image/ajax/add.gif"  alt="" />                  
                        </div>                       
                        <div class="js_drop_data_button" id="drop_down_1">
                            <ul class="table_clear_button_suggest_friend">
                                <li><button type="button" name="" class="button btn btn-success btn-sm" onclick="return $Core.addAsFriend('<?php echo $aPeopleYouMayKnow['info_suggestion_friend']['user_id'];?>'); "><?php echo $addfriend;?></button></li>
                            </ul>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div style="width:120px;line-height: 1.6"">
                        <span class="user_profile_link_span" id="js_user_name_link_<?php echo $aPeopleYouMayKnow['info_suggestion_friend']['user_name'];?>"><a href="<?php echo $sLink;?>"><?php echo $aPeopleYouMayKnow['info_suggestion_friend']['full_name'];?></a></span><br>                        
                        <span class="user_profile_link_span" style='color: #4F4F4F;' > <?php echo $PhraseMutualFriends;?> </span>
                    </div>                  
        </div>              
    </div>
    <div class="clear"></div>       
    </li>
</ul>
<?php } 
}
else{
?>
<div class="holder_notify_drop_title"><?php echo $PhrasePeopleMayYouKnow; ?></div>
<div class="clear"></div> 
<div class="holder_notify_drop_data"><div class="drop_data_empty">
<?php echo $no_recommend_to_you;?></div>
<div class="clear"></div> 
</div>
 <?php
}
?>
</div>
<a href="<?php echo $findFriendLink;?>" class="holder_notify_drop_link"><?php echo $findFriends; ?></a>



<?php if (Phpfox::isModule('suggestion') && Phpfox::isUser()){?>

<script lang="javascript">

    function ignoreSuggestFriend(suggest_id,user_id,item_id) {
        $('#js_new_friend_holder_drop_'+suggest_id).slideUp(); 
       
         $.ajaxCall('suggestion.ignoreSuggestFriend','iUserId='+user_id+
                                                    '&iItemId='+item_id
                                                        ); 

        if($('.all_list_suggest_friend > ul:visible').length == 1){
            $('.all_list_suggest_friend').append('<div class="clear"></div><div class="holder_notify_drop_data"><div class="drop_data_empty"> <?php echo $no_new_suggest_friend;?></div><div class="clear"></div></div>');
                                                 
         }
    }

    var _iFriendId = "";  
    
    $('.table_clear_button').find('input').eq(0).click(function(e){            
        e.preventDefault();
        var _html = $(this).attr('onclick');
        _iFriendId = /user_id=[\d]+/i.exec(_html)+"";
        _iFriendId = _iFriendId.replace(/user_id=/g,"");

        $.ajaxCall('suggestion.approve','iItemId='+<?php  echo Phpfox::getUserId();?>+'&iApprove=1&sModule=suggestion_friend&iFriendId='+_iFriendId+'&bAddFriend=0');
        <?php if (Phpfox::getService('suggestion')->isAllowSuggestionPopup() && Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_friend_suggestion_popup')){?>
            if(_iFriendId!="null"){suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId+'&sSuggestionType=suggestion'+'&sModule=suggestion_friend'));}
        <?php }elseif(Phpfox::getService('suggestion')->isAllowRecommendationPopup() && Phpfox::getUserParam('suggestion.enable_friend_recommend')){?>
            suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId+'&sSuggestionType=recommendation'));
        <?php }?>
        return true;
    });
    
    $('.table_clear_button').find('input').eq(1).click(function(e){
        e.preventDefault();
        var _html = $(this).attr('onclick');
        _iFriendId = /user_id=[\d]+/i.exec(_html)+"";
        _iFriendId = _iFriendId.replace(/user_id=/g,"");
        $.ajaxCall('suggestion.approve','iItemId='+<?php  echo Phpfox::getUserId();?>+'&iApprove=2&sModule=suggestion_friend&iFriendId='+_iFriendId);
        return true;
    });    
</script>

<?php }/*end check module*/?>