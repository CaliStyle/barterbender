<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Promotebusiness extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$iBusinessId = $this->getParam('iBusinessId'); 
		$sFrameUrl = Phpfox::getService('directory')->getFrameUrl($iBusinessId);
		$sBadgeCode = Phpfox::getService('directory')->getBadgeCode($sFrameUrl);
		
        $this->template()->assign(array(
                'iBusinessId'        => $iBusinessId,
                'sBadgeCode'        => $sBadgeCode,
                'sCorePath'        => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
	}

}

?>
