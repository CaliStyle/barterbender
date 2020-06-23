<?php

defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Permission extends Phpfox_Service
{

	public function canCreateStore($bRedirect = false)
	{
		if(Phpfox::getUserParam('ynsocialstore.can_create_store', $bRedirect)) {
			if(Phpfox::isUser($bRedirect)) { // even this group is authorized, guest is not allowed
				return true;
			}
		}
			
		return false;		
	}

	public function canCreateStoreWithLimit()
	{
		$countStore = Phpfox::getService('ynsocialstore')->countStoreOfUserId(Phpfox::getUserId());
		$limit = (int)Phpfox::getUserParam('ynsocialstore.number_store_user_can_create');
		if($countStore < $limit){
			return true;
		}

		return false;
	}

	public function canEditStore($bRedirect = false, $iOwnerId)
	{
		if(($iOwnerId == Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_edit_own_store', $bRedirect)) || ($iOwnerId != Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_edit_other_user_store', $bRedirect)))
        {
            return true;
        }
        return false;
	}

	public function canDeleteStore($bRedirect = false, $iOwnerId)
	{
		if(($iOwnerId == Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_delete_own_store', $bRedirect)) || ($iOwnerId != Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_delete_other_user_store', $bRedirect)))
        {
            return true;
        }
        return false;
	}

	public function canFeatureStore($bRedirect = false, $iOwnerId, $sStatus)
	{
		if(Phpfox::getUserParam('ynsocialstore.can_feature_store', $bRedirect))
		{
			return 2;
		}
		else{
			if($iOwnerId == Phpfox::getUserId() && ($sStatus == 'public') && Phpfox::getUserParam('ynsocialstore.can_feature_own_store', $bRedirect))
	        {
	            return 1;
	        }
    	}
        return 0;
	}

	public function canDenyStore($bRedirect = false, $sStatus)
    {
		if(Phpfox::getUserParam('ynsocialstore.can_approve_store', $bRedirect) && $sStatus == 'pending')
		{
			return true;
		}
		return false;
    }

	public function canApproveStore($bRedirect = false, $sStatus)
    {
        if(Phpfox::getUserParam('ynsocialstore.can_approve_store', $bRedirect) && in_array($sStatus, array('pending', 'denied')))
        {
            return true;
        }
        return false;
    }

	public function canCloseStore($iOwnerId, $sStatus)
	{
		if (($iOwnerId == Phpfox::getUserId() || Phpfox::isAdmin()) && $sStatus == 'public')
		{
			return true;
		}
		return false;
	}

	public function canReopenStore($iOwnerId, $sStatus)
	{
		if (($iOwnerId == Phpfox::getUserId() || Phpfox::isAdmin()) && $sStatus == 'closed')
		{
			return true;
		}
		return false;
	}

	public function canPublishStore($iOwnerId, $sStatus)
	{
		if ($iOwnerId == Phpfox::getUserId() && ($sStatus == 'draft' || $sStatus == 'expired'))
		{
			return true;
		}
		return false;
	}

	public function canPublishProduct($iOwnerId, $sStatus)
    {
        if ($iOwnerId == Phpfox::getUserId() && ($sStatus == 'draft'))
        {
            return true;
        }
        return false;
    }

	public function canCreateProduct($aStore)
	{

		if($aStore['user_id'] != Phpfox::getUserId() || $aStore['status'] != 'public')
		{
			return 0;
		}
		$aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
		$iTotal = Phpfox::getService('ynsocialstore.product')->countProductOfStore($aStore['store_id']);
		if($aPackage['max_products'] <= $iTotal && $aPackage['max_products'] > 0)
		{
			return 1;
		}

		return 2;
	}

	public function canDeleteProduct($bRedirect = false, $iOwnerId)
    {
        if(($iOwnerId == Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_delete_own_product', $bRedirect)) || ($iOwnerId != Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_delete_product_of_other_users', $bRedirect)))
        {
            return true;
        }
        return false;
    }

    public function canFeatureProduct($bRedirect = false, $iOwnerId, $sStatus)
    {
        if(Phpfox::getUserParam('ynsocialstore.can_feature_product', $bRedirect))
        {
            return 2;
        }
        else{
            if($iOwnerId == Phpfox::getUserId() && ($sStatus == 'running') && Phpfox::getUserParam('ynsocialstore.can_feature_own_product', $bRedirect))
            {
                return 1;
            }
        }
        return 0;
    }

    public function canDenyProduct($bRedirect = false, $sStatus)
    {
        if(Phpfox::getUserParam('ynsocialstore.can_approve_product', $bRedirect) && $sStatus == 'pending')
        {
            return true;
        }
        return false;
    }

    public function canApproveProduct($bRedirect = false, $sStatus)
    {
        if(Phpfox::getUserParam('ynsocialstore.can_approve_product', $bRedirect) && in_array($sStatus, array('pending', 'denied')))
        {
            return true;
        }
        return false;
    }

    public function canCloseProduct($iOwnerId, $sStatus)
    {
        if (($iOwnerId == Phpfox::getUserId() || Phpfox::isAdmin()) && in_array($sStatus, array('running', 'public')))
        {
            return true;
        }
        return false;
    }

    public function canReopenProduct($iOwnerId, $sStatus)
    {
        if (($iOwnerId == Phpfox::getUserId() || Phpfox::isAdmin()) && in_array($sStatus, array('paused', 'closed')))
        {
            return true;
        }
        return false;
    }

	public function canEditProduct($bRedirect = false, $iOwnerId)
	{
		if(($iOwnerId == Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_edit_own_product', $bRedirect)) || ($iOwnerId != Phpfox::getUserId() && Phpfox::getUserParam('ynsocialstore.can_edit_product_of_other_users', $bRedirect)))
		{
			return true;
		}
		return false;
	}
}