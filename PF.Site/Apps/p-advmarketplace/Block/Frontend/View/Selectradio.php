<?php

namespace Apps\P_AdvMarketplace\Block\Frontend\View;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Selectradio extends Phpfox_Component
{
    public function process()
    {
        $aField = $this->getParam("aField");
        $this->template()->assign(array(
            "sPhraseVarName" => $aField["phrase_var_name"],
            "aField" => $aField,
            'sCustomClassName' => 'ync-block',
        ));
    }
}
