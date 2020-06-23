<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Requests extends Phpfox_Component {

    public function process()
    {

        Phpfox::isUser(true);
        $sModule = "admincp.ecommerce.requests";
        $this->template()
                ->setTitle(_p('manage_requests'))
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('module_ecommerce'), $this->url()->makeUrl('admincp.app').'?id=__module_ecommerce')
                ->setBreadcrumb(_p('manage_requests'), $this->url()->makeUrl($sModule));

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');

        // Variables
        $aConds = array();
        
        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
                'type'   => 'request',
                'search' => 'search',
        ));

        $aForms['requester_name']    = $oSearch->get('requester_name'); 
		$aForms['request_status']      = $oSearch->get('request_status');  
        $aForms['submit']           = $oSearch->get('submit');
		$aForms['reset'] 			= $this->request()->get('reset');

		if($aForms['reset'])
		{
			$this->url()->send('admincp.ecommerce.requests');
		}
		
        if($aForms['requester_name'])
        {
            $aConds[] = 'AND u.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['requester_name']) . '%"';
        }

        if($aForms['request_status'])
        {
        	if($aForms['request_status'] != 'all')
        	{
            	$aConds[] = 'AND ecmr.creditmoneyrequest_status = "' . $aForms['request_status'] . '"';
			}	
        }

        $aVals = $this->request()->getArray('val');
        $aForms = array_merge($aForms, $aVals);
        $iDay = 30;
        $sDefaultDateTo = PHPFOX_TIME;
        $sDefaultDateFrom = $sDefaultDateTo - ($iDay * 86400);
        if (empty($aForms['from_month']) && empty($aForms['valid_to_month'])) {
            $aForms['from_day'] = Phpfox::getTime('j', $sDefaultDateFrom);
            $aForms['from_month'] = Phpfox::getTime('n', $sDefaultDateFrom);
            $aForms['from_year'] = Phpfox::getTime('Y', $sDefaultDateFrom);

            $aForms['to_day'] = Phpfox::getTime('j', $sDefaultDateTo);
            $aForms['to_month'] = Phpfox::getTime('n', $sDefaultDateTo);
            $aForms['to_year'] = Phpfox::getTime('Y', $sDefaultDateTo);
        }
        $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aForms['from_month'], $aForms['from_day'], $aForms['from_year']);
        $iEndTime = Phpfox::getLib('date')->mktime(23, 23, 59, $aForms['to_month'], $aForms['to_day'], $aForms['to_year']);

        $aConds[] = 'AND ecmr.creditmoneyrequest_creation_datetime > ' . $iStartTime . ' AND ecmr.creditmoneyrequest_creation_datetime < ' . $iEndTime;

        /*sort table*/
        if($this->request()->get('sortfield') !='' ){
            $sSortField = $this->request()->get('sortfield'); 
            Phpfox::getLib('session')->set('ynecommerce_requests_sortfield',$sSortField);  
        }
        $sSortField = Phpfox::getLib('session')->get('ynecommerce_requests_sortfield');
        if(empty($sSortField)){
            $sSortField = ($this->request()->get('sortfield') !='' )?$this->request()->get('sortfield'):'time'; 
            Phpfox::getLib('session')->set('ynecommerce_requests_sortfield',$sSortField);  
        }

        if($this->request()->get('sorttype') !='' ){
            $sSortType = $this->request()->get('sorttype'); 
            Phpfox::getLib('session')->set('ynecommerce_requests_sorttype',$sSortType);  
        }
        $sSortType = Phpfox::getLib('session')->get('ynecommerce_requests_sorttype');
        if(empty($sSortType)){
            $sSortType = ($this->request()->get('sorttype') !='' )?$this->request()->get('sorttype'):'asc'; 
            Phpfox::getLib('session')->set('ynecommerce_requests_sorttype',$sSortType);  
        }

        $sSortFieldDB = 'ecmr.creditmoneyrequest_id';
        switch ($sSortField) {
			case 'request_date':
				$sSortFieldDB = 'ecmr.creditmoneyrequest_creation_datetime';
                break;
			case 'amount':
				$sSortFieldDB = 'ecmr.creditmoneyrequest_amount';
                break;
			case 'status':
				$sSortFieldDB = 'ecmr.creditmoneyrequest_status';
                break;
			case 'requester':
				$sSortFieldDB = 'u.full_name';
                break;	
			case 'response_date':
				$sSortFieldDB = 'ecmr.creditmoneyrequest_modification_datetime';
                break;	
            default:
                break;
        }
        $aSort = array('field' => $sSortFieldDB,'type' => $sSortType);
        $sSort = implode(" ", $aSort);

        $iLimit = 10;
        

        list($iCnt, $aRequestRows) = Phpfox::getService('ecommerce.request')->getRequest($aConds,$sSort, $oSearch->getPage(), $iLimit);
        
        $sCustomBaseLink  = Phpfox::getLib('url')->makeUrl($sModule); 
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));

        $this->template()
                ->setHeader('cache', array(
                    'ynecommerce.js' => 'module_ecommerce'          
                ))
                ->assign(array(
                    'aRequestRows' => $aRequestRows, 
                    'sCustomBaseLink' => $sCustomBaseLink,
                    'aForms'     => $aForms,
                    'iTotalAmount'     => $iCnt,
                    'sFromDate' => $sFromDate,
                    'sToDate' => $sToDate,
                    'sFormatDatePicker' => $sFormatDatePicker,
                    'sModule' => $sModule,
                ));


    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_admincp_transactions_clean')) ? eval($sPlugin) : false);
    }

}

?>