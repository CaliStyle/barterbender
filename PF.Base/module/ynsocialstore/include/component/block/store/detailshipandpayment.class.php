<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/7/16
 * Time: 12:06 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailShipAndPayment extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aStore = $this->getParam('aStore');

        if (empty($aStore)) {
            return false;
        }

        $this->template()->assign(array(
                'sShipAndPayment'	=> $aStore['ship_payment_info'],
            )
        );
    }
}