<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<style>
    {if Phpfox::getParam('advancedfooter.enablebgimage')}
    .main-footer:before {ldelim}
        background-image:url('{if Phpfox::getParam('advancedfooter.footerbgimage')}{php} echo Phpfox::getParam('advancedfooter.footerbgimage');{/php}{else}{$footerPath}footerbg.jpg{/if}');
        background-size: cover;
        background-position:center right;
        content: "";
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        opacity: {if Phpfox::getParam('advancedfooter.footerbgopacity')}{php} echo Phpfox::getParam('advancedfooter.footerbgopacity');{/php}{else}0.1{/if};
    {rdelim}
    {/if}
    {if Phpfox::getParam('advancedfooter.advancedfootertheme') == 'light'}
    {literal}
        .main-footer{
            background: #fefefe;
        }
        .main-footer .copyright h4,.main-footer .footer-heading,.main-footer .footer-heading a,.main-footer .footer-heading a:hover,.main-footer  .footer-heading a:focus,.main-footer  .footer-heading a:visited{
            color: #000;
        }
         .main-footer .copyright-title{
            color:#777;
        }
        .main-footer ul li a:hover,.main-footer .design-2 .select-lang:hover{
            color: #000;
        }
        .copyright-wrap.design-3 .copyright,.main-footer.copyright{
            border-top-color:#33333357;
        }
    {/literal}
    {/if}
    {if Phpfox::getParam('advancedfooter.footerbackgroundcolor')}
        .main-footer{ldelim}
            background: {php} echo Phpfox::getParam('advancedfooter.footerbackgroundcolor');{/php};
        {rdelim}
    {/if}
</style>

<div class="main-footer {php}echo Phpfox::getParam('advancedfooter.advancedfooterdesign');{/php} {if Phpfox::getParam('advancedfooter.advancedfootertheme') == 'light'}light-theme{/if}">
    {if Phpfox::getParam('advancedfooter.advancedfooterdesign') == 'design1'}
        {template file='advancedfooter.block.design1'}
    {elseif Phpfox::getParam('advancedfooter.advancedfooterdesign') == 'design2'}
        {template file='advancedfooter.block.design2'}
    {elseif Phpfox::getParam('advancedfooter.advancedfooterdesign') == 'design3'}
        {template file='advancedfooter.block.design3'}
    {elseif Phpfox::getParam('advancedfooter.advancedfooterdesign') == 'design4'}
        {template file='advancedfooter.block.design4'}
    {elseif Phpfox::getParam('advancedfooter.advancedfooterdesign') == 'design5'}
        {template file='advancedfooter.block.design5'}
    {/if}
</div>

