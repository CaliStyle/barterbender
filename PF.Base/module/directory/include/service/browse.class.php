<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Service_Browse extends Phpfox_Service
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('directory_business');
    }

    public function query()
    {
        $this->database()->select('DISTINCT dbus.business_id, dbt.description_parsed AS description, ');

        $this->database()->leftJoin(Phpfox::getT('directory_business_text'), 'dbt', 'dbt.business_id = dbus.business_id');

        if (Phpfox::isUser() && Phpfox::isModule('like'))
        {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'directory\' AND lik.item_id = dbus.business_id AND lik.user_id = '.Phpfox::getUserId());
        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        $sLocation = $this->request()->get('locationaddress');
        $sLocationLat = floatval($this->request()->get('locationlat'));
        $sLocationLng = floatval($this->request()->get('locationlng'));
        $iCategory = intval($this->request()->get('category', $this->request()->getInt('req3')));
        $iRadius = floatval($this->request()->get('radius'));
        if (!$bIsCount && $sLocation && $sLocationLat && $sLocationLng)
        {
            $this->database()->leftJoin(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id');
            $this->database()->group('dbus.business_id');
        }
        if($this->request()->get('req2') == 'category' && $iCategory) {
            $this->database()->leftJoin(Phpfox::getT('directory_category_data'), 'dcd',
                'dcd.business_id = dbus.business_id');
        }

        if($bIsCount && ($this->search()->get('advsearch') || $this->request()->get('advsearch')) && $sLocation && $sLocationLat && $sLocationLng){
            $this->database()->leftJoin(Phpfox::getT('directory_business_location'), 'dbl', 'dbl.business_id = dbus.business_id');
        }

        $aCategories =  $this->request()->get('search');
        if (isset($aCategories['category'])) {
            $this->database()->leftJoin(Phpfox::getT('directory_category_data'), 'dcd', 'dcd.business_id = dbus.business_id');
        }

        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = dbus.user_id AND friends.friend_user_id = '.Phpfox::getUserId());
        }

        if ($this->request()->get('view') && $this->request()->get('view') == 'myfavoritebusinesses')
        {
            $this->database()->join(Phpfox::getT('directory_favorite'), 'dfav', 'dfav.business_id = dbus.business_id');
        }

        if ($this->request()->get('view') && $this->request()->get('view') == 'myfollowingbusinesses')
        {
            $this->database()->join(Phpfox::getT('directory_follow'), 'dfo', 'dfo.business_id = dbus.business_id');
        }

        if ($this->request()->get('req2') == 'tag')
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = dbus.business_id AND tag.category_id = \'business\'');    
        }
    }

    public function processRows(&$aRows) {
        $oCategoryService = Phpfox::getService('directory.category');
        // Add main category info
        foreach($aRows as $key=>$aRow) {
            $aCategory = $oCategoryService->getMainCategoryByBusinessId($aRow['business_id']);
            $aRows[$key]['category_id'] = isset($aCategory['category_id']) ? $aCategory['category_id'] : 0;
            $aRows[$key]['category_title'] = isset($aCategory['title']) ? $aCategory['title'] : '';
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {

    }
}

?>
