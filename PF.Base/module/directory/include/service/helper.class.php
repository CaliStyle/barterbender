<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		LyTK
 * @package  		Module_directory
 */

class Directory_Service_Helper extends Phpfox_Service
{
	private $_aTransaction;
	private $_aPackage;
	private $_aDate;
	private $_aBusiness;
	public function __construct() {
		// returned array should be under this format : array( 'key_string' => array('id' => id, 'phrase' => phrase))
		$this->_aPackage = array(
		);

		$this->_aTransaction = array(
			'status' => array(
				"initialized" => array(
					"id" => 1,
					"phrase" => _p('directory.initialized'),
					'name' => 'initialized',
					'description' => '',
				),
				"expired" => array(
					"id" => 2,
					"phrase" => _p('directory.expired'),
					'name' => 'expired',
					'description' => '',
				),
				"pending" => array(
					"id" => 3,
					"phrase" => _p('directory.pending'),
					'name' => 'pending',
					'description' => '',
				),
				"completed" => array(
					"id" => 4,
					"phrase" => _p('directory.completed'),
					'name' => 'completed',
					'description' => '',
				),
				"canceled" => array(
					"id" => 5,
					"phrase" => _p('directory.canceled'),
					'name' => 'canceled',
					'description' => '',
				),
			),
			'method' => array(
				"paypal" => array(
					"id" => 1,
					"phrase" => _p('directory.paypal'),
					'name' => 'paypal',
					'description' => '',
				),
				"2checkout" => array(
					"id" => 2,
					"phrase" => _p('directory.2checkout'),
					'name' => '2checkout',
					'description' => '',
				),
				"paylater" => array(
					"id" => 3,
					"phrase" => _p('directory.pay_later'),
					'name' => 'paylater',
					'description' => '',
				),
				"paybycredit" => array(
					"id" => 4,
					"phrase" => _p('directory.pay_credit'),
					'name' => 'paybycredit',
					'description' => '',
				),
			),
		);

		$this->_aDate = array(
			'dayofweek' => array(
				"monday" => array(
					"id" => 1,
					"phrase" => _p('directory.monday'),
					'name' => 'monday',
					'description' => '',
				),
				"tuesday" => array(
					"id" => 2,
					"phrase" => _p('directory.tuesday'),
					'name' => 'tuesday',
					'description' => '',
				),
				"wednesday" => array(
					"id" => 3,
					"phrase" => _p('directory.wednesday'),
					'name' => 'wednesday',
					'description' => '',
				),
				"thursday" => array(
					"id" => 4,
					"phrase" => _p('directory.thursday'),
					'name' => 'thursday',
					'description' => '',
				),
				"friday" => array(
					"id" => 5,
					"phrase" => _p('directory.friday'),
					'name' => 'friday',
					'description' => '',
				),
				"saturday" => array(
					"id" => 6,
					"phrase" => _p('directory.saturday'),
					'name' => 'saturday',
					'description' => '',
				),
				"sunday" => array(
					"id" => 7,
					"phrase" => _p('directory.sunday'),
					'name' => 'sunday',
					'description' => '',
				),
			),
		);

		$this->_aBusiness = array(
			'status' => array(
				'draft' => array (
					'id' => 1,
					'phrase' => _p('directory.draft'),
					'description' => '',
					'name' => 'draft'),
				'unpaid' => array (
					'id' => 2,
					'phrase' => _p('directory.unpaid'),
					'description' => '',
					'name' => 'unpaid'),
				'pending' => array (
					'id' => 3,
					'phrase' => _p('directory.pending'),
					'description' => '',
					'name' => 'pending'),
				'denied' => array (
					'id' => 4,
					'phrase' => _p('directory.denied'),
					'description' => '',
					'name' => 'denied'),
				'running' => array (
					'id' => 5,
					'phrase' => _p('directory.running'),
					'description' => '',
					'name' => 'running'),
				'paused' => array (
					'id' => 6,
					'phrase' => _p('directory.paused'),
					'description' => '',
					'name' => 'paused'),
				'completed' => array (
					'id' => 7,
					'phrase' => _p('directory.expired'),
					'description' => '',
					'name' => 'completed'),
				'deleted' => array (
					'id' => 8,
					'phrase' => _p('directory.deleted'),
					'description' => '',
					'name' => 'deleted'),
				'approved' => array (
					'id' => 9,
					'phrase' => _p('directory.approved'),
					'description' => '',
					'name' => 'approved'),
				'pendingclaiming' => array (
					'id' => 10,
					'phrase' => _p('directory.pending_for_claiming'),
					'description' => '',
					'name' => 'pendingclaiming'),
				'claimingdraft' => array (
					'id' => 11,
					'phrase' => _p('directory.claiming_draft'),
					'description' => '',
					'name' => 'claimingdraft'),
				'closed' => array (
					'id' => 12,
					'phrase' => _p('directory.closed'),
					'description' => '',
					'name' => 'closed'),
			),
		);

	}

	public function getListNameOfBlockInDetailLinkBusiness(){
		return array('photos', 'videos', 'musics', 'blogs', 'polls', 'coupons', 'events', 'jobs', 'marketplace');
	}

	public function isPhoto(){
		if(Phpfox::isModule('photo') || Phpfox::isModule('advancedphoto')){
			return true;
		}
		return false;
	}
	public function isVideo(){
		if(Phpfox::isModule('v')){
			return true;
		}
		return false;
	}
	public function isVideoChannel(){
		if(Phpfox::isModule('videochannel')){
			return true;
		}
		return false;
	}
	public function isMusic(){
		if(Phpfox::isModule('music')){
			return true;
		}
		return false;
	}
	public function isBlog(){
		if(Phpfox::isModule('blog')){
			return true;
		}
		return false;
	}
	public function isPoll(){
		if(Phpfox::isModule('poll')){
			return true;
		}
		return false;
	}
	public function isCoupon(){
		if(Phpfox::isModule('coupon')){
			return true;
		}
		return false;
	}
	public function isEvent(){
		if(Phpfox::isModule('event') || Phpfox::isModule('fevent')){
			return true;
		}
		return false;
	}
	public function isJob(){
		if(Phpfox::isModule('jobposting')){
			return true;
		}
		return false;
	}
	public function isMarketplace(){
		if(Phpfox::isModule('marketplace') || Phpfox::isModule('advancedmarketplace')){
			return true;
		}
		return false;
	}

	public function isAdvCoupon(){
		return false;
	}
	public function getModuleIdCoupon(){
		$sController = 'coupon';
		if($this->isAdvCoupon()){
			$sController = 'coupon';
		}

		return $sController;
	}

	public function isAdvMarketplace(){
		return Phpfox::isModule('advancedmarketplace');
	}

	public function getModuleIdMarketplace(){
		$sController = 'marketplace';
		if($this->isAdvMarketplace()){
			$sController = 'advancedmarketplace';
		}

		return $sController;
	}

	public function isAdvJob(){
		return false;
	}

	public function getModuleIdJob(){
		$sController = 'jobposting';
		if($this->isAdvEvent()){
			$sController = 'jobposting';
		}

		return $sController;
	}

	public function isAdvEvent(){
		return Phpfox::isModule('fevent');
	}

	public function getModuleIdEvent(){
		$sController = 'event';
		if($this->isAdvEvent()){
			$sController = 'fevent';
		}

		return $sController;
	}

	public function isAdvPolls(){
		return false;
	}

	public function getModuleIdPolls(){
		$sController = 'poll';
		if($this->isAdvPolls()){
			$sController = 'poll';
		}

		return $sController;
	}

	public function isAdvDiscussion(){
		return false;
	}

	public function getModuleIdDiscussion(){
		$sController = 'forum';
		if($this->isAdvDiscussion()){
			$sController = 'forum';
		}

		return $sController;
	}

	public function isAdvBlog(){
		return Phpfox::isModule('ynblog');
	}

	public function getModuleIdBlog(){
		$sController = 'blog';
		if($this->isAdvBlog() && $this->request()->get('req5') == 'advanced-blog'){
			$sController = 'ynblog';
		}

		return $sController;
	}

	public function isAdvMusic(){
		return false;
		// return Phpfox::isModule('musicsharing');
	}

	public function getModuleIdMusic(){
		$sController = 'music';
		if($this->isAdvMusic()){
			$sController = 'musicsharing';
		}

		return $sController;
	}

	public function isAdvPhoto(){
		return Phpfox::isModule('advancedphoto');
	}

	public function isAdvVideo(){
		return Phpfox::isModule('videochannel');
	}

	public function isUltVideo(){
		return Phpfox::isModule('ultimatevideo');
	}
	public function getModuleIdUltimateVideo(){
		$sController = 'video';
		if($this->isUltVideo()){
			$sController = 'ultimatevideo';
		}

		return $sController;
	}

	public function getModuleIdV(){
		$sController = 'video';
		return $sController;
	}

	public function getModuleIdPhoto(){
		$sController = 'photo';
		if($this->isAdvPhoto()){
			$sController = 'advancedphoto';
		}

		return $sController;
	}

	public function getModuleIdVideo(){
		$sController = 'video';
		if($this->isAdvVideo()){
			$sController = 'videochannel';
		}

		return $sController;
	}

	public function setSessionBeforeAddItemFromSubmitForm($iBusinessId, $type)
	{
		$iCurrentUserId = Phpfox::getUserId();
		$_SESSION[Phpfox::getParam('core.session_prefix')]['yndirectory']['add_new_item'][$iCurrentUserId]['business_id'] = $iBusinessId;
		$_SESSION[Phpfox::getParam('core.session_prefix')]['yndirectory']['add_new_item'][$iCurrentUserId]['type'] = $type;
	}

	public function getSessionAfterUserAddNewItem($type)
	{
		$iCurrentUserId = Phpfox::getUserId();

		if(isset($_SESSION[Phpfox::getParam('core.session_prefix')]['yndirectory']['add_new_item'][$iCurrentUserId]))
		{
			if($_SESSION[Phpfox::getParam('core.session_prefix')]['yndirectory']['add_new_item'][$iCurrentUserId]['type'] == $type)
			{
				return $_SESSION[Phpfox::getParam('core.session_prefix')]['yndirectory']['add_new_item'][$iCurrentUserId]['business_id'];
			}
		}

		return false;
	}

	public function removeSessionAddNewItemOfUser()
	{
		$iCurrentUserId = Phpfox::getUserId();
		unset($_SESSION[Phpfox::getParam('core.session_prefix')]['yndirectory']['add_new_item'][$iCurrentUserId]);

		return true;
	}

	private $_sYnAddParamForNavigateBack = 'ynbusinessid';

	public function getYnAddParamForNavigateBack()
	{
		return $this->_sYnAddParamForNavigateBack;
	}

	public function getMoneyText($sAmount, $sCurrency) {
		// return $sAmount . " " . $sCurrency;
		return $sCurrency . $sAmount;
	}

	/**
	 * to create left sub menu for a controller
	 */
	public function buildMenu() {
		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {

			$sTextMenuMyBusiness = "";
			$iTotalMyMenu = Phpfox::getService('directory')->getTotalMenuMyBusiness();
			if($iTotalMyMenu != 0)
			{
				$sTextMenuMyBusiness = '<span class="my count-item">' . $iTotalMyMenu . '</span>';
			}

			$sTextMenuMyFollow = "";
			$iTotalMyFollow = Phpfox::getService('directory')->countTotalFollowingBusinessByUserId(Phpfox::getUserId());
			if($iTotalMyFollow != 0)
			{
				$sTextMenuMyFollow = '<span class="my count-item">' . $iTotalMyFollow . '</span>';
			}

			$sTextMenuMyFavorite = "";
			$iTotalMyFavorite = Phpfox::getService('directory')->countTotalFavoriteBusinessByUserId(Phpfox::getUserId());
			if($iTotalMyFavorite != 0)
			{
				$sTextMenuMyFavorite = '<span class="my count-item">' . $iTotalMyFavorite . '</span>';
			}
            
            $aFilterMenu = array(
                _p('directory.all_businesses')=> '',
                (_p('directory.my_favorite_businesses'). $sTextMenuMyFavorite )=> 'myfavoritebusinesses',
                (_p('directory.my_following_businesses') . $sTextMenuMyFollow) => 'myfollowingbusinesses',
                (_p('directory.my_businesses') . $sTextMenuMyBusiness) => 'mybusinesses',
                _p('directory.menu_directory_claim_a_business')=> 'directory.view_claimingbusiness',
            );

			Phpfox::getLib('template')->buildSectionMenu('directory', $aFilterMenu);
		}
	}

	/**
	 * getPhraseById('transaction.status', 1) returns phrase of status 1 of transaction
	 * @params $sParam string
	 * @params $iID integer
	 * @return string phrase of the id
	 */
	public function getPhraseById($sParam, $iId) {
		$sType = 'phrase';
		return $this->getConstById($sParam, $iId, $sType);
	}

	public function getNameById($sParam, $iId) {
		$sType = 'name';
		return $this->getConstById($sParam, $iId, $sType);
	}

	public function getAllById($sParam, $iId) {
		$sType = 'all';
		return $this->getConstById($sParam, $iId, $sType);
	}

	public function getDescriptionById($sParam, $iId) {
		$sType = 'description';
		return $this->getConstById($sParam, $iId, $sType);
	}

	public function getConstById($sParam, $iId, $sType) {
		$aParts = explode('.', $sParam);

		if(count($aParts) > 3) {
			return false;
		}

		$sFirst = array_shift($aParts);
		$sLast = array_pop($aParts);

		$aList = $this->getList($sFirst);

		if(!isset($aList[$sLast])) {
			return false;
		}

		foreach($aList[$sLast] as $aItem) {
			if($aItem['id'] == $iId) {
				if($sType == 'all') {
					return $aItem;
				}

				return isset($aItem[$sType]) ? $aItem[$sType] : false;
			}
		}

		return false;
	}

	/**
	 * @params sParam input string, for ex, ad.pending, track.view
	 * @param get : id, phrase or get all
	 * @return bool of the const
	 */
	public function getConst($sParam, $sGet = 'id') {
		$aParts = explode('.', $sParam);

		if(count($aParts) > 3) {
			return false;
		}

		$sFirst = array_shift($aParts);
		$sLast = array_pop($aParts);

		// first is name of corresponding item
		$aList = $this->getList($sFirst);


		if(!$aParts) { // if there is not middle part
			// assume that it is to get all list ex 'transaction.status'
			if($aList) {
				return $aList[$sLast];
			} else {
				return false;
			}
			//$sMiddle = 'type'; // by default, all constants seem to be a type of s.thing
		} else {
			$sMiddle = $aParts[0];
		}

		if(isset($aList[$sMiddle][$sLast])) {
			switch($sGet) {
			case 'id' :
				return $aList[$sMiddle][$sLast]['id'];
				break;
			case 'phrase' :
				return $aList[$sMiddle][$sLast]['phrase'];
				break;
			case 'name' :
				return $aList[$sMiddle][$sLast]['name'];
				break;
			case 'description' :
				return $aList[$sMiddle][$sLast]['description'];
				break;

			case 'all' :
				return $aList[$sMiddle][$sLast];
				break;
			}

			return $aList[$sMiddle][$sLast]['id'];


		} else {
			return false;
		}
	}

	public function getList($sName) {
		$aList = array();
		switch($sName) {
			case 'transaction':
				$aList = $this->_aTransaction;
				break;

			case 'package':
				$aList = $this->_aPackage;
				break;

			case 'date':
				$aList = $this->_aDate;
				break;

			case 'business':
				$aList = $this->_aBusiness;
				break;

			case 'default':
				break;
			}

		return $aList;
	}

	public function removeEmptyElement($aArray) {
		$aReturn = array();
		foreach($aArray as $sElement ) {
			if($sElement) {
				$aReturn[] = $sElement;
			}
		}

		return $aReturn;
	}

	public function checkEmptyInputArrayField($aArray){
		foreach ($aArray as $key => $value) {
			if(strlen(trim($value)) > 0){
				return false;
			}
		}

		return true;
	}

	public function getCurrentCurrencies($sGateway = 'paypal', $sDefaultCurrency = '') {
		$aFoxCurrencies = Phpfox::getService('core.currency')->getForBrowse();
		$aResults = array();
		foreach($aFoxCurrencies as $aCurrency)
		{
			if ($aCurrency['is_default'] == '1'){
                $aResults[] = $aCurrency;
			}
		}

		return $aResults;
	}

	public function getScript($sScript) {
		return '<script type="text/javascript"> ' . $sScript . ' </script>';
	}

	public function getJsSetupParams() {
		return array(
			'fb_small_loading_image_url' => Phpfox::getLib('template')->getStyle('image', 'ajax/add.gif'),
			'ajax_file_url' => Phpfox::getParam('core.path') . 'static/ajax.php',
		);
	}

	public function loadDirectoryJsCss() {
		$aParams = $this->getJsSetupParams();
		Phpfox::getLib('template')
			->setHeader('cache' ,array(
				'jquery.validate.js'	=> 'module_directory',

				'yndirectoryhelper.js' => 'module_directory',
				'jquery.wookmark.js' => 'module_directory',
				'jquery.easing.1.3.js' => 'module_directory',
				'jquery.flexslider.js' => 'module_directory',
				'yndirectory.js' => 'module_directory',


				'<script type="text/javascript">$Behavior.loadYnDirectorySetupParam = function() { yndirectory.setParams(\''. json_encode($aParams) .'\'); }</script>'
			))
			->setPhrase( array(  // phrase for JS
				'directory.please_enter_location',
				'directory.this_field_is_required',
				'directory.please_enter_a_valid_url_for_example_http_example_com',
				'directory.please_enter_a_value_with_a_valid_extension',
				'directory.please_enter_at_least_0_characters',
				'directory.please_enter_a_value_greater_than_or_equal_to_0',
				'directory.please_enter_a_valid_number',
				'directory.please_enter_no_more_than_0_characters',
				'directory.category',
				'directory.short_description',
				'directory.business_sizes',
				'directory.operating_hours',
				'directory.founders',
				'directory.contact_information',
				'directory.locations',
				'directory.description',
				'directory.from',
				'directory.to',
				'directory.get_directions',
				'directory.featured',
				'directory.businesses',
				'directory.cannot_load_google_api_library_please_reload_the_page_and_try_again',
				'directory.compare',
				'directory.please_select_more_than_one_entry_for_the_comparison',
				'directory.add_to_compare',
				'directory.remove_from_compare',
				'directory.transfer_owner',
				'directory.promote_business',
				'directory.timezone',
				'directory.phone',
				'directory.fax',
				'directory.email',
				'directory.website',
				'directory.are_you_sure_you_want_to_delete_this_business',
				'directory.yes',
				'directory.no',
				'directory.you_can_add_only_maximum_3_categories',
				'directory.business',
				'directory.are_you_sure_you_want_to_delete_businesses_that_you_selected',
				'directory.announcement_detail',
				'directory.address_is_required',
				'directory.delete_business',
                'directory.additional_infomation',
                'notice'
			));
	}

    /**
     * Show datetime in interface
     */
    public function convertToUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;

        $iTime = $iTime + $iTimeZoneOffsetInSecond;

        return $iTime;
    }

    /**
     * Store datetime in server with GMT0
     */
    public function convertFromUserTimeZone($iTime)
    {
        $iTimeZoneOffsetInSecond = Phpfox::getLib('date') -> getTimeZone() * 60 * 60;

        $iTime = $iTime - $iTimeZoneOffsetInSecond;

        return $iTime;
    }

	public function getStartOfDay($iTimeStamp) {
		return mktime(0, 0,0, date('m', $iTimeStamp), date('d', $iTimeStamp), date('y', $iTimeStamp));
	}

	public function getEndOfDay($iTimeStamp) {
		return mktime(23, 59, 59, date('m', $iTimeStamp), date('d', $iTimeStamp), date('y', $iTimeStamp));
	}

	public function getDateString($aData) {
		return $aData['month'] . '/' . $aData['day'] ;
		   //	'-' . $aData['year'];
	}

	public function convertTime($iTimeStamp, $format = null) {
		if(!$iTimeStamp) {
			return _p('directory.none');
		}
		if(null == $format){
			$format = Phpfox::getParam('core.global_update_time');
		}
		return date($format, $iTimeStamp);
	}

    public function isNumeric($val){
        if(strlen(trim($val)) == 0){
            return false;
        }

        if (!is_numeric($val))
        {
            return false;
        }

        return true;
    }

    public function price($sPrice)
    {
        if (empty($sPrice))
        {
            return '0.00';
        }

        $sPrice = str_replace(array(' ', ','), '', $sPrice);
        $aParts = explode('.', $sPrice);
        if (count($aParts) > 2)
        {
            $iCnt = 0;
            $sPrice = '';
            foreach ($aParts as $sPart)
            {
                $iCnt++;
                $sPrice .= (count($aParts) == $iCnt ? '.' : '') . $sPart;
            }
        }

        return $sPrice;
    }

    public function getUserParam($sParam, $iUserId) {

        $iGroupId = $this->getUserBy('user_group_id', $iUserId);

        return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

    }

    private function getUserBy($sVar, $iUserId ) {

        $result = $this->_getUserInfo($iUserId);
        if (isset($result[$sVar]))
        {
            return $result[$sVar];
        }

        return false;
    }

    private function _getUserInfo($iUserId) {
        $aRow = $this->database()->select('u.*')
            ->from(Phpfox::getT('user'), 'u')
            ->where('u.user_id = ' . $iUserId)
            ->execute('getRow');
        if(!$aRow) {
            return false;
        }

        $aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
        $aRow['location'] = $aRow['country_iso']; // we will improve it later to deal with cities 
        $aRow['language'] = $aRow['language_id'];
        return $aRow;
        // $this->_aUser = $aRow;
    }

    public function getBusinessSize(){
    	return array('1 - 50', '51 - 250', '251 - 1000', '> 1000');
    }

    public function getVisitingHours(){
    	return array(
    		'dayofweek' => $this->_aDate['dayofweek'],
    		'hour' => array(
                'Closed',
    			'00:00', '00:30',
    			'01:00', '01:30',
    			'02:00', '02:30',
    			'03:00', '03:30',
    			'04:00', '04:30',
    			'05:00', '05:30',
    			'06:00', '06:30',
    			'07:00', '07:30',
    			'08:00', '08:30',
    			'09:00', '09:30',
    			'10:00', '10:30',
    			'11:00', '11:30',
    			'12:00', '12:30',
    			'13:00', '13:30',
    			'14:00', '14:30',
    			'15:00', '15:30',
    			'16:00', '16:30',
    			'17:00', '17:30',
    			'18:00', '18:30',
    			'19:00', '19:30',
    			'20:00', '20:30',
    			'21:00', '21:30',
    			'22:00', '22:30',
    			'23:00', '23:30',
			),
		);
    }
    public function getHoursFormat($aVisitingHours){
        if(Phpfox::getParam('directory.display_format_hour_of_operation')=='24h'){
            $aVisitingHours['format'] = array(
                'Closed',
                '00:00', '00:30',
                '01:00', '01:30',
                '02:00', '02:30',
                '03:00', '03:30',
                '04:00', '04:30',
                '05:00', '05:30',
                '06:00', '06:30',
                '07:00', '07:30',
                '08:00', '08:30',
                '09:00', '09:30',
                '10:00', '10:30',
                '11:00', '11:30',
                '12:00', '12:30',
                '13:00', '13:30',
                '14:00', '14:30',
                '15:00', '15:30',
                '16:00', '16:30',
                '17:00', '17:30',
                '18:00', '18:30',
                '19:00', '19:30',
                '20:00', '20:30',
                '21:00', '21:30',
                '22:00', '22:30',
                '23:00', '23:30',
            );
            return $aVisitingHours;
        }
        else
        {
            $aVisitingHours['format'] = array(
                'Closed',
                '12:00 AM', '12:30 AM',
                '01:00 AM', '01:30 AM',
                '02:00 AM', '02:30 AM',
                '03:00 AM', '03:30 AM',
                '04:00 AM', '04:30 AM',
                '05:00 AM', '05:30 AM',
                '06:00 AM', '06:30 AM',
                '07:00 AM', '07:30 AM',
                '08:00 AM', '08:30 AM',
                '09:00 AM', '09:30 AM',
                '10:00 AM', '10:30 AM',
                '11:00 AM', '11:30 AM',
                '12:00 PM', '12:30 PM',
                '01:00 PM', '01:30 PM',
                '02:00 PM', '02:30 PM',
                '03:00 PM', '03:30 PM',
                '04:00 PM', '04:30 PM',
                '05:00 PM', '05:30 PM',
                '06:00 PM', '06:30 PM',
                '07:00 PM', '07:30 PM',
                '08:00 PM', '08:30 PM',
                '09:00 PM', '09:30 PM',
                '10:00 PM', '10:30 PM',
                '11:00 PM', '11:30 PM',
            );
            return $aVisitingHours;
        }
    }
    public function getHoursFormatToView($aHours){
        if(Phpfox::getParam('directory.display_format_hour_of_operation')!='24h')
        {

            if($aHours['vistinghour_starttime'] != 'Closed' && $aHours['vistinghour_endtime'] !='Closed')
            {
                $aHours['vistinghour_starttime'] = ($aHours['vistinghour_starttime'] != null ? date('h:i a', strtotime($aHours['vistinghour_starttime'])) : '') ;
                $aHours['vistinghour_endtime'] = ($aHours['vistinghour_endtime'] != null ? date('h:i a', strtotime($aHours['vistinghour_endtime'])) : '') ;
            }
        }
        return $aHours;
    }
    public function getHour(){

    }

	public function getPeriodByUserTimeZone($period = 'today', $range_of_dates = array()){
		$curTimeOfUser = $this->convertToUserTimeZone(PHPFOX_TIME);
		// $curTime = PHPFOX_TIME;

        $month = date('n', $curTimeOfUser);
        $day = date('j', $curTimeOfUser);
        $year = date('Y', $curTimeOfUser);
        $start_hour = date('H', $curTimeOfUser);
        $start_minute = date('i', $curTimeOfUser);
        $start_second = date('s', $curTimeOfUser);

        $iStartTime = 0;
        $iEndTime = 0;
		switch ($period) {
			case 'today':
				// Today
				$iStartTime = $this->getStartOfDay($curTimeOfUser);
				$iEndTime = $this->getEndOfDay($curTimeOfUser);
				break;
			case 'yesterday':
				// Yesterday
				$curTimeOfUser = $curTimeOfUser - (1 * 24 * 60 * 60);
				$iStartTime = $this->getStartOfDay($curTimeOfUser);
				$iEndTime = $this->getEndOfDay($curTimeOfUser);
				$iLimit = 2;
				break;
			case 'last_week':
				// Last week
	            $week = date("W", $curTimeOfUser);
	            $result = $this->getStartAndEndDateOfWeek($week - 1, $year, $curTimeOfUser);

            	// this is this_week, need to convert to last_week
	            $iStartTime = $result[0];
	            $iEndTime = $result[1];

	            $iStartTime = $iStartTime - (7 * 24 * 60 * 60);
	            $iEndTime = $iEndTime - (7 * 24 * 60 * 60);
				break;
			case 'range_of_dates':
				// Range of dates
				break;
			default:
				break;
		}

        $iStartTime = $this->convertFromUserTimeZoneToServerTime($iStartTime);
        $iEndTime = $this->convertFromUserTimeZoneToServerTime($iEndTime);
        return array('start' => $iStartTime, 'end' => $iEndTime);
	}

	public function convertFromUserTimeZoneToServerTime($iTime)
	{
		$iTimeZoneOffsetInSecond = Phpfox::getLib('date')->getTimeZone() * 60 * 60;
		// on the interface we have convert into gmt, now we roll back to server time
		$iTime = $iTime - $iTimeZoneOffsetInSecond;

		return $iTime;
	}


    public function getStartAndEndDateOfWeek($week, $year, $curUser)
    {
        $time = strtotime("1 January $year", $curUser);
        $day = date('w', $time);
        $time += ((7*$week)+1-$day)*24*3600;

        $return[0] = $time;

        $time += 7*24*3600 - 1;
        $return[1] = $time;

        return $return;
    }

    public function getDefaultLanguage(){

		$aCond = array('l.is_default = 1');
  		$aLanguageDefault = Phpfox::getService('language')->get($aCond);
  		if(isset($aLanguageDefault[0])){
  			return $aLanguageDefault[0]['language_code'];
  		}
  		else{
  			return 'en';
  		}
    }

    public function getStatusCode($sStatus)
    {
        if (isset($this->_aBusiness['status'][$sStatus])) {
            return $this->_aBusiness['status'][$sStatus]['id'];
        } else {
            return false;
        }
    }

    public function isEmail($email){
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		return true;
    }

	public function array_divide($array, $segmentCount) {
	    $dataCount = count($array);
	    if ($dataCount == 0) return false;
	    $segmentLimit = ceil($dataCount / $segmentCount);
	    $outputArray = array_chunk($array, $segmentLimit);

	    return $outputArray;
	}


	public function isHavingFeed($type_id, $item_id){
		$aFeed = $this -> database()
					-> select('feed.*')
					->from(Phpfox::getT("feed"), 'feed')
					->where('type_id = \'' . $type_id . '\' AND item_id = ' . (int)$item_id)
					-> execute("getSlaveRow");

		if(isset($aFeed['feed_id'])){
			return true;
		}

		return false;
	}

	public function getFeed($type_id, $item_id){
		return $this -> database()
					-> select('feed.*')
					->from(Phpfox::getT("feed"), 'feed')
					->where('type_id = \'' . $type_id . '\' AND item_id = ' . (int)$item_id)
					-> execute("getSlaveRow");
	}

	public function getCurrencyById($currency_id){
		return $this -> database()
					-> select('currency.*')
					->from(Phpfox::getT("currency"), 'currency')
					->where('currency_id = \'' . $currency_id . '\' ')
					-> execute("getSlaveRow");
	}

	public function getLanguageIdByUserId($iUserId){
		$language_id = $this -> database()
					->select("u.language_id")
					->from(Phpfox::getT("user"),'u')
					->where('u.user_id = ' . (int)$iUserId)
					->execute("getSlaveField");

		if($language_id == null){
			$language_id = 'en';
		}

		return $language_id;
	}

	public function getThemeFolder(){
		return Phpfox::getLib('template')->getThemeFolder();
	}

    public function getTimeLineStatus($iStart, $iEnd)
    {
        if ($iStart > PHPFOX_TIME)
        {
            return 'upcoming';
        }
        elseif ($iEnd < PHPFOX_TIME)
        {
            return 'past';
        }
        else
        {
            return 'ongoing';
        }
    }

    public function timestampToCountdownString($iTimeStamp, $type = 'upcoming')
    {
        $result = '';

        $iLeft = $iTimeStamp - PHPFOX_TIME;

        if ($iLeft >= 60)
        {
            $sLeft = $this->secondsToString($iLeft);
            if('upcoming' == $type){
                $result = _p('directory.start_in_uppercase') . ': ' . $sLeft;
            } else if('ongoing' == $type){
                $result = $sLeft.' '._p('directory.left');
            }
        }
        elseif ($iLeft > 0)
        {
            if('upcoming' == $type){
                $result = _p('directory.start_in_uppercase') . ': ' . '1'.' '._p('directory.minute');
            } else if('ongoing' == $type){
                $result = '1'.' '._p('directory.minute').' '._p('directory.left');
            }
        }

        return $result;
    }

    public function displayRepeatTime($isrepeat = -1, $timerepeat = 0)
    {
        $content_repeat = "";
        $until = "";
        if ($isrepeat == 0)
        {
            $content_repeat = _p('directory.daily');
        }
        elseif ($isrepeat == 1)
        {
            $content_repeat = _p('directory.weekly');
        }
        elseif ($isrepeat == 2)
        {
            $content_repeat = _p('directory.monthly');
        }
        if ($content_repeat != "")
        {
            if ($timerepeat != 0)
            {
                $until = Phpfox::getTime("M j, Y", $this->convertToUserTimeZone($timerepeat), false);
                $content_repeat .= ", " . _p('directory.until') . " " . $until;
            }
        }

        return $content_repeat;
    }

    public function displayTimeByFormat($format = 'M j, Y g:i a', $time)
    {
		return Phpfox::getTime($format, $this->convertToUserTimeZone($time), false);
    }

    public function secondsToString($timeInSeconds)
    {
        static $phrases = null;

        $seeks = array(
            31536000,
            2592000,
            86400,
            3600,
            60
        );

        if (null == $phrases)
        {
            $phrases = array(
                array(
                    ' '._p('directory.year'),
                    ' '._p('directory.month_lower_case'),
                    ' '._p('directory.day_lc'),
                    ' '._p('directory.hour'),
                    ' '._p('directory.minute')
                ),
                array(
                    ' '._p('directory.years'),
                    ' '._p('directory.months'),
                    ' '._p('directory.days'),
                    ' '._p('directory.hours'),
                    ' '._p('directory.minutes')
                )
            );
        }

        $result = array();

        $remain = $timeInSeconds;

        foreach ($seeks as $index => $seek)
        {
            $check = intval($remain / $seek);
            $remain = $remain % $seek;

            if ($check > 0)
            {
                $result[] = $check . $phrases[($check > 1) ? 1 : 0][$index];
            }

            if ($timeInSeconds < 86400)
            {
                if (count($result) > 1)
                {
                    break;
                }
            }
            else
            {
                if (count($result) > 0)
                {
                    break;
                }
            }
        }

        return implode(' ', $result);
    }
}
