<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<?php

class contactimporter_Component_Controller_Admincp_statisticsbydate extends Phpfox_Component
{
	public function process()
	{
		if(date('D') == 'Mon')
			$start = date('Y-m-d', strtotime('Monday', time()));
		else
			$start = date('Y-m-d', strtotime('Last Monday', time()));
		if(date('D') == 'Sun')
			$end = date('Y-m-d', strtotime('Sunday', time()));
		else 
			$end = date('Y-m-d', strtotime('Next Sunday', time()));
		if (!empty($_POST['submit']))
		{
			$aVal = $_POST['val'];
			$start = $aVal['start_year'] . "/" . $aVal['start_month'] . "/" . $aVal['start_day'];
			$end = $aVal['end_year'] . "/" . $aVal['end_month'] . "/" . $aVal['end_day'];
			;
		}
		$sWhere = " st.date >=  '$start' AND st.date <= '$end'";
		$iPage = $this -> request() -> getInt('page');
		$iPageSize = 10;
		list($iCnt, $items) = phpfox::getService('contactimporter') -> getAdminStatistics($sWhere, "", $iPage, $iPageSize);
		Phpfox::getLib('pager') -> set(array(
			'page' => $iPage,
			'size' => $iPageSize,
			'count' => $iCnt
		));

		$aDate['start_time'] = strtotime($start);
		$aDate['end_time'] = strtotime($end);

		$aDate['start_month'] = date('n', $aDate['start_time']);
		$aDate['start_day'] = date('j', $aDate['start_time']);
		$aDate['start_year'] = date('Y', $aDate['start_time']);

		$aDate['end_month'] = date('n', $aDate['end_time']);
		$aDate['end_day'] = date('j', $aDate['end_time']);
		$aDate['end_year'] = date('Y', $aDate['end_time']);

		$this -> template()
            -> setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            -> setBreadCrumb(_p('module_contactimporter'),$this->url()->makeUrl('admincp.app',['id' => '__module_contactimporter']))
            -> setBreadCrumb(_p('statisticsbydate'), $this -> url() -> makeUrl('admincp.contactimporter.statics'));
		$this -> template() -> assign(array(
			'items' => $items,
			'iCnt' => $iCnt,
			'iPage' => $iPage,
			'aForms' => $aDate
		));
	}

}
?>