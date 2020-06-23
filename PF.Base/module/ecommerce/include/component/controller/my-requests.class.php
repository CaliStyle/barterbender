<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_My_Requests extends Phpfox_Component {

    public function process()
    {
        Phpfox::isUser(true);
        $aForms = array();
        $sModule = $this->request()->get('req1');
        $fTotalSold = (int)Phpfox::getService('ecommerce.order')->getTotalSaleOfMyItem($sModule);
        $fTotalCommissions = (float)Phpfox::getService('ecommerce.order')->getTotalCommissionOfMyItem($sModule);
        $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney();
        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');
        
        // Variables
        $aVals = array();
        $aConds = array();
        $aConds[] = "AND ecmr.user_id = ".Phpfox::getUserId();
        
            // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
                'type'   => 'request',
                'search' => 'search',
        ));
        $formatDatePicker = str_split(Phpfox::getParam('core.date_field_order'));
        $aFormatIntial = array();
        foreach ($formatDatePicker as $key => $value) {
       
            if($formatDatePicker[$key] != 'Y'){
                $formatIntial = strtolower($formatDatePicker[$key]);
            }
            else{
                $formatIntial = $formatDatePicker[$key];
            }
       
            $aFormatIntial[] = $formatIntial;

            $formatDatePicker[$key] .= $formatDatePicker[$key];
            $formatDatePicker[$key] = strtolower($formatDatePicker[$key]);                
        }

        $sFormatDatePicker = implode("/", $formatDatePicker);

        $oRequest = Phpfox::getLib('request');
        $sSearchFromDate  = $oRequest->get('js_from__datepicker');
        $sSearchToDate  = $oRequest->get('js_to__datepicker');
        $sFormatIntial = implode("/", $aFormatIntial);
        $sFromDate = Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false) ;
        $sToDate = Phpfox::getTime($sFormatIntial, PHPFOX_TIME + 24 * 60 * 60, false) ;

        if(!empty($sSearchFromDate) && !empty($sSearchToDate)){
            $aSearchFromDate = explode("/", $sSearchFromDate);
            $aSearchToDate = explode("/", $sSearchToDate);
            $aFromDate = array();
            $aToDate = array();

            if(!empty($aFormatIntial)){
                foreach ($aFormatIntial as $key => $aItem) {
                    $aFromDate[$aItem] = $aSearchFromDate[$key];
                    $aToDate[$aItem] = $aSearchToDate[$key];
                }
            }

            if (count($aFromDate) && count($aToDate))
            {
                    $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aFromDate['m'], $aFromDate['d'], $aFromDate['Y']);
                    $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aToDate['m'], $aToDate['d'], $aToDate['Y']);
                    if ($iStartTime > $iEndTime)
                    {
                            $iTemp = $iStartTime;
                            $iStartTime = $iEndTime;
                            $iEndTime = $iTemp;
                    }

                     $aConds[] = 'AND ecmr.creditmoneyrequest_creation_datetime > ' . $iStartTime . ' AND ecmr.creditmoneyrequest_creation_datetime < ' . $iEndTime;
            }

            $sFromDate = $iStartTime ? Phpfox::getTime($sFormatIntial, $iStartTime, false) : Phpfox::getTime($sFormatIntial,PHPFOX_TIME,false);

            $sToDate = $iEndTime ? Phpfox::getTime($sFormatIntial, $iEndTime, false) : Phpfox::getTime($sFormatIntial,PHPFOX_TIME,false);
        }

        /*sort table*/
        if($this->request()->get('sortfield') !='' ){
            $sSortField = $this->request()->get('sortfield'); 
            Phpfox::getLib('session')->set('ynecommerce_manageorders_sortfield',$sSortField);  
        }
        $sSortField = Phpfox::getLib('session')->get('ynecommerce_manageorders_sortfield');
        if(empty($sSortField)){
            $sSortField = ($this->request()->get('sortfield') !='' )?$this->request()->get('sortfield'):'time'; 
            Phpfox::getLib('session')->set('ynecommerce_manageorders_sortfield',$sSortField);  
        }


        if($this->request()->get('sorttype') !='' ){
            $sSortType = $this->request()->get('sorttype'); 
            Phpfox::getLib('session')->set('ynecommerce_myrequest_sorttype',$sSortType);  
        }
        $sSortType = Phpfox::getLib('session')->get('ynecommerce_myrequest_sorttype');
        if(empty($sSortType)){
            $sSortType = ($this->request()->get('sorttype') !='' )?$this->request()->get('sorttype'):'asc'; 
            Phpfox::getLib('session')->set('ynecommerce_myrequest_sorttype',$sSortType);  
        }

        $sSortFieldDB = 'ecmr.creditmoneyrequest_creation_datetime';
        switch ($sSortField) {
            case 'request-date':
                $sSortFieldDB = 'ecmr.creditmoneyrequest_creation_datetime';
                break;
            case 'amount':
                $sSortFieldDB = 'ecmr.creditmoneyrequest_amount';
                break;
            case 'response-date':
                $sSortFieldDB = 'ecmr.creditmoneyrequest_modification_datetime';
                break;
            default:
                break;
        }
        $aSort = array('field' => $sSortFieldDB,'type' => $sSortType);
        $sSort = implode(" ", $aSort);

        $iLimit = 10;
    
        list($iCnt, $aCreditMoneyRequests) = Phpfox::getService('ecommerce.request')->getRequest($aConds,$sSort, $oSearch->getPage(), $iLimit);

        foreach ($aCreditMoneyRequests as $iKey => $aCreditMoneyRequest)
        {
            $aCreditMoneyRequests[$iKey]['status_title'] = _p('' . $aCreditMoneyRequest['creditmoneyrequest_status']);
        }
        Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $iLimit, 'count' => $this->search()->browse()->getCount()));
        
        $bCanShowRequestButton = true;
        $fMinimumAmountToRequest = (float) Phpfox::getParam('ecommerce.ecommerce_minimum_amount_to_request');
        if ($aCreditMoney['creditmoney_remain_amount'] < $fMinimumAmountToRequest)
        {
            $bCanShowRequestButton = false;
        }
        
        $aGateway = Phpfox::getService('api.gateway')->getActive();
        
        $fTotalPendingAmount = Phpfox::getService('ecommerce.request')->getTotalPendingAmount();
        $fTotalReceivedAmount = Phpfox::getService('ecommerce.request')->getTotalReceivedAmount();
        
        $sCustomBaseLink = $this->url()->makeUrl('current');
        $sCustomBaseLink = preg_replace('/\?page=(.?)/i', '', $sCustomBaseLink);
		$sCustomBaseLink = preg_replace('/\&page=(.?)/i', '', $sCustomBaseLink);
        $sCustomBaseLink = str_replace('sortfield_' . $this->request()->get('sortfield') . '/', '', $sCustomBaseLink);
		$sCustomBaseLink = str_replace('sorttype_' . $this->request()->get('sorttype') . '/', '', $sCustomBaseLink);
		$sCustomBaseLink = str_replace('?sortfield=' . $this->request()->get('sortfield') , '', $sCustomBaseLink);
		$sCustomBaseLink = str_replace('&sorttype=' . $this->request()->get('sorttype'), '', $sCustomBaseLink);
       
        $this->template()
                ->setHeader('cache', array(
                    'magnific-popup.css' => 'module_ecommerce',
                    'jquery.magnific-popup.js' => 'module_ecommerce',
                    'ynecommerce.js' => 'module_ecommerce'			
                ))
                ->setPhrase(array('ecommerce.are_you_sure_you_want_to_cancel_this_request', 'ecommerce.yes', 'ecommerce.no'))
                ->assign(array(
            'aCreditMoneyRequests' => $aCreditMoneyRequests,
            'fTotalSold' => $fTotalSold,
            'fTotalCommissions' => $fTotalCommissions,
            'aCreditMoney' => $aCreditMoney,
            'bCanShowRequestButton' => $bCanShowRequestButton,
            'fTotalPendingAmount' => $fTotalPendingAmount,
            'fTotalReceivedAmount' => $fTotalReceivedAmount,
            'fMinimumAmountToRequest' => $fMinimumAmountToRequest,
            'sMinimumAmountToRequest' => $sCurrencySymbol . $fMinimumAmountToRequest,
            'aGateway' => $aGateway,
            'aForms' => $aForms,
            'sCustomBaseLink' => $sCustomBaseLink,
            'sModule' => $sModule,
            'iPage' => $iPage,
        ));

         $this->template()->setTitle(_p('my_requests'))
                          ->setBreadcrumb(_p(''.$sModule), $this->url()->makeUrl($sModule));

        if($sModule == 'ecommerce'){
                $this->template()->setBreadcrumb(_p('my_requests'), $this->url()->makeUrl('ecommerce.my-requests'));
                Phpfox::getService('ecommerce.helper')->buildMenu();
                
        } 
    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_my_requests_clean')) ? eval($sPlugin) : false);
    }

}

?>