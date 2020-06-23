<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		LyTK
 * @package  		Module_directory
 */

class Auction_Service_Helper extends Phpfox_Service
{
	private $_aPackage;
	private $_aDate;

	public function __construct() {
		// returned array should be under this format : array( 'key_string' => array('id' => id, 'phrase' => phrase))
		$this->_aPackage = array( 
		);

        $this->_aDate = array(
			'dayofweek' => array( 
				"monday" => array(
					"id" => 1, 
					"phrase" => _p('monday'),
					'name' => 'monday',
					'description' => '', 
				),
				"tuesday" => array(
					"id" => 2, 
					"phrase" => _p('tuesday'),
					'name' => 'tuesday',
					'description' => '', 
				),
				"wednesday" => array(
					"id" => 3, 
					"phrase" => _p('wednesday'),
					'name' => 'wednesday',
					'description' => '', 
				),
				"thursday" => array(
					"id" => 4, 
					"phrase" => _p('thursday'),
					'name' => 'thursday',
					'description' => '', 
				),
				"friday" => array(
					"id" => 5, 
					"phrase" => _p('friday'),
					'name' => 'friday',
					'description' => '', 
				),
				"saturday" => array(
					"id" => 6, 
					"phrase" => _p('saturday'),
					'name' => 'saturday',
					'description' => '', 
				),
				"sunday" => array(
					"id" => 7, 
					"phrase" => _p('sunday'),
					'name' => 'sunday',
					'description' => '', 
				),
			),
		);
	}

	public function getListNameOfBlockInDetailLinkAuction(){
		return array('photos', 'videos');
	}

	public function isPhoto(){
		if(Phpfox::isModule('photo') || Phpfox::isModule('advancedphoto')){
			return true;
		}
		return false;
	}
	public function isVideo(){
		if(Phpfox::isModule('video') || Phpfox::isModule('videochannel')){
			return true;
		}
		return false;
	}

	public function isAdvPhoto(){
		return Phpfox::isModule('advancedphoto');
	}

	public function isAdvVideo(){
		return Phpfox::isModule('videochannel');
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

	public function setSessionBeforeAddItemFromSubmitForm($iProductId, $type)
	{
		$iCurrentUserId = Phpfox::getUserId();
		$_SESSION[Phpfox::getParam('core.session_prefix')]['ynauction']['add_new_item'][$iCurrentUserId]['product_id'] = $iProductId;
		$_SESSION[Phpfox::getParam('core.session_prefix')]['ynauction']['add_new_item'][$iCurrentUserId]['type'] = $type;
	}

	public function getSessionAfterUserAddNewItem($type)
	{
		$iCurrentUserId = Phpfox::getUserId();

		if(isset($_SESSION[Phpfox::getParam('core.session_prefix')]['ynauction']['add_new_item'][$iCurrentUserId]))
		{
			if($_SESSION[Phpfox::getParam('core.session_prefix')]['ynauction']['add_new_item'][$iCurrentUserId]['type'] == $type)
			{
				return $_SESSION[Phpfox::getParam('core.session_prefix')]['ynauction']['add_new_item'][$iCurrentUserId]['auction_id'];
			}
		}

		return false;
	}

	public function removeSessionAddNewItemOfUser()
	{
		$iCurrentUserId = Phpfox::getUserId();
		unset($_SESSION[Phpfox::getParam('core.session_prefix')]['ynauction']['add_new_item'][$iCurrentUserId]);

		return true;
	}

	private $_sYnAddParamForNavigateBack = 'ynproductid';

	public function getYnAddParamForNavigateBack()
	{
		return $this->_sYnAddParamForNavigateBack;
	}

	/**
	 * to create left sub menu for a controller 
	 */
	public function buildMenu() 
    {
		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) 
        {
			$aFilterMenu = array(_p('all_auctions') => '');

            if (Phpfox::isUser())
            {
                $aFilterMenu[_p('seller_section')] = 'auction.statistic';
                $aFilterMenu[_p('buyer_section')] = 'auction.my-bids';
				$totalWatch = 0;
                $aFilterMenu[_p('my_watch_list').($totalWatch?("<span class='count-item'>".$totalWatch."</span>"):"")] = 'my-watch-list';

                if (Phpfox::isModule('friend'))
                {
					$totalBidden = 0;
                    $aFilterMenu[_p('auctions_my_friends_bid_on').($totalBidden?("<span class='count-item'>".$totalBidden."</span>"):"")] = 'bidden-by-my-friends';
					$totalWon = 0;
					$aFilterMenu[_p('auctions_won_by_friends').($totalWon?("<span class='count-item'>".$totalWon."</span>"):"")] = 'won-by-my-friends';
					$totalAuctionFriend = 0;
					$aFilterMenu[_p('friends_auctions').($totalAuctionFriend?("<span class='count-item'>".$totalAuctionFriend."</span>"):"")] = 'friend';
                }
				
				if(Phpfox::getUserParam('auction.can_approve_auction'))
				{
					$totalPending = Phpfox::getService('auction')->getTotalPending();;
					$aFilterMenu[_p('pending_auctions').($totalPending?("<span class='count-item'>".$totalPending."</span>"):"")] = 'pending';
				}
            }
            
			Phpfox::getLib('template')->buildSectionMenu('auction', $aFilterMenu);
		}
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
    
    public function loadAuctionJsCss()
    {
        $aParams = $this->getJsSetupParams();
		Phpfox::getLib('template')
			->setHeader('cache' ,array(
				'magnific-popup.css' => 'module_auction',

                // detail page
                'jquery.prettyPhoto.js'	=> 'module_auction',
                'ms-caro3d.css'=> 'module_auction',
                'ms-lightbox.css'=> 'module_auction',
                'ms-partialview.css'=> 'module_auction',
                'ms-showcase2.css'=> 'module_auction',
                'prettyPhoto.css'=> 'module_auction',
                // end detail page

                'jquery.wookmark.js' => 'module_auction',
				'jquery.magnific-popup.js'	=> 'module_auction',
				'jquery.validate.js'	=> 'module_auction',
                'jquery.flexslider.js' => 'module_auction',
				'ynauctionhelper.js' => 'module_auction',
				'ynauction.js' => 'module_auction',
			))
			->setPhrase( array(
				'auction.this_field_is_required',
				'auction.please_enter_number_for_quantity',
				'auction.please_edit_quantity_more_than_zero',
				'auction.are_you_sure_you_want_to_delete_auctions_that_you_selected',
				'auction.are_you_sure_you_want_to_delete_this_auction',
				'auction.are_you_sure_you_want_to_publish_this_auction',
				'auction.are_you_sure_you_want_to_approve_this_auction',
				'auction.are_you_sure_you_want_to_deny_this_auction',
				'auction.are_you_sure_you_want_to_close_this_auction',
                'auction.yes',
                'auction.no',
                'auction.compare',
                'auction.please_select_more_than_one_entry_for_the_comparison',
                'auction.confirm'
			));
    }
    
    public function getCurrentCurrencies($sDefaultCurrency = '')
    {
        $aFoxCurrencies = Phpfox::getService('core.currency')->getForBrowse();

        if (empty($sDefaultCurrency))
        {
            $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        }
        
        $aResults = array();
        
        foreach ($aFoxCurrencies as $aCurrency)
        {
            if ($aCurrency['is_default'] == '1')
            {
                $aResults[] = $aCurrency;
            }
        }

        return $aResults;
    }
    
    public function getVisitingHours(){
    	return array(
    		'dayofweek' => $this->_aDate['dayofweek'], 
    		'hour' => array(
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
    
    public function getDuration($iStartTime, $iEndTime)
    {
        $iSeconds = $iEndTime - $iStartTime;

        $iDays = floor($iSeconds / 86400);
        $iSeconds %= 86400;
        $sDays = ($iDays > 0 ? ($iDays . ' ' . ($iDays > 1 ? _p('days') : _p('day'))) : '');
        
        $iHours = floor($iSeconds / 3600);
        $iSeconds %= 3600;
        $sHours = ($iHours > 0 ? ($iHours . ' ' . ($iHours > 1 ? _p('hours') : _p('hour'))) : '');
        
        $iMinutes = floor($iSeconds / 60);
        $sMinutes = ($iMinutes > 0 ? ($iMinutes . ' ' . ($iMinutes > 1 ? _p('mins') : _p('min'))) : '');

        return $sDays . (empty($sHours) ? '' : ' ' . $sHours) . (empty($sMinutes) ? '' : ' ' . $sMinutes);
    }
    
    public function pagination($iTotal, $iLimit = 10, $iPage = 1, $sParams = '')
    {
        $sAdjacents = "2";

        $iPage = ($iPage == 0 ? 1 : $iPage);
        
        $prev = $iPage - 1;
        $next = $iPage + 1;
        $iLastPage = ceil($iTotal / $iLimit);
        $iLpm1 = $iLastPage - 1;

        $sPagination = "";
        if ($iLastPage > 1)
        {
            $sPagination .= "<ul class='ynauction-custom-pager'>";
            $sPagination .= "<li class='details'>Page $iPage of $iLastPage</li>";

            if ($iPage != 1)
            {
                $iPrevious = $iPage - 1;
                $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=1\");'>First</a></li>";
                $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iPrevious\");'>Previous</a></li>";
            }
            else
            {
                $sPagination.= "<li><a href='javascript:;' class='current'>First</a></li>";
                $sPagination.= "<li><a href='javascript:;' class='current'>Previous</a></li>";
            }

            if ($iLastPage < 7 + ($sAdjacents * 2))
            {
                for ($iCounter = 1; $iCounter <= $iLastPage; $iCounter++)
                {
                    if ($iCounter == $iPage)
                        $sPagination.= "<li><a href='javascript:;' class='current'>$iCounter</a></li>";
                    else
                        $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iCounter\")'>$iCounter</a></li>";
                }
            }
            elseif ($iLastPage > 5 + ($sAdjacents * 2))
            {
                if ($iPage < 1 + ($sAdjacents * 2))
                {
                    for ($iCounter = 1; $iCounter < 4 + ($sAdjacents * 2); $iCounter++)
                    {
                        if ($iCounter == $iPage)
                            $sPagination.= "<li><a href='javascript:;' class='current'>$iCounter</a></li>";
                        else
                            $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iCounter\")'>$iCounter</a></li>";
                    }
                    $sPagination.= "<li class='dot'>...</li>";
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iLpm1\")'>$iLpm1</a></li>";
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iLastPage\")'>$iLastPage</a></li>";
                }
                elseif ($iLastPage - ($sAdjacents * 2) > $iPage && $iPage > ($sAdjacents * 2))
                {
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=1\")'>1</a></li>";
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=2\")'>2</a></li>";
                    $sPagination.= "<li class='dot'>...</li>";
                    for ($iCounter = $iPage - $sAdjacents; $iCounter <= $iPage + $sAdjacents; $iCounter++)
                    {
                        if ($iCounter == $iPage)
                            $sPagination.= "<li><a href='javascript:;' class='current'>$iCounter</a></li>";
                        else
                            $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iCounter\")'>$iCounter</a></li>";
                    }
                    $sPagination.= "<li class='dot'>..</li>";
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iLpm1\")'>$iLpm1</a></li>";
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iLastPage\")'>$iLastPage</a></li>";
                }
                else
                {
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=1\")'>1</a></li>";
                    $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=2\")'>2</a></li>";
                    $sPagination.= "<li class='dot'>..</li>";
                    for ($iCounter = $iLastPage - (2 + ($sAdjacents * 2)); $iCounter <= $iLastPage; $iCounter++)
                    {
                        if ($iCounter == $iPage)
                            $sPagination.= "<li><a href='javascript:;' class='current'>$iCounter</a></li>";
                        else
                            $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iCounter\")'>$iCounter</a></li>";
                    }
                }
            }

            if ($iPage < $iCounter - 1)
            {
                $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$next\")'>Next</a></li>";
                $sPagination.= "<li><a href='javascript:;' onclick='paginationAjaxReload(\"{$sParams}&page=$iLastPage\")'>Last</a></li>";
            }
            else
            {
                $sPagination.= "<li><a href='javascript:;' class='current'>Next</a></li>";
                $sPagination.= "<li><a href='javascript:;' class='current'>Last</a></li>";
            }
            $sPagination.= "</ul>\n";
        }

        return $sPagination;
    }

	/**
	 * Compare two float number.
	 * If $f1 < $f2, return -1.
	 * If $f1 > $f2, return 1.
	 * If $f1 == $f2, return 0.
	 */
	public function floatCmp($f1, $f2, $precision = 2)
	{
		$e = pow(10, $precision);
		
		$i1 = intval($f1 * $e);
		$i2 = intval($f2 * $e);
		
		return ($i1 == $i2 ? 0 : ($i1 < $i2 ? -1 : 1));
	}

        /**
     * @see Phpfox_Template
     */
    public function buildSectionMenu()
    {
        $iTotalMyBids = Phpfox::getService('auction.bid')->getTotalBids();
        $iTotalMyOffers = Phpfox::getService('auction.offer')->getTotalOffers();
        $iTotalDidntWinBid = Phpfox::getService('auction')->getTotalDidntWinBid();
        $iTotalMyWonBid = Phpfox::getService('auction')->getTotalMyWonBid();
        $iTotalMyOrders = Phpfox::getService('ecommerce.order')->getTotalMyOrders('auction');
        $iTotalMyCart = Phpfox::getService('ecommerce')->getCountNumberCartItem();;
        
        $aFilterMenu = array(
            _p('my_bids') . ($iTotalMyBids?('<span class="count-item">' . $iTotalMyBids . '</span>'):'') => 'auction.my-bids',
            _p('my_offers') . ($iTotalMyOffers?('<span class="count-item">' . $iTotalMyOffers . '</span>'):'') => 'auction.my-offers',
            _p('didnt_win') . ($iTotalDidntWinBid?('<span class="count-item">' . $iTotalDidntWinBid . '</span>'):'') => 'auction.didnt-win',
            _p('my_won_bids') . ($iTotalMyWonBid?('<span class="count-item">' . $iTotalMyWonBid . '</span>'):'') => 'auction.my-won-bids',
            _p('my_orders') . ($iTotalMyOrders?('<span class="count-item">' . $iTotalMyOrders . '</span>'):'') => 'auction.my-orders',
            _p('my_cart') . ($iTotalMyCart?('<span class="count-item">' . $iTotalMyCart . '</span>'):'') => 'auction.mycart',
            _p('checkout') => 'auction.checkout'
        );
        
        Phpfox::getLib('template')->buildSectionMenu('auction', $aFilterMenu);
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
     

}
