<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class FoxFeedsPro_Component_Controller_Admincp_ApprovalFeed extends Phpfox_Component {

	public function process() {
		if ($this -> request() -> get('deleteselect')) {
			$arr_select = $this -> request() -> get('arr_selected');
			$arr_select = trim($arr_select, ", ");
			if ($arr_select) {
				Phpfox::getLib('phpfox.database') -> delete(Phpfox::getT('ynnews_feeds'), 'feed_id IN (' . $arr_select . ')');
				Phpfox::getLib('phpfox.database') -> delete(Phpfox::getT('ynnews_items'), 'feed_id IN (' . $arr_select . ')');
			}
		}

		// approved
		if ($this -> request() -> get('approval')) {
			$selected_ids = $this -> request() -> get('arr_selected');
			$selected_ids = substr($selected_ids, 1);
			$selected_ids = explode(",", $selected_ids);
			if (!empty($selected_ids)) {
				foreach ($selected_ids as $iKey => $aValue) {
					$aSelectedFeed = phpfox::getLib('database') 
								-> select('user_id , page_id') 
								-> from(phpfox::getT('ynnews_feeds')) 
								-> where('feed_id = ' . $aValue) 
								-> execute('getRow');

					$user_id = 0;
					if((int)$aSelectedFeed['user_id'] > 0){
						$user_id = (int)$aSelectedFeed['user_id'];								
					} else if (){
						// get owner page
						$aPage = Phpfox::getService('pages')->getPage((int)$aSelectedFeed['page_id']);
						if(isset($aPage['user_id'])){
							$user_id = (int)$aPage['user_id'];								
						}
					}
					
					if (Phpfox::isModule('notification')) {
						Phpfox::getService('notification.process') -> add('foxfeedspro_feedapproved', $aValue, $user_id);
					}
					Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('ynnews_feeds'), array(
						'is_active' => 1,
						'is_approved' => 1
					), 'feed_id = ' . $aValue);
				}
			}
		}

		$getfeed = $this -> request() -> get('getfeed');
		$aActive = array(
			'1' => 'Approval',
			'0' => 'Unapproval',
			'All' => 'All'
		);
		$feeds = phpfox::getService('foxfeedspro') -> getFeed();
		$aFilters = array(
			'feed_name' => array(
				'type' => 'input:text',
				'search' => "nf.feed_name LIKE '%[VALUE]%'"
			),
			'is_approved' => array(
				'type' => 'select',
				'options' => $aActive,
				'default' => 'All',
				'search' => "nf.is_approved = [VALUE]"
			)
		);
		$oSearch = Phpfox::getLib('search') -> set(array(
			'type' => 'ynnews_items',
			'filters' => $aFilters,
			'search' => 'search'
		));

		$request = $this -> request() -> get('search');
		$position_status = 2;
		$arr_filter = $oSearch -> getConditions();
		$arrSearch = array();
		foreach ($arr_filter as $a => $k) {
			$test = explode('=', $k);
			if (count($test) >= 2 && $test[1] != " ") {
				$arrSearch[] = $k;
			}
			$test = explode('LIKE', $k);
			if (count($test) >= 2 && $test[1] != " ") {
				$arrSearch[] = $k;
			}
		}
		// if(count($arrSearch) ==0)
		//{
		$arrSearch[] = 'nf.is_approved = 0 ';
		//}
		//$arrSearch[] ='nf.owner_type !="admin" ';
		$this -> template() -> assign(array('position_status' => $position_status, ));
		$iPage = $this -> request() -> getInt('page');
		$iPageSize = 10;
		list($iCnt, $feeds) = Phpfox::getService('foxfeedspro') -> getFeeds($arrSearch, $iPage, $iPageSize);
		Phpfox::getLib('pager') -> set(array(
			'page' => $iPage,
			'size' => $iPageSize,
			'count' => $iCnt
		));
		$this -> template() -> assign(array(
			'feeds' => $feeds,
			'iPage' => $iPage
		));
		$this -> template() -> setHeader(array(
			'news.js' => 'module_foxfeedspro',
			'pager.css' => 'style_css',
		));
		$this -> template() -> setBreadCrumb(_p('foxfeedspro.pending_feeds'), $this -> url() -> makeurl('admincp.foxfeedspro.approvalfeed'));
	}

}
?>