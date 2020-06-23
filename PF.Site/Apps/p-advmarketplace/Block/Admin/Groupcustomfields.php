<?php
namespace Apps\P_AdvMarketplace\Block\Admin;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Groupcustomfields extends Phpfox_Component
{
    public function process()
    {
        $sKeyVar = $this->getParam("sKeyVar");
        $phrase = \Core\Lib::phrase();
        if (!$phrase->isPhrase($sKeyVar)) {
            $phrase->clearCache();
        }

        $aCustomFields = $this->getParam("aCustomFields");
        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
            "sKeyVar" => $sKeyVar,
            "sGroupName" => _p($sKeyVar),
            'corepath' => phpfox::getParam('core.path'),
            'sCustomClassName' => 'ync-block',
        ));

        return "block";
    }
}
