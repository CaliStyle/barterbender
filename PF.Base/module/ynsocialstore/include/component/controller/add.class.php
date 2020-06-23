<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/25/16
 * Time: 10:21
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Add extends Phpfox_Component
{
    private $_iEditedId = null;
    private $_bIsEdit = false;
    private $_iStoreId = null;

    private function _checkIfSubmittingAForm() {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }
    private function _checkIsInEditProduct() {
        if ($this->request()->getInt('id') && !isset($_POST['val']['add'])) {
            $this->_iEditedId = $this->request()->getInt('id');

            return true;
        } else {
            return false;
        }
    }
    private function _getValidationParams($aVals = array()) {

        $aParam = array(
        );

        return $aParam;
    }
    private function payFeature($aVals,$aPackage,$aProduct,$isUpdateFeature = false)
    {
        $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
        $currency_id = $aCurrentCurrencies[0]['currency_id'];
        $featureFee = doubleval(($aVals['feature_number_days'] * $aPackage['feature_product_fee']));

        if ($featureFee > 0) {
            // add invoice
            $iInvoice = Phpfox::getService('ynsocialstore.process')->addInvoice($aProduct['product_id'], $currency_id, $featureFee, 'product_feature', array(
                'pay_type' => (Phpfox::getUserParam('ynsocialstore.can_feature_own_product') ? ($aVals['feature_number_days'] > 0 ? 'feature' : '') : ''). '|' . ($isUpdateFeature ? 'update_feature' : ''),
                'feature_days' => $aVals['feature_number_days']
            ), 'product');
            $aPurchase = Phpfox::getService('ynsocialstore')->getInvoice($iInvoice);

            // process payment
            if (empty($iInvoice['status'])) {
                $this->setParam('gateway_data', array(
                      'item_number' => 'ynsocialstore|' . $aPurchase['invoice_id'],
                      'currency_code' => $aPurchase['default_currency_id'],
                      'amount' => $aPurchase['default_cost'],
                      'item_name' => ($aVals['feature_number_days'] > 0 ? 'feature' : ''),
                      'return' => Phpfox::permalink('ynsocialstore.product', $aProduct['product_id'],Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), false, '') . 'productpayment_done/',
                      'recurring' => '',
                      'recurring_cost' => '',
                      'alternative_cost' => '',
                      'alternative_recurring_cost' => ''
                  )
                );
                return $aPurchase['invoice_id'];
            }
        } else {
            /*feature with no fee*/
            /*not in feature or feature is expired*/
            if ($aProduct['feature_end_time'] < PHPFOX_TIME) {

                if ((int)$aVals['feature_number_days'] > 0) {

                    $start_feature_time = PHPFOX_TIME;

                    $end_feature_time = $start_feature_time + ((int)$aVals['feature_number_days'] * 86400);

                    if ($end_feature_time >= 4294967295) {
                        $end_feature_time = 4294967295;
                    }

                    Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aProduct['product_id'], $start_feature_time, $end_feature_time, (int)$aVals['feature_number_days'], $featureFee);

                }
                /*publish with no fee*/
                if($aProduct['product_status'] == 'draft') {
                    if (Phpfox::getService('ecommerce.helper')->getUserParam('ynsocialstore.auto_approved_product', (int)Phpfox::getUserId())) {
                        $status = 'running';
                    } else {
                        $status = 'pending';
                    }
                }
                else{
                    $status = $aProduct['product_status'];
                }

                Phpfox::getService('ecommerce.process')->updateProductStatus($aProduct['product_id'], $status);

                if($status == 'running' && $isUpdateFeature == false){
                    // call approve function
                    Phpfox::getService('ecommerce.process')->approveProduct($aProduct['product_id'], null,'ynsocialstore_product');
                }


            } else {/*already featured ,wanna expand feature time*/

                $feature_day = $aProduct['feature_day'];
                $feature_days = (int)$aVals['feature_number_days'];

                if ((int)$feature_days > 0) {
                    if(PHPFOX_TIME < $aProduct['feature_end_time']){   //still in feature,wanna expend featured time.
                        $start_feature_time = $aProduct['feature_start_time'];
                        $end_feature_time =   $aProduct['feature_end_time'] + (int)$feature_days*86400 ;
                    }
                    else{

                        $start_feature_time = PHPFOX_TIME;
                        $end_feature_time =   $start_feature_time + $feature_days*86400;
                    }


                    if ($end_feature_time >= 4294967295) {
                        $end_feature_time = 4294967295;
                    }

                    Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aProduct['product_id'], $start_feature_time, $end_feature_time, (int)$feature_day, $featureFee);

                }
            }

        }
    }

    public function process()
    {
        Phpfox::isUser(true);
        $sError = "";
        $aAllStore = null;
        if (empty($sError) && $this->_checkIsInEditProduct())
        {
            $this->_bIsEdit = true;
            $aEditProduct = Phpfox::getService('ynsocialstore.product')->getProductForEdit($this->_iEditedId);
            if(!$aEditProduct)
            {
                $sError = _p('unable_to_find_the_product_you_are_looking_for');
            }

            if(empty($sError) && !Phpfox::getService('ynsocialstore.permission')->canEditProduct(false,$aEditProduct['user_id']))
            {
                $sError = _p('you_do_not_have_permission_to_edit_this_product');
            }
            if(empty($sError))
            {
                $this->_iStoreId = $aEditProduct['item_id'];
                $aStore = Phpfox::getService('ynsocialstore')->getStoreById($this->_iStoreId);
                $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
                $this->template()
                    ->assign([
                        'aForms' => $aEditProduct,'aPackage' => $aPackage, 'detailUrl' => Phpfox::permalink('social-store.product', $aEditProduct['product_id'], $aEditProduct['name'])
                    ])->buildPageMenu('js_ynsocialstore_products_block', [], [
                        'link' => Phpfox::permalink('social-store.product', $this->_iEditedId, null),
                        'phrase' => _p('ynsocialstore_view_product_detail')
                    ]);

            }

            (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_add_process_edit')) ? eval($sPlugin) : false);

        }
        elseif(empty($sError))
        {

            $this->_iStoreId = $this->request()->getInt('store',0);
            if($this->_iStoreId == 0)
            {
                $check = Phpfox::getService('ynsocialstore')->checkUserStores();
                if(!$check) {
                    $this->url()->send('ynsocialstore');
                }

                $aAllStore = Phpfox::getService('ynsocialstore')->getAllUserStore(Phpfox::getUserId());
            }
            elseif($this->_iStoreId > 0){
                $aStore = Phpfox::getService('ynsocialstore')->getStoreById($this->_iStoreId);

                if(!$aStore || !count($aStore))
                {
                    $sError = _p('unable_to_find_the_store_you_are_looking_for');
                }
                else {
                    $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);

                    $iCheck = Phpfox::getService('ynsocialstore.permission')->canCreateProduct($aStore);
                    if ($iCheck == 0) {
                        $sError = _p('you_do_not_have_permission_to_create_product_in_this_store');
                    } elseif ($iCheck == 1) {
                        $sError = _p('you_have_reached_your_creating_product_limit_with_current_your_store_package_please_upgrade_to_other_package');
                    }
                }
            }
        }
        $aValidationParam = $this->_getValidationParams();

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_add_process_validation')) ? eval($sPlugin) : false);

        $oValid = Phpfox::getLib('validator')->set(array(
                           'sFormName' => 'ynsocialstore_add_product_form',
                           'aParams' => $aValidationParam
                       )
        );
        $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
        if($this->_checkIfSubmittingAForm())
        {
            $aVals = $this->request()->getArray('val');
            $aValidationParam = $this->_getValidationParams();
            $oValid = Phpfox::getLib('validator')->set(array(
                           'sFormName' => 'ynsocialstore_add_product_form',
                           'aParams' => $aValidationParam
                       )
            );
            if($oValid->isValid($aVals) && Phpfox_Error::isPassed())
            {
                if ($this->_bIsEdit)
                {
                    if (isset($aVals['featureinbox'])) {
                        $iProductId = $this->request()->getInt('id');
                        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
                        if (empty($aProduct))
                            return false;
                        $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aProduct['item_id']);
                        $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
                        Phpfox::getService('ynsocialstore.product.process')->updateFeatureDay($iProductId, $aVals['feature_number_days'] + $aProduct['feature_day']);

                        $invoice_id = $this->payFeature($aVals,$aPackage,$aProduct,true);

                        if ((int)$invoice_id > 0) {
                            $this->template()->assign(array(
                                'invoice_id' => $invoice_id,
                            ));
                        } else {
                            $this->url()->permalink('ynsocialstore.product', $iProductId, Phpfox::getLib('parse.input')->cleanTitle($aProduct['name']), true, _p('product_successfully_updated'));
                        }
                    }
                    else {
                        if (!Phpfox::getService('ynsocialstore.product.process')->updateProduct($this->_iEditedId, $aVals)) {
                            $this->template()->assign('bFail', 1);
                        } else {
                            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($this->_iEditedId);
                            $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aProduct['item_id']);
                            $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);

                            if (isset($aVals['create'])) {
                                $invoice_id = $this->payFeature($aVals, $aPackage, $aProduct, false);
                            } elseif ((($aVals['feature_number_days'] > 0) && $aProduct['product_status'] != 'draft')) {
                                $invoice_id = $this->payFeature($aVals, $aPackage, $aProduct, true);
                            } else {
                                $invoice_id = 0;
                            }

                            if ((int)$invoice_id > 0) {
                                $this->template()->assign(array(
                                                              'invoice_id' => $invoice_id,
                                                          ));
                            } else {
                                $this->url()->permalink('ynsocialstore.product', $this->_iEditedId, Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), true, _p('product_successfully_updated'));
                            }
                        }
                    }
                }
                else
                {
                    if($iProductId = Phpfox::getService('ynsocialstore.product.process')->addProduct($aVals))
                    {
                        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
                        if(!isset($aPackage)){
                            $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aProduct['item_id']);
                            $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
                        }
                        if(isset($aVals['draft'])){
                            $this->url()->send('ynsocialstore.product', array($aProduct['product_id']),_p('product_successfully_added'));
                        }
                        elseif (isset($aVals['create'])) {
                            $invoice_id = $this->payFeature($aVals,$aPackage,$aProduct);
                        }
                        if((int)$invoice_id > 0){
                            $this->template()->assign(array(
                                                          'invoice_id' => $invoice_id,
                                                      ));
                        }
                        else{
                            $this->url()->permalink('ynsocialstore.product', $aProduct['product_id'], Phpfox::getLib('parse.input')->cleanTitle($aProduct['name']), true, _p('product_successfully_added'));
                        }
                    }
                }
            }
        } else if(($bIsPublishFromDetail = $this->request()->get('req4')) && isset($bIsPublishFromDetail) && $bIsPublishFromDetail == 'publish') {
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($this->_iEditedId);
            $aValsForPublish = array(
                'feature_number_days' => $aProduct['feature_day'],
                'name' => $aProduct['name'],
            );
            if (empty($aProduct) || $aProduct['product_status'] != 'draft')
                return false;
            $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aProduct['item_id']);
            $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
            Phpfox::getService('ynsocialstore.product.process')->updateFeatureDay($this->_iEditedId, $aProduct['feature_day']);

            $invoice_id = $this->payFeature($aValsForPublish, $aPackage, $aProduct, false);

            if ((int)$invoice_id > 0) {
                $this->template()->assign(array(
                    'invoice_id' => $invoice_id,
                ));
            } else {
                $this->url()->permalink('ynsocialstore.product', $this->_iEditedId, Phpfox::getLib('parse.input')->cleanTitle($aProduct['name']), true, _p('product_successfully_updated'));
            }
        }

        $this->template()
            ->setTitle(($this->_bIsEdit) ? _p('edit_information') : _p('add_new_product'))
            ->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));
        if(isset($aStore) & isset($aStore['name']))
        {
            $this->template()->setBreadCrumb($aStore['name'],$this->url()->makeUrl('ynsocialstore.store',$aStore['store_id'],$aStore['name']));
        }
        if($this->_bIsEdit && $aEditProduct)
        {
            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.ynsocialstoreEditCategory = function(){
                            var aCategories = explode(\',\', \'' . $aEditProduct['categories'] . '\');
                            for (i in aCategories) {
                                 $(\'#js_mp_holder_\' + aCategories[i]).show();
                                 $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            }

                            var iMainCategoryId = $(\'#js_mp_id_0\').val();

                            ynsocialstore.changeCustomFieldByCategory(iMainCategoryId);

                    }
                 </script>'
            ));
            $this->template()->setBreadCrumb($aEditProduct['name'],$this->url()->makeUrl('ynsocialstore.product',$aEditProduct['product_id'],$aEditProduct['name']));
            $iTotalAttQuantity = Phpfox::getService('ynsocialstore.product')->countTotalAttributeQuantity($this->_iEditedId);
            $this->template()->assign([
                              'iTotalAttQuantity' => $iTotalAttQuantity,
                              'sLinkManageAttr' => $this->url()->makeurl('ynsocialstore.manage-attributes',['id' => $this->_iEditedId])
                                      ]);
        }
        $this->template()->setBreadcrumb(($this->_bIsEdit) ? _p('edit_information') : _p('add_new_product'),'')
            ->setEditor(array('wysiwyg' => true))
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'country.js' => 'module_core'
            ))
        ;
        $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();
        $this->template()->assign([
                      'iStoreId' => $this->_iStoreId,
                      'aStore' => (isset($aStore) && count($aStore)) ? $aStore : null,
                      'aPackage' => isset($aPackage) ? $aPackage : null,
                      'aAllStore' => $aAllStore,
                      'sError' => $sError,
                      'sCreateJs' => $oValid->createJS(),
                      'sGetJsForm' => $oValid->getJsForm(),
                      'core_path' => Phpfox::getParam('core.path_actual').'PF.Base/',
                      'bIsEdit' => $this->_bIsEdit,
                      'aCurrentCurrencies' => $aCurrentCurrencies,
                      'sCategories' => Phpfox::getService('ecommerce.category')->get(),
                      'aUOMs' => $aUOMs,
                  ])->keepBody(true);
        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                              'type' => 'ynsocialstore',
                              'id' => 'ynsocialstore_add_product_form',
                              'edit_id' => ($this->_bIsEdit ? $this->_iEditedId : 0),

                          )
            );
        }
        $this->setParam('sError',$sError);
        $this->template()->assign('bNoAttachaFile', true);
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_add_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.ynsocialstore_component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}