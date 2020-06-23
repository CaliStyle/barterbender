<?php

defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Browse extends Phpfox_Service
{
	 /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynstore_store');
    }

    public function query()
    {
        $this->database()->select('DISTINCT slq.address, slq.location,');
        $lat = $this->search()->get('location_address_lat');
        $lng = $this->search()->get('location_address_lng');
        if ($this->search()->isSearch() && !empty($lat) && !empty($lng)) {
            $this->database()->join(Phpfox::getT('ynstore_store_location'), 'slq', 'slq.store_id = st.store_id');
        } else {
            $this->database()->leftJoin(Phpfox::getT('ynstore_store_location'), 'slq', 'slq.store_id = st.store_id');
        }
        $this->database()->group('st.store_id');
    }

	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        $lat = $this->search()->get('location_address_lat');
        $lng = $this->search()->get('location_address_lng');
        if ($this->search()->isSearch() && !empty($lat) && !empty($lng)) {
            $this->database()->join(Phpfox::getT('ynstore_store_location'), 'sl', 'sl.store_id = st.store_id');
        } else {
            $this->database()->leftJoin(Phpfox::getT('ynstore_store_location'), 'sl', 'sl.store_id = st.store_id');
        }

        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = st.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if ($this->request()->get('req3') == 'category')
        {
            $this->database()
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = st.store_id AND ecd.product_type = \'store\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.category_id = ' . $this->request()->getInt('req4'));
        }
        else
        {
            $this->database()
                ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = st.store_id AND ecd.product_type = \'store\'')
                ->join(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id');
        }

        if ($this->request()->get('view') != '') {
            $sView = $this->request()->get('view');
            switch ($sView) {
                case 'favorite':
                    $this->database()
                        ->join(Phpfox::getT('ynstore_store_favorite'), 'stf', 'stf.store_id = st.store_id');
                    break;
                case 'follow':
                    $this->database()
                        ->join(Phpfox::getT('ynstore_store_following'), 'stfl', 'stfl.store_id = st.store_id');
                    break;
            }
        }

        $this->database()->group('st.store_id');
    }
	public function processRows(&$aRows)
	{
        foreach ($aRows as $key => $aRow)
        {
            $aRows[$key] = Phpfox::getService('ynsocialstore')->retrieveMoreInfoFromStore($aRow);
        }
	}
}