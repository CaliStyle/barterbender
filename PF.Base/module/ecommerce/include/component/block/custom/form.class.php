<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Custom_Form extends Phpfox_Component {

    public function process()
    {
        $aCustomFields = $this->getParam('aCustomFields');
        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
        ));
    }

}

?>
