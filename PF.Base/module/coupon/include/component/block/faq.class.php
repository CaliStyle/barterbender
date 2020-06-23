<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_FAQ extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

        $aFAQ = Phpfox::getService('coupon.faq')->get_frontend();

        if(!$aFAQ)
        {
            return false;
        }

        $this->template()->assign(array(
                'aFAQ' => $aFAQ,
                'sHeader' => ''
            )
        );

		return 'block';
	}

}

?>
