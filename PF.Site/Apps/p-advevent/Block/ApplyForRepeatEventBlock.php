<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class ApplyForRepeatEventBlock extends Phpfox_Component
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