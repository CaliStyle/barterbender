<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Manage_Packages extends Phpfox_Component
{
	public function process()
	{
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        if ($this->request()->getInt('id')) {
            $iEditedBusinessId = $this->request()->getInt('id');
            $this->setParam('iBusinessId',$iEditedBusinessId);
        }

        if(!(int)$iEditedBusinessId){
                   $this->url()->send('directory');
        }

        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);
        if(!$aBusiness)
        {
            Phpfox::getLib('url')->send('directory', null, _p('directory.business_not_found'));
        }
        $sModule = $aBusiness['module_id'];
        $iItemId = $aBusiness['item_id'];
        if ($sModule !== false && $iItemId !== false) {
            if (Phpfox::hasCallback($sModule, 'getItem')) {
                $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
                if ($aCallback === false) {
                    return Phpfox_Error::display(_p('Cannot find the parent item.'));
                }
            }
        }
        // check permission 
        if(!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'],$iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canUpdatePackage($iEditedBusinessId)
          ){
                $this->url()->send('subscribe');
          }
          

        $aPackages = Phpfox::getService('directory.package')->getPackages();
        $aPackageBusiness = json_decode($aBusiness['package_data'],true);
        $review_and_confirm_purchase = false;

        if ($aVals = $this->request()->getArray('val'))
        {
            if(isset($aVals['update_setting']) && $aVals['update_setting']){
                Phpfox::getService('directory.process')->updateRenewalNotificationForBusiness($aBusiness['business_id'], $aVals['renewal_notification']);
                $this->url()->send("directory.manage-packages",array('id' => $iEditedBusinessId),_p('directory.updated_setting_successfully'));
            } else {
                if(isset($aVals['apply_package'])){                  
                    $aNewPackageBusiness = Phpfox::getService('directory.package')->getById($aVals['package_id']);
                    if(isset($aNewPackageBusiness['package_id'])){
                        if((int)$aNewPackageBusiness['fee']){
                            $packageFee = $aNewPackageBusiness['fee'];
                            $currency_id = $aNewPackageBusiness['currency'];
                            // add invoice
                            $iInvoice = Phpfox::getService('directory.process')->addInvoice($iEditedBusinessId,$currency_id, $packageFee, 'business', array(
                                'aPackage' => $aNewPackageBusiness,
                                'pay_type' => ($packageFee > 0 ? 'package|' : '') ,
                                'change_package_id' => $aVals['package_id'] ,
                            ));
                            $aPurchase = Phpfox::getService('directory')->getInvoice($iInvoice);

                            // process payment
                            if (empty($iInvoice['status'])){
                                $this->setParam('gateway_data', array(
                                        'item_number' => 'directory|' . $aPurchase['invoice_id'],
                                        'currency_code' => $aPurchase['default_currency_id'],
                                        'amount' => $aPurchase['default_cost'],
                                        'item_name' => ($packageFee > 0 ? 'package' : '') ,
                                        'return' => Phpfox::permalink('directory.detail', $iEditedBusinessId, $aBusiness['name'], false, '') . 'businesspayment_done/',
                                        'recurring' => '',
                                        'recurring_cost' => '',
                                        'alternative_cost' => '',
                                        'alternative_recurring_cost' => ''
                                    )
                                );


                                $this->template()->assign(array(
                                    'invoice_id' => $aPurchase['invoice_id']
                                    ));

                                $review_and_confirm_purchase= true;
                                $this->template()->setTitle(_p('directory.review_and_confirm_purchase'))
                                    ->setBreadcrumb(_p('directory.review_and_confirm_purchase'), null, false);
                            }

                        }
                        else{

                           // pay zero fee - package
                            //update new package
                            Phpfox::getService('directory.process')->updatePackageForBusiness($aVals['package_id'],$iEditedBusinessId);
                            $status = Phpfox::getService('directory.helper')->getConst('business.status.draft');

                            if($aBusiness['creating_type'] == 'claiming'){
                                $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                            } else if($aBusiness['package_start_time'] == 0 || $aBusiness['package_end_time'] == 0 ){
                                //still not approved
                                $status = Phpfox::getService('directory.helper')->getConst('business.status.draft');
                                if(Phpfox::getService('directory.helper')->getUserParam('directory.business_created_by_user_automatically_approved', Phpfox::getUserId())){
                                    $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                                } else {
                                    $status = Phpfox::getService('directory.helper')->getConst('business.status.pending');
                                }
                            }
                            else{
                                //already approved
                                $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                            }

                            Phpfox::getService('directory.process')->updateBusinessStatus($iEditedBusinessId, $status);

                            if($status == Phpfox::getService('directory.helper')->getConst('business.status.approved')){
                                // call approve function
                                Phpfox::getService('directory.process')->approveBusiness($iEditedBusinessId, null);
                            }
                            $this->url()->send("directory.manage-packages",array('id' => $iEditedBusinessId),_p('directory.manage_package_updated_successfully'));
                        }
                    }
                }
            }
        }

        $this->template()
            ->setEditor()
            ->setPhrase(array(
                'directory.add_new_role',
                'directory.edit_role',
                'directory.delete_role',
                'directory.delete',
                'directory.confirm_delete_role_member',
            ))
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core',
            ))
            ;

        $this->template()->assign(array(
                'aBusiness'  =>  $aBusiness,
                'aPackages'  =>  $aPackages,
                'iBusinessid' => $iEditedBusinessId,
            ));
        if(isset($aPackageBusiness['package_id'])){
            $this->template()->assign(array(
                'aPackageBusiness' => $aPackageBusiness,
            ));
        }
        if(!$review_and_confirm_purchase)
        {
            $this->template()->setBreadcrumb(_p('directory.manage_packages'), $this->url()->permalink('directory.edit','id_'.$iEditedBusinessId));
        }

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {
        
    }

}
?>