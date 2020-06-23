<?php
defined('PHPFOX') or exit('NO DICE!');

class Ynchat_Component_Block_Friendlist extends Phpfox_Component{
    public function process(){
        $buddyList = $this->getParam('buddyList');

        $this->template()->assign(array(
            'buddyList' => $buddyList,
        ));
    }

}