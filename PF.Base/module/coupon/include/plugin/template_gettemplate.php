<?php

if ($sTemplate == 'core.block.template-menusub') {
    if (!empty($this->_aVars['aFilterMenusIcons']) && is_array($this->_aVars['aFilterMenusIcons'])) {
        $this->_aVars['aFilterMenusIcons']['Coupons'] = 'ico ico-cart-o';
    }
}

