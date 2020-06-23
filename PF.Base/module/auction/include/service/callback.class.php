<?php


defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Callback extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{

	}
		public function globalUnionSearch($sSearch)
	{
		$sWhere = '';
		$sWhere .= ' and item.product_creating_type = \'auction\' and item.product_status IN ( \'running\',\'completed\',\'approved\') ';

		$this->database()->select('item.product_id AS item_id, item.name AS item_title, item.product_creation_datetime AS item_time_stamp, item.user_id AS item_user_id,  item.product_creating_type AS item_type_id, item.logo_path AS item_photo, item.server_id AS item_photo_server')
			->from(Phpfox::getT('ecommerce_product'), 'item')
			->where(' 1=1 ' . $sWhere . ' AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.name', $sSearch))
			->union();
	}

	public function getSearchInfo($aRow)
	{
		$aInfo = array();

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

		
        
		return $aInfo;
	}

	public function getSearchTitleInfo()
	{
		return array(
			'name' => _p('auction')
		);
	}
	
	public function onDeleteUser($iUser)
	{
		$aItems = $this->database()
			->select('product_id')
			->from(Phpfox::getT('ecommerce_product'))
			->where('user_id = ' . (int)$iUser)
			->execute('getSlaveRows');

		if(count($aItems)){
				
			foreach ($aItems as $aItem)
			{
				Phpfox::getService('ecommerce.process')->delete($aItem['product_id']);
			}		
		}
	}

	public function paymentApiCallback($aParams){

		Phpfox::log('Module callback recieved: ' . var_export($aParams, true));	
		Phpfox::log('Attempting to retrieve purchase from the database');		
		

		$aInvoice = Phpfox::getService('ecommerce')->getInvoice($aParams['item_number']);
		

		if ($aInvoice === false)
		{
			Phpfox::log('Not a valid invoice');
			
			return false;
		}


		$aItem = false;
		switch ($aInvoice['type']) {
			case 'product':
				$aItem = Phpfox::getService('auction')->getAuctionForEdit($aInvoice['item_id'], true);
				break;
			case 'feature':
				$aItem = Phpfox::getService('auction')->getAuctionForEdit($aInvoice['item_id'], true);
				break;
		}
		
		if ($aItem === false)
		{
			Phpfox::log('Not a valid listing.');
			
			return false;
		}
		
		Phpfox::log('Purchase is valid: ' . var_export($aInvoice, true));
		
		if ($aParams['status'] == 'completed')
		{
			if ($aParams['total_paid'] == $aInvoice['price'])
			{
				Phpfox::log('Paid correct price');
			}
			else 
			{
				Phpfox::log('Paid incorrect price');
				
				return false;
			}
		}
		else 
		{
			Phpfox::log('Payment is not marked as "completed".');
			
			return false;
		}
		Phpfox::log('Handling purchase');
		
		$this->database()->update(Phpfox::getT('ecommerce_invoice'), array(
				'status' => $aParams['status'],
				'param' => json_encode($aParams),
				'payment_method' => isset($aParams['gateway']) ? $aParams['gateway'] : '',
				'time_stamp_paid' => PHPFOX_TIME
			), 'invoice_id = ' . $aInvoice['invoice_id']
		);		

		//update data 
		switch ($aInvoice['type']) {
			case 'product':

					// update featured time 
					$aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);
					$pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);

					foreach($pay_type as $val){
						switch ($val) {
							case 'publish':
									
									//handle to publish with feature zero 	
									if((int)$aItem['feature_day'] > 0){

										if((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id']) <= 0)  {
												$start_feature_time = 0;
						                        $end_feature_time = 0;

						                        $start_time = $aItem['start_time'];
						                        
						                        if($start_time < PHPFOX_TIME){/*in available time of auction*/   
						                            $start_feature_time = PHPFOX_TIME;
						                        }
						                        else{/*start time of auction in future*/
						                            $start_feature_time = $start_time;

						                        }   

						                        $end_feature_time = $start_feature_time + ((int)$aItem['feature_day'] * 86400); 
						                       
						                       if($end_feature_time >= 4294967295){
						                            $end_feature_time = 4294967295;
						                        }


										        $featureFee = doubleval(((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id']) ));
										        Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aItem['product_id'], $start_feature_time, $end_feature_time,(int)$aItem['feature_day'],$featureFee);
										 		
										}
									}

									/*update creating fee item*/
                    				$publishFee = doubleval((Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_publishing',(int)$aItem['user_id'])));
								    Phpfox::getLib('database')->update(  Phpfox::getT('ecommerce_product'), array('creating_item_fee'=> (int)$publishFee),'product_id = ' . $aItem['product_id']);   

									//handle to update status when publish
									$status = 'draft';
			                         if(Phpfox::getService('ecommerce.helper')->getUserParam('auction.admin_want_auction_to_be_automatically_approved_after_published', (int)$aItem['user_id'])){
			                            $status = 'approved';
			                        } else {
			                            $status = 'pending';
			                        }


			                        Phpfox::getService('ecommerce.process')->updateProductStatus($aItem['product_id'], $status);

			                         if($status == 'approved'){
			                            // call approve function 
			                            Phpfox::getService('ecommerce.process')->approveProduct($aItem['product_id'], null,'auction');                                    
			                        }
                                (($sPlugin = Phpfox_Plugin::get('auction.service_callback_payment_product_publish__end')) ? eval($sPlugin) : false);
								break;

							case 'feature':

									//handle to update feature time
			                        $start_feature_time = 0;
			                        $end_feature_time = 0;

			                        $start_time = $aItem['start_time'];
			                        
			                        if($start_time < PHPFOX_TIME){/*in available time of auction*/   
			                            $start_feature_time = PHPFOX_TIME;
			                        }
			                        else{/*start time of auction in future*/
			                            $start_feature_time = $start_time;

			                        }   

			                        $end_feature_time = $start_feature_time + ((int)$aItem['feature_day'] * 86400); 
			                       
			                       if($end_feature_time >= 4294967295){
			                            $end_feature_time = 4294967295;
			                        }

							        $featureFee = doubleval(((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id']) ));
							        Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aItem['product_id'], $start_feature_time, $end_feature_time,(int)$aItem['feature_day'],$featureFee);
							 		
							 		//handle to publish with fee zero 
							        if(Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_publishing',(int)$aItem['user_id']) <= 0) {
										$status = 'draft';
				                         if(Phpfox::getService('ecommerce.helper')->getUserParam('auction.admin_want_auction_to_be_automatically_approved_after_published', (int)$aItem['user_id']) ){
				                            $status = 'approved';
				                        } else {
				                            $status = 'pending';
				                        }


				                        Phpfox::getService('ecommerce.process')->updateProductStatus($aItem['product_id'], $status);

				                         if($status == 'approved'){
				                            // call approve function 
				                            Phpfox::getService('ecommerce.process')->approveProduct($aItem['product_id'], null ,'auction');                                    
				                        }

							        }

                                (($sPlugin = Phpfox_Plugin::get('auction.service_callback_payment_product_feature__end')) ? eval($sPlugin) : false);

								break;
						}
					}


					/*update separate two invoice feature and publish*/
					//if(count($pay_type) >= 2){
					if(in_array( "feature" ,$pay_type) && in_array( "publish" ,$pay_type)){

						$aInvoice = Phpfox::getService('ecommerce')->getInvoice($aParams['item_number']);
						$aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);

			   			$featureFee = doubleval(((int)$aInvoice['invoice_data']['feature_days'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id'])));
			            $publishFee = doubleval((Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_publishing',(int)$aItem['user_id'])));
						
						Phpfox::getLib('database')->insert(Phpfox::getT('ecommerce_invoice'), array(
				                'item_id' => $aInvoice['item_id'],
				                'type' => 'feature',
				                'user_id' => $aInvoice['user_id'],
			                	'currency_id' => $aInvoice['currency_id'],
				                'price' => $featureFee,
				                'status' => $aInvoice['status'],
				                'time_stamp' => $aInvoice['time_stamp'], 
				                'time_stamp_paid' => $aInvoice['time_stamp_paid'], 
				                'param' => $aInvoice['param'], 
				                'payment_method' => $aInvoice['payment_method'], 	
				                'invoice_data' => json_encode($aInvoice['invoice_data']), 
				                'pay_type' => 'feature', 
				            )

				        );

						Phpfox::getLib('database')->insert(Phpfox::getT('ecommerce_invoice'), array(
				                'item_id' => $aInvoice['item_id'],
				                'type' => 'product',
				                'user_id' => $aInvoice['user_id'],
				                'currency_id' => $aInvoice['currency_id'],
				                'price' => $publishFee,
				                'status' => $aInvoice['status'],
				                'time_stamp' => $aInvoice['time_stamp'], 
				                'time_stamp_paid' => $aInvoice['time_stamp_paid'], 
				                'param' => $aInvoice['param'], 
				                'payment_method' => $aInvoice['payment_method'],
				                'invoice_data' => json_encode($aInvoice['invoice_data']), 
				                'pay_type' => 'publish', 
				            )

				        );

				        Phpfox::getLib('database')->delete(Phpfox::getT('ecommerce_invoice'),'invoice_id = '.(int)$aInvoice['invoice_id']);
					

					}
					break;
			case 'feature':
					// update featured time 
					$aInvoice['invoice_data'] = (array)json_decode($aInvoice['invoice_data']);
					$pay_type = explode("|", $aInvoice['invoice_data']['pay_type']);

					foreach($pay_type as $val){
						switch ($val) {
							case 'feature':
										
									/*no matter what expand feature time or feature first time,we just using feature day in DB*/
									 if($aItem['feature_end_time'] < PHPFOX_TIME ){

									 	 if((int)$aItem['feature_day'] > 0){

			                                $start_feature_time = 0;
			                                $end_feature_time = 0;

			                                $start_time = $aItem['start_time'];
			                                
			                                if($start_time < PHPFOX_TIME){/*in available time of auction*/   
			                                    $start_feature_time = PHPFOX_TIME;
			                                }
			                                else{/*start time of auction in future*/
			                                    $start_feature_time = $start_time;

			                                }   

			                                $end_feature_time = $start_feature_time + ((int)$aItem['feature_day'] * 86400); 
			                               
			                               if($end_feature_time >= 4294967295){
			                                    $end_feature_time = 4294967295;
			                                }

							        		$featureFee = doubleval(((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id']) ));
			                                Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aItem['product_id'], $start_feature_time, $end_feature_time,(int)$aItem['feature_day'],$featureFee);

	                                	}	

									 }

									 else{/*already featured ,wanna expand feature time*/
									 		 
									 		 $start_feature_time = 0;
			                                 $end_feature_time = 0;

			                                 $feature_day = $aItem['feature_day'];
			                                 
			                                 if((int)$feature_day > 0){

			                                    $start_feature_time = $aItem['feature_start_time'];

			                                    $end_feature_time = $start_feature_time + ((int)$feature_day * 86400); 
			                                   
			                                   if($end_feature_time >= 4294967295){
			                                        $end_feature_time = 4294967295;
			                                    }

							        			$featureFee = doubleval(((int)$aItem['feature_day'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.how_much_is_user_worth_for_auction_featured',(int)$aItem['user_id']) ));
			                                    Phpfox::getService('ecommerce.process')->updateProductFeatureTime($aItem['product_id'], $start_feature_time, $end_feature_time,(int)$feature_day,$featureFee);

			                                }


									 }

                                (($sPlugin = Phpfox_Plugin::get('auction.service_callback_payment_product_feature__end')) ? eval($sPlugin) : false);

								break;
						}
					}

					break;
		}
		
		Phpfox::log('Handling complete');		
	}

    public function getAuctionsDetails($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id']))
        {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages.pages'),
            'breadcrumb_home' => Phpfox::getLib('url')->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'module' => 'pages',
            'item' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_pages' => $sLink . 'auction/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }


    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()
                ->select('product_id, name, user_id')
                ->from(Phpfox::getT('ecommerce_product'))
                ->where('product_id = ' . (int) $iItemId)
                ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'auction\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) $iItemId);

        if (!$bDoNotSendEmail)
        {
            $sLink = Phpfox::permalink('auction.detail', $aRow['product_id'], $aRow['name']);

            Phpfox::getLib('mail')
                    ->to($aRow['user_id'])
                    ->subject(array('auction.full_name_liked_your_auction_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['name'])))
                    ->message(array('auction.full_name_liked_your_auction_a_href_link_title_a_to_view_this_auction_follow_the_link_below_a_href_link_link_a', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['name'])))
                    ->notification('like.new_like')
                    ->send();

            Phpfox::getService('notification.process')->add('auction_like', $aRow['product_id'], $aRow['user_id']);
        }
    }
    
    public function deleteLike($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'auction\' AND item_id = ' . (int) $iItemId . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) $iItemId);
	}

    public function getNotificationLike($aNotification)
	{
		$aRow = $this->database()
                ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')	
                ->from(Phpfox::getT('ecommerce_product'), 'e')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                ->where('e.product_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');
			
		if (!isset($aRow['product_id']))
		{
			return false;
		}			
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
		
		$sPhrase = '';
		if ($aNotification['user_id'] == $aRow['user_id'])
		{
			$sPhrase = _p('users_liked_gender_own_auction_title', array('users' => $sUsers, 'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1), 'title' => $sTitle));
		}
		elseif ($aRow['user_id'] == Phpfox::getUserId())		
		{
			$sPhrase = _p('users_liked_your_auction_title', array('users' => $sUsers, 'title' => $sTitle));
		}
		else 
		{
			$sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_039_s_span_auction_title', array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
		}
			
		return array(
			'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	
	}

	 
    public function updateCounterList()
    {
        $aList = array();

        $aList[] =	array(
            'name' => _p('users_auction_count'),
            'id' => 'auction-total'
        );

        $aList[] =	array(
            'name' => _p('update_users_activity_auction_points'),
            'id' => 'auction-activity'
        );

        return $aList;
    }

	public function getDashboardActivity()
	{
		$aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

		return array(
			_p('auctions') => $aUser['activity_auction']
		);
	}

    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'auction-total')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(epa.auction_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('ecommerce_product'), 'ep', 'ep.user_id = u.user_id AND ep.product_status != \'deleted\'')
                ->leftJoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
                $this->database()->update(Phpfox::getT('user_field'), array('total_auction' => $aRow['total_items']), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
        elseif ($iId == 'auction-activity')
        {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user_activity'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('m.user_id, m.activity_auction, m.activity_points, m.activity_total, COUNT(epa.auction_id) AS total_items')
                ->from(Phpfox::getT('user_activity'), 'm')
                ->leftJoin(Phpfox::getT('ecommerce_product'), 'ep', 'ep.user_id = m.user_id AND ep.product_status != \'deleted\'')
                ->leftJoin(Phpfox::getT('ecommerce_product_auction'), 'epa', 'epa.product_id = ep.product_id')
                ->group('m.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow)
            {
				$iPointsPerBlog = Phpfox::getService('user.group.setting')->getGroupParam( $aRow['user_group_id'], 'auction.points_auction');

                $this->database()->update(Phpfox::getT('user_activity'), array(
                    'activity_points' => (($aRow['activity_points'] - ($aRow['activity_auction'] * $iPointsPerBlog )) + ($aRow['total_items'] * $iPointsPerBlog)),
                    'activity_total' => (($aRow['activity_total'] - $aRow['activity_auction']) + $aRow['total_items']),
                    'activity_auction' => $aRow['total_items']
                ), 'user_id = ' . $aRow['user_id']);
            }

            return $iCnt;
        }
    }
    
    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
	{		
		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = e.user_id');
		}			

		$sWhere = '';
		$sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
		$aRow = $this->database()->select('u.user_id, e.product_id, e.module_id, e.item_id, e.product_id, e.name, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.server_id as image_server_id, e.total_like, e.total_comment, et.description_parsed as description_parsed, l.like_id AS is_liked')
			->from(Phpfox::getT('ecommerce_product'), 'e')
			->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
			->leftJoin(Phpfox::getT('ecommerce_product_text'), 'et', 'et.product_id = e.product_id')
			->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'auction\' AND l.item_id = e.product_id AND l.user_id = ' . Phpfox::getUserId())
			->where('e.product_id = ' . (int) $aItem['item_id'] . $sWhere)
			->execute('getSlaveRow');
        if (empty($aRow['image_path'])) {
            $aRow['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
        }
        \Phpfox_Template::instance()->assign('aAuction', $aRow);
        \Phpfox_Component::setPublicParam('custom_param_' . $aItem['feed_id'], $aRow);


		if (!isset($aRow['product_id']))
		{
			return false;
		}

		if ($bIsChildItem)
		{
			$aItem = $aRow;
		}			
		
		if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'auction.view_browse_auctions'))
			|| (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['item_id'], 'auction.view_browse_auctions'))			
		)
		{
			return false;
		}

        $aReturn = array(
            'feed_title' => $aRow['name'],
            'feed_info' => _p('create_an_auction'),
            'feed_link' => Phpfox::permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'feed_content' => $aRow['description_parsed'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/auction.png', 'return_url' => true)),
            'time_stamp' => $aRow['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'enable_like' => true,
            'like_type_id' => 'auction',
            'total_comment' => $aRow['total_comment'],
            'load_block' => 'auction.feedrows'
        );

		if ($bIsChildItem)
		{
			$aReturn = array_merge($aReturn, $aItem);
		}		

		return $aReturn;
	}	

	public function addLikeBid($iItemId, $bDoNotSendEmail = false)
    {

		$sWhere = ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
		$aRow = $this->database()->select('eab.auctionbid_id,eab.auctionbid_user_id, e.product_id, e.module_id, e.item_id, e.product_id, e.name, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.server_id as image_server_id,et.description_parsed as description_parsed,eab.auctionbid_total_like as total_like,eab.auctionbid_total_comment as total_comment')
			->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
			->join(Phpfox::getT('ecommerce_product'), 'e', 'eab.auctionbid_product_id = e.product_id')
			->leftJoin(Phpfox::getT('ecommerce_product_text'), 'et', 'et.product_id = e.product_id')
			->join(PHpfox::getT('user'),'u','u.user_id = eab.auctionbid_user_id')
			->where('eab.auctionbid_id = ' . (int) $iItemId . $sWhere)
			->execute('getSlaveRow');

		if (!isset($aRow['product_id']))
		{
			return false;
		}

        $this->database()->updateCount('like', 'type_id = \'auction_bid\' AND item_id = ' . (int) $iItemId . '', 'auctionbid_total_like', 'ecommerce_auction_bid', 'auctionbid_id = ' . (int) $iItemId);

    }

    public function addLikeWonBid($iItemId, $bDoNotSendEmail = false)
    {

        $aRow = $this->database()
            ->select('product_id, name, user_id')
            ->from(Phpfox::getT('ecommerce_product'))
            ->where('product_id = ' . (int) $iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'auction_wonbid\' AND item_id = ' . (int) $iItemId . '', 'auction_won_bid_total_like', 'ecommerce_product_auction', 'product_id = ' . (int) $iItemId);

    }


 	public function deleteLikeBid($iItemId)
	{
		$this->database()->updateCount('like', 'type_id = \'auction_bid\' AND item_id = ' . (int) $iItemId . '', 'auctionbid_total_like', 'ecommerce_auction_bid', 'auctionbid_id = ' . (int) $iItemId);
	}

    public function deleteLikeWonBid($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'auction_wonbid\' AND item_id = ' . (int) $iItemId . '', 'auction_won_bid_total_like', 'ecommerce_product_auction', 'product_id = ' . (int) $iItemId);
    }

	public function getAjaxCommentVarBid(){
            return;

	}

    public function getAjaxCommentVarWonBid(){
        return;

    }

	public function getCommentItemBid($iId)
	{
		$aRow = $this->database()->select('eab.auctionbid_id AS comment_item_id, e.privacy_comment, eab.auctionbid_user_id AS comment_user_id')
			->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
			->join(Phpfox::getT('ecommerce_product'), 'e', 'eab.auctionbid_product_id = e.product_id')
			->where('eab.auctionbid_id = ' . (int) $iId)
			->execute('getSlaveRow');		
			
		$aRow['comment_view_id'] = '0';
		
		if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
		{
			unset($aRow['comment_item_id']);
		}
			
		return $aRow;
	}

    public function getCommentItemWonBid($iId)
    {
        $aRow = $this->database()->select('e.product_id AS comment_item_id, e.privacy_comment, e.user_id AS comment_user_id')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->where('e.product_id = ' . (int) $iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment']))
        {
            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }


	public function addCommentBid($aVals, $iUserId = null, $sUserName = null)
	{	
		

		$sWhere = '';
		$sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
		$aRow = $this->database()->select('eab.auctionbid_id,eab.auctionbid_user_id, e.product_id, e.module_id, e.item_id, e.product_id, e.name, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.server_id as image_server_id,et.description_parsed as description_parsed,eab.auctionbid_total_like as total_like,eab.auctionbid_total_comment as total_comment')
			->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
			->join(Phpfox::getT('ecommerce_product'), 'e', 'eab.auctionbid_product_id = e.product_id')
			->leftJoin(Phpfox::getT('ecommerce_product_text'), 'et', 'et.product_id = e.product_id')
			->join(PHpfox::getT('user'),'u','u.user_id = eab.auctionbid_user_id')
			->where('eab.auctionbid_id = ' . (int) $aVals['item_id'] . $sWhere)
			->execute('getSlaveRow');

		if (!isset($aRow['product_id']))
		{
			return false;
		}

		// Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
		if (empty($aVals['parent_id']))
		{
			$this->database()->updateCounter('ecommerce_auction_bid', 'auctionbid_total_comment', 'auctionbid_id', $aVals['item_id']);
		}
	}

    public function deleteCommentBid($iId){
        $this->database()->update(Phpfox::getT('ecommerce_auction_bid'), array('auctionbid_total_comment' => array('= auctionbid_total_comment -', 1)), 'auctionbid_id = ' . (int) $iId);
    }

    public function addCommentWonBid($aVals, $iUserId = null, $sUserName = null)
    {

        $aRow = $this->database()
            ->select('product_id, name, user_id')
            ->from(Phpfox::getT('ecommerce_product'))
            ->where('product_id = ' . (int) $aVals['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id']))
        {
            $this->database()->updateCounter('ecommerce_product_auction', 'auction_won_bid_total_comment', 'product_id', $aVals['item_id']);
        }
    }

    public function deleteCommentWonbid($iId){
        $this->database()->update(Phpfox::getT('ecommerce_product_auction'), array('auction_won_bid_total_comment' => array('= auction_won_bid_total_comment -', 1)), 'product_id = ' . (int) $iId);
    }

    public function getActivityFeedBid($aItem, $aCallback = null, $bIsChildItem = false)
	{		

		if (Phpfox::isUser())
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'auction_bid\' AND l.item_id = eab.auctionbid_id AND l.user_id = ' . Phpfox::getUserId());
		}

		if ($bIsChildItem)
		{
			$this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = eab.auctionbid_user_id');
		}			

		$sWhere = '';
		$sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
		$aRow = $this->database()->select('eab.auctionbid_id,eab.auctionbid_user_id, e.product_id, e.module_id, e.item_id, e.product_id, e.name, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.server_id as image_server_id,et.description_parsed as description_parsed,eab.auctionbid_total_like as total_like,eab.auctionbid_total_comment as total_comment')
			->from(Phpfox::getT('ecommerce_auction_bid'), 'eab')
			->join(Phpfox::getT('ecommerce_product'), 'e', 'eab.auctionbid_product_id = e.product_id')
			->leftJoin(Phpfox::getT('ecommerce_product_text'), 'et', 'et.product_id = e.product_id')
			->join(PHpfox::getT('user'),'u','u.user_id = eab.auctionbid_user_id')
			->where('eab.auctionbid_id = ' . (int) $aItem['item_id'] . $sWhere)
			->execute('getSlaveRow');

		if (!isset($aRow['product_id']))
		{
			return false;
		}
        if (empty($aRow['image_path'])) {
            $aRow['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
        }
        \Phpfox_Template::instance()->assign('aAuction', $aRow);
        \Phpfox_Component::setPublicParam('custom_param_' . $aItem['feed_id'], $aRow);

		$aReturn = array(
			'can_post_comment' => true,
			'feed_title' => $aRow['name'],
			'feed_info' => _p('bid_on_an_auction'),
			'feed_link' => Phpfox::permalink('auction.detail', $aRow['product_id'], $aRow['name']),
			'feed_content' => $aRow['description_parsed'],
			'total_comment' => $aRow['total_comment'],
			'feed_total_like' => $aRow['total_like'],
			'feed_is_liked' => $aRow['is_liked'],			
			'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/auction.png', 'return_url' => true)),
			'time_stamp' => $aItem['time_stamp'],
			'enable_like' => true,			
			'like_type_id' => 'auction_bid',
			'comment_type_id' => 'auction_bid',
            'load_block' => 'auction.feedrows'
		);

		if ($bIsChildItem)
		{
			$aReturn = array_merge($aReturn, $aItem);
		}

		return $aReturn;
	}


    public function getActivityFeedWonbid($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem)
        {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2', 'u2.user_id = epa.auction_won_bidder_user_id');
        }

        $sWhere = '';
        $sWhere .= ' and e.product_status IN ( \'running\',\'approved\',\'bidden\',\'completed\') ';
        $aRow = $this->database()->select('e.product_id, e.module_id, e.item_id, e.product_id, e.name, e.product_creation_datetime as time_stamp, e.logo_path as image_path, e.server_id as image_server_id,et.description_parsed as description_parsed,epa.auction_won_bid_total_like as total_like, epa.auction_won_bid_total_comment as total_comment, l.like_id AS is_liked')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(PHpfox::getT('ecommerce_product_auction'),'epa','epa.product_id = e.product_id')
            ->join(PHpfox::getT('user'),'u','u.user_id = e.user_id')
            ->leftJoin(PHpfox::getT('ecommerce_product_text'),'et','et.product_id = e.product_id')
            ->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'auction_wonbid\' AND l.item_id = e.product_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('e.product_id = ' . (int) $aItem['item_id'] . $sWhere)
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $aReturn = array(
            'can_post_comment' => true,
            'feed_title' => $aRow['name'],
            'feed_info' => _p('has_won_the_auction'),
            'feed_link' => Phpfox::permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'feed_content' => $aRow['description_parsed'],
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array('theme' => 'module/auction.png', 'return_url' => true)),
            'time_stamp' => $aItem['time_stamp'],
            'enable_like' => true,
            'like_type_id' => 'auction_wonbid',
            'comment_type_id' => 'auction_wonbid',
        );

        if (!empty($aRow['image_path']))
        {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'core.url_pic',
                    'file' => $aRow['image_path'],
                    'suffix' => ''
                )
            );

            $aReturn['feed_image_banner'] = $sImage;
        }else {
            $sImage = Phpfox::getParam('core.path_file') . 'module/auction/static/image/default_ava.png';
            $aReturn['feed_image_banner'] = $sImage;
        }

        if ($bIsChildItem)
        {
            $aReturn = array_merge($aReturn, $aItem);
        }

        return $aReturn;

    }

	public function getNotificationBid($aNotification)
	{

		$aRow = $this->database()
                ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
                ->from(Phpfox::getT('ecommerce_product'), 'e')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
                ->where('e.product_id = ' . (int) $aNotification['item_id'])
                ->execute('getSlaveRow');
			
		if (!isset($aRow['product_id']))
		{
			return false;
		}			
			
		$sUsers = Phpfox::getService('notification')->getUsers($aNotification);
		$sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aRow['user_id'] == Phpfox::getUserId()){
            $sPhrase = _p('full_name_has_bid_on_your_auction_title', array('full_name' => $sUsers, 'title' => $sTitle));
        }
        else{

            $sPhrase = _p('you_have_been_outbid_on_auction_title', array('title' => $sTitle));
        }

		return array(
			'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
			'message' => $sPhrase,
			'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
		);	

	}

    public function getNotificationWonbid($aNotification)
    {

        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name,epa.auction_won_bidder_user_id')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'e.product_id = epa.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aRow['user_id'] == Phpfox::getUserId()){ // send to seller
            $aWonUser =  Phpfox::getService('user')->get($aRow['auction_won_bidder_user_id']);
            $sPhrase = _p('full_name_has_won_your_auction_title', array('full_name' => $aWonUser['full_name'], 'title' => $sTitle));
        }
        else{
            //send to won bid user
            $sPhrase = _p('congratulations_you_have_won_the_auction_title', array('title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationLosebid($aNotification)
    {

        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'e.product_id = epa.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        //send to didnt win user
        $sPhrase = _p('you_ve_been_outbid_and_the_auction_title_has_ended', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationEndbid($aNotification)
    {

        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'e.product_id = epa.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        if($aRow['user_id'] == Phpfox::getUserId()){
            $sPhrase = _p('your_auction_title_has_ended', array('title' => $sTitle));
        }
        else{
            $sPhrase = _p('the_auction_title_has_ended', array('title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationTransferbid($aNotification)
    {

        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('ecommerce_product_auction'), 'epa', 'e.product_id = epa.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('the_auction_title_has_transferred_to_another_winning_bidder', array('title' => $sTitle));


        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationMakeoffer($aNotification)
    {
        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'eao.auctionoffer_product_id = e.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('eao.auctionoffer_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id'])) 
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');
        $sPhrase = _p('you_have_receive_offer_on_auction_title', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']).'offerhistory',
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

	public function getNotificationApprove($aNotification)
    {
        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_product'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.product_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_auction_title_has_been_approved', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationApproveoffer($aNotification)
    {

        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'eao.auctionoffer_product_id = e.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('eao.auctionoffer_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_offer_has_been_approved_on_auction_title', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

    public function getNotificationDenyoffer($aNotification)
    {

        $aRow = $this->database()
            ->select('e.product_id, e.name, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('ecommerce_auction_offer'), 'eao')
            ->join(Phpfox::getT('ecommerce_product'), 'e', 'eao.auctionoffer_product_id = e.product_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('eao.auctionoffer_id = ' . (int) $aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['product_id']))
        {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['name'], Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_offer_has_been_denied_on_auction_title', array('title' => $sTitle));

        return array(
            'link' => Phpfox::getLib('url')->permalink('auction.detail', $aRow['product_id'], $aRow['name']),
            'message' => $sPhrase,
            'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog')
        );

    }

	public function canShareItemOnFeed(){}	

	public function getAjaxProfileController()
    {
        return 'auction.index';
    }

	public function getProfileLink()
    {
        return 'profile.auction';
    }

	public function getProfileMenu($aUser)
    {
        $aUser['total_auction'] = Phpfox::getService('auction')->getTotalMyAuction($aUser['user_id']);

        if (!Phpfox::getParam('profile.show_empty_tabs'))
        {
            if (!isset($aUser['total_auction']))
            {
                return false;
            }

            if (isset($aUser['total_auction']) && (int) $aUser['total_auction'] === 0)
            {
                return false;
            }
        }

        $aSubMenu = array();

        $aMenus[] = array(
            'phrase' => 'Auctions',
            'url' => 'profile.auction',
            'total' => (int) (isset($aUser['total_auction']) ? $aUser['total_auction'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/auction.png',
            'icon_class' => 'ico ico-hammer'
        );

        return $aMenus;
    }   

	public function addAuction($iProductId)
    {
        Phpfox::getService('pages')->setIsInPage();

        return array(
            'module' => 'pages',
            'item_id' => $iProductId,
            'table_prefix' => 'pages_'
        );
    }

 	public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'auction.who_can_view_browse_auctions'))
        {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('auctions'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'auction/',
            'icon' => 'feed/auction.png',
            'landing' => 'auction',
            'menu_icon' => 'ico ico-hammer'
        );

        return $aMenus;
    }

    public function getPageSubMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'auction.share_auctions'))
        {
            return null;
        }

        return array(
            array(
                'phrase' => _p('create_an_auction_cap'),
                'url' => Phpfox::getLib('url')->makeUrl('auction.add', array('module' => 'pages', 'item' => $aPage['page_id']))
            )
        );
    }
	public function getPagePerms()
    {
        $aPerms = array();

        $aPerms['auction.share_auctions'] = _p('who_can_share_auctions');
        $aPerms['auction.view_browse_auctions'] = _p('who_can_view_browse_auctions');

        return $aPerms;
    }

    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'auction.view_browse_auctions'))
        {
            return false;
        }

        return true;
    }

    public function getGlobalPrivacySettings()
	{
		return array(
			'auction.default_privacy_setting' => array(
				'phrase' => _p('auctions')
			)
		);
	}

    public function getFeedDetails($iItemId)
    {
        return array(
            'module' => 'ecommerce',
            'table_prefix' => 'ecommerce_',
            'item_id' => $iItemId
        );
    }

    /**
     * @return array
     */
    public function getUploadParamsLogo() {
        $iMaxFileSize = Phpfox::getUserParam('auction.photo_max_upload_size');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize/1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        return [
            'label' => _p('main_photo'),
            'max_size' => $iMaxFileSize,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynecommerce' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynecommerce' . PHPFOX_DS,
            'thumbnail_sizes' => array(50, 100, 120, 200, 400, 1024),
            'remove_field_name' => 'remove_logo'
        ];
    }

    public function getUploadParamsProduct($aParams = null)
    {
        $iRemainImage = $aParams['remain_upload'];
        $iMaxFileSize = $aParams['file_size'];
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);
        $aEvents = [
            'sending' => '$Core.auction.dropzoneOnSending',
            'success' => '$Core.auction.dropzoneOnSuccess',
            'queuecomplete' => '$Core.auction.dropzoneQueueComplete',
        ];
        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'upload_url' => Phpfox::getLib('url')->makeUrl('ecommerce.frame-upload'),
            'component_only' => true,
            'max_file' => $iRemainImage,
            'js_events' => $aEvents,
            'upload_now' => "true",
            'submit_button' => '#js_listing_done_upload',
            'first_description' => _p('drag_n_drop_multi_photos_here_to_upload'),
            'upload_dir' => Phpfox::getParam('core.dir_pic') . 'ynecommerce' . PHPFOX_DS,
            'upload_path' => Phpfox::getParam('core.url_pic') . 'ynecommerce' . PHPFOX_DS,
            'update_space' => false,
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'style' => '',
            'extra_description' => [
                _p('maximum_photos_you_can_upload_is_number', ['number' => $iRemainImage])
            ],
            'thumbnail_sizes' => array(100, 120, 200, 400, 1024)
        ];
    }

}	