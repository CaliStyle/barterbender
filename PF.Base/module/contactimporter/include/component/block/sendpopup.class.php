<?php
defined('PHPFOX') or exit('NO DICE!');
class Contactimporter_Component_Block_SendPopup extends Phpfox_Component
{
    public function process()
    {    	    	
        $this->template()->assign(array(
            "sMainUrl" => Phpfox::getLib('url')->makeUrl('contactimporter'),
            "sCorePath" => Phpfox::getParam('core.path'),
            "sProvider" => $this->getParam("sProvider"),
            "iTotal" => $this->getParam("iTotal"),
			'sNoticeQuota' => $this->getParam('sNoticeQuota'),
            'sEmptyMsg' => _p('no_message_input'),
			'yncontacts' => json_encode($this->getParam('yncontacts')),
        ));       
    }
}
?>