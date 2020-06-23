<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class OngoingBlock extends Phpfox_Component
{
    public function process()
    {
        $aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;
        list($iTotal, $aUpcoming) = Phpfox::getService('fevent')->getUpcoming($bIsPage, false);

        if (!$iTotal)
        {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('upcoming_events'),
                'aUpcoming' => $aUpcoming,
                'bViewMore' => $iTotal > 7,
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_upcoming_clean')) ? eval($sPlugin) : false);
    }
}