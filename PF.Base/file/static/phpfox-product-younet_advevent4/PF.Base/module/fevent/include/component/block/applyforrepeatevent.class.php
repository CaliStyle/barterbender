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

class Fevent_Component_Block_Applyforrepeatevent extends Phpfox_Component
{
    public function process()
    {
        $isRepeat = 0;
        if ($iEditId = $this->request()->get('id')) {
            if (($aEvent = Phpfox::getService('fevent')->getForEdit($iEditId))) {
                if ($aEvent['isrepeat'] > -1 ) {
                    $isRepeat = 1;
                }
            }
        }
        else{
            return false;
        }
        if (!$isRepeat) {
            return false;
        }
        $this->template()->assign(array(
            'isRepeat' => $isRepeat
        ));
    }
}