<?php

namespace Apps\P_AdvMarketplace\Block\Frontend;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ViewCustomfield extends Phpfox_Component
{
    public function process()
    {
        $aCustomFields = $this->getParam("aCustomFields");
        $cfInfors = $this->getParam("cfInfors");
        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
            'cfInfors' => $cfInfors,
            'sCustomClassName' => 'ync-block',
        ));
    }
}
