<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Custom extends Phpfox_Component
{
    public function process()
    {
        $aCustomFields = $this->getParam('aCustomFields');
        $this->template()->assign(array(
            "aCustomFields" => $aCustomFields
        ));
    }
}