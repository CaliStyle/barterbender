<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Duration extends Phpfox_Component
{
    public function process()
    {
        $sView = $this->getParam('sView');
        if (!$sView || ($sView != 'add' && $sView != 'entry'))
        {
            return false;
        }
        
        $aContest = $this->getParam('aContest');
        if (!$aContest)
        {
            return false;
        }

        $this->template()->assign(array(
            'aItem' => $aContest
        ));
    }
}

?>