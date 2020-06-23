<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 20/02/2017
 * Time: 11:30
 */
function suggestion_install401p8()
{

    $oDb = Phpfox::getLib('phpfox.database');

    $oDb->query("UPDATE  `". Phpfox::getT('user_group_setting') ."`
		 SET default_admin ='friend,photo,blog,forum,pages,poll,video,quiz,event,music,marketplace,fevent,document,videochannel,musicsharing,musicstore,advancedphoto,contest,fundraising,coupon,gettingstarted,jobposting,petition,ultimatevideo,ynsocialstore,ynblog'
		 WHERE module_id ='suggestion' AND name = 'support_module';");

    $oDb->query("UPDATE  `". Phpfox::getT('user_group_setting') ."`
		 SET default_user ='friend,photo,blog,forum,pages,poll,video,quiz,event,music,marketplace,fevent,document,videochannel,musicsharing,musicstore,advancedphoto,contest,fundraising,coupon,gettingstarted,jobposting,petition,ultimatevideo,ynsocialstore,ynblog'
		 WHERE module_id ='suggestion' AND name = 'support_module';");

    $oDb->query("UPDATE  `". Phpfox::getT('user_group_setting') ."`
		 SET default_staff ='friend,photo,blog,forum,pages,poll,video,quiz,event,music,marketplace,fevent,document,videochannel,musicsharing,musicstore,advancedphoto,contest,fundraising,coupon,gettingstarted,jobposting,petition,ultimatevideo,ynsocialstore,ynblog'
		 WHERE module_id ='suggestion' AND name = 'support_module';");

    $oDb->query("UPDATE  `". Phpfox::getT('language_phrase') ."`
		 SET text ='Supported modules to be integrated with Recommendation & Suggestion module.<BR>Default modules: photo,blog,forum,pages,poll,video,quiz,event,music,marketplace,fevent,document,videochannel,musicsharing,<BR>musicstore,advancedphoto,contest,fundraising,coupon,gettingstarted,jobposting,petition,ultimatevideo,ynsocialstore,ynblog.<BR>Notice: If you want to support other modules, please input the module names into textbox beside and separate them with commas.'
		 WHERE module_id ='suggestion' AND var_name = 'user_setting_support_module';");

    $oDb->query("UPDATE  `". Phpfox::getT('language_phrase') ."`
		 SET text_default ='Supported modules to be integrated with Recommendation & Suggestion module.<BR>Default modules: photo,blog,forum,pages,poll,video,quiz,event,music,marketplace,fevent,document,videochannel,musicsharing,<BR>musicstore,advancedphoto,contest,fundraising,coupon,gettingstarted,jobposting,petition,ultimatevideo,ynsocialstore,ynblog.<BR>Notice: If you want to support other modules, please input the module names into textbox beside and separate them with commas.'
		 WHERE module_id ='suggestion' AND var_name = 'user_setting_support_module';");

}

suggestion_install401p8();
?>