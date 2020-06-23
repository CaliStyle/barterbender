<?php if (Phpfox::isModule('suggestion') && Phpfox::isUser()){?>
<script lang="javascript">
	
   $('a[class^="main_link"]').each(function(){
       var _href = $(this).attr('href');
       
       if (_href.indexOf("#suggest") >= 0){
           _href = _href.split("_");
           
           iUserId = _href[1];      
           sUserName = _href[2];           
           $(this).click(function(e){
         _href = $(this).attr('href');
				_href = _href.split("_");
				iUserId = _href[1];      
				sUserName = _href[2];
               e.preventDefault();
               <?php  if (Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_friend_suggestion_popup') && Phpfox::getService('suggestion')->isAllowSuggestionPopup()) { ?>
                    suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+iUserId+'&sSuggestionType=friend_add&sModule=suggestion_friend'));
               <?php }?>
           });           
       }
   });   
</script>
<?php }/*end check module*/?>