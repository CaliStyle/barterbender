<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/8/16
 * Time: 9:05 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Friend_Bought_This extends Phpfox_Component
{
    public function process()
    {
        $aProduct = $this->getParam('aProduct');

        if (empty($aProduct) || !Phpfox::isUser()) {
            return false;
        }
        $iLimit = 6;

        list($iCount, $aFriends) = Phpfox::getService('ynsocialstore.product')->getFriendBoughtThisProduct($aProduct['product_id'], $iLimit);

        if (!$iCount)
        {
            return false;
        }

        $more_friends = $iCount - $iLimit;

        $this->template()->assign(array(
                'aFriends' => $aFriends,
                'iProductId' => $aProduct['product_id'],
                'sHeader' => _p('also_bought_by_friends'),
            )
        );

        if ($more_friends) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_more') => [
                        'link' => 'javascript:void(0);',
                        'attr' => 'onclick="$Core.box(\'ynsocialstore.getUsers\', 500, \'iProductId=' . $aProduct['product_id'] . '&sType=friend-bought-this\');"'
                    ]
                )
            ));
        }

        return 'block';
    }
}