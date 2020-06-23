<?php


defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Store_Add extends Phpfox_Component
{
    private $_sModule = null;
    private $_iItem = null;
    private $_iEditedId = null;
    private $_bIsEdit = false;

    private function _checkIfSubmittingAForm() {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }
   	private function _checkIsInEditStore() {
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
    private function payPackage($aVals, $iId){
        if (isset($aVals['create']) && $aVals['create']){
            if((int)$aVals['package_id'] > 0){

                $package_id = $aVals['package_id'];
                $aPackage = Phpfox::getService('ynsocialstore.package')->getById($package_id);
                if(isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1){
                    // do nothing
                } else {
                    $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
                    $currency_id = $aCurrentCurrencies[0]['currency_id'];
                    $packageFee = doubleval( $aPackage['fee']);
                    $featureFee = doubleval(($aVals['feature_number_days'] * $aPackage['feature_store_fee']));
                    $fFee =  $packageFee + $featureFee;
                    if($fFee > 0){
                        // add invoice 
                        $iInvoice = Phpfox::getService('ynsocialstore.process')->addInvoice($iId,$currency_id, $fFee, 'store', array(
                            'pay_type' => ( Phpfox::getUserParam('ynsocialstore.can_feature_own_store')?($featureFee > 0 ? 'feature' : ''):'' ). '|' .($packageFee > 0 ? 'package' : ''),
                            'aPackage' => $aPackage, 
                            'feature_days' => $aVals['feature_number_days']
                        ), 'store');
                        $aPurchase = Phpfox::getService('ynsocialstore')->getInvoice($iInvoice);

                        // process payment 
                        if (empty($iInvoice['status'])){
                            $this->setParam('gateway_data', array(
                                    'item_number' => 'ynsocialstore|' . $aPurchase['invoice_id'],
                                    'currency_code' => $aPurchase['default_currency_id'],
                                    'amount' => $aPurchase['default_cost'],
                                    'item_name' => ($packageFee > 0 ? $aPackage['name'] : '') . ($featureFee > 0 ? '|feature' : ''),
                                    'return' => Phpfox::permalink('ynsocialstore.store', $iId, Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), false, '') . 'storepayment_done/',
                                    'recurring' => '',
                                    'recurring_cost' => '',
                                    'alternative_cost' => '',
                                    'alternative_recurring_cost' => ''                      
                                )
                            ); 
                            return $aPurchase['invoice_id'];                           
                        }
                    } else {
                        // pay zero fee - package
                        $status = 'draft';
                        if(Phpfox::getService('ynsocialstore.helper')->getUserParam('ynsocialstore.auto_approved_store',(int)Phpfox::getUserId())){
                            $status = 'public';
                        } else {
                            $status = 'pending';
                        }
                        Phpfox::getService('ynsocialstore.process')->updateStoreStatus($iId, $status);

                        if($status == 'public'){
                            // call approve function 
                            Phpfox::getService('ynsocialstore.process')->approveStoreByPackage($iId, null);                                    
                        }            
                        // pay zero fee - feature
                        if((int)$aVals['feature_number_days'] > 0){
                            $start_time = PHPFOX_TIME;
                            $end_time = $start_time + ((int)$aVals['feature_number_days'] * 86400);
                            Phpfox::getService('ynsocialstore.process')->updateStoreFeatureTime($iId, $end_time, $aVals['feature_number_days']);
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }
    private function payFeature($aVals, $iId, $aStore = null){
        if(null == $aStore){
            $aStore = Phpfox::getService('ynsocialstore')->getQuickStoreById($iId);
        }

        $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
        $currency_id = $aCurrentCurrencies[0]['currency_id'];
        $featureFee = doubleval(($aVals['feature_number_days'] * $aStore['feature_fee']));
        $fFee =   $featureFee;

        if($fFee > 0){
            // add invoice
            $iInvoice = Phpfox::getService('ynsocialstore.process')->addInvoice($iId, $currency_id, $fFee, 'feature', array(
                'aPackage' => array(),
                'pay_type' => (Phpfox::getUserParam('ynsocialstore.can_feature_own_store')?($featureFee > 0 ? 'feature' : ''):''),
                'feature_days' => $aVals['feature_number_days']
            ), 'store');
            $aPurchase = Phpfox::getService('ynsocialstore')->getInvoice($iInvoice);

            // process payment
            if (empty($iInvoice['status'])){
                $this->setParam('gateway_data', array(
                                                  'item_number' => 'ynsocialstore|' . $aPurchase['invoice_id'],
                                                  'currency_code' => $aPurchase['default_currency_id'],
                                                  'amount' => $aPurchase['default_cost'],
                                                  'item_name' => 'feature',
                                                  'return' => Phpfox::permalink('ynsocialstore.store', $iId, $aVals['name'], false, '') . 'storepayment_done/',
                                                  'recurring' => '',
                                                  'recurring_cost' => '',
                                                  'alternative_cost' => '',
                                                  'alternative_recurring_cost' => ''
                                              )
                );
                return $aPurchase['invoice_id'];
            }
        } else {
            // pay zero fee - feature
            if((int)$aVals['feature_number_days'] > 0){
                $feature_days = (int)$aVals['feature_number_days'];
                /*already approved*/
                if(PHPFOX_TIME < $aStore['feature_end_time']){   //still in feature,wanna expend featured time.

                    $end_time =   $aStore['feature_end_time'] + (int)$feature_days*86400 ;
                    $feature_days = (int)$feature_days + (int)$aStore['feature_day'];
                }
                else{

                    $start_time = PHPFOX_TIME;
                    $end_time =   $start_time + $feature_days*86400;
                }

                if($end_time >= 4294967295){
                    $end_time = 4294967295;
                }

                Phpfox::getService('ynsocialstore.process')->updateStoreFeatureTime($this->_iEditedId, $end_time, $feature_days);
            }
        }
    }

    public function process()
    {
        Phpfox::isUser(true);
        $this->_sModule = $this->request()->get('module', '');
        $this->_iItem = $this->request()->getInt('item', '');
    	
        $aPackage = array();
    	$sError = "";
        $iPackageId = 0;
		if (empty($sError) && $this->_checkIsInEditStore()) 
		{
            $this->_bIsEdit = true;
            $aEditStore = Phpfox::getService('ynsocialstore')->getStoreForEdit($this->_iEditedId);
            if(!$aEditStore)
            { 
                $sError = _p('unable_to_find_the_store_you_are_looking_for');
            }
            if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false,$aEditStore['user_id']))
            {
                $sError = _p('you_do_not_have_permission_to_edit_this_store');
            }
            if(empty($sError))
            {
                $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aEditStore['package_id']);
                $aPackage['themes'] = json_decode($aPackage['themes']);

                // Get current logo and cover for editing
                if (!empty($aEditStore['logo_path'])) {
                    $aEditStore['current_logo'] = Phpfox::getLib('image.helper')->display(
                        array(
                            'server_id' => $aEditStore['server_id'],
                            'path' => 'core.url_pic',
                            'file' => 'ynsocialstore' . PHPFOX_DS . $aEditStore['logo_path'],
                            'suffix' => '_90',
                            'return_url' => true
                        )
                    );
                }

                if (!empty($aEditStore['cover_path'])) {
                    $aEditStore['current_cover'] = Phpfox::getLib('image.helper')->display(
                        array(
                            'server_id' => $aEditStore['cover_server_id'],
                            'path' => 'core.url_pic',
                            'file' => 'ynsocialstore' . PHPFOX_DS . $aEditStore['cover_path'],
                            'suffix' => '_480',
                            'return_url' => true
                        )
                    );
                }
                $this->template()->assign(['aForms' => $aEditStore,'aPackage' => $aPackage,'sModule' => $aEditStore['module_id'],'iItem' => $aEditStore['item_id']]);
                $this->setParam(array(
                                    'country_child_value' => (isset($aEditStore['country_iso']) ? $aEditStore['country_iso'] : 0),
                                    'country_child_id' => (isset($aEditStore['country_child_id']) ? $aEditStore['country_child_id'] : 0)
                                )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_add_process_edit')) ? eval($sPlugin) : false);
		}
		elseif(empty($sError))
		{
            // check permission
            $bCanCreateStore = false;
            if(Phpfox::getService('ynsocialstore.permission')->canCreateStore()){
                $bCanCreateStore = true;
            }
            else{
                $sError = _p('you_do_not_have_permission_to_create_a_store_please_contact_administrator');
            }
            $sParams = "";
            if(Phpfox::getService('ynsocialstore.permission')->canCreateStoreWithLimit() == false){
                $sError = _p('you_have_reached_your_creating_store_limit_please_contact_administrator');
            }
			$iPackageId = $this->request()->get('package', false);
			if(($iPackageId === false || (int)$iPackageId <= 0)){
				Phpfox::getLib('url')->send($this->url()->makeUrl('ynsocialstore.store.storetype'));
			}
			$aPackage = Phpfox::getService('ynsocialstore.package')->getById($iPackageId);
			if(isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1){
					Phpfox::getLib('url')->send($this->url()->makeUrl('ynsocialstore.store.storetype'));
			}
			$aPackage['themes'] = json_decode($aPackage['themes']);		
			$sParams .= 'package_' . $iPackageId . '/';
			$this->template()->assign(array(
                'package_id' => $iPackageId,
                'sModule'   => $this->_sModule,
                'iItem' => $this->_iItem
            ));	
		}
		$aValidationParam = $this->_getValidationParams();

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_add_process_validation')) ? eval($sPlugin) : false);

		$oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynsocialstore_add_store_form',
                'aParams' => $aValidationParam
            )
        );
        $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
        $aBusinessType = Phpfox::getService('ynsocialstore.helper')->getAllBusinessType();
        $aAllCategories = Phpfox::getService('ynsocialstore')->getAllCategories();
        if($this->_checkIfSubmittingAForm())
        {
            $aVals = $this->request()->getArray('val');

            if(isset($aVals['back'])){
                Phpfox::getLib('url')->send($this->url()->makeUrl('ynsocialstore.storetype'));
            }
            $aValidationParam = $this->_getValidationParams();
            $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ynsocialstore_add_store_form',
                'aParams' => $aValidationParam
            ));
            if($oValid->isValid($aVals) && Phpfox_Error::isPassed())
            {
                if ($this->_sModule && $this->_iItem && !$this->_bIsEdit)
                {
                    $aVals['module_id'] = $this->_sModule;
                    $aVals['item_id'] = $this->_iItem;
                }
                if ($this->_bIsEdit)
                {
                    $aStore = Phpfox::getService('ynsocialstore')->getQuickStoreById($this->_iEditedId);
                    $aVals['package_id'] = $aEditStore['package_id'];
                    if (isset($aVals['featureinbox'])) {
                        $sUrlBack = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url()->makeUrl('ynsocialstore.store');
                        $invoice_id = $this->payFeature($aVals, $this->_iEditedId, $aStore);
                        if ((int)$invoice_id > 0) {
                            $this->template()->assign(array('invoice_id' => $invoice_id));
                        } else {

                            $this->url()->send($sUrlBack);
                        }
                    }
                    else {
                        if (!Phpfox::getService('ynsocialstore.process')->updateStore($this->_iEditedId, $aVals)) {
                            $this->template()->assign('bFail', 1);
                        } else {
                            if (isset($aVals['create'])) {
                                $invoice_id = $this->payPackage($aVals, $this->_iEditedId);
                            } elseif ($aVals['feature_number_days'] > 0 && $aStore['status'] != 'draft') {
                                $invoice_id = $this->payFeature($aVals, $this->_iEditedId);
                            } else {
                                $invoice_id = 0;
                            }
                            if ($invoice_id === true) {
                                // create business withou fee
                                $this->url()->permalink('ynsocialstore.store', $this->_iEditedId, Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), true, _p('store_successfully_added'));
                            } elseif ((int)$invoice_id > 0) {
                                $this->template()->assign(array(
                                                              'invoice_id' => $invoice_id,
                                                          ));
                            } else {
                                $this->url()->permalink('ynsocialstore.store', $this->_iEditedId, Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), true, _p('store_successfully_updated'));
                            }
                        }
                    }
                }
                else
                {
                    $aVals['package_id'] = $this->request()->getInt('package');
                    if($iStoreId = Phpfox::getService('ynsocialstore.process')->addStore($aVals))
                    {
                        if((int)$aVals['package_id'] > 0){
                            // create business type 
                            $invoice_id = $this->payPackage($aVals, $iStoreId);
                            if($invoice_id === true){
                                // create business withou fee
                                $this->url()->permalink('ynsocialstore.store', $iStoreId, Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), true, _p('store_successfully_added'));
                            } else if((int)$invoice_id > 0){
                                $this->template()->assign(array(
                                    'invoice_id' => $invoice_id, 
                                ));                            
                            }else if(isset($aVals['draft'])){
                                $this->url()->permalink('ynsocialstore.store', $iStoreId, Phpfox::getLib('parse.input')->cleanTitle($aVals['name']), true, "");
                            } else {
                                Phpfox::getLib('url')->send('ynsocialstore.storetype', null, _p('some_issues_happen_please_try_again_thanks'));
                            }
                        }
                    }
                }
            }

        }

    	$this->template()
			->setTitle(($this->_bIsEdit) ? _p('edit_information') : _p('open_new_store'))
			->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));
        if($this->_bIsEdit && $aEditStore)
        {
            $this->template()->setBreadcrumb($aEditStore['name'], $this->url()->makeUrl('ynsocialstore.store',$aEditStore['store_id']));
        }
		$this->template()->setBreadcrumb(($this->_bIsEdit) ? _p('edit_information') : _p('open_new_store'), $this->url()->makeUrl('ynsocialstore.store.add',($this->_bIsEdit ? 'id_'.$this->_iEditedId : 'package_'.$iPackageId)),true)
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
		$this->template()->assign([
				'sError' => $sError,
				'aPackage' => $aPackage, 
				'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sBackUrl' => !$this->_bIsEdit ? $this->url()->makeUrl('ynsocialstore.store.storetype') : $this->url()->makeUrl('ynsocialstore.store',$this->_iEditedId),
                'core_path' => Phpfox::getParam('core.path_actual').'PF.Base/', 
                'bIsEdit' => $this->_bIsEdit,
                'aCurrentCurrencies' => $aCurrentCurrencies,
                'aBusinessType' => $aBusinessType,
                'aAllCategories' => $aAllCategories,
                'apiKey' => Phpfox::getParam('core.google_api_key'),
			]);
		if (Phpfox::isModule('attachment')) {
               $this->setParam('attachment_share', array(
						'type' => 'ynsocialstore',
						'id' => 'ynsocialstore_edit_store_form',
						'edit_id' => ($this->_bIsEdit ? $this->_iEditedId : 0),

					)
				);
            }
        $this->template()->assign('bNoAttachaFile', true); 
        if(!$this->_bIsEdit){
            Phpfox::getService('ynsocialstore.helper')->buildMenu();   
        }
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_add_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.ynsocialstore_component_controller_store_add_clean')) ? eval($sPlugin) : false);
    }
}