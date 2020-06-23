<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (($iEditId = $this->request()->getInt('id')) && ($aPage = Phpfox::getService('pages')->getForEdit($iEditId)))			
{
    $bshow = 0;
    $_aFeed['item_id'] = $iEditId;
    $_aFeed['sModule'] = 'pages';
    $sTitle = base64_encode(urlencode($aPage['title']));
    $sPrefix = phpfox::getT('');
    if($aPage['time_stamp']+100>PHPFOX_TIME)
    {
        if(!isset($_SESSION['pages_popup'][$iEditId]))
        {
            $bshow = 1;
            $_SESSION['pages_popup'][$iEditId] = true;
        }
    }
    if($bshow==1){
?>

<script text="text/javascript">
  
<?php  
if (Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_content_suggestion_popup') && Phpfox::getService('suggestion')->isAllowContentSuggestionPopup()){?>
    $Behavior.loadAddPagePluginSuggestion = function(){
        $(document).ready(function(){
            setTimeout(function(){            
                suggestion_and_recommendation_tb_show("...",$.ajaxBox('suggestion.friends','iFriendId='+<?php echo $_aFeed['item_id'];?>+'&sSuggestionType=suggestion'+'&sModule=suggestion_<?php   echo $_aFeed['sModule']?>&sLinkCallback=&sTitle=<?php   echo $sTitle;?>&sPrefix=<?php   echo $sPrefix;?>&sExpectUserId='));
            }, 500);                
        });
    }
<?php  
/*unset all suggestion section if suggestion is not active*/
}else{        
    unset($_SESSION['suggestion']);
}
?>    
   
</script>

<?php }} ?>