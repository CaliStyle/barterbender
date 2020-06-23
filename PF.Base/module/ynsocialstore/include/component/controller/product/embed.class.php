<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/2/16
 * Time: 4:04 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Product_Embed extends Phpfox_Component
{
    public function process()
    {
        $iProductId = $this->request()->get('req4');

        if(!$iProductId)
        {
            exit(_p('invalid_param'));
        }

        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForDetailById($iProductId);

        if(!$aProduct)
        {
            exit(_p('unable_to_find_the_product_you_are_looking_for'));
        }

        $aStaticFiles = $this->template()->getHeader(true);

        foreach($aStaticFiles as $key => $sFile)
        {
            if(!preg_match('/jquery(.*).js/i',$sFile)){
                unset($aStaticFiles[$key]);
            }
        }

        $this->template()->setTitle(Phpfox::getLib('locale')->convert($aProduct['name']));

        $this -> template() -> assign(array(
            'aProduct'	=> $aProduct,
            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
            'aFiles' => $aStaticFiles,
        ));
        Phpfox_Module::instance()->getControllerTemplate();
        die;
    }
}