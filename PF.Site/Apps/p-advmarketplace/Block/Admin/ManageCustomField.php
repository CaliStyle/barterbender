<?php

namespace Apps\P_AdvMarketplace\Block\Admin;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class ManageCustomField extends Phpfox_Component
{
    public function process()
    {
        $iCatId = $this->getParam("lid");
        $aCustomFieldGroups = Phpfox::getService('advancedmarketplace.customfield.advancedmarketplace')->loadAllCustomFieldGroup($iCatId);
        $this->template()->assign(array(
            'corepath' => Phpfox::getParam('core.path_actual'),
            'iListingId' => $iCatId,
            'sKeyVar' => $this->getParam("sKeyVar"),
            'sText' => $this->getParam("sText"),
            'aCustomFieldGroups' => $aCustomFieldGroups,
            "aCustomFieldInfors" => Phpfox::getService("advancedmarketplace")->backend_getcustomfieldinfos(),
            'sCustomClassName' => 'ync-block',
        ));

        return "block";
    }
}
