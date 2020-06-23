<?php

if ($sTemplate == 'core.block.template-menusub') {
    if (!empty($this->_aVars['aFilterMenusIcons']) && is_array($this->_aVars['aFilterMenusIcons'])) {
        $this->_aVars['aFilterMenusIcons']['Contests'] = 'ico ico-flag-rectangle-o';
    }
}

