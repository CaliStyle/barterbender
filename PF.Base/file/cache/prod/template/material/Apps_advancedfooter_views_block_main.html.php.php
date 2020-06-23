<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */

?>
<style>
<?php if (Phpfox ::getParam('advancedfooter.enablebgimage')): ?>
    .main-footer:before {
        background-image:url('<?php if (Phpfox ::getParam('advancedfooter.footerbgimage')):   echo Phpfox::getParam('advancedfooter.footerbgimage');  else:  echo $this->_aVars['footerPath']; ?>footerbg.jpg<?php endif; ?>');
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
        opacity: <?php if (Phpfox ::getParam('advancedfooter.footerbgopacity')):   echo Phpfox::getParam('advancedfooter.footerbgopacity');  else: ?>0.1<?php endif; ?>;
    }
<?php endif; ?>
<?php if (Phpfox ::getParam('advancedfooter.advancedfootertheme') == 'light'): ?>
    <?php echo '
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
    '; ?>

<?php endif; ?>
<?php if (Phpfox ::getParam('advancedfooter.footerbackgroundcolor')): ?>
        .main-footer{
            background: <?php  echo Phpfox::getParam('advancedfooter.footerbackgroundcolor'); ?>;
        }
<?php endif; ?>
</style>

<div class="main-footer <?php echo Phpfox::getParam('advancedfooter.advancedfooterdesign'); ?> <?php if (Phpfox ::getParam('advancedfooter.advancedfootertheme') == 'light'): ?>light-theme<?php endif; ?>">
<?php if (Phpfox ::getParam('advancedfooter.advancedfooterdesign') == 'design1'): ?>
        <?php
						Phpfox::getLib('template')->getBuiltFile('advancedfooter.block.design1');
						?>
<?php elseif (Phpfox ::getParam('advancedfooter.advancedfooterdesign') == 'design2'): ?>
        <?php
						Phpfox::getLib('template')->getBuiltFile('advancedfooter.block.design2');
						?>
<?php elseif (Phpfox ::getParam('advancedfooter.advancedfooterdesign') == 'design3'): ?>
        <?php
						Phpfox::getLib('template')->getBuiltFile('advancedfooter.block.design3');
						?>
<?php elseif (Phpfox ::getParam('advancedfooter.advancedfooterdesign') == 'design4'): ?>
        <?php
						Phpfox::getLib('template')->getBuiltFile('advancedfooter.block.design4');
						?>
<?php elseif (Phpfox ::getParam('advancedfooter.advancedfooterdesign') == 'design5'): ?>
        <?php
						Phpfox::getLib('template')->getBuiltFile('advancedfooter.block.design5');
						?>
<?php endif; ?>
</div>


