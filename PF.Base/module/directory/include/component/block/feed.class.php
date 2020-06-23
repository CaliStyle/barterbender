<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Feed extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($iFeedId = $this->getParam('this_feed_id')) {
            $aAssign = $this->getParam('custom_param_directory_' . $iFeedId);

            if (!empty($aAssign)) {
                $this->template()->assign(
                    $this->getParam('custom_param_directory_' . $iFeedId)
                );
            }

            $this->clearParam('custom_param_directory_' . $iFeedId);
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

        (($sPlugin = Phpfox_Plugin::get('directory.component_block_feed_clean')) ? eval($sPlugin) : false);
    }
}
