<?php
	// Get Viewer Id and Viewed User 
	
	$iViewerId = Phpfox::getUserId();
	if(!$iViewerId)
	return;
	$aUser = $this->getParam('aUser');
	$sViewPhrase = _p('resume.view_resume');
	$sManagePhrase = _p('resume.manage_resume');
	
	// Get Published Resume of Viewed User
	$aResume = PHpfox::getService('resume')->getPublishedResumeByUserId($aUser['user_id']);
	
	$aResumeLink = "";
	if($aResume && isset($aResume['resume_id']) && isset($aResume['headline']))
	{
		$sResumeLink = Phpfox::getLib('url')->permalink('resume.view',$aResume['resume_id'],$aResume['headline']);
	}
	
	$sManageLink = Phpfox::getLib('url')->makeUrl('resume', array('view' => 'my'));
	
	// Can view resume
	$bIsFriend = Phpfox::isModule('friend') ? Phpfox::getService('friend')->isFriend($iViewerId, $aUser['user_id']) : 0;
	
	$bViewResumeRegistry = Phpfox::getService('resume.account')->checkViewResumeRegistration($iViewerId);
	
	$bCanViewResume = TRUE;
	
	if($iViewerId != $aUser['user_id'] && !$bIsFriend && !$bViewResumeRegistry)
	{
		$bCanViewResume = FALSE;
	}
?>

<style>
	#js_is_user_profile .profile_image
	{
		margin-bottom: 5px;
	}
	#resume_profile_linked_button
	{
		margin-bottom: 10px;
		text-align: center;
	}
	#resume_profile_linked_button a:hover
	{
		text-decoration: none;
	}
</style>

<script type="text/javascript">
    $Behavior.loadProfileUserResume = function(){

	// View Resume Button
	<?php if(!$aUser['use_timeline']){ ?>
		if($('.sub_section_menu').find('#resume_profile_linked_button').attr('rel')==undefined){

	$jqObject = $('#js_block_border_resume_categories');

	<?php if($aResume) { ?> 
		<?php if($bCanViewResume) { ?>
			
		<?php } else { ?>
			
  		<?php } ?> 
  	<?php }  ?>
  	
  	// Manage Resume Button
  	<?php if($aUser['user_id'] == $iViewerId && !$aResume) { ?>
	<?php } ?>
	}
	<?php }else{ ?>
		var breaklooptimeline = 0;
		$('#section_menu').find('ul:first').find('li').each(function(item,value){
		    if($(value).attr('id')=="viewresume")
		    {
		        breaklooptimeline = 1;
		    }
		});
		if(breaklooptimeline==0){
		$jqObject = $('#section_menu').find('ul:first');
	<?php if($aResume) { ?> 
		<?php if($bCanViewResume) { ?>
			
		<?php } else { ?>
			
  		<?php } ?> 
  	<?php }  ?>
  	
  	// Manage Resume Button
  	<?php if($aUser['user_id'] == $iViewerId && !$aResume) { ?>
	<?php } ?>
		}
		
	<?php } ?>
        }
</script>

<?php
	if(PHpfox::getLib("module")->getModuleName()=="resume")
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}	
?>