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
class Resume_Service_Basic_Process extends Phpfox_Service
{
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_basicinfo');
	}
	
	public function add($aVals)
	{

        if (isset($aVals['custom'])) {
            $aCustomFields = Phpfox::getService('resume.custom')->getFields(0);
            foreach ($aCustomFields as $aCustomField) {
                if (empty($aVals['custom'][$aCustomField['field_id']]) && $aCustomField['is_required']) {
                    return Phpfox_Error::set(_p('the_field_field_is_required', array('field' => _p($aCustomField['phrase_var_name']))));
                }
            }
        }
		$birthday = Phpfox::getService('user')->buildAge($aVals['day'],$aVals['month'], $aVals['year']);
		$birthday_search = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['month'], $aVals['day'], $aVals['year']);
		
		$oFilter = Phpfox::getLib('parse.input');
		
		$support_getInfoProfile = true;
		$aInfoUser = Phpfox::getService('resume')->getExtraInfo();
		if(isset($aVals['linkedin']))
		{
			if($support_getInfoProfile){
				
				$aVals['gender'] = $aInfoUser['gender'];
				$aVals['marital_status'] = $aInfoUser['marital_status'];
				$aVals['full_name'] = $aInfoUser['full_name'];
				$birthday = Phpfox::getService('user')->buildAge($aInfoUser['day'],$aInfoUser['month'],$aInfoUser['year']);
				$birthday_search = Phpfox::getLib('date')->mktime(0, 0, 0, $aInfoUser['month'], $aInfoUser['day'], $aInfoUser['year']);
			}
			else
			{
				$aInfoUser = Phpfox::getService('resume')->getExtraInfo();
				if(!isset($aVals['gender']) || $aVals['gender']==0)
					$aVals['gender'] = $aInfoUser['gender'];
				if(!isset($aVals['marital_status']))
					$aVals['marital_status'] = $aInfoUser['marital_status'];
				if(!isset($aVals['full_name']))
					$aVals['full_name'] = $aInfoUser['full_name'];
				if(!$aVals['day'] && !$aVals['year'] && !$aVals['month'])
				{
					$birthday = Phpfox::getService('user')->buildAge($aInfoUser['day'],$aInfoUser['month'],$aInfoUser['year']);
					$birthday_search = Phpfox::getLib('date')->mktime(0, 0, 0, $aInfoUser['month'], $aInfoUser['day'], $aInfoUser['year']);
				}
			}
		}
		
		$aSql = array(
            'city' => isset($aVals['city'])?$aVals['city']:"",
            'zip_code' =>isset($aVals['zip_code'])?$aVals['zip_code']:"",
			'marital_status' => $aVals['marital_status'],
            'privacy' => isset($aVals['privacy'])?$aVals['privacy']:1,
			'full_name' => $oFilter->clean($aVals['full_name']),
			'birthday' => $birthday,
			'birthday_search' => $birthday_search,
			'gender' => $aVals['gender'],
			'user_id' => Phpfox::getUserId(),
			'time_stamp' => PHPFOX_TIME,
			'time_update' => PHPFOX_TIME,
			'country_iso' => $aInfoUser['country_iso'],
			'country_child_id' => $aInfoUser['country_child_id'],
			'linkedin' => isset($aVals['linkedin'])?1:0,
            'is_synchronize' => isset($aVals['is_synchronize']) ? 1 : 0,
            'display_date_of_birth' => isset($aVals['display_date_of_birth']) ? 1 : 0,
            'display_gender' => isset($aVals['display_gender']) ? 1 : 0,
            'display_marital_status' => isset($aVals['display_marital_status']) ? 1 : 0
		);
		
		if(count($aVals['emailaddress'])>0)
		{
			foreach($aVals['emailaddress'] as $key=>$email)
			{
				if($email && !Phpfox::getService('resume.process')->check_email($email))
				{
					return Phpfox_Error::set(_p('resume.email_address_is_invalid'));
				}
				if(empty($email))
				{
					unset($aVals['emailaddress'][$key]);
				}
			}
			if(count($aVals['emailaddress'])>0)
				$aSql['email'] = serialize($aVals['emailaddress']);
			else {
				$aSql['email'] = "";
			}
		}
		else {
			$aSql['email'] = "";
		}

		if(count($aVals['homepage'])>0)
		{
			$homestyle = array();
			foreach($aVals['homepage'] as $key=>$homepage)
			{
				if(!empty($homepage))
				{
					$homestyle[$key]['text'] = $homepage;
					$homestyle[$key]['type'] = $aVals['homepagestyle'][$key];
				}
			}
			if(count($homestyle)>0)
			{
				$aSql['imessage'] = serialize($homestyle);
			}
		}

		if(count($aVals['phone'])>0)
		{
			$phonestyle = array();
			foreach($aVals['phone'] as $key=>$phone)
			{
				if(!empty($phone))
				{
					$phonestyle[$key]['text'] = $phone;
					$phonestyle[$key]['type'] = $aVals['phonestyle'][$key];
				}
			}
			if(count($phonestyle)>0)
			{
				$aSql['phone'] = serialize($phonestyle);
			}
		}
        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aSql['image_path'] = $aFile['path'];
                $aSql['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }
		$iId = $this->database()->insert($this->_sTable,$aSql);

		if(isset($aVals['picture_url']) && $aVals['picture_url']!="")
		{
			$pathimage = Phpfox::getParam('core.dir_pic')."resume/";
			$p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'resume' . PHPFOX_DS;
            if (!is_dir($p)) {
				if (!@mkdir($p, 0777, 1)) {
				}
            }
			$sImage = $aVals['picture_url'];
			$sImageLocation = Phpfox::getLib('file')->getBuiltDir(Phpfox::getParam('core.dir_pic')."resume/"). md5($iId.'resume') . '%s.jpg';
			$sFileName = str_replace(Phpfox::getParam('core.dir_pic')."resume/", "", $sImageLocation);
			$oImage = Phpfox::getLib('request')->send($sImage, array(), 'GET');
	        $sTempImage = 'resume_temporal_image_'.$iId;
			
	        Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);

			$oImage1 = Phpfox::getLib('image');

			@copy(PHPFOX_DIR_CACHE . $sTempImage, sprintf($sImageLocation, '_temp'));
			$temp = sprintf($sImageLocation, '_temp');
			$oImage1->createThumbnail($temp, sprintf($sImageLocation, ''),80,80);
			
			$oImage1->createThumbnail($temp, sprintf($sImageLocation, '_50'),50,50);
			$oImage1->createThumbnail($temp, sprintf($sImageLocation, '_120'),120,120);
			$oImage1->createThumbnail($temp, sprintf($sImageLocation, '_200'),200,200);

	        unlink(PHPFOX_DIR_CACHE . $sTempImage);
			
			$this->database()->update($this->_sTable,array('image_path'=>$sFileName,'server_id'=>Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')),'resume_id='.$iId);
		}
		
		// If is synchronize, update profile info.
        if (isset($aVals['is_synchronize']))
        {
            $aInsert = array();
            $aInsert['birthday_search'] = (Phpfox::getUserParam('user.can_edit_dob') && isset($aVals['day']) && isset($aVals['month']) && isset($aVals['year']) ? Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['month'], $aVals['day'], $aVals['year']) : 0);
            $aInsert['birthday'] = date('mdY', $aInsert['birthday_search']);
            if (Phpfox::getUserParam('user.can_edit_gender_setting') && isset($aVals['gender']))
			{
				$aInsert['gender'] = (int) $aVals['gender'];
			}
            $aInsert['full_name'] = $oFilter->clean($aVals['full_name'], 255);
            
            if (isset($aVals['city']))
			{
				Phpfox::getService('user.field.process')->update(Phpfox::getUserId(), 'city_location', (empty($aVals['city']) ? null : Phpfox::getLib('parse.input')->clean($aVals['city'], 100)));
			}

			if (isset($aVals['zip_code']))
			{
				Phpfox::getService('user.field.process')->update(Phpfox::getUserId(), 'postal_code', (empty($aVals['zip_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['zip_code'], 20)));
			}
            
            $this->database()->update(Phpfox::getT('user'), $aInsert, 'user_id = ' . (int) Phpfox::getUserId());
            
            $iRelation = 1;
            if ($aVals['marital_status'] == 'single')
            {
                $iRelation = 2;
            }
            elseif ($aVals['marital_status'] == 'married')
            {
                $iRelation = 4;
            }
            
            // For marital status.
            Phpfox::getService('custom.relation.process')->updateRelationship($iRelation, null);
        }
        if (isset($aVals['custom']))
		{
			foreach($aVals['custom'] as $iFieldId => $sValue)
			{
				$aInfoCustom = Phpfox::getService('resume.custom')->getCustomFieldsByFieldId($iFieldId);

				$this->database()->insert(Phpfox::getT('resume_custom_value'), array(
						'resume_id' => $iId,
						'field_id' => $iFieldId,
						'value' => is_array($sValue) ? json_encode($sValue) : $sValue
					)
				);
			}
		}

		
		$this->database()->updateCounter('user_field', 'total_resume', 'user_id', Phpfox::getUserId());
		$this->synchronisebyUserId(Phpfox::getUserId());
		
		(($sPlugin = Phpfox_Plugin::get('resume.service_basic_process_add_end')) ? eval($sPlugin) : false);
		
		return $iId;
	}

	
    public function updateUserProfile($iUserId, $aVals, $aSpecial = array(), $bIsAccount = false)
    {
        if (isset($aVals['full_name']) && Phpfox::getParam('user.validate_full_name'))
		{
			if (!Phpfox::getLib('validator')->check($aVals['full_name'], array('html', 'url')))
			{
				return Phpfox_Error::set(_p('user.not_a_valid_name'));
			}
			if (Phpfox::getParam('user.maximum_length_for_full_name') > 0 && strlen($aVals['full_name']) > Phpfox::getParam('user.maximum_length_for_full_name'))
			{
				$aChange = array('iMax' => Phpfox::getParam('user.maximum_length_for_full_name'));
				$sPhrase = Phpfox::getParam('user.display_or_full_name') == 'full_name' ? _p('user.please_shorten_full_name', $aChange) : _p('user.please_shorten_display_name', $aChange);
				return Phpfox_Error::set($sPhrase);
			}
		}
        
        if (!$bIsAccount && (empty($aVals['day']) || empty($aVals['month']) || empty($aVals['year'])))
		{
			return Phpfox_Error::set(_p('user.please_enter_your_date_of_birth'));
		}
    }
    
	public function update($aVals)
	{
        if (isset($aVals['custom'])) {
            $aCustomFields = Phpfox::getService('resume.custom')->getFields($aVals['resume_id']);

            foreach ($aCustomFields as $aCustomField) {
                if (empty($aVals['custom'][$aCustomField['field_id']]) && $aCustomField['is_required']) {
                    return Phpfox_Error::set(_p('the_field_field_is_required', array('field' => _p($aCustomField['phrase_var_name']))));
                }
            }
        }
        if (empty($aVals['day']) || empty($aVals['month']) || empty($aVals['year']))
		{
			return Phpfox_Error::set(_p('user.please_enter_your_date_of_birth'));
		}
		$birthday = Phpfox::getService('user')->buildAge($aVals['day'], $aVals['month'], $aVals['year']);
		$oFilter = Phpfox::getLib('parse.input');
        
		$aInfoUser = Phpfox::getService('resume')->getExtraInfo();
	
		$aSql = array(
            'city' => $aVals['city'],
            'zip_code' => $aVals['zip_code'],
			'marital_status' => $aVals['marital_status'],
            'privacy' => isset($aVals['privacy'])?$aVals['privacy']:1,
			'full_name' => $oFilter->clean($aVals['full_name']),
			'birthday' => $birthday,
			'birthday_search' => (defined('PHPFOX_INSTALLER') || (!defined('PHPFOX_INSTALLER') && Phpfox::getParam('core.registration_enable_dob')) ? Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['month'], $aVals['day'], $aVals['year']) : 0),
			'gender' => $aVals['gender'],
			'time_update' => PHPFOX_TIME,
			'country_iso' => $aInfoUser['country_iso'],
			'country_child_id' => $aInfoUser['country_child_id'],
            'is_synchronize' => isset($aVals['is_synchronize']) ? 1 : 0,
            'display_date_of_birth' => isset($aVals['display_date_of_birth']) ? 1 : 0,
            'display_gender' => isset($aVals['display_gender']) ? 1 : 0,
            'display_marital_status' => isset($aVals['display_marital_status']) ? 1 : 0
		);
		
		
		if(count($aVals['emailaddress'])>0)
		{
			foreach($aVals['emailaddress'] as $key=>$email)
			{
				if($email && !Phpfox::getService('resume.process')->check_email($email))
				{
					return Phpfox_Error::set(_p('resume.email_address_is_invalid'));
				}
				if(empty($email))
				{
					unset($aVals['emailaddress'][$key]);
				}
			}
			if(count($aVals['emailaddress'])>0)
				$aSql['email'] = serialize($aVals['emailaddress']);
			else {
				$aSql['email'] = "";
			}
		}
		else 
        {
			$aSql['email'] = "";
		}
		
		if(count($aVals['homepage'])>0 && $aVals['homepage'][0]!="")
		{
			$homestyle = array();
			foreach($aVals['homepage'] as $key=>$homepage)
			{
				if(!empty($homepage))
				{
					$homestyle[$key]['text'] = $homepage;
					$homestyle[$key]['type'] = $aVals['homepagestyle'][$key];
				}
			}
			if(count($homestyle)>0)
			{
				$aSql['imessage'] = serialize($homestyle);
			}
		}
		else 
        {
			$aSql['imessage'] = "";
		}

		if(count($aVals['phone'])>0 && $aVals['phone'][0]!="")
		{
			$phonestyle = array();
			foreach($aVals['phone'] as $key=>$phone)
			{
				if(!empty($phone))
				{
					$phonestyle[$key]['text'] = $phone;
					$phonestyle[$key]['type'] = $aVals['phonestyle'][$key];
				}
			}
			if(count($phonestyle)>0)
			{
				$aSql['phone'] = serialize($phonestyle);
			}
		}
		else{
			$aSql['phone'] = "";	
		}
        if (!empty($aVals['temp_file']) || !empty($aVals['remove_photo'])) {
		    if ($this->deleteImage($aVals['resume_id'])) {
                $aSql['image_path'] = null;
                $aSql['server_id'] = 0;
            }
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aSql['image_path'] = $aFile['path'];
                $aSql['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }

		$iId = $this->database()->update($this->_sTable, $aSql, 'resume_id='.$aVals['resume_id']);
		
        // If is synchronize, update profile info.
        if (isset($aVals['is_synchronize']))
        {
            $aInsert = array();
			$aResumesInfo = PHpfox::getService('resume.basic')->getBasicInfo($aVals['resume_id']);
            $user_id = $aResumesInfo['user_id'];
            $aInsert['birthday_search'] = (Phpfox::getUserParam('user.can_edit_dob') && isset($aVals['day']) && isset($aVals['month']) && isset($aVals['year']) ? Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['month'], $aVals['day'], $aVals['year']) : 0);
            $aInsert['birthday'] = date('mdY', $aInsert['birthday_search']);
            if (Phpfox::getUserParam('user.can_edit_gender_setting') && isset($aVals['gender']))
			{
				$aInsert['gender'] = (int) $aVals['gender'];
			}
            $aInsert['full_name'] = $oFilter->clean($aVals['full_name'], 255);
             
            $this->database()->update(Phpfox::getT('user'), $aInsert, 'user_id = ' . (int) $user_id);
            
            if (isset($aVals['city']))
			{
				Phpfox::getService('user.field.process')->update($user_id, 'city_location', (empty($aVals['city']) ? null : Phpfox::getLib('parse.input')->clean($aVals['city'], 100)));
			}

			if (isset($aVals['zip_code']))
			{
				Phpfox::getService('user.field.process')->update($user_id, 'postal_code', (empty($aVals['zip_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['zip_code'], 20)));
			}
            
            $iRelation = 1;
            if ($aVals['marital_status'] == 'single')
            {
                $iRelation = 2;
            }
            elseif ($aVals['marital_status'] == 'married')
            {
                $iRelation = 4;
            }
            
            // For marital status.
            Phpfox::getService('custom.relation.process')->updateRelationship($iRelation, null, $user_id);
        }
        if (isset($aVals['custom'] ))
		{
			foreach($aVals['custom'] as $iFieldId => $sValue)
			{
				$aInfoCustom = Phpfox::getService('resume.custom')->getCustomFieldsByFieldId($iFieldId);

				if(!Phpfox::getService('resume.custom')->getFieldsByResumeIdAndFieldId($aVals['resume_id'],$iFieldId))
				{
					$this->database()->insert(Phpfox::getT('resume_custom_value'), array(
							'resume_id' => $aVals['resume_id'],
							'field_id' => $iFieldId,
							'value' => is_array($sValue) ? json_encode($sValue, JSON_UNESCAPED_UNICODE ) : $sValue
						)
					);
				}
				else
				{
					$this->database()->update(Phpfox::getT('resume_custom_value'), array(
						'value' => is_array($sValue) ? json_encode($sValue) : $sValue
					),
						'resume_id = ' . $aVals['resume_id'] . ' AND field_id = ' . $iFieldId
					);
				}
			}
		}

		$aResume = Phpfox::getService('resume.basic')->getBasicInfo($aVals['resume_id']);
		if(isset($aVals['is_synchronize']) && isset($aResume['user_id']))
		{
			$this->synchronisebyUserId($aResume['user_id']);
		}		
		return $iId;
	}

	public function updatePositionSection($resume_id,$position)
	{
		$aRow = $this->database()->select('position_section')
			->from($this->_sTable)
			->where('resume_id='.$resume_id)
			->execute('getSlaveRow');
		if(isset($aRow['position_section']))
		{
			if($aRow['position_section']<$position)
			{
				$this->database()->update($this->_sTable,array('position_section'=>$position),'resume_id='.$resume_id);
				return $position;
			}
			return $aRow['position_section'];
		}
		return 1;
	} 

	/**
	 * Delete basicinfo related to resume
	 * @param int $iId - the id of the resume need to be deleted
	 * @return true 
	 */
	public function delete($iId)
	{
		$aResume = Phpfox::getService('resume.basic')->getBasicInfo($iId);
		if($aResume)
		{
		    $this->deleteImage($iId);
			$this->database()->delete($this->_sTable, 'resume_id = ' . (int) $iId);
			$this->database()->updateCounter('user_field', 'total_resume', 'user_id', $aResume['user_id'], TRUE);
			return true;
		}
		return false;
	}

    public function deleteImage($iResumeId)
    {
        if (empty($iResumeId)) {
            return false;
        }
        $aInfo = db()-> select('*')
                -> from($this->_sTable)
                -> where('resume_id = '.$iResumeId)
                -> execute('getRow');
        if(empty($aInfo)) {
            return false;
        }
        if (empty($aInfo['image_path'])) {
            return true;
        }
        $aParams = Phpfox::getService('resume.callback')->getUploadParams();
        $aParams['type'] = 'resume';
        $aParams['path'] = $aInfo['image_path'];
        $aParams['user_id'] = $aInfo['user_id'];
        $aParams['update_space'] = false;
        $aParams['server_id'] = $aInfo['server_id'];

        return Phpfox::getService('user.file')->remove($aParams);
    }
	public function updateLocation($aVals){
		$this->synchronisebyUserId(Phpfox::getUserId());
	}
	
	public function updateFullName($aVals)
    {
        if (isset($aVals['full_name']) && $aVals['full_name'] != "")
        {
            $aUpdates['full_name'] = $aVals['full_name'];
            
            $oSetting = Phpfox::getService("resume.setting");
            $aPers = $oSetting->getAllPermissions();
            
            $this->database()->update(Phpfox::getT('resume_basicinfo'), $aUpdates, 'user_id = ' . Phpfox::getUserId() . ($aPers['get_basic_information'] ? '' : ' AND is_synchronize = 1'));
        }
    }
	
	public function getUserInfo($user_id){
		$aUser = Phpfox::getLib('database')->select('u.*, p.photo_id as cover_photo_exists, user_space.*, user_field.*, user_activity.*, ls.user_id AS is_online, ts.style_id AS designer_style_id, ts.folder AS designer_style_folder, t.folder AS designer_theme_folder, t.total_column, ts.l_width, ts.c_width, ts.r_width, t.parent_id AS theme_parent_id, ug.prefix, ug.suffix, ug.icon_ext, ug.title')
			->from(Phpfox::getT('user'), 'u')
			->join(Phpfox::getT('user_group'), 'ug', 'ug.user_group_id = u.user_group_id')
			->join(Phpfox::getT('user_space'), 'user_space', 'user_space.user_id = u.user_id')
			->join(Phpfox::getT('user_field'), 'user_field', 'user_field.user_id = u.user_id')
			->join(Phpfox::getT('user_activity'), 'user_activity', 'user_activity.user_id = u.user_id')
			->leftJoin(Phpfox::getT('theme_style'), 'ts', 'ts.style_id = user_field.designer_style_id AND ts.is_active = 1')
			->leftJoin(Phpfox::getT('theme'), 't', 't.theme_id = ts.theme_id')
			->leftJoin(Phpfox::getT('log_session'), 'ls', 'ls.user_id = u.user_id AND ls.im_hide = 0')
            ->leftJoin(Phpfox::getT('photo'), 'p', 'p.photo_id = user_field.cover_photo')
			->where("u.user_id = " . (int) $user_id)
			->execute('getSlaveRow');
		return $aUser;
	}
	
	public function synchronisebyUserId($user_id){
			$aUser = $this->getUserInfo($user_id);
			if($aUser)
			{
				$aUpdates = array();
				$aUpdates['full_name'] = $aUser['full_name'];
				$aUpdates['gender'] = $aUser['gender'];
				$aUpdates['birthday'] = $aUser['birthday'];
				$aUpdates['birthday_search'] = $aUser['birthday_search'];
				$aUpdates['country_iso'] = isset($aUser['country_iso'])?$aUser['country_iso']:"";
				$aUpdates['country_child_id'] = isset($aUser['country_child_id'])?$aUser['country_child_id']:"";
				$aUpdates['city'] = isset($aUser['city_location'])?$aUser['city_location']:"";
				$aUpdates['zip_code'] = isset($aUser['postal_code'])?$aUser['postal_code']:"";
				
				$aRelation = Phpfox::getService('custom.relation')->getLatestForUser($user_id, null, true);		
				$aRelationStatus = array(
					'2' => 'single',
					'4' => 'married'
				);
			
				if(isset($aRelation['relation_id']) && isset($aRelationStatus[$aRelation['relation_id']]))
				{
					$aUpdates['marital_status'] = $aRelationStatus[$aRelation['relation_id']];
				}	
				else
				{
					$aUpdates['marital_status'] = 'other';
				}	
				
                $oSetting = Phpfox::getService("resume.setting");
                $aPers = $oSetting->getAllPermissions();
            
				$this->database()->update(Phpfox::getT('resume_basicinfo'),$aUpdates,'user_id = ' . $user_id . ($aPers['get_basic_information'] ? '' : ' AND is_synchronize = 1'));
			}
	}
	
	public function synchronise(){
		
		$aResume = Phpfox::getService('resume.basic')->getUserAllResume();
		
		foreach($aResume as $Resume){
			$this->synchronisebyUserId($Resume['user_id']);
		}
	}
    
    
	public function updateShowInProfileInfo($iResumeId, $iShowInProfile)
    {
        $this->database()->update(Phpfox::getT('resume_basicinfo'), array('is_show_in_profile' => 0), '1=1');
        return $this->database()->update(Phpfox::getT('resume_basicinfo'), array('is_show_in_profile' => $iShowInProfile), 'resume_id = ' . (int) $iResumeId);
    }
}

?>