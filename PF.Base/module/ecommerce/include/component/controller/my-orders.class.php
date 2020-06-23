<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_My_Orders extends Phpfox_Component {

	public function process() {

		Phpfox::isUser(true);
		$sModule = $this -> request() -> get('req1');

		// Page Number & Limit Per Page
		$iPage = $this -> request() -> getInt('page');

		// Variables
		$aVals = array();
		$aConds = array();

		// Search Filter
		$oSearch = Phpfox::getLib('search') -> set(array('type' => 'request', 'search' => 'search', ));

		$aForms['product_title'] = $oSearch -> get('product_title');
		$aForms['order_id'] = $oSearch -> get('order_id');
		$aForms['order_status'] = $oSearch -> get('order_status');
		$aForms['item_type'] = $oSearch -> get('item_type');
		$aForms['submit'] = $oSearch -> get('submit');

		if ($aForms['product_title']) {
			$aConds[] = 'AND eop.orderproduct_product_name LIKE "%' . Phpfox::getLib('parse.input') -> clean($aForms['product_title']) . '%"';

		}

		if ($aForms['order_id']) {
			$aConds[] = 'AND eo.order_code LIKE "%' . Phpfox::getLib('parse.input') -> clean($aForms['order_id']) . '%"';

		}

		if ($aForms['order_status']) {
			if ($aForms['order_status'] != 'all') {
				$aConds[] = 'AND eo.order_status = "' . $aForms['order_status'] . '"';
			}
		}

		if ($aForms['item_type'] && $aForms['item_type'] != 'all') {
			$aConds[] = 'AND e.product_creating_type = "' . Phpfox::getLib('parse.input') -> clean($aForms['item_type']) . '"';
		}

		$formatDatePicker = str_split(Phpfox::getParam('core.date_field_order'));

		$aFormatIntial = array();
		foreach ($formatDatePicker as $key => $value) {

			if ($formatDatePicker[$key] != 'Y') {
				$formatIntial = strtolower($formatDatePicker[$key]);
			} else {
				$formatIntial = $formatDatePicker[$key];
			}

			$aFormatIntial[] = $formatIntial;

			$formatDatePicker[$key] .= $formatDatePicker[$key];
			$formatDatePicker[$key] = strtolower($formatDatePicker[$key]);
		}

		$sFormatDatePicker = implode("/", $formatDatePicker);

		$sSearchFromDate = $this->request() -> get('js_start_time__datepicker','');
		$sSearchToDate = $this->request() -> get('js_end_time__datepicker','');
		if(!$sSearchFromDate && !$sSearchToDate)
        {
            $aForms['start_time_day'] = $aForms['end_time_day'] = Phpfox::getTime('j');
            $aForms['start_time_month'] = $aForms['end_time_month'] = Phpfox::getTime('n');
            $aForms['start_time_year']  = $aForms['end_time_year'] = Phpfox::getTime('Y');
        }
        else{
            $aFromDate = explode('/', $sSearchFromDate);
            $aForms['start_time_month'] = $aFromDate[0];
            $aForms['start_time_day'] = $aFromDate[1];
            $aForms['start_time_year'] = $aFromDate[2];

            $aToDate = explode('/', $sSearchToDate);
            $aForms['start_time_month'] = $aToDate[0];
            $aForms['start_time_day'] = $aToDate[1];
            $aForms['start_time_year'] = $aToDate[2];
        }
		$sFormatIntial = implode("/", $aFormatIntial);
		$sFromDate = Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false);
		$sToDate = Phpfox::getTime($sFormatIntial, PHPFOX_TIME + 24 * 60 * 60, false);

		if (!empty($sSearchFromDate) && !empty($sSearchToDate)) {

			$aSearchFromDate = explode("/", $sSearchFromDate);
			$aSearchToDate = explode("/", $sSearchToDate);

			$aFromDate = array();
			$aToDate = array();

			if (!empty($aFormatIntial)) {
				foreach ($aFormatIntial as $key => $aItem) {
					$aFromDate[$aItem] = $aSearchFromDate[$key];
					$aToDate[$aItem] = $aSearchToDate[$key];
				}
			}

			if (count($aFromDate) && count($aToDate)) {
				$iStartTime = Phpfox::getLib('date') -> mktime(0, 0, 0, $aFromDate['m'], $aFromDate['d'], $aFromDate['Y']);
				$iEndTime = Phpfox::getLib('date') -> mktime(23, 59, 59, $aToDate['m'], $aToDate['d'], $aToDate['Y']);
				if ($iStartTime > $iEndTime) {
					$iTemp = $iStartTime;
					$iStartTime = $iEndTime;
					$iEndTime = $iTemp;
				}

				$aConds[] = 'AND eo.order_creation_datetime > ' . $iStartTime . ' AND eo.order_creation_datetime < ' . $iEndTime;
			}

			$sFromDate = $iStartTime ? Phpfox::getTime($sFormatIntial, $iStartTime, false) : Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false);

			$sToDate = $iEndTime ? Phpfox::getTime($sFormatIntial, $iEndTime, false) : Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false);

		}

		$aConds[] = 'AND eo.user_id = ' . Phpfox::getUserId();

		if ($sModule != 'ecommerce') {
            $aConds[] = 'AND eo.module_id = \''.$sModule .'\'';
        }

		/*sort table*/
		if ($this -> request() -> get('sortfield') != '') {
			$sSortField = $this -> request() -> get('sortfield');
			Phpfox::getLib('session') -> set('ynecommerce_myorders_sortfield', $sSortField);
		}
		$sSortField = Phpfox::getLib('session') -> get('ynecommerce_myorders_sortfield');
		if (empty($sSortField)) {
			$sSortField = ($this -> request() -> get('sortfield') != '') ? $this -> request() -> get('sortfield') : 'time';
			Phpfox::getLib('session') -> set('ynecommerce_myorders_sortfield', $sSortField);
		}

		if ($this -> request() -> get('sorttype') != '') {
			$sSortType = $this -> request() -> get('sorttype');
			Phpfox::getLib('session') -> set('ynecommerce_myorders_sorttype', $sSortType);
		}
		$sSortType = Phpfox::getLib('session') -> get('ynecommerce_myorders_sorttype');
		if (empty($sSortType)) {
			$sSortType = ($this -> request() -> get('sorttype') != '') ? $this -> request() -> get('sorttype') : 'asc';
			Phpfox::getLib('session') -> set('ynecommerce_myorders_sorttype', $sSortType);
		}

		$sSortFieldDB = 'eo.order_id';
		switch ($sSortField) {
			case 'order_id' :
				$sSortFieldDB = 'eo.order_id';
				break;
			case 'buyer' :
				$sSortFieldDB = 'u.full_name';
				break;
			case 'order_date' :
				$sSortFieldDB = 'eo.order_creation_datetime';
				break;
			case 'order_total' :
				$sSortFieldDB = 'eo.order_total_price';
				break;
			case 'order_commission' :
				$sSortFieldDB = 'eo.order_commission_value';
				break;
			default :
				break;
		}
		$aSort = array('field' => $sSortFieldDB, 'type' => $sSortType);
		$sSort = implode(" ", $aSort);

        if ($this->search()->getPage() <= 1) {
            $_SESSION[Phpfox::getParam('core.session_prefix') . "ecommerce_my_orders_search"] = $aConds;
            $_SESSION[Phpfox::getParam('core.session_prefix') . "ecommerce_my_orders_sort"] = $sSort;
        }

        if ($this->search()->getPage() > 1)
        {
            $aConds = $_SESSION[Phpfox::getParam('core.session_prefix')."ecommerce_my_orders_search"];
            $sSort = $_SESSION[Phpfox::getParam('core.session_prefix') . "ecommerce_my_orders_sort"];
        }

		$iLimit = 10;

		list($iCnt, $aMyOrderRows) = Phpfox::getService('ecommerce.order') -> getOrders($aConds, $sSort, $oSearch -> getPage(), $iLimit);

		$iTotalAmount = 0;
		$iTotalCommission = 0;
		foreach ($aMyOrderRows as $iKey => $aRow) {
			$iTotalAmount += $aRow['order_total_price'];
			$iTotalCommission += $aRow['order_commission_value'];
			$aMyOrderRows[$iKey]['sStatusTitle'] = _p('' . $aRow['order_status']);
			$aMyOrderRows[$iKey]['order_creation_datetime'] = Phpfox::getTime('d/m/Y', $aMyOrderRows[$iKey]['order_creation_datetime']);
			$aMyOrderRows[$iKey]['order_payment_status'] = _p('' . $aRow['order_payment_status']);

			$aLocation = array();
			if (!empty($aRow['order_delivery_location_address'])) {
				$aLocation[] = $aRow['order_delivery_location_address'];
			}

			if (!empty($aRow['order_delivery_location_address_2'])) {
				$aLocation[] = $aRow['order_delivery_location_address_2'];
			}

			if (!empty($aRow['order_delivery_province'])) {
				$aLocation[] = $aRow['order_delivery_province'];
			}
			if (!empty($aRow['order_delivery_city'])) {
				$aLocation[] = $aRow['order_delivery_city'];
			}
			if (!empty($aRow['order_delivery_country_iso'])) {
				$aLocation[] = Phpfox::getService('core.country') -> getCountry($aRow['order_delivery_country_iso']);
			}
			if ($aRow['order_delivery_country_child_id']) {
				$aLocation[] = Phpfox::getService('core.country') -> getChild($aRow['order_delivery_country_child_id']);
			}
			$aMyOrderRows[$iKey]['sLocation'] = implode(', ', $aLocation);

		}

		$sCustomBaseLink = Phpfox::getLib('url') -> makeUrl($sModule . '.my-orders');

		Phpfox::getLib('pager') -> set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch -> getSearchTotal($iCnt)));

		$this -> template() -> setHeader('cache', 
			array(
				'magnific-popup.css' => 'module_ecommerce', 
				'jquery.magnific-popup.js' => 'module_ecommerce', 
				'ynecommerce.js' => 'module_ecommerce')) 
			-> assign(array('aMyOrderRows' => $aMyOrderRows, 
				'sCustomBaseLink' => $sCustomBaseLink, 
				'aForms' => $aForms, 
				'iTotalAmount' => $iTotalAmount, 
				'iTotalCommission' => $iTotalCommission, 
				'sFromDate' => $sFromDate, 
				'sToDate' => $sToDate, 
				'sFormatDatePicker' => $sFormatDatePicker, 
				'sModule' => $sModule, 
				'iPage' => $iPage,
			));

		$this -> template() -> setTitle(_p('my_orders')) -> setBreadcrumb(_p('' . $sModule), $this -> url() -> makeUrl($sModule));
		if ($sModule == 'ecommerce') {
			$this -> template() -> setBreadcrumb(_p('my_orders'), $this -> url() -> makeUrl('ecommerce.my-orders'));
			Phpfox::getService('ecommerce.helper') -> buildMenu();
		}
	}

	/**
	 * This function is used to add plugin. Do not delete.
	 */
	public function clean() {
		(($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_my_orders_clean')) ? eval($sPlugin) : false);
	}

}
?>