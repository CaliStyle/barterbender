<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<link rel="stylesheet" type="text/css" href="{$sCoreUrl}module/tourguides/static/css/default/default/tourguides.css" />
{literal}
<style>
.yntourguideclose
{
    background-image:url({/literal}{$sCoreUrl}{literal}module/tourguides/static/image/close.png);     
}
.yntourguidetooltip_arrow_T,
.yntourguidetooltip_arrow_B,
.yntourguidetooltip_arrow_TL,
.yntourguidetooltip_arrow_TR,
.yntourguidetooltip_arrow_BL,
.yntourguidetooltip_arrow_BR{
    background-image:url({/literal}{$sCoreUrl}{literal}module/tourguides/static/image/topbottom.png);     
}
.yntourguidetooltip_arrow_L,
.yntourguidetooltip_arrow_R,
.yntourguidetooltip_arrow_LT,
.yntourguidetooltip_arrow_LB,
.yntourguidetooltip_arrow_RT,
.yntourguidetooltip_arrow_RB{
    
    background-image:url({/literal}{$sCoreUrl}{literal}module/tourguides/static/image/leftright.png);     
}
</style>
<script type="text/javascript">
var iUserId = {/literal}{$iUserId}{literal};
var aTourSteps = {/literal}{$aTourSteps}{literal};
var sTourCurrentUrl = '{/literal}{$aParamsTour.sCurrentUrl}{literal}';
var sTourController = '{/literal}{$aParamsTour.sControllerName}{literal}';
var bCanCreate = {/literal}{$bCanCreate}{literal};
var aYnTourSession = {/literal}{$aYnTourSession}{literal}; 
var aYnTour = {/literal}{$aYnTour}{literal};
var bCurrentView = true;
var sCurrentURL = "";
oTranslations['restart_tour_guide'] = "{/literal}{_p var='restart_tour_guide'}{literal}";
oTranslations['end_tour_guide'] = "{/literal}{_p var='end_tour_guide'}{literal}";
oTranslations['previous'] = "{/literal}{_p var='previous'}{literal}";
oTranslations['next'] = "{/literal}{_p var='next'}{literal}";
oTranslations['first_time_here'] = "{/literal}{_p var='first_time_here'}{literal}";
oTranslations['start_the_tour'] = "{/literal}{_p var='start_the_tour'}{literal}";
oTranslations['create_a_tour'] = "{/literal}{_p var='create_a_tour'}{literal}";
oTranslations['complete'] = "{/literal}{_p var='complete'}{literal}";
oTranslations['cancel'] = "{/literal}{_p var='cancel'}{literal}";
oTranslations['login_as_admin'] = "{/literal}{_p var='login_as_admin'}{literal}";
oTranslations['write_description_tour_guide'] = "{/literal}{_p var='write_description_tour_guide'}{literal}";
oTranslations['background_color'] = "{/literal}{_p var='background_color'}{literal}";
oTranslations['font_color'] = "{/literal}{_p var='font_color'}{literal}";
oTranslations['position_display'] = "{/literal}{_p var='position_display'}{literal}";
oTranslations['save_countinue'] = "{/literal}{_p var='save_countinue'}{literal}";
oTranslations['tour_guide_name'] = "{/literal}{_p var='tour_guide_name'}{literal}";
oTranslations['autorun_this_tour'] = "{/literal}{_p var='autorun_this_tour'}{literal}";
oTranslations['second_s'] = "{/literal}{_p var='second_s'}{literal}";
oTranslations['don_t_show_it_again_for_this_page'] = "{/literal}{_p var='don_t_show_it_again_for_this_page'}{literal}";
oTranslations['time_display'] = "{/literal}{_p var='time_display'}{literal}";
oTranslations['write_description_for_this_step'] = "{/literal}{_p var='write_description_for_this_step'}{literal}";
oTranslations['yn_tour_language'] = "{/literal}{_p var='language'}{literal}";
oTranslations['yn_multiple_languages'] = "{/literal}{_p var='multiple_languages'}{literal}";
oTranslations['tourguides.provide_tour_guide_name'] = "{/literal}{_p var='provide_tour_guide_name'}{literal}";
</script>
{/literal}
<script type="text/javascript" src="{$sCoreUrl}module/tourguides/static/jscript/cufon-yui.js"></script>
<script type="text/javascript" src="{$sCoreUrl}module/tourguides/static/jscript/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="{$sCoreUrl}module/tourguides/static/jscript/tourguides.js"></script>
{$sJsLanguage}