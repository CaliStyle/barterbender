<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class EditLocationBlock extends Phpfox_Component
{
    public function process()
    {
        $zoom=13;

        $this->template()->assign(array(
            'zoom' => $zoom
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_editlocation_clean')) ? eval($sPlugin) : false);
    }
}