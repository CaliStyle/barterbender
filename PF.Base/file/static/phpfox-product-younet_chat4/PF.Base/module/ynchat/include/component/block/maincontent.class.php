<?php
defined('PHPFOX') or exit('NO DICE!');

class Ynchat_Component_Block_Maincontent extends Phpfox_Component{
    public function process(){

        Phpfox::getLib('setting')->setParam('core.is_auto_hosted',false);
        if(Phpfox::isUser() == false){
            return false;
        }

        $sSiteLink = Phpfox::getParam('core.path_file');
        $version = '?v=' . PHPFOX_TIME;
        $sIframe = $sSiteLink . 'ynchat/iframe.php';

        $sAgent = Phpfox::getService('ynchat.helper')->getBrowser();
        $type = 'web';
        if(Phpfox::getService('ynchat.helper')->isMobile()){
            $type = 'mobile';
        }        
        
        $this->template()->assign(array(
                'sSiteLink' => $sSiteLink,
                'version' => $version,
                'sIframe' => $sIframe,
                'type' => $type,
            )
        );

        return 'block';
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynchat.component_block_maincontent_clean')) ? eval($sPlugin) : false);
    }

}