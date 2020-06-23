<?php

defined('PHPFOX') or exit('NO DICE!');


class Coupon_Service_Browse extends Phpfox_Service
{

    private $_sCategory = null;

    private $_bIsSeen = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon');
    }

    public function query()
    {
        $this->database()->select('ct.description_parsed AS description, ');

        $this->database()->join(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id');

        if (Phpfox::isUser() && Phpfox::isModule('like'))
        {
            $this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'coupon\' AND lik.item_id = c.coupon_id AND lik.user_id = '.Phpfox::getUserId());
        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        $this->database()->innerJoin(Phpfox::getT('coupon_category_data'), 'ccd', 'ccd.coupon_id = c.coupon_id');
        if (!$bIsCount)
        {
            $this->database()->group('c.coupon_id');
        }

        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = c.user_id AND friends.friend_user_id = '.Phpfox::getUserId());
        }

        if (Phpfox::getParam('core.section_privacy_item_browsing'))
        {
            if ($this->search()->isSearch() && !in_array($this->request()->get('view'), ['my', 'pending']))
            {
                $this->database()->join(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id');
            }
        }
        else
        {
            if ($bIsCount && $this->search()->isSearch() && $this->request()->get('view') != 'my')
            {
                $this->database()->join(Phpfox::getT('coupon_text'), 'ct', 'ct.coupon_id = c.coupon_id');
            }
        }

        if ($this->request()->get('view') && $this->request()->get('view') == 'my_claims')
        {
            $this->database()->join(Phpfox::getT('coupon_claim'), 'cc', 'cc.coupon_id = c.coupon_id');
        }

        if ($this->request()->get('view') && $this->request()->get('view') == 'favorite')
        {
            $this->database()->join(Phpfox::getT('coupon_favorite'), 'cf', 'cf.coupon_id = c.coupon_id');
        }

        if ($this->request()->get('view') && $this->request()->get('view') == 'following')
        {
            $this->database()->join(Phpfox::getT('coupon_follow'), 'fo', 'fo.coupon_id = c.coupon_id');
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
        if ($sPlugin = Phpfox_Plugin::get('coupon.service_browse__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method '.__class__.'::'.$sMethod.'()', E_USER_ERROR);
    }
}

?>
