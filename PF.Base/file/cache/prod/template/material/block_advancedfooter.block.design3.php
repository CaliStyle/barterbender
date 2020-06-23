<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 16, 2020, 4:45 pm */ ?>
<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */

?>
<div class="container design-3">
<?php if (Phpfox ::getParam('advancedfooter.enablejoinshareimage') && ! Phpfox ::getUserId()): ?>
        <div class="footer-register">
            <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('user.register'); ?>" class="join-share-a">
                <img src="<?php echo $this->_aVars['footerPath'];  if (Phpfox ::getParam('advancedfooter.advancedfootertheme') == 'light'): ?>join_light.png<?php else: ?>join.png<?php endif; ?>" />
            </a>
            <a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl('user.register'); ?>" class="button btn btn-primary footer-register-button btn-gradient">
<?php echo _p('Join Now'); ?>
            </a>
        </div>
<?php endif; ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
<?php if (count((array)$this->_aVars['aFooterMenus'])):  $this->_aPhpfoxVars['iteration']['footerMenus'] = 0;  foreach ((array) $this->_aVars['aFooterMenus'] as $this->_aVars['aItem']):  $this->_aPhpfoxVars['iteration']['footerMenus']++; ?>

<?php if ($this->_aPhpfoxVars['iteration']['footerMenus'] < 5): ?>
                        <div class="col-sm-6">
                            <h4 class="footer-heading">
<?php if (! empty ( $this->_aVars['aItem']['link'] ) || ! empty ( $this->_aVars['aItem']['direct_link'] )): ?>
                                    <a href="<?php if (! empty ( $this->_aVars['aItem']['link'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aItem']['link']);  elseif (! empty ( $this->_aVars['aItem']['direct_link'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['aItem']['direct_link']);  endif; ?>">
<?php endif; ?>
<?php echo $this->_aVars['aItem']['name']; ?>
<?php if (! empty ( $this->_aVars['aItem']['link'] ) || ! empty ( $this->_aVars['aItem']['direct_link'] )): ?>
                                    </a>
<?php endif; ?>
                            </h4>
<?php if (! empty ( $this->_aVars['aItem']['sub'] )): ?>
                                <ul>
<?php if (count((array)$this->_aVars['aItem']['sub'])):  foreach ((array) $this->_aVars['aItem']['sub'] as $this->_aVars['sub']): ?>
                                        <li>
                                            <a href="<?php if (! empty ( $this->_aVars['sub']['link'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['sub']['link']);  elseif (! empty ( $this->_aVars['sub']['direct_link'] )):  echo Phpfox::getLib('phpfox.url')->makeUrl($this->_aVars['sub']['direct_link']);  endif; ?>">
<?php echo $this->_aVars['sub']['name']; ?>
                                            </a>
                                        </li>
<?php endforeach; endif; ?>
                                </ul>
<?php endif; ?>
                        </div>
<?php endif; ?>
<?php endforeach; endif; ?>
            </div>
        </div>
        <div class="col-sm-3">
            <h4 class="footer-heading">
<?php echo _p('Our Members'); ?>
            </h4>
<?php if (! empty ( aFooterUsers )): ?>
                <div class="footer-users">
<?php if (count((array)$this->_aVars['aFooterUsers'])):  foreach ((array) $this->_aVars['aFooterUsers'] as $this->_aVars['aUser']): ?>
                        <div>
<?php echo Phpfox::getLib('phpfox.image.helper')->display(array('user' => $this->_aVars['aUser'],'suffix' => '_50_square','max_width' => 50,'max_height' => 50)); ?>
                        </div>
<?php endforeach; endif; ?>
                    <p class="clear" style="margin:0;"></p>
                </div>
<?php endif; ?>
        </div>
        <div class="col-sm-3">
            <h4 class="footer-heading">
<?php echo _p('About Us'); ?>
            </h4>
            <div class="about-us-text">
<?php echo _p('Welcome to our Social Network. Be Part of a Real Community. Our social network is made up of many different communities.These allow you to meet people with the same tastes and desires!'); ?>
            </div>
            <div class="social-link-block">
<?php if (! empty ( $this->_aVars['aSocialIcons'] )): ?>
<?php if (count((array)$this->_aVars['aSocialIcons'])):  foreach ((array) $this->_aVars['aSocialIcons'] as $this->_aVars['aIcon']): ?>
                        <a href="<?php echo $this->_aVars['aIcon']['link']; ?>" title="<?php echo $this->_aVars['aIcon']['info']['name']; ?>" class="footer-social-icon" onmouseover="this.style.backgroundColor='<?php echo $this->_aVars['aIcon']['info']['color']; ?>';" onmouseout="this.style.backgroundColor='inherit';">
                            <i class="fa fa-<?php echo $this->_aVars['aIcon']['info']['icon']; ?>"></i>
                        </a>
<?php endforeach; endif; ?>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="design-2 design-3 copyright-wrap">
    <div class="container">
        <div class="copyright">
            <div class="copyright-title">
<?php echo Phpfox::getParam('core.site_copyright'); ?>  <?php if (( defined ( 'PHPFOX_TRIAL_MODE' ) )): ?>
                &middot; <a href="https://www.phpfox.com/">Powered by phpFox</a>
<?php endif; ?>
                <ul class="list-inline footer-menu">
<?php if (count((array)$this->_aVars['aFooterMenu'])):  $this->_aPhpfoxVars['iteration']['footer'] = 0;  foreach ((array) $this->_aVars['aFooterMenu'] as $this->_aVars['iKey'] => $this->_aVars['aMenu']):  $this->_aPhpfoxVars['iteration']['footer']++; ?>

                    <li<?php if ($this->_aPhpfoxVars['iteration']['footer'] == 1): ?> class="first"<?php endif; ?>><a href="<?php echo Phpfox::getLib('phpfox.url')->makeUrl(''.$this->_aVars['aMenu']['url'].''); ?>" class="ajax_link<?php if ($this->_aVars['aMenu']['url'] == 'mobile'): ?> no_ajax_link<?php endif; ?>"><?php echo _p($this->_aVars['aMenu']['var_name']); ?></a></li>
<?php endforeach; endif; ?>
                </ul>
                <a href="#" class="select-lang" onclick="$('#select_lang_pack').trigger('click');return false;"><?php if (! empty ( $this->_aVars['sLocaleFlagId'] )): ?><img src="<?php echo $this->_aVars['sLocaleFlagId']; ?>" alt="<?php echo $this->_aVars['sLocaleName']; ?>" class="v_middle" /><?php endif; ?> <?php echo $this->_aVars['sLocaleName']; ?></a>
            </div>
        </div>
    </div>
</div>
