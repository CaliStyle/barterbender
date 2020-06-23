<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */

namespace Apps\P_AdvMarketplace\Block\Admin;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Customfieldcell extends Phpfox_Component
{
    public function process()
    {
        $aCustomFields = $this->getParam('aCellCustomFields');
        $isAdd = $this->getParam('isAdd');
        /* var_dump($aCustomFields); */
        /* Phpfox::getLib('cache')->remove(); */
        $this->template()->assign(array(
            'aCellCustomFields' => $aCustomFields,
            "sKeyVarCell" => $this->getParam("sKeyVarCell"),
            "aCustomFieldInfors" => Phpfox::getService("advancedmarketplace")->backend_getcustomfieldinfos(),
            'corepath' => phpfox::getParam('core.path'),
            "isAdd" => isset($isAdd),
            'sCustomClassName' => 'ync-block',
        ));

        return "block";
    }
}
