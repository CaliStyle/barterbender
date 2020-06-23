<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_Latest extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
        if(!$this->getParam('bInHomepageFr'))
        {
            return false;
        }

        $iLimit = Phpfox::getParam('coupon.limit_of_coupon_in_a_block');
        if(!$iLimit)
        {
            $iLimit = 2;
        }

		$aLatestCoupons = Phpfox::getService('coupon')->getCoupon($sType = 'latest', $iLimit );

		if(!$aLatestCoupons)
		{
			return false;
		}

		$this->template()->assign(array(
				'aLatestCoupons' => $aLatestCoupons,
				'sHeader' => _p('latest_coupons'),
				'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png",
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('coupon') . '?view=running&sort=latest'
                ),
            )
		);
		return 'block';
	}

}
?>
