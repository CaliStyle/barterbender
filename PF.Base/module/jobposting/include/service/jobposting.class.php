<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class JobPosting_Service_Jobposting extends Phpfox_service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        
    }
    
    /**
     * Check favorite status
     * @param $sType: item_type
     * @param $iId: item_id
     * @param $iUserId
     * @return bool
     */
    public function isFavorited($sType, $iId, $iUserId)
    {
        $iId = $this->database()->select('favorite_id')
            ->from(Phpfox::getT('jobposting_favorite'))
            ->where('item_type = "'.$sType.'" AND item_id = '.(int)$iId.' AND user_id = '.(int)$iUserId)
            ->execute('getSlaveField');
        
		if(!$iId)
		{
			return false;
		}
        
		return true;
    }
    
    /**
     * Check follow status
     * @param $sType: item_type
     * @param $iId: item_id
     * @param $iUserId
     * @return bool
     */
    public function isFollowed($sType, $iId, $iUserId)
    {
        $iId = $this->database()->select('follow_id')
            ->from(Phpfox::getT('jobposting_follow'))
            ->where('item_type = "'.$sType.'" AND item_id = '.(int)$iId.' AND user_id = '.(int)$iUserId)
            ->execute('getSlaveField');
        
        if(!$iId)
		{
			return false;
		}
        
		return true;
    }
    
    public function getOwner($sType, $iId)
    {
        $iOwner = $this->database()->select('user_id')
            ->from(Phpfox::getT('jobposting_'.$sType))
            ->where($sType.'_id = '.(int)$iId)
            ->execute('getSlaveField');
        
        return $iOwner;
    }
    
    public function getFollowers($sType, $iId)
    {
        $aFollower = array();

        $aRows = $this->database()->select('DISTINCT user_id')
            ->from(Phpfox::getT('jobposting_follow'))
            ->where('item_type = "'.$sType.'" AND item_id = '.(int)$iId)
            ->execute('getSlaveRows');
        
        if (count($aRows))
        {
            foreach ($aRows as $aRow)
			{
            	$aFollower[] = $aRow['user_id'];
			}
        }
        
        return $aFollower;
    }
    
    public function getApplicants($sType, $iId)
    {
        $aApplicant = array();
        $aRows = array();
        
        if ($sType == 'job')
        {
            $aRows = $this->database()->select('DISTINCT user_id')
                ->from(Phpfox::getT('jobposting_application'))
                ->where('job_id = '.(int)$iId)
                ->execute('getSlaveRows');
        }
        
        if ($sType == 'company')
        {
            $aRows = $this->database()->select('DISTINCT a.user_id')
                ->from(Phpfox::getT('jobposting_application'), 'a')
                ->join(Phpfox::getT('jobposting_job'), 'j', 'j.job_id = a.job_id')
                ->where('j.company_id = '.(int)$iId)
                ->execute('getSlaveRows');
        }
        
        if (count($aRows))
        {
            foreach ($aRows as $aRow)
			{
            	$aApplicant[] = $aRow['user_id'];
			}
        }
        
        return $aApplicant;
    }
    
    public function getItemTitle($sItemType, $iItemId)
    {
        $sTitle = $this->database()->select(($sItemType == 'company') ? 'name' : 'title')
			->from(Phpfox::getT('jobposting_'.$sItemType))
			->where($sItemType.'_id = '.(int)$iItemId)
			->execute('getSlaveField');
        
        return $sTitle;
    }

    public function getWorkingCompanyRequestByUserIDAndCompanyID($userID = null, $companyID = null){
        if($companyID == null){
            return false;
        }
        if($userID == null){
            $userID = Phpfox::getUserId();
        }

        return $this->database()
            ->select('*')
            ->from(Phpfox::getT('jobposting_company_working_request'))
            ->where('user_id = ' . (int)$userID . ' AND company_id = ' . (int)$companyID . ' ')
            ->execute('getSlaveRow');
    }

    public function getPendingParticipantByCompanyID($companyID = null){
        if($companyID == null){
            return false;
        }

        return $this->database()->select('ca.*, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('jobposting_company_working_request'), 'ca')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
            ->where('ca.company_id = ' . (int)$companyID)
            ->execute('getSlaveRows');        
    }

    public function geMytFavoriteTotal($sType = null)
    {
        if (empty($sType) || !in_array($sType, ['job', 'company'])) {
            return 0;
        }

        return db()->select('COUNT(*)')->from(':jobposting_favorite')->where('item_type = \'' . $sType . '\' AND user_id = ' . Phpfox::getUserId())->executeField();
    }

    public function geMytFollowingTotal($sType = null)
    {
        if (empty($sType) || !in_array($sType, ['job', 'company'])) {
            return 0;
        }

        return db()->select('COUNT(*)')->from(':jobposting_follow')->where('item_type = \'' . $sType . '\' AND user_id = ' . Phpfox::getUserId())->executeField();
    }

    public function geMyCompaniesTotal()
    {
        return db()->select('COUNT(*)')->from(':jobposting_company')->where('is_deleted = 0 AND user_id = ' . Phpfox::getUserId())->executeField();
    }

    public function geMytAppliedJobsTotal()
    {
        return db()->select('COUNT(*)')->from(':jobposting_application')->where('user_id = ' . Phpfox::getUserId())->executeField();
    }
}