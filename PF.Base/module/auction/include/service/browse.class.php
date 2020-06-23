<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Browse extends Phpfox_Service {

    private $_sCategory = null;
    private $_bIsSeen = false;

    public function query()
    {
        $this->database()->select('ept.description_parsed AS description, ');
        $this->database()->select('epa.auction_total_bid,epa.auction_latest_bid_price, epa.auction_item_reserve_price,');
        $sView = $this->request()->get('view');
        if (Phpfox::getParam('core.section_privacy_item_browsing') && 'pending' != $sView)
        {
            $this->database()->select('ec.category_id, ec.title AS category_title, ');
            $this->database()->innerJoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id');
            if ($this->request()->get('req2') == 'category')
            {
                $this->database()
                        ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                        ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.category_id = ' . $this->request()->getInt('req3'));
            }
            else
            {
                $this->database()
                        ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                        ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.parent_id = 0');
            }
        }
        else 
        {
            $this->database()->select('ec.category_id, ec.title AS category_title, ');
        }
        
        $this->database()->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id');

        if (Phpfox::isUser() && Phpfox::isModule('like'))
            
        {
            $this->database()
                    ->select('lik.like_id AS is_liked, ')
                    ->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'auction\' AND lik.item_id = ep.product_id AND lik.user_id = ' . Phpfox::getUserId());
        }
        
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
    	$this->database()->select('epa.auction_total_bid,epa.auction_latest_bid_price, epa.auction_item_reserve_price,');
        $this->database()->innerJoin(Phpfox::getT('user'), 'userDelete', 'userDelete.user_id = ep.user_id');

        if ($this->request()->get('req2') == 'category')
        {
            $this->database()
                    ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                    ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.category_id = ' . $this->request()->getInt('req3'));
        }
        else
        {
            $this->database()
                    ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ep.product_id AND ecd.product_type = \'auction\'')
                    ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id');
            $this->database()->group('auction_id');
        }
        
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = ep.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        $sView = $this->request()->get('view');

        if (Phpfox::getParam('core.section_privacy_item_browsing') && $sView != 'pending')
        {
            if ($this->search()->isSearch())
            {
                $this->database()->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id');
            }
        }
        else
        {
            if ($bIsCount && $this->search()->isSearch())
            {
                $this->database()->leftJoin(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ep.product_id');
            }
        }

        if ($this->request()->get('req2') == 'tag')
        {
            $this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = ep.product_id AND tag.category_id = \'auction\'');
        }
        $this->database()->innerJoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id');

        switch ($sView) {
            case 'bidden-by-my-friends':
                // Do nothing.
                break;

            case 'won-by-my-friends':
                $this->database()
                    ->join(Phpfox::getT('user'), 'filter_u', 'filter_u.user_id = epa.auction_latest_bidder')
					->join(Phpfox::getT('friend'), 'filter_f', 'filter_f.user_id = epa.auction_latest_bidder AND filter_f.friend_user_id = ' . Phpfox::getUserId());
                break;
            
            case 'my-watch-list':
                $this->database()->join(Phpfox::getT('ecommerce_watch'), 'w', 'w.product_id = ep.product_id AND w.user_id = ' . Phpfox::getUserId());
                break;
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
        /**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('auction.service_browse__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>
