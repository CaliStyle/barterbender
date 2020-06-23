<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php 

 

?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_aVars['sCoreUrl']; ?>module/tourguides/static/css/default/default/tourguides.css" />
<?php echo '
<style>
.yntourguideclose
{
    background-image:url(';  echo $this->_aVars['sCoreUrl'];  echo 'module/tourguides/static/image/close.png);     
}
.yntourguidetooltip_arrow_T,
.yntourguidetooltip_arrow_B,
.yntourguidetooltip_arrow_TL,
.yntourguidetooltip_arrow_TR,
.yntourguidetooltip_arrow_BL,
.yntourguidetooltip_arrow_BR{
    background-image:url(';  echo $this->_aVars['sCoreUrl'];  echo 'module/tourguides/static/image/topbottom.png);     
}
.yntourguidetooltip_arrow_L,
.yntourguidetooltip_arrow_R,
.yntourguidetooltip_arrow_LT,
.yntourguidetooltip_arrow_LB,
.yntourguidetooltip_arrow_RT,
.yntourguidetooltip_arrow_RB{
    
    background-image:url(';  echo $this->_aVars['sCoreUrl'];  echo 'module/tourguides/static/image/leftright.png);     
}
</style>
<script type="text/javascript">
var iUserId = ';  echo $this->_aVars['iUserId'];  echo ';
var aTourSteps = ';  echo $this->_aVars['aTourSteps'];  echo ';
var sTourCurrentUrl = \'';  echo $this->_aVars['aParamsTour']['sCurrentUrl'];  echo '\';
var sTourController = \'';  echo $this->_aVars['aParamsTour']['sControllerName'];  echo '\';
var bCanCreate = ';  echo $this->_aVars['bCanCreate'];  echo ';
var aYnTourSession = ';  echo $this->_aVars['aYnTourSession'];  echo '; 
var aYnTour = ';  echo $this->_aVars['aYnTour'];  echo ';
var bCurrentView = true;
var sCurrentURL = "";
oTranslations[\'restart_tour_guide\'] = "';  echo _p('restart_tour_guide');  echo '";
oTranslations[\'end_tour_guide\'] = "';  echo _p('end_tour_guide');  echo '";
oTranslations[\'previous\'] = "';  echo _p('previous');  echo '";
oTranslations[\'next\'] = "';  echo _p('next');  echo '";
oTranslations[\'first_time_here\'] = "';  echo _p('first_time_here');  echo '";
oTranslations[\'start_the_tour\'] = "';  echo _p('start_the_tour');  echo '";
oTranslations[\'create_a_tour\'] = "';  echo _p('create_a_tour');  echo '";
oTranslations[\'complete\'] = "';  echo _p('complete');  echo '";
oTranslations[\'cancel\'] = "';  echo _p('cancel');  echo '";
oTranslations[\'login_as_admin\'] = "';  echo _p('login_as_admin');  echo '";
oTranslations[\'write_description_tour_guide\'] = "';  echo _p('write_description_tour_guide');  echo '";
oTranslations[\'background_color\'] = "';  echo _p('background_color');  echo '";
oTranslations[\'font_color\'] = "';  echo _p('font_color');  echo '";
oTranslations[\'position_display\'] = "';  echo _p('position_display');  echo '";
oTranslations[\'save_countinue\'] = "';  echo _p('save_countinue');  echo '";
oTranslations[\'tour_guide_name\'] = "';  echo _p('tour_guide_name');  echo '";
oTranslations[\'autorun_this_tour\'] = "';  echo _p('autorun_this_tour');  echo '";
oTranslations[\'second_s\'] = "';  echo _p('second_s');  echo '";
oTranslations[\'don_t_show_it_again_for_this_page\'] = "';  echo _p('don_t_show_it_again_for_this_page');  echo '";
oTranslations[\'time_display\'] = "';  echo _p('time_display');  echo '";
oTranslations[\'write_description_for_this_step\'] = "';  echo _p('write_description_for_this_step');  echo '";
oTranslations[\'yn_tour_language\'] = "';  echo _p('language');  echo '";
oTranslations[\'yn_multiple_languages\'] = "';  echo _p('multiple_languages');  echo '";
oTranslations[\'tourguides.provide_tour_guide_name\'] = "';  echo _p('provide_tour_guide_name');  echo '";
</script>
'; ?>

<script type="text/javascript" src="<?php echo $this->_aVars['sCoreUrl']; ?>module/tourguides/static/jscript/cufon-yui.js"></script>
<script type="text/javascript" src="<?php echo $this->_aVars['sCoreUrl']; ?>module/tourguides/static/jscript/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?php echo $this->_aVars['sCoreUrl']; ?>module/tourguides/static/jscript/tourguides.js"></script>
<?php echo $this->_aVars['sJsLanguage']; ?>
