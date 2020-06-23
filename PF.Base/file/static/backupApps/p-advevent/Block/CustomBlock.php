<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class CustomBlock extends Phpfox_Component
{
    public function process()
    {
        $aCustomFields = $this->getParam('aCustomFields');
        $this->template()->assign(array(
            "aCustomFields" => $aCustomFields
        ));
    }
}