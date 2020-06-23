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

class JobPosting_Service_Company_Company extends Phpfox_service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_company');
        $this->_sTableAdmin = Phpfox::getT('jobposting_company_admin');
        $this->_sTableForm = Phpfox::getT('jobposting_company_form');
        $this->_sTableImage = Phpfox::getT('jobposting_company_image');
        $this->_sTableText = Phpfox::getT('jobposting_company_text');
    }
    
    public function getGeneralInfo($iId)
    {
        return $this->database()->select('*')->from($this->_sTable)->where('company_id = '.(int)$iId.' AND is_deleted = 0')->execute('getSlaveRow');
    }
	
	public function getCompanyById($iId)
	{
		return $this->database()->select('*')->from($this->_sTable)->where('company_id = '.(int)$iId)->execute('getSlaveRow');
	}
	
    /**
     * Get company
     * @param int $iId
     */
    public function getForEdit($iId)
    {
		if (Phpfox::isModule('friend'))
		{
			$this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = c.user_id AND f.friend_user_id = " . Phpfox::getUserId());					
		}		
		
		if (Phpfox::isModule('like'))
		{
			$this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'jobposting_company\' AND l.item_id = c.company_id AND l.user_id = ' . Phpfox::getUserId());
		}
		
        $aCompany = $this->database()->select('u.*,c.*, ct.description, ct.description_parsed, '.$this->getCompanyFormFields('cf'))
            ->from($this->_sTable, 'c')
			->join(Phpfox::getT('user'),'u','u.user_id = c.user_id')
            ->leftJoin($this->_sTableText, 'ct', 'ct.company_id = c.company_id')
            ->leftJoin($this->_sTableForm, 'cf', 'cf.company_id = c.company_id')
            ->where('c.company_id = '.$iId.' AND c.is_deleted = 0')
            ->execute('getSlaveRow');
        
		if(!$aCompany)
		{
			return null;
		}
        
        if (!isset($aCompany['is_friend']))
		{
			$aCompany['is_friend'] = 0;
		}
		
        #Contact info
		$aContactInfo = unserialize($aCompany['contact_info']);
		if(is_array($aContactInfo))
		{
			foreach($aContactInfo as $k => $sInfo)
			{
				$aCompany[$k] = $sInfo;
			}
		}
		
        #Industry
		$aCompany['category'] = Phpfox::getService('jobposting.category')->getCategoryData($iId);
		$aCompany['industrial_phrase'] = Phpfox::getService('jobposting.category')->getPhraseCategory($aCompany['company_id']);
		
        #Packages
        $aCompany['packages'] = Phpfox::getService('jobposting.package')->getBoughtPackages($iId);
        $aCompany['tobuy_packages'] = Phpfox::getService('jobposting.package')->getToBuyPackages($iId);
        
        #Logo
        if(!empty($aCompany['logo_path']))
        {
            $aCompany['logo_image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aCompany['logo_server_id'],
                'file' => 'jobposting/'.$aCompany['logo_path'],
                'path' => 'core.url_pic',
                'suffix' => '_120',
                'max_width' => '120',
                'max_height' => '120'				
            ));
        }
        
        #Custom field
        $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($iId);
        if(is_array($aFields) && count($aFields))
        {
            $aCompany['custom_field'] = $aFields;
        }
        
        #Admins
        $aCompany['admins'] = $this->database()->select(Phpfox::getUserField(). ', ca.*')
			->from($this->_sTableAdmin, 'ca')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
			->where('ca.company_id = '.$iId)
			->execute('getSlaveRows');
		
		//Gmap
		$aGmap = unserialize($aCompany['gmap']);
		if(is_array($aGmap))
		{
			foreach($aGmap as $k => $sGmap)
			{
				$aCompany[$k] = $sGmap;
			}
		}
		
		//Country
       	$aCompany = $this->implementsCountry($aCompany);
		
        return $aCompany;
    }
    
    /**
     * Get fields of _sTableForm
     * @param string $sT
     */
    public function getCompanyFormFields($sT = 'cf')
    {
        $aFields = array(
            'logo_path',
            'server_id as logo_server_id',
            'job_title_enable',
            'candidate_name_enable',
            'candidate_name_require',
            'candidate_photo_enable',
            'candidate_photo_require',
            'candidate_email_enable',
            'candidate_email_require',
            'candidate_telephone_enable',
            'candidate_telephone_require',
            'resume_enable'
        );
        
        $sFields = $sT.'.title as form_title, '.$sT.'.description as form_description';
        
        foreach($aFields as $sField)
        {
            $sFields .= ', '.$sT.'.'.$sField;
        }
        
        return $sFields;
    }
    
    /**
     * Get company images
     * @param int $iCompanyId
     * @param int $iLimit
     * @return array
     */
    public function getImages($iCompanyId, $iLimit = null)
    {
        $aImages = $this->database()->select('image_id, image_path, server_id')
            ->from($this->_sTableImage)
            ->where('company_id = '.$iCompanyId)
            ->order('ordering ASC')
            ->limit($iLimit)
            ->execute('getSlaveRows');
        
        if($aImages)
        {
            foreach($aImages as $k=>$aImage)
            {
                $aImages[$k]['image'] = Phpfox::getLib('image.helper')->display(array(
        				'server_id' => $aImage['server_id'],
        				'file' => 'jobposting/'.$aImage['image_path'],
        				'path' => 'core.url_pic',
        				'suffix' => '_120',
        				'max_width' => '120',
        				'max_height' => '120'				
        			)
        		);
            }
        }
        
        return $aImages;
    }
    
    /**
     * Check if user has a company
     * @param int $iUserId
     * @return bool
     */
    public function hasCompany($iUserId)
    {
        $iCompanyNumbers = $this->database()->select('COUNT(*)')->from($this->_sTable)->where('user_id = '.(int)$iUserId.' AND is_deleted = 0')->execute('getField');
        if($iCompanyNumbers < $this->getUserParam('jobposting.numbers_of_company', $iUserId))
        {
            return false;
        }
        return true;
    }
	public  function  getMaxCompany($iUserId)
	{
		return $this->getUserParam('jobposting.numbers_of_company', $iUserId);
	}
	
	private function getUserParam($sParam, $iUserId) {

		$iGroupId = $this->getUserBy('user_group_id', $iUserId);

		return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

	}
	
	private function getUserBy($sVar, $iUserId ) {

		$result = $this->_getUserInfo($iUserId);
		if (isset($result[$sVar]))
		{
			return $result[$sVar];
		}

		return false;
	}
	
	private function _getUserInfo($iUserId) {
		$aRow = $this->database()->select('u.*')
			->from(Phpfox::getT('user'), 'u')
			->where('u.user_id = ' . $iUserId)
			->execute('getRow');
		if(!$aRow) {
			return false;
		}

		$aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
		$aRow['location'] = $aRow['country_iso']; // we will improve it later to deal with cities 
		$aRow['language'] = $aRow['language_id']; 
		return $aRow;
		// $this->_aUser = $aRow;
	}
    
    /**
     * Get sponsor status
     * @param int $iId
     * @return bool
     */
    public function isSponsor($iId)
    {
        $bIsSponsor = $this->database()->select('is_sponsor')->from($this->_sTable)->where('company_id = '.(int)$iId)->execute('getField');
        if(!$bIsSponsor)
        {
            return false;
        }
        return true;
    }
    
    public function isAdmin($iId, $iUserId)
	{
		$aAdmin = $this->database()->select('*')
			->from($this->_sTableAdmin)
			->where('company_id = '.(int)$iId.' AND user_id = '.(int)$iUserId)
			->execute('getSlaveRow');
		
		if(!$aAdmin)
		{
			return false;
		}
		
		return true;
	}
	
	
	public function isAdminPermission($iId, $iUserId, $action)
	{
		//owner company
		$company = $this->getCompanyById($iId);
		if($company['user_id'] == $iUserId){
			return true;
		}
			
		$aAdmin = $this->database()->select('*')
			->from($this->_sTableAdmin)
			->where('company_id = '.(int)$iId.' AND '.$action.' = 1 AND user_id = '.(int)$iUserId)
			->execute('getSlaveRow');
		if(isset($aAdmin[$action]))
		{
			return true;
		}
		
		return false;
	}
	public function getAdminPermission($iId, $iUserId)
	{
		$aAdmin = $this->database()->select('*')
			->from($this->_sTableAdmin)
			->where('company_id = '.(int)$iId.' AND user_id = '.(int)$iUserId)
			->execute('getSlaveRow');
		
		if(!$aAdmin)
		{
			return false;
		}
		
		return $aAdmin;
	}
    
    public function getAdmins($iId)
    {
        $aRows = $this->database()->select('user_id')
            ->from($this->_sTableAdmin)
            ->where('company_id = '.(int)$iId)
            ->execute('getSlaveRows');
        
        $aAdmin = array();
        if (!empty($aRows))
        {
            foreach ($aRows as $aRow)
            {
                $aAdmin[] = $aRow['user_id'];
            }
        }
        
        return $aAdmin;
    }
    
	private function implementsCountry($aCompany){
		$aCompany['country_iso_phrase']  = Phpfox::getService('core.country')->getCountry($aCompany['country_iso']);
		$aCompany['country_child_id_phrase']  = Phpfox::getService('core.country')->getCountry($aCompany['country_child_id']);
		
		$aCompany['country_phrase'] = "";
		$aCompany['city_country_phrase'] = "";
		$aCompany['location_city_country_phrase'] = "";
		
		if(strlen($aCompany['country_child_id_phrase'])>0)
			$aCompany['country_phrase'] .= $aCompany['country_child_id_phrase']." ";
		if(strlen($aCompany['country_iso_phrase'])>0)
			$aCompany['country_phrase'] .= $aCompany['country_iso_phrase'];	
		
		if(strlen($aCompany['city'])>0){
			$aCompany['city_country_phrase'].= $aCompany['city'].", ";
		if(strlen($aCompany['country_phrase'])>0)
			$aCompany['city_country_phrase'] .= $aCompany['country_phrase'];
		}
		
		if(strlen($aCompany['location'])>0){
			$aCompany['location_city_country_phrase'].= $aCompany['location'].", ";
		}
		$aCompany['location_city_country_phrase'].=$aCompany['city_country_phrase'];
		
		$aCompany['encode_location_city_country_phrase'] = htmlentities(urlencode($aCompany['location_city_country_phrase']));
		return $aCompany;
	}

    public function setAdvSearchConditions($aVals)
    {
        // Filter keywords
        if (!empty($aVals['name']))
        {
            $this->search()->setCondition("AND ca.name LIKE '%".$aVals['name']."%'");
        }
        if (!empty($aVals['location']))
        {
            $this->search()->setCondition("AND ca.location LIKE '%".$aVals['location']."%'");
        }
		
		if(!empty($aVals['categories'])){
			
			$listcategory = trim($aVals['categories'],",");
			$aList = explode(",", $listcategory);
			if(count($aList)>=2){
				if($aList[1]>0)
					$listcategory = $aList[1];
				else {
					$listcategory = $aList[0];
				}
			}
			$this->search()->setCondition("AND 0<(select(count(*)) from ".Phpfox::getT('jobposting_category_data')." data where data.company_id = ca.company_id and data.category_id in (".$listcategory."))");
		}
    }

    public function getAdvSearchFields()
    {
        $aVals = array();

        $aVals['name'] = $this->search()->get('name');
        $aVals['location'] = $this->search()->get('location');
        $aVals['industry'] = $this->search()->get('industry');
        $aVals['company_size'] = $this->search()->get('company_size');
		$aCategory = $this->search()->get('category');
		$aVals['categories']  = "";
		
		if(isset($aCategory['search_0']))
		{
			$aValue = str_replace("search_", "", $aCategory['search_0']);
		
			$aVals['categories'].= $aValue;
			if(isset($aCategory["search_".$aValue]))
			{
				$aValue = str_replace("search_", "", $aCategory["search_".$aValue]);
				$aVals['categories'].= ",".$aValue;
			}
			
		}
        return $aVals;
    }

    public function getBlockCompany($Conds, $Order, $iLimit = 3)
    {
    	if($Conds){
			$Conds.=" and ca.is_deleted = 0 and ca.is_approved = 1 and ca.post_status = 1";
		}
		else {
			$Conds.="ca.is_deleted = 0 and ca.is_approved = 1 and ca.post_status = 1";
		}
        $oQuery = $this->database()->select('ca.*,catext.description,catext.description_parsed')
            ->from($this->_sTable, 'ca')
            ->leftJoin(Phpfox::getT('jobposting_company_text'),'catext','catext.company_id = ca.company_id');

        if ($Conds)
        {
            $oQuery->where($Conds);
        }
        if ($Order)
        {
            $oQuery->order($Order);
        }
        if ($iLimit > 0)
        {
            $oQuery->limit($iLimit);
        }
        $aRows = $oQuery->execute('getRows');

        return $this->implementFields($aRows);
    }

    private function implementFields($aRows)
    {
        foreach($aRows as $key=>$aRow){
            $aRow['industrial_phrase'] = Phpfox::getService('jobposting.category')->getPhraseCategory($aRow['company_id']);
            $aRow['description_parsed_phrase'] = strip_tags($aRow['description_parsed']);
            $aRows[$key] = $aRow;
        }
        return $aRows;
    }
	
	public function getCompany($user_id){
		$aRow = $this->database()->select('*')
		->from($this->_sTable,'ca')
		->join(PHpfox::getT('user'),'u','u.user_id = ca.user_id')
		->where('ca.user_id = '.$user_id.' AND ca.is_deleted = 0')
		->execute('getRow');
		if(isset($aRow['company_id'])){
			return $this->getForEdit($aRow['company_id']);
		}
		return "";
	}
	
	public function getCompanyIdByUserId($iUserId)
	{
		return $this->database()->select('company_id')->from($this->_sTable)->where('user_id = '.(int)$iUserId.' AND is_deleted = 0')->execute('getSlaveField');
	}

	public function searchCompanies($aConds, $sSort = 'ca.name ASC', $iPage = '', $iLimit = '')
	{
		
    	if($aConds){
    		if(!is_array($aConds))
				$aConds.=" and ca.is_deleted = 0";
			else {
				$aConds[] = " and ca.is_deleted = 0";
			}
		}
		else {
			$aConds="ca.is_deleted = 0";
		}
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'ca')
			->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
			->where($aConds)
			->execute('getSlaveField');	
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('ca.*, ' . Phpfox::getUserField())
				->from($this->_sTable, 'ca')
				->leftJoin(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
				->where($aConds)
				->limit($iPage, $iLimit, $iCnt)
				->order('ca.time_stamp desc')
				->execute('getSlaveRows');
            
            foreach ($aItems as $k => $aItem)
            {
                $aItems[$k]['paid_to_sponsor'] = Phpfox::getService('jobposting.transaction')->isPaidToSponsor($aItem['company_id']);
                $aItems[$k]['packages'] = Phpfox::getService('jobposting.package')->getBoughtPackages($aItem['company_id'], true);
            }
		}
		
		return array($iCnt, $aItems);
	}	
	
	public function searchEmployees($aConds, $iPage = '', $iLimit = '')
	{
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('user_field'), 'uf')
			->leftJoin(Phpfox::getT('user'), 'u', 'uf.user_id = u.user_id')
			->where($aConds)
			->execute('getSlaveField');	
		$aItems = array();
		if ($iCnt)
		{		
			$aItems = $this->database()->select('u.*')
				->from(Phpfox::getT('user_field'), 'uf')
				->leftJoin(Phpfox::getT('user'), 'u', 'uf.user_id = u.user_id')
				->where($aConds)
				->limit($iPage, $iLimit, $iCnt)
				->execute('getSlaveRows');
		}
		
		return array($iCnt, $aItems);
	}	
	
	public function getPendingTotal()
	{
		return (int)$this->database()->select('COUNT(*)')
			->from($this->_sTable)
			->where('is_approved = 0 and is_deleted = 0')
			->execute('getSlaveField');
	}

	public function iCounttotalfollow($company_id,$type = 'company'){
		$iCnt = $this->database()->select('count(follow_id)')
		->from(Phpfox::getT('jobposting_follow'))
		->where('item_type = "'.$type.'" and item_id = '.$company_id)
		->execute('getField');
		return $iCnt;
	}
	public function getCompaniesHasCreateJob($iUserId)
	{

		$aCompanies = $this->database()->select('c.*')
			->from($this->_sTable, 'c')
			->where('c.is_deleted = 0 AND c.is_approved =1 AND c.post_status = 1 ORDER BY c.time_stamp desc' )
			->execute('getSlaveRows');
		if(isset($aCompanies) && count($aCompanies)>0)
		{
			$arr = array();
			foreach($aCompanies as $ikey => $aCompany)
			{
				if(Phpfox::getService('jobposting.company')->isAdminPermission($aCompany['company_id'], $iUserId, 'add_job') )
				{
					$arr[] = $aCompany	;
				}
			}
			return $arr;
		}
		
		
		return null;
		
		
		
	}
	
}

?>