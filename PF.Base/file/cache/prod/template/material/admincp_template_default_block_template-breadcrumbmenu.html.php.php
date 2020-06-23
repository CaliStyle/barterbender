<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 20, 2020, 7:51 pm */ ?>
<?php if (isset ( $this->_aVars['aAdmincpBreadCrumb'] ) && ! empty ( $this->_aVars['aAdmincpBreadCrumb'] )): ?>
<?php if (count((array)$this->_aVars['aAdmincpBreadCrumb'])):  foreach ((array) $this->_aVars['aAdmincpBreadCrumb'] as $this->_aVars['sUrl'] => $this->_aVars['sPhrase']): ?>
    <a class="child" href="<?php echo $this->_aVars['sUrl']; ?>"><?php echo $this->_aVars['sPhrase']; ?></a>
<?php endforeach; endif;  else: ?>
    <a class="child" href=""><?php echo $this->_aVars['sSectionTitle']; ?></a>
<?php endif; ?>
