<?php

namespace Apps\P_AdvMarketplace\Block\Frontend\Edit;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Textline extends Phpfox_Component
{
    public function process()
    {
        $aField = $this->getParam("aField");
        $cfInfors = $this->getParam("cfInfors");

        $sDisplay = $cfInfors[$aField["var_type"]]["tag"];

        $sDisplay = str_replace("jh_#%id%#_", "custom_field_" . $aField["field_id"], $sDisplay);
        $sDisplay = str_replace("jh_#%name%#_", sprintf("customfield[%s]", $aField["field_id"]), $sDisplay);
        $sDisplay = str_replace("jh_#%value%#_", $aField["field_id"], $sDisplay);
        $sDisplay = str_replace("jh_#%class%#_", "cus_textline form-control", $sDisplay);
        $sDisplay = str_replace("jh_#%custom_attribute%#_", "", $sDisplay);
        $sDisplay = str_replace("jh_#%text%#_", $aField["data"], $sDisplay);
        $this->template()->assign(array(
            "sPhraseVarName" => $aField["phrase_var_name"],
            "sDisplay" => $sDisplay,
            'sCustomClassName' => 'ync-block',
        ));

        return "block";
    }
}
