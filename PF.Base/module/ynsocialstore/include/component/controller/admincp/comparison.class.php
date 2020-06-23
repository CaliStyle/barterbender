<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 5:22 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Admincp_Comparison extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aProductFields = Phpfox::getService('ynsocialstore')->getFieldsComparison('product');
        $aStoreFields = Phpfox::getService('ynsocialstore')->getFieldsComparison('store');

        if ($aVals = $this->request()->getArray('val')) {
            $sType = isset($aVals['product']) ? 'product' : 'store';
            unset($aVals[$sType]);
            if (Phpfox::getService('ynsocialstore.process')->activateFieldComparison($aVals, $sType)) {
                $this->url()->send('admincp.ynsocialstore.comparison', array(), _p('fields_comparison_successfully_updated'));
            }
        }

        $this->template()->setHeader(array(
        ))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ynsocialstore'), $this->url()->makeUrl('admincp.app').'?id=__module_ynsocialstore')
            ->setTitle(_p('manage_comparison'))
            ->setBreadcrumb(_p('manage_comparison'))
            ->assign(array(
                    'aProductFields' => $aProductFields,
                    'aStoreFields' => $aStoreFields,
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }
}