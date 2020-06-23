<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Coupon_Component_Block_Feed
 */
class Coupon_Component_Block_Feed extends Phpfox_Component
{
    public function process()
    {
        if ($this_feed_id = $this->getParam('this_feed_id')) {
            $aItem = $this->getParam('custom_param_coupon_' . $this_feed_id);
            $this->template()->assign([
                'sLink' => Phpfox::permalink('coupon.detail', $aItem['coupon_id'], $aItem['title']),
                'aItem' => $aItem,
                'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
            ]);
            $this->clearParam('custom_param_coupon_' . $this_feed_id);
        }
    }
}