<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Directorybadge extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$iBusinessId = $this->getParam('iBusinessId'); 
		$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		$aBusiness['no_image'] = Phpfox::getParam('core.path_file') . 'module/directory/static/image/default_ava.png';

        $this->template()->assign(array(
                'iBusinessId'        => $iBusinessId,
                'aBusiness'        => $aBusiness,
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
	}

}

?>
