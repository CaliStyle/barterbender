<?php
namespace Apps\P_AdvMarketplace\Block\Admin;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Customfieldgroup extends Phpfox_Component
{
    public function process()
    {
        $this->template()->assign(array(
            'corepath' => phpfox::getParam('core.path'),
            "sKeyVar" => $this->getParam("sKeyVar"),
            "sText" => $this->getParam("sText"),
            "is_active" => $this->getParam("is_active"),
            'sCustomClassName' => 'ync-block',
        ));

        return "block";
    }
}
