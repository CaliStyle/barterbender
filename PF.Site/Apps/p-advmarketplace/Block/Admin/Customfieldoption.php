<?php
namespace Apps\P_AdvMarketplace\Block\Admin;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Customfieldoption extends Phpfox_Component
{
    public function process()
    {
        $this->template()->assign(array(
            "iCusfieldId" => $this->getParam("iCusfieldId"),
            "sTextOption" => (($this->getParam("sTextOption")) ? ($this->getParam("sTextOption")) : (_p($this->getParam("sKeyVarOption")))),
            "sKeyVarOption" => $this->getParam("sKeyVarOption"),
            'corepath' => phpfox::getParam('core.path'),
            'sCustomClassName' => 'ync-block',
        ));

        return "block";
    }
}
