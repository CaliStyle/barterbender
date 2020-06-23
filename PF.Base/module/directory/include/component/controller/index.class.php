<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Index extends Phpfox_Component
{
    /**
     * it is true if we are in profile of an user
     *
     * @var boolean
     */
    private $_bIsProfile = false;

    /**
     * this array contains all information about the user we are viewing profile
     *
     * @var array
     */
    private $_aProfileUser = array();

    /**
     *  an array contains information of parent module which calls this controller
     * array(
     *  'module_id' => string 'pages',
     *  'item_id' => string '1',
     *  'url' => string 'http://minhta.younetco.com/minhta/index.php?do=/pages/1/'
     * )
     *
     * @var array
     */
    private $_aParentModule = null;


    private function _checkIsInAjaxControllerAndInUserProfile() {
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $this->_bIsProfile = true;
            $this->_aProfileUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $this->_aProfileUser);
        } else {
            $this->_bIsProfile = $this->getParam('bIsProfile');
            if ($this->_bIsProfile === true) {
                $this->_aProfileUser = $this->getParam('aUser');
            }
        }
    }

    private function _checkIsInPageModule()
    {
        $bIsInPages = FALSE;
        if($this->request()->get('req1') == 'pages')
        {
            $bIsInPages = TRUE;
        }
        return $bIsInPages;
    }

    /**
     * check if we are in home page or profile page
     * @return bool
     */
    private function _checkIsInHomePage() {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempSearch = $this->request()->get('s', 0);
        $sTempView = $this->request()->get('view', false);
        if ($sTempSearch == '' && $sTempView == '' && !isset($aParentModule['module_id']) && !$this->request()->get('search-id')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('search')
            && $this->request()->get('req2') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }
        return $bIsInHomePage;
    }

    private function _checkIsInSearch() {
        $bIsSearch = false;
        if ($this->request()->get('search-id')
            || $this->request()->get('sort')
            || $this->request()->get('show')) {
            $bIsSearch = true;
        }
        return $bIsSearch;
    }
    /**
     * @return array param to browse campaign list
     */
    private function _initializeSearchParams() {
        $aSearchFields = array(
            'type' => 'directory',
            'field' => 'dbus.directory_id',
            'search_tool' => array(
                'table_alias' => 'dbus',
                'search' => array(
                    'action' => $this->_aParentModule != null ? $this->url()->makeUrl($this->_aParentModule['module_id'], array($this->_aParentModule['item_id'], 'directory')) : ($this->_bIsProfile === true ? $this->url()->makeUrl($this->_aProfileUser['user_name'], array('directory', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('directory', array('view' => $this->request()->get('view')))),
                    'default_value' => _p('directory.search_business'),
                    'name' => 'search',
                    'field' => array('dbus.name','dbt.description'),
                ),
                'sort' => array(
                    'newest' => array('dbus.time_stamp', _p('directory.newest')),
                    'oldest' => array('dbus.time_stamp', _p('directory.oldest'),'ASC'),
                    'a-z' => array('dbus.name', _p('directory.a_z'),'ASC'),
                    'z-a' => array('dbus.name', _p('directory.z_a')),
                ),
                'show' => array(12, 24, 36),
            )
        );

        $bInHomepage = $this->_checkIsInHomePage();
        if ($bInHomepage) {
            $aSearchFields['search_tool']['no_filters'] = [_p('sort'), _p('show'), _p('when')];
            unset($aSearchFields['search_tool']['custom_filters']);
        }
        $this->search()->set($aSearchFields);

        $aBrowseParams = array(
            'module_id' => 'directory',
            'alias' => 'dbus',
            'field' => 'business_id',
            'table' => Phpfox::getT('directory_business'),
            'hide_view' => array('pending', 'my'),
        );

        if (Phpfox::getParam('core.section_privacy_item_browsing')) {
            $aBrowseParams['join'] = array(
                'alias' => 'dbt',
                'field' => 'business_id',
                'table' => Phpfox::getT('directory_business_text')
            );
        }

        return $aBrowseParams;
    }

    private function _setAdvSearchConditions()
    {
        $aSearch = $this->request()->get('search');
        $aCategory = $aSearch['category'];

        $sLocation = $this->search()->get('location_address');
        $sLocationLat = floatval($this->search()->get('location_address_lat'));
        $sLocationLng = floatval($this->search()->get('location_address_lng'));
        $iRadius = floatval($this->search()->get('radius'));

        $sView = $this->request()->get('view');

        $bAdvSearch = false;

        switch ($sView) {
            case 'mybusinesses':
                $this->_setConditionAndHandleMyView();
                break;
            case 'myfavoritebusinesses':
                $this->_setConditionAndHandleFavoriteView();
                break;
            case 'myfollowingbusinesses':
                $this->_setConditionAndHandleFollowingView();
                break;
        }

        $aForms = array(
            'category' => $aCategory,
            'searchblock_location' => $sLocation,
            'location_address' => $sLocation,
            'location_address_lat' => $sLocationLat,
            'location_address_lng' => $sLocationLng,
            'radius' => ($iRadius != 0)?$iRadius:'',
        );

        $this->template()->assign('aForms', $aForms);

        $aCategory = array_filter(array_unique($aCategory));

        if (is_array($aCategory) && !empty($aCategory))
        {
            $bAdvSearch = true;
            $iChild = $aCategory[0];
            foreach ($aCategory as $k => $iCategory)
            {
                if (!is_numeric($iCategory)) {
                    unset($aCategory[$k]);
                    continue;
                }
                if (Phpfox::getService('directory.category')->isChild($iCategory, $iChild))
                {
                    $iChild = $iCategory;
                }
            }
            $this->_setConditionByCategory($iChild);
            $this->setParam('category', $iChild);
        }

        if($iRadius == 0){
            $iRadius = 1;
        }
        if($iRadius > 0 && $sLocation != '' ){
            $bAdvSearch = true;
            Phpfox::getLib('database')
                ->join(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id');
            $this->_setConditionByLocation($sLocationLat,$sLocationLng,$iRadius);
        }

        $this->template()->assign('bAdvSearch', $bAdvSearch);
    }

    public function _setAdvSearchConditionsUrl(){

        $sKeyword = $this->request()->get('keyword');
        $sLocation = $this->search()->get('location_address');
        $sLocationLat = floatval($this->search()->get('location_address_lat'));
        $sLocationLng = floatval($this->search()->get('location_address_lng'));
        $iRadius = floatval($this->search()->get('radius'));

        $sViewCategory = $this->request()->get('category');
        $aCategory = explode(",", $sViewCategory);
        $aCategory = array_filter(array_unique($aCategory));

        $sView = $this->request()->get('view');

        switch ($sView) {
            case 'mybusinesses':
                $this->_setConditionAndHandleMyView();
                break;
            case 'myfavoritebusinesses':
                $this->_setConditionAndHandleFavoriteView();
                break;
            case 'myfollowingbusinesses':
                $this->_setConditionAndHandleFollowingView();
                break;
        }


        $aForms = array(
            'keyword' => $sKeyword,
            'category' => $aCategory,
            'searchblock_location' => $sLocation,
            'location_address' => $sLocation,
            'location_address_lat' => $sLocationLat,
            'location_address_lng' => $sLocationLng,
            'radius' => ($iRadius != 0)?$iRadius:'',
        );

        $this->template()->assign('aForms', $aForms);

        if (!empty($sKeyword))
        {
            $this->search()->setCondition('AND dbus.name LIKE "%'.$sKeyword.'%" ');
        }


        if (is_array($aCategory) && !empty($aCategory))
        {
            $iChild = $aCategory[0];
            foreach ($aCategory as $k => $iCategory)
            {
                if (Phpfox::getService('directory.category')->isChild($iCategory, $iChild))
                {
                    $iChild = $iCategory;
                }
            }

            $this->_setConditionByCategory($iChild);


            $this->setParam('category', $iChild);
        }

        if($iRadius == 0){
            $iRadius = 1;
        }
        if($iRadius > 0 && $sLocation != '' ){
            $this->_setConditionByLocation($sLocationLat,$sLocationLng,$iRadius);
        }

    }

    private function _setConditionByLocation($sLat,$sLng,$iRadius){
        $this->search()->setCondition(" AND (
                        (3959 * acos(
                                cos( radians('{$sLat}')) 
                                * cos( radians( dbl.location_latitude ) ) 
                                * cos( radians( dbl.location_longitude ) - radians('{$sLng}') ) 
                                + sin( radians('{$sLat}') ) * sin( radians( dbl.location_latitude ) ) 
                            ) <= {$iRadius} 
                        )                     
                    )");
    }
    private function _setConditionAndHandleMyView() {

        Phpfox::isUser(true);
        $this->search()->setCondition('AND dbus.user_id = ' . Phpfox::getUserId() . ' AND dbus.business_status != ' . (int)Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming') .' AND dbus.business_status != ' . (int)Phpfox::getService('directory.helper')->getConst('business.status.deleted'));

    }
    private function _setConditionAndHandleFavoriteView() {
        Phpfox::isUser(true);
        $this->search()->setCondition(' AND dfav.user_id = '.Phpfox::getUserId().' AND dbus.business_status IN '."("
            . Phpfox::getService('directory.helper')->getConst('business.status.approved')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.completed')
            . ")" );
    }

    private function _setConditionAndHandleFollowingView() {
        Phpfox::isUser(true);
        $this->search()->setCondition(' AND dfo.user_id = '.Phpfox::getUserId().' AND dbus.business_status IN '."("
            . Phpfox::getService('directory.helper')->getConst('business.status.approved')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')
            . "," . Phpfox::getService('directory.helper')->getConst('business.status.completed')
            . ")" );
    }

    private function _setConditionAndHandleClaimingBusinessView() {
        Phpfox::getService('directory.permission')->canClaimBusiness(true);
        $this->search()->setCondition(''
            .' AND dbus.type = \'claiming\''
            .' AND dbus.business_status != '.Phpfox::getService('directory.helper')->getStatusCode('deleted')
            .' AND dbus.business_status != '.Phpfox::getService('directory.helper')->getStatusCode('pendingclaiming')
            .' AND dbus.business_status != '.Phpfox::getService('directory.helper')->getStatusCode('claimingdraft')
        );
    }

    /**
     *
     * @param string $sView name of the view we are going to see
     */
    private function _setConditionAndHandleDefaultView($sView) {

        if ($this->_bIsProfile === true) {

            $this->search()->setCondition("AND  dbus.module_id = 'directory' AND dbus.user_id = " . $this->_aProfileUser['user_id']);

            if($this->_aProfileUser['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin())
            {
                $this->search()->setCondition(" AND dbus.business_status IN(" . (Phpfox::getService('directory.helper')->getStatusCode('running')) . ") AND dbus.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($this->_aProfileUser)) . ")");
            }
        } else if ($this->_aParentModule != null && defined('PHPFOX_IS_PAGES_VIEW')) {

            $this->search()->setCondition("AND dbus.module_id = '" . $this->_aParentModule['module_id'] . "' AND dbus.item_id  = " . $this->_aParentModule['item_id'] . " AND dbus.privacy IN(%PRIVACY%) ");

            if(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->isAdmin($this->_aParentModule['item_id']) || Phpfox::isAdmin())
            {
                $this->search()->setCondition("AND ( (dbus.business_status IN "."(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')  . ")"." ) || dbus.user_id = " . Phpfox::getUserId() . ")");
            }
            else
            {
                $this->search()->setCondition("AND ( (dbus.business_status IN "."(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')  . ")"." ) || dbus.user_id = " . Phpfox::getUserId() . ")");
            }

        } else {
            $this->search()->setCondition("AND dbus.module_id = 'directory' AND dbus.privacy IN(%PRIVACY%) ");// . Phpfox::getService('directory')->getStatusCode('running') . ' ');
        }
        if (!$this->_bIsProfile && $this->_aParentModule == null && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $this->search()->setCondition("AND dbus.business_status IN "."(" . Phpfox::getService('directory.helper')->getConst('business.status.approved') . "," . Phpfox::getService('directory.helper')->getConst('business.status.running')  . ")" );
        }

    }

    private function _checkIsThisACategoryRequestAndHandleIt() {
        // check category request and set corresponding condition
        if ($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) == 'category') {
            if ($aDirectoryCategory = Phpfox::getService('directory.category')->getForEdit($this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3')))) {
                if (!$aDirectoryCategory['is_active']) {
                    $this->template()->setBreadCrumb(_p('directory'), $this->url()->makeUrl('directory'))
                    ;
                    return Phpfox_Module::instance()->setController('error.404');
                }
                if($aParentCategory = Phpfox::getService('directory.category')->getForEdit($aDirectoryCategory['parent_id']))
                {
                    $sPhrase = (Core\Lib::phrase()->isPhrase($aParentCategory['title']) ? _p($aParentCategory['title']) : Phpfox::getLib('locale')->convert($aParentCategory['title']));
                    $this->template()->setBreadCrumb($sPhrase, $this->url()->permalink(array('directory.category', 'view' => $this->request()->get('view')), $aParentCategory['category_id'], $aParentCategory['title']));
                }

                $iCategory = $this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3'));

                $this->_setConditionByCategory($iCategory);

                $sPhrase = (Core\Lib::phrase()->isPhrase($aDirectoryCategory['title']) ? _p($aDirectoryCategory['title']) : Phpfox::getLib('locale')->convert($aDirectoryCategory['title']));
                $this->template()->setTitle($sPhrase);
                $this->template()->setBreadCrumb($sPhrase, $this->url()->makeUrl('current'), true);

                $this->search()->setFormUrl($this->url()->permalink(array('directory.category', 'view' => $this->request()->get('view')), $aDirectoryCategory['category_id'], $aDirectoryCategory['title']));
            }
        }
    }


    private function _setGlobalModeration($aMenu) {
        $this->setParam('global_moderation', array(
                'name' => 'directory',
                'ajax' => 'directory.moderation',
                'menu' => $aMenu,
            )
        );
    }

    private function _setConditionByCategory($iCategory)
    {
        $sCategories = $iCategory;

        $sChildIds = Phpfox::getService('directory.category')->getChildIds($iCategory);
        if (!empty($sChildIds))
        {
            $sCategories .= ','.$sChildIds;
        }

        $this->search()->setCondition('AND dcd.category_id IN(' . $sCategories . ')');
    }

    public function process()
    {
        //https://jira.younetco.com/browse/PFBIZPAGE-606
        if (defined('PHPFOX_IS_PAGES_VIEW')) {
            $this->url()->send('directory');
        }

        // redirect to detail
        if(in_array($this->request()->get('req3'), ['photo', 'advancedphoto'])) {
            $id = $this->request()->get('req2');
            $aBusiness = Phpfox::getService('directory')->getBusinessById($id);
            $this->url()->permalink('directory.detail', $id, $aBusiness['name'], true, '', array('photos'));
        }

        Phpfox::getUserParam('directory.can_view_business', true);
        $this->_checkIsInAjaxControllerAndInUserProfile();
        $bIsHomepage = $this->_checkIsInHomePage();
        $bIsSearch = $this->_checkIsInSearch();
        $this->_aParentModule = $this->getParam('aParentModule');
        $sView = $this->request()->get('view');

        $aBrowseParams = $this->_initializeSearchParams();

        $sSearch = $this->request()->get('search');
        if(is_array($sSearch)) {
            $sSearch = $sSearch['search'];
        }

        $this->template()->assign('sSearch', $sSearch);

        $this->_setAdvSearchConditions();

        if ($this->search()->get('advsearch'))
        {

        }
        else
            if($this->request()->get('advsearch')){ //bookmark url for search result
                $this->_setAdvSearchConditionsUrl();
            }

        $this->template()
            ->setBreadCrumb(_p('directory.business_directory'), $this->url()->makeUrl('directory'))
        ;

        $aModerateMenu = array();
        switch ($sView) {
            case 'mybusinesses':
                $this->_setConditionAndHandleMyView();
                $aModerateMenu = array(
                    array(
                        'phrase' => _p('directory.delete_business'),
                        'action' => 'delete'
                    )
                );
                break;
            case 'myfavoritebusinesses':
                $this->_setConditionAndHandleFavoriteView();
                break;
            case 'myfollowingbusinesses':
                $this->_setConditionAndHandleFollowingView();
                break;
            case 'claimingbusiness':
                $this->_setConditionAndHandleClaimingBusinessView();
                break;
            default:
                $this->_setConditionAndHandleDefaultView($sView);
                break;
        }
        $this->template()->setTitle(_p('directory.business_directory'));
        $this->_checkIsThisACategoryRequestAndHandleIt();
        if('tag' == $this->request()->get('req2')){
            if ($aTag = Phpfox::getService('tag')->getTagInfo('business', $this->request()->get('req3')))
            {
                $this->setParam('sTagType', 'business');
                $this->template()->setBreadCrumb(_p('tag.topic') . ': ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);
                $this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\'');
            }
        }

        $sCategory = '';

        if ($this->request()->get('req2') == 'category') {
            $sCategory = $this->request()->getInt('req3');
            $this->search()->setCondition('AND dcd.category_id = ' . (int) $sCategory);
        }

        $this->setParam('sCategory', $sCategory);

        // For performance
        if ($this->request()->get('sort') == 'oldest') {
            $this->search()->setContinueSearch(false);
        } else {
            $this->search()->setContinueSearch(true);
        }
        $this->search()->browse()->setPagingMode(Phpfox::getParam('directory.directory_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();
        $aRows = $this->search()->browse()->getRows();

        $aSearch = $this->request()->get('search');
        $aCategory = $aSearch['category'];
        if(!empty($aCategory)) {
            Phpfox::getService('directory.process')->saveLastingSearch($aCategory);
        }

        $sType = '';
        foreach($aRows as $key =>$aBusiness)
        {
            $aRows[$key] = Phpfox::getService('directory')->retrieveMoreInfoFromBusiness($aRows[$key],$sType);
            $aChildCategory = Phpfox::getService('directory')->isHaveChildCategory($aBusiness['business_id'],$aBusiness['category_id']);
            $aRows[$key]['childCategory'] = $aChildCategory;
            $aPhoneNumber = Phpfox::getService('directory')->getBusinessPhone($aBusiness['business_id']);
            $aRows[$key]['phone_number'] = $aPhoneNumber[0]['phone_number'];
        }

        $aItems = $aRows;

        foreach ($aItems as $key => $aItem) {
            $aCoverPhotos = Phpfox::getService('directory')->getImages($aItem['business_id'], 1);
            $sPathCoverPhoto = "";
            if(count($aCoverPhotos)){

                $aItems[$key]['default_cover'] =  false;
                $sPathCoverPhoto = 'yndirectory/'.$aCoverPhotos[0]['image_path'];
            }
            else{
                $aItems[$key]['default_cover'] =  true;
                $sPathCoverPhoto = Phpfox::getParam('core.path').'module/directory/static/image/default_cover.png';
            }

            $aItems[$key]['cover_photo'] = $sPathCoverPhoto;
            $aItems[$key]['cover_photo_server_id'] = $aCoverPhotos[0]['server_id'];
            if (empty($aItems[$key]['logo_path'])) {
                $aItems[$key]['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
            }
        }

        if(!$bIsHomepage || $bIsSearch)
        {
            if((int)$this->search()->getPage() > 0){
                $sViewType = Phpfox::getCookie('yndirectory_menu_viewtype');
            }
        } else {
            $sViewType = $this->request()->get('viewtype');
            Phpfox::setCookie('yndirectory_menu_viewtype', '', -1);
        }
        if(empty($sViewType)){
            $default_view = strtolower(Phpfox::getParam('directory.default_view'));
            switch ($default_view) {
                case 'list':
                    $sViewType = 'listview';
                    break;
                case 'grid':
                    $sViewType = 'gridview';
                    break;
                case 'pinboard':
                    $sViewType = 'pinboardview';
                    break;
                case 'map':
                    $sViewType = 'mapview';
                    break;
            }
        }

        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        Phpfox::getService('directory.helper')->buildMenu();
        $sCondition = $this->search()->getConditions();
        $menu_display_page = 12;
        if((int)$this->request()->get('show') > 0){
            $menu_display_page = (int)$this->request()->get('show');
        }
        $this->template()->assign(array(
            'aProfileUser' => $this->_aProfileUser ,
            'corepath' => Phpfox::getParam('core.path'),
            'iCnt' => $this->search()->browse()->getCount(),
            'iPage' => $this->search()->getPage(),
            'aItems' => $aItems,
            'sSearchBlock' => _p('directory.search_business'),
            'bIsProfile' => $this->_bIsProfile,
            'bIsHomepage' => $bIsHomepage,
            'bIsInPages'=> $this->_checkIsInPageModule(),
            'sView' => $sView,
            'sViewType' => $sViewType,
            'menu_display_page' => $menu_display_page,
            'sNoimageUrl' => Phpfox::getLib('template')->getStyle('image', 'noimage/' . 'profile_50.png'),
            'sCondition' => base64_encode(json_encode($sCondition)),
            'apiKey' => Phpfox::getParam('core.google_api_key'),
        ))
            ->setHeader('cache', array(
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'pager.css' => 'style_css',
                'jquery.rating.css' => 'style_css',
                'feed.js' => 'module_feed',
                'country.js' => 'module_core',
                'masonry/masonry.pkgd.min.js' => 'app_ync-core'
            ))
            ->setPhrase(array(
                'directory.categories_selected',
                'directory.select_category'
            ));

        $this->setParam('bInHomepageFr', $bIsHomepage);
        $this->setParam('page', $this->search()->getPage());
        $this->setParam('aItem', $aItems);
        $this->setParam('iCnt', $this->search()->browse()->getCount());
        $this->setParam('sViewType', $sViewType);

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

        // add support responsiveclean template
        if ( $this->template()->getThemeFolder() == 'ynresponsiveclean' ) {

        }
        $this->_setGlobalModeration($aModerateMenu);
    }
}
?>