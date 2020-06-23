<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Deny_Request extends Phpfox_Component {

    public function process()
    {
    	$iRequestId = $this->getParam('id');
		$this->template()->assign(array(
				'iRequestId' => $iRequestId,
		));
		return 'block';
    }

}

?>

