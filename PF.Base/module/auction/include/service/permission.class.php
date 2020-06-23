<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Service_Permission extends Phpfox_Service {

    public function canEditAuction($iUserId, $iProductId, $bRedirect = false)
    {
        if (Phpfox::isUser() == false)
        {
            return false;
        }

        if ((($iUserId == Phpfox::getUserId() && Phpfox::getUserParam('auction.can_edit_own_auction', $bRedirect))))
        {
            if (Phpfox::isUser($bRedirect))
            {
                return true;
            }
        }
		
		if ($iUserId != Phpfox::getUserId() && Phpfox::getUserParam('auction.can_edit_auction_created_by_other_users', $bRedirect))
		{
			return true;
		}
		
        return false;
    }

    public function canDeleteAuction($iUserId, $bRedirect = false)
    {
        if ((($iUserId == Phpfox::getUserId() && Phpfox::getUserParam('auction.can_delete_own_auction', $bRedirect)) || Phpfox::getUserParam('auction.can_delete_auction_created_by_other_users', $bRedirect)))
        {
            if (Phpfox::isUser($bRedirect))
            {
                return true;
            }
        }

        return false;
    }

    public function canManageAuctionDashBoard($iAuctionId, $aAuction = null)
    {
        if (Phpfox::isUser() == false)
        {
            return false;
        }

        if (null == $aAuction)
        {
            $aAuction = Phpfox::getService('auction')->getQuickAuctionById($iAuctionId);
        }

        if ($aAuction['user_id'] == Phpfox::getUserId())
        {
            return true;
        }
        
        return false;
    }

    public function canViewPhotoInAuction($iAuctionId, $bRedirect = false, $listPageMenu = null, $keyLandingPage = null)
    {
        if (!Phpfox::getService('auction.helper')->isPhoto())
        {
            return false;
        }
        
        return true;
    }

    public function canViewVideoInAuction($iAuctionId, $bRedirect = false, $listPageMenu = null, $keyLandingPage = null)
    {
        if (!Phpfox::getService('auction.helper')->isVideo())
        {
            return false;
        }

        return true;
    }

	public function canApproveAuction($bRedirect = false)
	{
		if (!Phpfox::isUser($bRedirect))
        {
            return false;
        }
		
		if (Phpfox::getUserParam('auction.can_approve_auction', $bRedirect))
		{
			return true;
		}
		
		return false;
	}
	
	public function canDenyAuction($bRedirect = false)
	{
		if (!Phpfox::isUser($bRedirect))
        {
            return false;
        }
		
		if (Phpfox::getUserParam('auction.can_deny_auction', $bRedirect))
		{
			return true;
		}
		
		return false;
	}
	
	public function canCloseAuction($iUserId, $bRedirect = false)
    {
        if ((($iUserId == Phpfox::getUserId() && Phpfox::getUserParam('auction.can_close_own_auction', $bRedirect)) || Phpfox::getUserParam('auction.can_close_auction_created_by_other_users', $bRedirect)))
        {
            if (Phpfox::isUser($bRedirect))
            {
                return true;
            }
        }

        return false;
    }
	
	public function canBidAuction($bRedirect = false)
	{
		if (!Phpfox::isUser($bRedirect))
        {
            return false;
        }
		
		if (Phpfox::getUserParam('auction.can_bid_auction', $bRedirect))
		{
			return true;
		}
		
		return false;
	}
}
