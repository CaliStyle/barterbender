<?php


defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Callback extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {

    }

    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        switch ($aRow['product_creating_type']) {
            case 'auction':
                $aInfo['item_link'] = Phpfox::getLib('url')->permalink('auction.detail', $aRow['item_id'], $aRow['item_title']);
                $aInfo['item_name'] = _p('auction');

                $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aRow['item_photo_server'],
                        'file' => $aRow['item_photo'],
                        'path' => 'core.url_pic',
                        'suffix' => '_200',
                        'max_width' => '120',
                        'max_height' => '120'
                    )
                );
                break;

            default:
                # code...
                break;
        }


        return $aInfo;
    }

    public function onDeleteUser($iUser)
    {
        $aItems = $this->database()
            ->select('product_id')
            ->from(Phpfox::getT('ecommerce_product'))
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        foreach ($aItems as $aItem) {
            Phpfox::getService('ecommerce.process')->delete($aItem['product_id']);
        }
    }

    public function moneyRequestApiCallBack($iRequestId)
    {
        //get money request
        $request = Phpfox::getService('ecommerce.request')->get($iRequestId);
        if ($request) {
            Phpfox::getService('ecommerce.request.process')->approve($iRequestId);
        }

    }

    public function paymentApiCallback($aParams)
    {

        Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
        Phpfox::log('Attempting to retrieve purchase from the database');

        $iOrderIds = explode("_", $aParams['item_number']);

        if (empty($iOrderIds)) {
            return false;
        }

        //check if money request callback
        if ($iOrderIds[0] == 'request') {
            $this->moneyRequestApiCallBack($iOrderIds[1]);
            return true;
        }
        //end checking

        if ($aParams['status'] == 'completed') {
            $iTotalPrice = 0;
            if (count($iOrderIds)) {
                foreach ($iOrderIds as $key => $iOrderId) {
                    $aOrder = Phpfox::getService('ecommerce.order')->geQuickOrderById($iOrderId);
                    if (!empty($aOrder)) {
                        $iTotalPrice += $aOrder['order_total_price'];
                    }
                }
            }
            if ($aParams['total_paid'] == $iTotalPrice) {
                if (Phpfox::isModule('ynsocialstore')) {
                    $this->cache()->remove('ynsocialstore_product_buyingactivity_bought_by_friends', 'substr');
                }
                Phpfox::log('Paid correct price');
                $iBuyerId = 0;
                /*update status order*/
                if (count($iOrderIds)) {
                    foreach ($iOrderIds as $key => $iOrderId) {
                        $aUpdate = array(
                            'order_payment_status' => 'completed',
                            'order_payment_method' => $aParams['gateway'],
                            'order_purchase_datetime' => PHPFOX_TIME,
                        );
                        Phpfox::getService('ecommerce.order.process')->updateOrder($iOrderId, $aUpdate);

                        $aOrder = Phpfox::getService('ecommerce.order')->getOrderById($iOrderId);

                        $iBuyerId = $aOrder['user_id'];

                        /*update amount money for seller*/
                        $aCreditMoney = Phpfox::getService('ecommerce.creditmoney')->getCreditMoney($aOrder['seller_id']);

                        $aMoneyRequestForSeller = array(
                            'creditmoney_total_amount' => $aCreditMoney['creditmoney_total_amount'] + $aOrder['order_total_price'] - $aOrder['order_commission_value'],
                            'creditmoney_remain_amount' => $aCreditMoney['creditmoney_remain_amount'] + $aOrder['order_total_price'] - $aOrder['order_commission_value'],
                            'creditmoney_creation_datetime' => PHPFOX_TIME,
                            'creditmoney_modification_datetime' => PHPFOX_TIME,
                            'creditmoney_description' => '',
                        );

                        Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_creditmoney'), $aMoneyRequestForSeller, 'creditmoney_user_id = ' . $aOrder['seller_id']);

                        /*update amount money for seller*/

                        /*prepare email*/
                        $aUserSeller = Phpfox::getService('user')->get($aOrder['seller_id']);
                        $aUserBuyer = Phpfox::getService('user')->get($aOrder['user_id']);

                        $sMessageSellerItemSold = '';
                        $sMessageBuyerItemSold = '';
                        $sOrderModule = 'auction';
                        $aStoreUpdated = [];
                        //Using in affiliate
                        $sCurrencyId = 'USD';
                        if (isset($aOrder['product']) && count($aOrder['product'])) {
                            foreach ($aOrder['product'] as $key => $aOrderProduct) {
                                $aProductItem = Phpfox::getService('ecommerce')->getQuickProductById($aOrderProduct['orderproduct_product_id']);
                                if (empty($aProductItem)) {
                                    continue;
                                }
                                $sCurrencyId = $aOrder['order_currency'];
                                /*compose email*/
                                $sMessageSellerItemSold .= _p('product_name_title_sold_price_symbol_currency_amount_buyer_buyer',
                                        array(
                                            'title' => $aProductItem['name'],
                                            'buyer' => $aUserBuyer['full_name'],
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOrder['order_currency']),
                                            'amount' => $aOrderProduct['orderproduct_product_price'],
                                        )
                                    ) . '<br><br>';

                                $sMessageBuyerItemSold .= _p('product_name_title_sold_price_symbol_currency_amount_by_seller',
                                        array(
                                            'title' => $aProductItem['name'],
                                            'seller' => $aUserSeller['full_name'],
                                            'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOrder['order_currency']),
                                            'amount' => $aOrderProduct['orderproduct_product_price'],
                                        )
                                    ) . '<br><br>';

                                /*update quantity product*/
                                if($aProductItem['product_creating_type'] == 'ynsocialstore_product')
                                {
                                    $sOrderModule = 'ynsocialstore';
                                    $aProductStore = Phpfox::getService('ynsocialstore.product')->getProductSomeInfo($aOrderProduct['orderproduct_product_id']);
                                    if((int)$aProductItem['product_quantity_main'] > 0 && $aProductStore['product_type'] == 'physical')
                                    {
                                        $iRestQuantity = $aProductItem['product_quantity'] - $aOrderProduct['orderproduct_product_quantity'];
                                        if($iRestQuantity < 0) $iRestQuantity = 0;
                                        Phpfox::getService('ecommerce.process')->updateProductQuantity($aOrderProduct['orderproduct_product_id'], $iRestQuantity,'ynsocialstore_product');
                                        if($iRestQuantity == 0 && $aProductStore['auto_close'])
                                        {
                                            Phpfox::getService('ynsocialstore.product.process')->closeProduct((int)$aOrderProduct['orderproduct_product_id']);
                                        }
                                    }
                                    if((int)$aOrderProduct['orderproduct_attribute_id'] > 0)
                                    {
                                        $aAttribute = Phpfox::getService('ynsocialstore.product')->getElementAttribute($aOrderProduct['orderproduct_attribute_id']);
                                        if($aAttribute['quantity'] > 0)
                                        {
                                            $iRestQuantity = $aAttribute['remain'] - $aOrderProduct['orderproduct_product_quantity'];
                                            if($iRestQuantity < 0) $iRestQuantity = 0;
                                            Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_product_attribute'), ['remain' => $iRestQuantity] ,'attribute_id = '.(int)$aOrderProduct['orderproduct_attribute_id']);
                                        }
                                    }
                                }
                                else{
                                    $iRestQuantity = $aProductItem['product_quantity'] - $aOrderProduct['orderproduct_product_quantity'];
                                    Phpfox::getService('ecommerce.process')->updateProductQuantity($aOrderProduct['orderproduct_product_id'], $iRestQuantity);
                                }
                                /*update my cart*/
                                $aCart = Phpfox::getService('ecommerce.cart')->get($iBuyerId);
                                if (!empty($aCart)) {
                                    /*update status of product offer*/
                                    $aCartProduct = Phpfox::getService('ecommerce.cart')->getProductsByProductId($iBuyerId, $aOrderProduct['orderproduct_product_id'], 'offer');
                                    if (!empty($aCartProduct) && $aCartProduct['cartproduct_module'] == 'auction' && $aCartProduct['cartproduct_type'] == 'offer') {

                                        $this->database()->update(Phpfox::getT('ecommerce_auction_offer'), array(
                                            'auctionoffer_status' => 4,
                                        ),
                                            'auctionoffer_user_id = ' . (int)$iBuyerId . ' AND auctionoffer_product_id = ' . (int)$aOrderProduct['orderproduct_product_id'] . ' AND  auctionoffer_status = 1');

                                    }

                                    if (!empty($aCartProduct) && $aCartProduct['cartproduct_module'] == 'auction' && $aCartProduct['cartproduct_type'] == 'bid') {

                                        $this->database()->update(Phpfox::getT('ecommerce_auction_bid'), array(
                                            'auctionbid_status' => 1,
                                        ),
                                            'auctionbid_user_id = ' . (int)$iBuyerId . ' AND auctionbid_product_id = ' . (int)$aOrderProduct['orderproduct_product_id']);

                                    }

                                    $this->database()->update(Phpfox::getT('ecommerce_cart_product'), array(
                                        'cartproduct_payment_status' => 'completed',
                                    ),
                                        'cartproduct_product_id = ' . (int)$aOrderProduct['orderproduct_product_id'] . ' AND cartproduct_cart_id = ' . (int)$aCart['cart_id'].' AND cartproduct_attribute_id = '.$aOrderProduct['orderproduct_attribute_id']);

                                }

                                /*update total order*/
                                Phpfox::getService('ecommerce.order.process')->updateTotalOrderProduct($aOrderProduct['orderproduct_product_id']);
                                if($sOrderModule == 'ynsocialstore')
                                {
                                    if(!in_array($aProductItem['item_id'],$aStoreUpdated)){
                                        Phpfox::getService('ynsocialstore.process')->updateTotalOrder($aProductItem['item_id']);
                                        $aStoreUpdated[] = $aProductItem['item_id'];
                                    }

                                }
                            }
                        }

                        /*send email and notification to buyer and seller*/
                        $sLinkSeller = Phpfox::permalink('ecommerce.manage-orders', null, null);
                        $sLinkBuyer = Phpfox::permalink('ecommerce.my-orders', null, null);

                        /*send to seller*/
                        $iReceiveId = $aOrder['seller_id'];
                        $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                        $email = $aUser['email'];
                        $iProductId = 1;
                        $aExtraData = array();
                        $aExtraData['lists_item'] = $sMessageSellerItemSold;
                        $aExtraData['url'] = $sLinkSeller;

                        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($sOrderModule, 'congratulations_your_item_sold', $language_id, $iReceiveId, $iProductId, $aExtraData);
                        Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

                        Phpfox::getService('notification.process')->add('ecommerce_soldseller', $aOrder['order_id'], $aOrder['seller_id'], $aOrder['seller_id']);

                        /*send to buyer*/
                        $iReceiveId = $aOrder['user_id'];
                        $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                        $email = $aUser['email'];
                        $iProductId = 1;
                        $aExtraData = array();

                        $aExtraData['lists_item'] = $sMessageBuyerItemSold;
                        $aExtraData['url'] = $sLinkBuyer;

                        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($sOrderModule, 'you_ve_bought_the_item', $language_id, $iReceiveId, $iProductId, $aExtraData);
                        Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);

                        Phpfox::getService('notification.process')->add('ecommerce_soldbuyer', $aOrder['order_id'], $aOrder['user_id'], $aOrder['user_id']);

                    }
                }
                (($sPlugin = Phpfox_Plugin::get('ecommerce.service_callback_payment_buy_item__end')) ? eval($sPlugin) : false);
            } else {
                Phpfox::log('Paid incorrect price');

                return false;
            }
        }


        Phpfox::log('Handling complete');
    }

    public function paymentApiCallbackAdaptivePayment($aParams)
    {

        Phpfox::log('Module callback recieved: ' . var_export($aParams, true));
        Phpfox::log('Attempting to retrieve purchase from the database');

        if ($aParams['status'] == 'COMPLETED') {
            $iTotalPrice = 0;


            Phpfox::log('Paid correct price');
            $iBuyerId = 0;

            $aPaymentInfo = $aParams['paymentInfoList']['paymentInfo'];

            /*update status order*/
            if (count($aPaymentInfo)) {
                foreach ($aPaymentInfo as $key => $aPayment) {
                    $aOrderIds = array();
                    if (strpos($aPayment['receiver']['invoiceId'], "-")) {
                        $aOrderIds = explode("-", $aPayment['receiver']['invoiceId']);
                    } else {
                        $aOrderIds = array($aPayment['receiver']['invoiceId']);
                    }

                    if (count($aOrderIds)) {
                        foreach ($aOrderIds as $keyOrderId => $iOrderId) {

                            $iOrderId = ltrim($iOrderId, '#');
                            if (!$iOrderId) {
                                continue;
                            }

                            $sStatus = null;
                            if (!isset($aPayment['transactionStatus'])) {
                                $sStatus = 'canceled';
                            }
                            switch ($aPayment['transactionStatus']) {
                                case 'COMPLETED':
                                    $sStatus = 'completed';
                                    break;
                                case 'PENDING':
                                    $sStatus = 'pending';
                                    break;
                                case 'REFUNDED':
                                case 'REVERSED':
                                    $sStatus = 'canceled';
                                    break;
                            }

                            $aUpdate = array(
                                'order_payment_status' => $sStatus,
                                'order_payment_method' => 'paypal',
                                'order_purchase_datetime' => PHPFOX_TIME,
                            );

                            Phpfox::getService('ecommerce.order.process')->updateOrder($iOrderId, $aUpdate);

                            $aOrder = Phpfox::getService('ecommerce.order')->getOrderById($iOrderId);

                            if (!isset($aOrder['user_id'])) {
                                continue;
                            }
                            $iBuyerId = $aOrder['user_id'];

                            /*prepare email*/
                            $aUserSeller = Phpfox::getService('user')->get($aOrder['seller_id']);
                            $aUserBuyer = Phpfox::getService('user')->get($aOrder['user_id']);

                            $sMessageSellerItemSold = '';
                            $sMessageBuyerItemSold = '';
                            $sOrderModule = 'auction';
                            $aStoreUpdated = [];
                            if (isset($aOrder['product']) && count($aOrder['product'])) {
                                foreach ($aOrder['product'] as $key => $aOrderProduct) {
                                    $aProductItem = Phpfox::getService('ecommerce')->getQuickProductById($aOrderProduct['orderproduct_product_id']);
                                    if (empty($aProductItem)) {
                                        continue;
                                    }

                                    /*compose email*/
                                    $sMessageSellerItemSold .= _p('product_name_title_sold_price_symbol_currency_amount_buyer_buyer',
                                            array(
                                                'title' => $aProductItem['name'],
                                                'buyer' => $aUserBuyer['full_name'],
                                                'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOrder['order_currency']),
                                                'amount' => $aOrderProduct['orderproduct_product_price'],
                                            )
                                        ) . '<br><br>';

                                    $sMessageBuyerItemSold .= _p('product_name_title_sold_price_symbol_currency_amount_by_seller',
                                            array(
                                                'title' => $aProductItem['name'],
                                                'seller' => $aUserSeller['full_name'],
                                                'symbol_currency' => Phpfox::getService('core.currency')->getSymbol($aOrder['order_currency']),
                                                'amount' => $aOrderProduct['orderproduct_product_price'],
                                            )
                                        ) . '<br><br>';

                                    /*update quantity product*/
                                    if($aProductItem['product_creating_type'] == 'ynsocialstore_product')
                                    {
                                        $sOrderModule = 'ynsocialstore';
                                        $aProductStore = Phpfox::getService('ynsocialstore.product')->getProductSomeInfo($aOrderProduct['orderproduct_product_id']);
                                        if((int)$aProductItem['product_quantity_main'] > 0 && $aProductStore['product_type'] == 'physical')
                                        {
                                            $iRestQuantity = $aProductItem['product_quantity'] - $aOrderProduct['orderproduct_product_quantity'];
                                            if($iRestQuantity < 0) $iRestQuantity = 0;
                                            Phpfox::getService('ecommerce.process')->updateProductQuantity($aOrderProduct['orderproduct_product_id'], $iRestQuantity,'ynsocialstore_product');
                                            if($iRestQuantity == 0 && $aProductStore['auto_close'])
                                            {
                                                Phpfox::getService('ynsocialstore.product.process')->closeProduct((int)$aOrderProduct['orderproduct_product_id'],true);
                                            }
                                        }
                                        if((int)$aOrderProduct['orderproduct_attribute_id'] > 0)
                                        {
                                            $aAttribute = Phpfox::getService('ynsocialstore.product')->getElementAttribute($aOrderProduct['orderproduct_attribute_id'],true);
                                            if($aAttribute['quantity'] > 0)
                                            {
                                                $iRestQuantity = $aAttribute['remain'] - $aOrderProduct['orderproduct_product_quantity'];
                                                if($iRestQuantity < 0) $iRestQuantity = 0;
                                                Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_product_attribute'), ['remain' => $iRestQuantity] ,'attribute_id = '.(int)$aOrderProduct['orderproduct_attribute_id']);
                                            }
                                        }
                                    }
                                    else{
                                        $iRestQuantity = $aProductItem['product_quantity'] - $aOrderProduct['orderproduct_product_quantity'];
                                        Phpfox::getService('ecommerce.process')->updateProductQuantity($aOrderProduct['orderproduct_product_id'], $iRestQuantity);
                                    }

                                    /*update my cart*/
                                    $aCart = Phpfox::getService('ecommerce.cart')->get($iBuyerId);
                                    if (!empty($aCart)) {
                                        /*update status of product offer*/
                                        $aCartProduct = Phpfox::getService('ecommerce.cart')->getProductsByProductId($iBuyerId, $aOrderProduct['orderproduct_product_id'], 'offer');
                                        if (!empty($aCartProduct) && $aCartProduct['cartproduct_module'] == 'auction' && $aCartProduct['cartproduct_type'] == 'offer') {

                                            $this->database()->update(Phpfox::getT('ecommerce_auction_offer'), array(
                                                'auctionoffer_status' => 4,
                                            ),
                                                'auctionoffer_user_id = ' . (int)$iBuyerId . ' AND auctionoffer_product_id = ' . (int)$aOrderProduct['orderproduct_product_id'] . ' AND  auctionoffer_status = 1');

                                        }

                                        /*update status of product bid*/
                                        if (!empty($aCartProduct) && $aCartProduct['cartproduct_module'] == 'auction' && $aCartProduct['cartproduct_type'] == 'bid') {

                                            $this->database()->update(Phpfox::getT('ecommerce_auction_bid'), array(
                                                'auctionbid_status' => 1,
                                            ),
                                                'auctionbid_user_id = ' . (int)$iBuyerId . ' AND auctionbid_product_id = ' . (int)$aOrderProduct['orderproduct_product_id']);

                                        }

                                        $this->database()->update(Phpfox::getT('ecommerce_cart_product'), array(
                                            'cartproduct_payment_status' => 'completed',
                                        ),
                                            'cartproduct_product_id = ' . (int)$aOrderProduct['orderproduct_product_id'] . ' AND cartproduct_cart_id = ' . (int)$aCart['cart_id']);

                                    }

                                    /*update total order*/
                                    Phpfox::getService('ecommerce.order.process')->updateTotalOrderProduct($aOrderProduct['orderproduct_product_id']);
                                    if($sOrderModule == 'ynsocialstore')
                                    {
                                        if(!in_array($aProductItem['item_id'],$aStoreUpdated)){
                                            Phpfox::getService('ynsocialstore.process')->updateTotalOrder($aProductItem['item_id']);
                                            $aStoreUpdated[] = $aProductItem['item_id'];
                                        }
                                    }
                                }
                            }

                            /*send email and notification to buyer and seller*/
                            $sLinkSeller = Phpfox::permalink('ecommerce.manage-orders', null, null);
                            $sLinkBuyer = Phpfox::permalink('ecommerce.my-orders', null, null);

                            /*send to seller*/
                            $iReceiveId = $aOrder['seller_id'];
                            $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                            $email = $aUser['email'];
                            $iProductId = 1;
                            $aExtraData = array();
                            $aExtraData['lists_item'] = $sMessageSellerItemSold;
                            $aExtraData['url'] = $sLinkSeller;

                            $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($sOrderModule, 'congratulations_your_item_sold', $language_id, $iReceiveId, $iProductId, $aExtraData);
                            Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
                            Phpfox::getService('notification.process')->add('ecommerce_soldseller', $aOrder['order_id'], $aOrder['seller_id'], $aOrder['seller_id']);

                            /*send to buyer*/
                            $iReceiveId = $aOrder['user_id'];
                            $aUser = Phpfox::getService('user')->getUser($iReceiveId);
                            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
                            $email = $aUser['email'];
                            $iProductId = 1;
                            $aExtraData = array();

                            $aExtraData['lists_item'] = $sMessageBuyerItemSold;
                            $aExtraData['url'] = $sLinkBuyer;

                            $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($sOrderModule, 'you_ve_bought_the_item', $language_id, $iReceiveId, $iProductId, $aExtraData);
                            Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
                            Phpfox::getService('notification.process')->add('ecommerce_soldbuyer', $aOrder['order_id'], $aOrder['user_id'], $aOrder['user_id']);
                        }
                    }

                }
            }
        }

        Phpfox::log('Handling complete');
    }

    public function getNotificationSoldbuyer($aNotification)
    {
        $aRow = $this->database()
            ->select('eo.order_id')
            ->from(Phpfox::getT('ecommerce_order'), 'eo')
            ->where('eo.order_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['order_id'])) {
            return false;
        }

        $sPhrase = _p('you_ve_bought_the_order_order_id', array('order_id' => $aRow['order_id']));

        return array(
            'link' => Phpfox::getLib('url')->permalink('ecommerce.my-orders', null, null),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationSoldseller($aNotification)
    {
        $aRow = $this->database()
            ->select('eo.order_id,eo.order_item_count,eo.module_id')
            ->from(Phpfox::getT('ecommerce_order'), 'eo')
            ->where('eo.order_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['order_id'])) {
            return false;
        }

        $sPhrase = _p('congratulations_item_count_your_items_sold', array('item_count' => $aRow['order_item_count']));
        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['module_id'].'.manage-orders', null, null),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationRequest($aNotification)
    {
        $aRow = $this->database()
            ->select('ec.creditmoneyrequest_id,ec.user_id,ec.creditmoneyrequest_amount,ec.creditmoneyrequest_status')
            ->from(Phpfox::getT('ecommerce_creditmoneyrequest'), 'ec')
            ->where('ec.creditmoneyrequest_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['creditmoneyrequest_id'])) {
            return false;
        }
        if ($aRow['creditmoneyrequest_status'] == 'approved') {
            $sPhrase = _p('your_request_points_were_approved');
        } else if ($aRow['creditmoneyrequest_status'] == 'rejected') {
            $sPhrase = _p('your_request_points_were_declined');
        } else {
            return false;
        }
        return array(
            'link' => Phpfox::getLib('url')->permalink('ecommerce.my-requests', null, null),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationStatusorder($aNotification)
    {
        $aRow = $this->database()
            ->select('eo.order_id, eo.order_item_count, eo.order_code, eo.module_id')
            ->from(Phpfox::getT('ecommerce_order'), 'eo')
            ->where('eo.order_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['order_id'])) {
            return false;
        }

        $sPhrase = _p('the_order_order_id_has_changed_its_status', array('order_id' => $aRow['order_code']));

        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['module_id'].'.my-orders', null, null),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getDashboardActivity()
    {
        return array();
    }

    public function updateCounterList()
    {
        $aList = array();

        $aList[] = array(
            'name' => _p('users_product_count'),
            'id' => 'ecommerce-total'
        );

        $aList[] = array(
            'name' => _p('update_users_activity_product_points'),
            'id' => 'ecommerce-activity'
        );

        return $aList;
    }

    public function getTotalItemCount($iUserId)
    {
        $result = array(
            'field' => 'total_ecommerce',
            'total' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ecommerce_product'))
                ->where('user_id = ' . (int)$iUserId . ' AND product_status IN (\'approved\',\'completed\',\'running\')')
                ->execute('getSlaveField')
        );
        return $result;
    }

    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'ecommerce-total') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(c.product_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ecommerce_product'), 'c', 'c.user_id = u.user_id AND c.product_status IN (\'approved\',\'completed\',\'running\')')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_field'), array('total_ecommerce' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        } elseif ($iId == 'ecommerce-activity') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('m.user_id, m.activity_ecommerce, m.activity_points, m.activity_total, COUNT(c.product_id) AS total_items')
                ->from(Phpfox::getT('user_activity'), 'm')
                ->leftJoin(Phpfox::getT('ecommerce_product'), 'c', 'c.user_id = m.user_id AND c.product_status IN ' . "('approved','completed','running')")
                ->group('m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_activity'), array(
                    'activity_points' => (($aRow['activity_total'] - ($aRow['activity_ecommerce'] * Phpfox::getUserParam('ecommerce.points_ecommerce'))) + ($aRow['total_items'] * Phpfox::getUserParam('ecommerce.points_ecommerce'))),
                    'activity_total' => (($aRow['activity_total'] - $aRow['activity_ecommerce']) + $aRow['total_items']),
                    'activity_ecommerce' => $aRow['total_items']
                ), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
    }


    public function getAjaxProfileController()
    {
        return 'ecommerce.index';
    }

    public function getProfileLink()
    {
        return 'profile.ecommerce';
    }

    public function getProfileMenu($aUser)
    {
    }

    public function getVideoDetails($aItem)
    {
        $aRow = Phpfox::getService('ecommerce')->getQuickProductById($aItem['item_id']);
        if (!isset($aRow['product_id'])) {
            return false;
        }

        $sLink = Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']);

        return array(
            'breadcrumb_title' => _p($aRow['product_creating_type'] . '.module_menu'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl(_p($aRow['product_creating_type'])),
            'module_id' => 'ecommerce',
            'item_id' => $aRow['product_id'],
            'title' => $aRow['name'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'videos/',
            'theater_mode' => _p('in_the_product_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
        );
    }

    public function uploadVideo($aVals)
    {
        return array(
            'module' => 'ecommerce',
            'item_id' => (is_array($aVals) && isset($aVals['callback_item_id']) ? $aVals['callback_item_id'] : (int)$aVals)
        );
    }

    public function convertVideo($aVideo)
    {
        return array(
            'module' => 'ecommerce',
            'item_id' => $aVideo['item_id'],
            'table_prefix' => 'ecommerce_'
        );
    }

    public function getPhotoDetails($aPhoto)
    {
        // Phpfox::getService('pages')->setIsInPage();
        $aRow = Phpfox::getService('ecommerce')->getQuickProductById($aPhoto['group_id']);

        if (!isset($aRow['product_id'])) {
            return false;
        }

        // Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']);

        return array(
            'breadcrumb_title' => _p($aRow['product_creating_type'] . '.module_menu'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl(_p($aRow['product_creating_type'])),
            'module_id' => 'ecommerce',
            'item_id' => $aRow['product_id'],
            'title' => $aRow['name'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'photos/',
            'theater_mode' => _p('in_the_product_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['name']))
        );
    }

    public function getPhotoCount($iProductId)
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('photo'))
            ->where("module_id = 'ecommerce' AND group_id = " . $iProductId)
            ->execute('getSlaveField');

        return ($iCnt > 0) ? $iCnt : 0;
    }

    public function getecommerceDetails($aItem)
    {
        return array();
    }

    public function getTagTypeproduct()
    {
        return 'product';
    }

    public function getTagCloud()
    {
        return array(
            'link' => 'ecommerce',
            'category' => 'product'
        );
    }

    public function getFeedDisplay($product_id)
    {
        return array(
            'module' => 'ecommerce',
            'table_prefix' => 'ecommerce_',
            'ajax_request' => 'ecommerce.addFeedComment',
            'item_id' => $product_id
        );
    }

    public function getAjaxCommentVar()
    {
        return;
    }

    public function getActivityFeedComment($aItem)
    {

        $aRow = $this->database()->select('fc.*, l.like_id AS is_liked, e.product_id, e.name, e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_feed_comment'), 'fc')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = fc.parent_user_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ecommerce_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('fc.feed_comment_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id'])) {
            return false;
        }

        $sLink = Phpfox::getLib('url')->permalink(array($aRow['product_creating_type'] . '.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['product_id'], $aRow['name']);

        $aReturn = array(
            'no_share' => true,
            'feed_status' => $aRow['content'],
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'misc/comment.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'ecommerce',
            'like_type_id' => 'ecommerce_comment',
            // http://www.phpfox.com/tracker/view/14689/
            'parent_user_id' => 0
        );
        return $aReturn;
    }

    public function deleteComment($iId)
    {
        $this->database()->updateCounter('ecommerce_product', 'total_comment', 'product_id', $iId, true);

    }

    public function addLikeComment($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, e.product_id, e.name, e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_feed_comment'), 'fc')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ecommerce_comment\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ecommerce_feed_comment', 'feed_comment_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::getLib('url')->permalink(array($aRow['product_creating_type'] . '.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['product_id'], $aRow['name']);
            $sItemLink = Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array('ecommerce.full_name_liked_a_comment_you_posted_on_the_product_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
                ->message(array('ecommerce.full_name_liked_your_comment_a_href_link_content_a_that_you_posted_on_the_product_a_href_item_link_title_a_to_view_this_product_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'content' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 50, '...'), 'item_link' => $sItemLink, 'title' => $aRow['name'])))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ecommerce_comment_like', $aRow['feed_comment_id'], $aRow['user_id']);
        }
    }

    public function deleteLikeComment($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ecommerce_comment\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ecommerce_feed_comment', 'feed_comment_id = ' . (int)$iItemId);
    }

    public function addPhoto($iId)
    {
        return array(
            'module' => 'ecommerce',
            'item_id' => $iId,
            'table_prefix' => 'ecommerce_'
        );
    }

    public function addLink($aVals)
    {
        return array(
            'module' => 'ecommerce',
            'item_id' => $aVals['callback_item_id'],
            'table_prefix' => 'ecommerce_'
        );
    }

    public function addLikeCheckinhere($iItemId, $bDoNotSendEmail = false)
    {
        $this->addLike($iItemId);
    }

    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('e.product_id, e.name, e.user_id,e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->where('e.product_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'ecommerce\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array('ecommerce.full_name_liked_your_product_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
                ->message(array('ecommerce.full_name_liked_your_product_a_href_link_title_a_to_view_this_product_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['name'])))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('ecommerce_like', $aRow['product_id'], $aRow['user_id']);
        }
    }

    public function deleteLikeCheckinhere($iItemId)
    {
        $this->deleteLike($iItemId);
    }

    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'ecommerce\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int)$iItemId);
    }

    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('e.product_id, e.name, e.product_creating_type, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('users_liked_gender_own_product_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_your_product_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_product_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function canShareItemOnFeed()
    {
    }

    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = e.user_id');
        }

        $sWhere = '';
        $sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'completed\' ) ';
        $aRow = $this->database()->select('u.user_id, ep.product_id, ep.module_id, ep.item_id, ep.product_id, ep.name, ep.product_creation_datetime, ep.logo_path as image_path, ep.server_id as image_server_id, ep.total_like, ep.total_comment, l.like_id AS is_liked')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(PHpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->leftJoin(Phpfox::getT('ecommerce_product_text'), 'et', 'et.product_id = e.product_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'ecommerce\' AND l.item_id = e.product_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('e.product_id = ' . (int)$aItem['item_id'] . $sWhere)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id'])) {
            return false;
        }

        if ($bIsChildItem) {
            $aItem = $aRow;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'ecommerce.view_browse_events'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'ecommerce.view_browse_events'))
        ) {
            return false;
        }

        $aReturn = array(
            'feed_title' => $aRow['name'],
            'feed_info' => _p('created_a_product'),
            'feed_link' => Phpfox::permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']),
            'feed_content' => $aRow['description_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/ecommerce.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'enable_like' => true,
            'like_type_id' => 'ecommerce',
            'total_comment' => $aRow['total_comment']
        );

        if (!empty($aRow['image_path'])) {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aRow['image_path'],
                    'ynecommerce_overridenoimage' => true,
                    'suffix' => ''
                )
            );

            $aReturn['feed_image_banner'] = $sImage;
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        (($sPlugin = Phpfox_Plugin::get('ecommerce.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        return $aReturn;
    }

    public function getFeedDetails($iItemId)
    {
        return array(
            'module' => 'ecommerce',
            'table_prefix' => 'ecommerce_',
            'item_id' => $iItemId
        );
    }

    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from(Phpfox::getT('ecommerce_feed_comment'))
            ->where('feed_comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        $aRow['parent_module_id'] = 'ecommerce';

        return $aRow;
    }

    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.product_id, e.name, e.product_creating_type, u.full_name, u.gender')
            ->from(Phpfox::getT('ecommerce_feed_comment'), 'fc')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('ecommerce_feed_comment', 'total_comment', 'feed_comment_id', $aRow['feed_comment_id']);
        }

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink(array($aRow['user_id'] . '.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['product_id'], $aRow['name']);
        $sItemLink = Phpfox::getLib('url')->permalink($aRow['user_id'] . '.detail', $aRow['product_id'], $aRow['name']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aRow['user_id'],
                'item_id' => $aRow['feed_comment_id'],
                'owner_subject' => _p('full_name_commented_on_a_comment_posted_on_the_product_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])),
                'owner_message' => _p('full_name_commented_on_one_of_your_comments_you_posted_on_the_product_a_href_item_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'ecommerce_comment_feed',
                'mass_id' => 'ecommerce',
                'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_product_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1))) : _p('full_name_commented_on_one_of_row_full_name_s_product_comments', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_own_comments_on_the_product_a_href_item_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)) : _p('full_name_commented_on_one_of_row_full_name_s_comments_on_the_product_a_href_item_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name'], 'item_link' => $sItemLink, 'title' => $aRow['name'], 'link' => $sLink)))
            )
        );
    }

    public function getNotificationComment_Feed($aNotification)
    {
        return $this->getCommentNotification($aNotification);
    }

    public function getCommentNotification($aNotification)
    {

        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.product_id, e.name, e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_comment_on_the_product_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_commented_on_gender_own_comment_on_product_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_one_of_your_comments_on_the_product_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_one_of_span_class_drop_data_user_row_full_name_s_span_comments_on_the_product_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink(array($aRow['product_creating_type'] . '.detail', 'comment-id' => $aRow['feed_comment_id']), $aRow['product_id']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment($aNotification)
    {

        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.product_id, e.name, e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_feed_comment'), 'fc')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!count($aRow)) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_product_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_commented_on_gender_own_product_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_your_product_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_product_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment_Like($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.product_id, e.name, e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!count($aRow)) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = '';
        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_product_title', array('users' => Phpfox::getService('notification')->getUsers($aNotification, true), 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_liked_gender_own_comment_on_the_product_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_one_of_your_comments_on_the_product_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_product_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getActions()
    {
        return array(
            'dislike' => array(
                'enabled' => true,
                'action_type_id' => 2, // 2 = dislike
                'phrase' => _p('like.dislike'),
                'phrase_in_past_tense' => 'disliked',
                'item_type_id' => 'ecommerce', // used to differentiate between photo albums and photos for example.
                'table' => 'ecommerce_product',
                'item_phrase' => _p('item_phrase'),
                'column_update' => 'total_dislike',
                'column_find' => 'product_id'
            )
        );
    }

    public function updateCommentText($aVals, $sText)
    {

    }

    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = array();
        $aCond[] = ' e.product_status IN ( \'running\',\'completed\',\'approved\') ';
        if ($iStartTime > 0) {
            $aCond[] = 'AND e.product_creating_type >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND e.product_creating_type <= \'' . $this->database()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->where($aCond)
            ->execute('getSlaveField');

        return array(
            'phrase' => 'ecommerce.products',
            'total' => $iCnt
        );
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $sWhere = '';
        $sWhere .= ' AND e.product_status IN ( \'running\',\'completed\',\'approved\')';

        return array(
            'phrase' => _p('products'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ecommerce_product'), 'e')
                ->where('e.product_creation_datetime >= ' . $iToday . $sWhere)
                ->execute('getSlaveField')
        );
    }

    /**
     *  Call back methods for report and comment on ecommerce product
     */
    public function getFeedRedirect($iId, $iChild = 0)
    {
        $aProduct = $this->database()->select('e.product_id, e.name, e.product_creating_type')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('dbus.product_id = ' . (int)$iId)
            ->execute('e');

        if (!isset($aProduct['product_id'])) {
            return false;
        }

        return Phpfox::permalink($aProduct['product_creating_type'] . '.detail', $aProduct['product_id'], $aProduct['name']);
    }

    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function getNotificationInvited($aNotification)
    {

        $aRow = $this->database()->select('ep.product_id, ep.name')
            ->from(Phpfox::getT('ecommerce_product'), 'ep')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ep.user_id')
            ->where('ep.product_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!$aRow) return false;

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('users_invited_you_to_the_product_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink($aRow['product_creating_type'] . '.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );
    }


}