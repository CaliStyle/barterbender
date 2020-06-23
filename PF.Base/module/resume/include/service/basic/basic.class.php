<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Service_Basic_Basic extends Phpfox_Service
{
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_basicinfo');
	}
	
	public function getUserAllResume(){
		$aRows = $this -> database()
				-> select('user_id')
				-> from($this->_sTable)
				-> group('user_id')
				-> execute('getRows');
		return $aRows;
	}
	
	public function getBasicInfo($resume_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('resume_id = '.$resume_id);
		$Info = $oQuery-> execute('getRow');
		
		//Process result
		if (isset($Info['birthday']) && count($Info['birthday']) > 0)
		{
			$Info['month'] = substr($Info['birthday'], 0, 2);
            $Info['day'] = substr($Info['birthday'], 2, 2);
            $Info['year'] = substr($Info['birthday'], 4);
		}
		
		//parse serialize phone variable to array 
		if(isset($Info['phone']) && count($Info['phone'])>0)
		{
			if (Phpfox::getLib('parse.format')->isSerialized($Info['phone']))
			{
				$aPhone = unserialize($Info['phone']);
				
				$Info['phone'] = $aPhone;
			}
			else {
				$Info['phone'] = array();
			}
		}
		else {
			$Info['phone'] = array();
		}
		
		//parse serialize imessage variable to array
		if(isset($Info['imessage']) && count($Info['imessage'])>0)
		{
			if (Phpfox::getLib('parse.format')->isSerialized($Info['imessage']))
			{
				$aimessage = unserialize($Info['imessage']);
				$Info['imessage'] = $aimessage;
			}
			else {
				$Info['imessage'] = array();
			}
		}
		else {
			$Info['imessage'] = array();
		}
		
		//parse serialize email variable to array
		if(isset($Info['email']) && count($Info['email'])>0)
		{
			if (Phpfox::getLib('parse.format')->isSerialized($Info['email']))
			{
				$aemail = unserialize($Info['email']);
				$Info['email'] = $aemail;
			}
			else {
				$Info['email'] = array();
			}
		}
		else {
			$Info['email'] = array();
		}
		
		$Info['directory'] = Phpfox::getParam("core.path")."file/pic/resume/";
        $aLevel = Phpfox::getService("resume.level")->getLevels();
        $aLevelLabel = array();
        foreach ($aLevel as $i => $aItem)
        {
            $aLevelLabel[$aItem['level_id']] = $aItem['name'];
        }
        $aCountries = Phpfox::getService('core.country')->get();

        if (!empty($Info['authorized']))
		{
			if (Phpfox::getLib('parse.format')->isSerialized($Info['authorized']))
			{
				$Info['authorized'] = unserialize($Info['authorized']);

                
                foreach ($Info['authorized'] as $i => $aItem)
                {
					 
					if (isset($aItem['country_iso']))
					{
						$Info['authorized'][$i]['country_iso'] = isset($aItem['country_iso']) ? $aItem['country_iso'] : '';

						$Info['authorized'][$i]['country_child'] = isset($aItem['country_child']) ? $aItem['country_child'] : '0';
						$Info['authorized'][$i]['location'] = isset($aItem['location']) ? $aItem['location'] : '';
						$Info['authorized'][$i]['level_id'] = isset($aItem['level_id']) ? $aItem['level_id'] : 0;
						$Info['authorized'][$i]['other_level'] = isset($aItem['other_level']) ? $aItem['other_level'] : '';
						$Info['authorized'][$i]['label_country_iso'] = isset($aCountries[$Info['authorized'][$i]['country_iso']]) ? $aCountries[$Info['authorized'][$i]['country_iso']] : '';
						$Info['authorized'][$i]['label_level_id'] = isset($aLevelLabel[$Info['authorized'][$i]['level_id']]) ? $aLevelLabel[$Info['authorized'][$i]['level_id']] : '';
						$Info['authorized'][$i]['label_country_child'] = Phpfox::getService('core.country')->getChild($Info['authorized'][$i]['country_child']);

					}


                }

			}
			else {
				$Info['authorized'] = array();
			}
		}
        else
        {
            $Info['authorized'] = array();
        }
        // Get Current Image
        if (!empty($Info['image_path'])) {
            $Info['current_image'] = Phpfox::getLib('image.helper')->display([
                        'server_id' => $Info['server_id'],
                        'path' => 'core.url_pic',
                        'file' => 'resume/' . $Info['image_path'],
                        'suffix' => '_120',
                        'return_url' => true
                    ]);
        }
		// Return result					 
		return $Info;	
	}
	/**
	 * Quickly get resume necessary fields for other process
	 * @param int $iId - the input ID
	 * @return array of selected resume basic field data | false 
	 */
	public function getQuick($iId)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('resume_id, user_id, status, headline, is_published, is_completed, level_id, privacy')
				   		-> from($this->_sTable)
						-> where('resume_id = '.$iId);
		$aInfo = $oQuery-> execute('getRow');
		
		return (isset($aInfo['resume_id']) ? $aInfo : false);
	}
	
	/**
	 * Get Resumeid which is published.
	 */
	public function getResumeIdIsPublished($user_id)
	{
		$resumeId = (int) $this->database()->select('resume_id')
				-> from($this->_sTable)
				-> where('user_id='.$user_id. ' and is_published=1 and status="approved"')
				-> execute('getSlaveField');
			 return $resumeId;
	}
	
	public function getItemCount($aConds = array())
	{
		$oQuery = $this -> database()
				-> select('count(*) as count')
				-> from($this->_sTable,'rbi');
		
		if($aConds)
		{
			$oQuery->where($aConds);
		}
		
		$iCnt = (int)$oQuery-> execute('getSlaveField');
		return $iCnt;
	}
	
    public function getTotalPublishedResumes($iUserId = 0)
    {
        if ($iUserId == 0)
        {
            $iUserId = Phpfox::getUserId();
        }
        
        $iTotal = $this->database()
                ->select('COUNT(resume_id)')
                ->from($this->_sTable)
                ->where('user_id = ' . $iUserId . ' AND is_published = 1 AND status = "approved"')
                ->execute('getSlaveField');
        
        return $iTotal;
    }
    
}

?>