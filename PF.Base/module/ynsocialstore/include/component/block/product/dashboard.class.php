<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 9:15 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Dashboard extends Phpfox_Component
{
    public function process()
    {
        $iProductId = $this->request()->getInt('id');

        if (!$iProductId)
            return false;
        if($this->getParam('sError'))
        {
            return false;
        }
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
        $sLink = $this->url()->permalink('ynsocialstore.product',$iProductId);
        $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aProduct['item_id']);
        $this->template()->assign(array(
            'iProductId' => $iProductId,
            'aPackage' => $aPackage,
            'sFullControllerName' => Phpfox::getLib('module')->getFullControllerName(),
            'sHeader' => '<a href="'.$sLink.'">'.$aProduct['name'].'</a>'
        ));

        return 'block';
    }
}