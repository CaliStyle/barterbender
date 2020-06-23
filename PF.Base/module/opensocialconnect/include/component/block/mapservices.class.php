<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Opensocialconnect_Component_Block_Mapservices extends Phpfox_Component
{
    public function process()
    {

        $provider = $this->getParam('provider');

        $aProviderOptions = Phpfox::getService('opensocialconnect')->getProviderOptions($provider);

        $aProviderFields = Phpfox::getService('opensocialconnect')->getProviderFields($provider);

        $this->template()->assign(array(
            'provider' => $provider,
            'titleProiver' => ucfirst($provider),
            'aProviderOptions' => $aProviderOptions,
            'aProviderFields' => $aProviderFields,
        ));

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('opensocialconnect.component_block_Mapservices_clean')) ? eval($sPlugin) : false);
    }

}
