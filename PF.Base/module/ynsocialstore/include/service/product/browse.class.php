<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/28/16
 * Time: 10:44 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Product_Browse extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ecommerce_product');
    }

    public function query()
    {
        $this->database()->select('ept.description_parsed AS short_description, ecp.privacy, ecp.user_id,')->join(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ecp.product_id');
        $this->database()->select('eps.*, (ecp.product_price - eps.discount_price) as discount_display, ')->leftJoin(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'ecp.product_id = eps.product_id');
        $this->database()->select('uom.title as uom_title, ')->leftJoin(Phpfox::getT('ecommerce_uom'), 'uom', 'uom.uom_id = ecp.uom_id');

        if ($this->search()->getSort() == 'total_orders DESC')
        {
            switch ($this->request()->get('when'))
            {
                case 'this-month':
                    $iStart = Phpfox::getLib('date')->convertToGmt(strtotime('first day of this month', PHPFOX_TIME));
                    $iEnd = Phpfox::getLib('date')->convertToGmt(strtotime('last day of this month', PHPFOX_TIME));
                    break;
                case 'this-week':
                    $iStart = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
                    $iEnd = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());
                    break;
                case 'today':
                    $iStart = Phpfox::getLib('date')->convertToGmt(strtotime('today', PHPFOX_TIME));
                    $iEnd = Phpfox::getLib('date')->convertToGmt(strtotime('tomorrow', PHPFOX_TIME) - 1);
                    break;
                default:
                    $iStart = 0;
                    $iEnd = strtotime('tomorrow', PHPFOX_TIME) - 1;
            }

            $this->database()->select('COUNT(eop.orderproduct_order_id) as total_purchased, ')
                ->join(Phpfox::getT('ecommerce_order_product'),'eop','ecp.product_id = eop.orderproduct_product_id')
                ->join(Phpfox::getT('ecommerce_order'),'eo','eo.order_id = eop.orderproduct_order_id AND eop.orderproduct_module like \'ynsocialstore\' AND eo.order_payment_status = \'completed\'')
                ->where('eo.order_creation_datetime >='.$iStart.' AND eo.order_creation_datetime <='.$iEnd)
                ->order('total_orders');
        }

        $this->database()->group('ecp.product_id');
    }
    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {

        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = ecp.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }
        if (Phpfox::isModule('friend'))
        {
            $this->database()->select('fr.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'fr', "fr.user_id = ecp.user_id AND fr.friend_user_id = " . Phpfox::getUserId());
        }

        if ($this->request()->get('req3') == 'category')
        {
            $this->database()
                ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ecp.product_id AND ecd.product_type = \'ynsocialstore_product\'')
                ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id AND ec.category_id = ' . $this->request()->getInt('req4'));
        }
        else
        {
            $this->database()
                ->innerJoin(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ecd.product_id = ecp.product_id AND ecd.product_type = \'ynsocialstore_product\'')
                ->innerJoin(Phpfox::getT('ecommerce_category'), 'ec', 'ec.category_id = ecd.category_id');
        }

        $this->database()->join(Phpfox::getT('ecommerce_product_text'), 'ept', 'ept.product_id = ecp.product_id');

        $this->database()->select('st.store_id, st.name as store_name, st.privacy as store_privacy,')->join(Phpfox::getT('ynstore_store'), 'st', 'ecp.item_id = st.store_id');

        if ($this->_isInDetailPage())
        {
            $this->database()->join(Phpfox::getT('ecommerce_product_ynstore'), 'eps', 'ecp.product_id = eps.product_id');
        }

        if ($this->search()->getSort() == 'total_orders DESC')
        {
            $this->database()
                ->join(Phpfox::getT('ecommerce_order_product'),'eop','ecp.product_id = eop.orderproduct_product_id')
                ->join(Phpfox::getT('ecommerce_order'),'eo','eo.order_id = eop.orderproduct_order_id AND eop.orderproduct_module like \'ynsocialstore\' AND eo.order_payment_status = \'completed\'');
        }

        $this->database()->group('ecp.product_id');
    }
    
    public function processRows(&$aRows)
    {
        if(count($aRows)) {
            foreach ($aRows as $key => $aItem)
            {
                Phpfox::getService('ynsocialstore.product')->retrieveMoreInfoForProduct($aItem);
                Phpfox::getService('ynsocialstore.product')->retrievePermission($aItem);
                $aItem['product_name'] = $aItem['name'];
                $aRows[$key] = $aItem;
            }

            if ($this->request()->get('view') == 'friendbuy')
            {
                foreach ($aRows as $ikey => $aItem)
                {
                    list($iTmp, $aTmp) = Phpfox::getService('ynsocialstore.product')->getFriendBoughtThisProduct($aItem['product_id'], 5);
                    $aRows[$ikey]['friends_list']['iMore'] = $iTmp - 5;
                    $aRows[$ikey]['friends_list']['top_friends'] = $aTmp;
                }
            }
        }
    }

    private function _isInDetailPage()
    {
        if ($this->request()->getInt('req3') > 0 && $this->request()->get('req2') == 'store') {
            return true;
        }

        return false;
    }
}