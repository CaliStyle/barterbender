<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Contest_Photo extends Phpfox_Component
{
    public function process()
    {
        $aContest = $this->getParam('aContest');
        
        $aButtonColor = array(
            'bgcolor_1' => '#50A1FF',
            'bgcolor_2' => '#2289FF',
            'text_color' => '#FFFFFF'
        );
        
        $aButtonColor['border_color'] = ($aButtonColor['bgcolor_2'] == '#2289FF') ? '#3075FF' : $aButtonColor['bgcolor_2'];
        
        $this->template()->assign(array(
            'aItem' => $aContest,
            'aButtonColor' => $aButtonColor
        ));
    }
}

?>