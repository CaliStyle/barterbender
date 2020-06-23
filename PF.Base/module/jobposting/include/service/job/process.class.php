<?php

defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Service_Job_Process extends Phpfox_service {
	
	private $_bHasLogo = false;
    private $_aSize = array(50, 120, 150, 200, 240);
    private $_aSuffix = array('', '_50', '_120', '_150', '_200', '_240');
    private $_aType = array('jpg', 'gif', 'png');

	public function __construct()
	{
		$this->_sTable = Phpfox::getT('jobposting_job');
	}
	
	public function add($aVals)
	{
		(($sPlugin = Phpfox_Plugin::get('jobposting.service_process__start')) ? eval($sPlugin) : false);
		$oFilter = Phpfox::getLib('parse.input');		
		
		if (!isset($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		$sTitle = $oFilter->clean($aVals['title'], 255);		
		$time_expire = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['time_expire_month'], $aVals['time_expire_day'], $aVals['time_expire_year']);
		$time_expire =  Phpfox::getService('jobposting.helper')->convertFromUserTimeZone($time_expire);
		
		$bHasAttachments = (!empty($aVals['attachment']));						
		$aInsert = array(
			'user_id' => Phpfox::getUserId(),
			'company_id' => $aVals['company_id'],
			'education_prefer' => $oFilter->clean($aVals['education_prefer']),
			'working_place' => $oFilter->clean($aVals['working_place']),
			'country_iso' => !empty($aVals['country_iso']) ? $aVals['country_iso'] : Phpfox::getUserBy('country_iso'),
            'country_child_id' => !empty($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0,
            'city' => !empty($aVals['city']) ? $oFilter->clean($aVals['city'], 255) : null,
            'postal_code' => !empty($aVals['postal_code']) ? $oFilter->clean($aVals['postal_code'], 20) : null,
            'gmap' => serialize($aVals['gmap']),
			'location' => !empty($aVals['location'])?$aVals['location']:'',
			'working_time' => $aVals['working_time'],
			'language_prefer' => $oFilter->clean($aVals['language_prefer']),
			'title' => $sTitle,
			'post_status' => 0,
            'is_approved' => 1,
			'time_stamp' => PHPFOX_TIME,
			'time_update' => PHPFOX_TIME,
			'time_expire' => $time_expire,
			'total_attachment' => ($bHasAttachments ? Phpfox::getService('attachment')->getCount($aVals['attachment']) : 0),
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
		);		
		
		if (Phpfox::getUserParam('jobposting.approved_job_before_displayed'))
		{
			$aInsert['is_approved'] = '0';
		}
		else
        {
            $aInsert['is_approved'] = '1';
        }

		(($sPlugin = Phpfox_Plugin::get('jobposting.service_process_add_start')) ? eval($sPlugin) : false);

		$iId = $this->database()->insert(Phpfox::getT('jobposting_job'), $aInsert);	
		
		#Custom fields for company
        if(isset($aVals['custom'])){
        	Phpfox::getService('jobposting.custom.process')->addValue($aVals['custom'], $iId, 2);
        } 

        // category Data
		if(isset($aVals['category'])) {
			$this->updateCategoryData($aVals['category'], $iId);
		}
				
		if ($bHasAttachments)
		{
			Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
		}	
		
		$this->database()->insert(Phpfox::getT('jobposting_job_text'), array(
				'job_id' => $iId,
				'description' => $oFilter->clean($aVals['description']),
				'description_parsed' => $oFilter->prepare($aVals['description']),
				'skills' => $oFilter->clean($aVals['skills']),
				'skills_parsed' => $oFilter->prepare($aVals['skills'])
			)
		);
        
        Phpfox::getService('jobposting.process')->sendEmail('add', 'job', $iId, $aInsert['user_id']);

        if ($aInsert['is_approved']) {
            $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($aInsert['company_id']);
            $aIndustries = Phpfox::getService('jobposting.category')->getCategoryIds($aCompany['company_id']);

            $aSubcribers = Phpfox_Database::instance()->select('*')->from(Phpfox::getT('jobposting_subscribe'))
                ->where('keywords LIKE \'%'.$aInsert['title'].'%\' 
                        OR company LIKE \'%'.$oFilter->clean($aCompany['name']).'%\' 
                        OR location LIKE \'%'.$aInsert['location'].'%\'
                        OR working_place LIKE \'%'.$aInsert['working_place'].'%\'
                        OR education_prefer LIKE \'%'.$aInsert['education_prefer'].'%\'
                        OR language_prefer LIKE \'%'.$aInsert['language_prefer'].'%\'
                        OR industry IN ('.$aIndustries.')
                        OR industry_child IN ('.$aIndustries.')
                        OR time_expire >= '.$aInsert['time_expire'])
                ->order('time_stamp DESC')
                ->execute('getRows');

            foreach ($aSubcribers as $aSubcriber)
            {

                $sTitle = Phpfox::getService('jobposting')->getItemTitle('job', $iId);
                $sLink = Phpfox::getLib('url')->permalink('jobposting', $iId, $sTitle);
                $unsubscribeLink = Phpfox_Url::instance()->makeUrl('jobposting.unsubscribe-job', ['subscribe_code' => $aSubcriber['subscribe_code']]);
                $sSubject = _p('subcriber_new_job_email_subject');
                $sMessage = _p('subcriber_new_job_email_message_replacement', array(
                    'title' => $sTitle,
                    'link' => '<a href="'.$sLink.'">'. $sLink . '</a>',
                    'unsubscribe_link' => '<a href="'.$unsubscribeLink.'">'. $unsubscribeLink . '</a>'
                ));

                Phpfox::getLib('mail')->to($aSubcriber['user_id'])->subject($sSubject)->message($sMessage)->send();
            }
        }
        
        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            Phpfox::getService('tag.process')->add('jobposting_job', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }

        (($sPlugin = Phpfox_Plugin::get('jobposting.service_process__end')) ? eval($sPlugin) : false);	
		(($sPlugin = Phpfox_Plugin::get('jobposting.service_job_process_add_end')) ? eval($sPlugin) : false);	
		
		return $iId;
	}

	public function updateCategoryData($aCats, $id)
    {
        $sTableCategoryData = Phpfox::getT('jobposting_job_category_data');

        #Remove old data
        $this->database()->delete($sTableCategoryData, 'job_id = '.$id);

        #Add new data
        foreach ($aCats as $k1 => $aCat)
        {
            foreach ($aCat as $k2 => $v2)
            {
                if (!empty($v2))
                {
                    $this->database()->insert($sTableCategoryData, array(
                        'job_id' => $id,
                        'no' => $k1,
                        'category_id' => $v2
                    ));
                }
            }
        }
    }	

	public function update($aVals)
	{
				
		(($sPlugin = Phpfox_Plugin::get('jobposting.service_process__start')) ? eval($sPlugin) : false);
		$oFilter = Phpfox::getLib('parse.input');		
		
		if (!isset($aVals['privacy']))
		{
			$aVals['privacy'] = 0;
		}
		
		$sTitle = $oFilter->clean($aVals['title'], 255);	
		$bHasAttachments = (!empty($aVals['attachment']));	
		if ($bHasAttachments)
		{
			Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $aVals['job_id']);
		}
				
		$time_expire = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['time_expire_month'], $aVals['time_expire_day'], $aVals['time_expire_year']);
		$time_expire =  Phpfox::getService('jobposting.helper')->convertFromUserTimeZone($time_expire);				
		$aUpdate= array(
			'company_id' => $aVals['company_id'],
			'education_prefer' => $oFilter->clean($aVals['education_prefer']),
			'working_place' => $oFilter->clean($aVals['working_place']),
			'country_iso' => !empty($aVals['country_iso']) ? $aVals['country_iso'] : Phpfox::getUserBy('country_iso'),
            'country_child_id' => !empty($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0,
            'city' => !empty($aVals['city']) ? $oFilter->clean($aVals['city'], 255) : null,
            'postal_code' => !empty($aVals['postal_code']) ? $oFilter->clean($aVals['postal_code'], 20) : null,
            'gmap' => serialize($aVals['gmap']),
			
			
			'working_time' => $aVals['working_time'],
			'language_prefer' => $oFilter->clean($aVals['language_prefer']),
			'title' => $sTitle,
			'time_update' => PHPFOX_TIME,
			'time_stamp' => PHPFOX_TIME,
			'time_expire' => $time_expire,
			'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($aVals['job_id'], 'jobposting') : '0'),
			'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
			'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
		);		
		
		(($sPlugin = Phpfox_Plugin::get('jobposting.service_process_add_start')) ? eval($sPlugin) : false);

		$this->database()->update(Phpfox::getT('jobposting_job'), $aUpdate,'job_id = '.$aVals['job_id']);		
		
		#Custom fields for company
        if(isset($aVals['custom'])){
        	Phpfox::getService('jobposting.custom.process')->updateValue($aVals['custom'], $aVals['job_id'], 2);
        } 
		
        // category Data
        $this->updateCategoryData($aVals['category'], $aVals['job_id']);		
		
		$aRowstext = $this->database()->select('*')
			->from(Phpfox::getT('jobposting_job_text'))
			->where('job_id = '.$aVals['job_id'])
			->execute('getSlaveRows');
		
		if(count($aRowstext)>0)
		{
			$this->database()->update(Phpfox::getT('jobposting_job_text'), array(
				'description' => $oFilter->clean($aVals['description']),
				'description_parsed' => $oFilter->prepare($aVals['description']),
				'skills' => $oFilter->clean($aVals['skills']),
				'skills_parsed' => $oFilter->prepare($aVals['skills'])
			),'job_id = '.$aVals['job_id']
			);	
			
		}
		else {
			$this->database()->insert(Phpfox::getT('jobposting_job_text'), array(
				'job_id' => $aVals['job_id'],
				'description' => $oFilter->clean($aVals['description']),
				'description_parsed' => $oFilter->prepare($aVals['description']),
				'skills' => $oFilter->clean($aVals['skills']),
				'skills_parsed' => $oFilter->prepare($aVals['skills'])
			)
			);	
		}

        $iId = $aVals['job_id'];
        $aJob = $this->database()->select('job.job_id, job.user_id')
            ->from(Phpfox::getT('jobposting_job'),'job')
            ->where('job.job_id = '.$iId)
            ->execute('getRow');

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']) && isset($aJob['job_id']))
        {
            Phpfox::getService('tag.process')->update('jobposting_job', $iId, $aJob['user_id'], $aVals['description'], true);
        }

		(($sPlugin = Phpfox_Plugin::get('jobposting.service_process__end')) ? eval($sPlugin) : false);	
		
	}

	public function unsubscribeJob($subscribeCode) {
	    if(empty($subscribeCode)) {
	        return false;
        }
        $table = Phpfox::getT('jobposting_subscribe');
        $exist = db()->select('subscribe_id')
                    ->from($table)
                    ->where('subscribe_code = "'. $subscribeCode .'"')
                    ->execute('getSlaveField');
        if(empty($exist)) {
            return false;
        }

        return db()->delete(Phpfox::getT('jobposting_subscribe'), 'subscribe_code = "'. $subscribeCode . '"');
    }

	public function addSubscribe($aVals){
		$aInserts = array();
		$oFilter = Phpfox::getLib('parse.input');	
		$user_id = Phpfox::getUserId();
		$aInserts['user_id'] = $user_id;
		$aInserts['time_stamp'] = PHPFOX_TIME;
		$aInserts['keywords'] = $oFilter->clean($aVals['keywords']);
		$aInserts['company'] = $oFilter->clean($aVals['company']);
		$aInserts['location'] = $oFilter->clean($aVals['location']);
		$aInserts['industry'] = 0;
		$aInserts['industry_child'] = 0;
		$aInserts['education_prefer'] = $oFilter->clean($aVals['education_prefer']);
		$aInserts['language_prefer'] = $oFilter->clean($aVals['language_prefer']);
		$aInserts['working_place'] = $oFilter->clean($aVals['working_place']);
		$aValsDate = explode("/", $aVals['searchdate']);
		$time_expire = Phpfox::getLib('date')->mktime(0, 0, 0, $aValsDate[0], $aValsDate[1], $aValsDate[2]);
		$aInserts['time_expire'] = $time_expire;
		$aInserts['subscribe_code'] = md5($aInserts['user_id'] . '_' . $aInserts['time_stamp'] .'_subscribe_code');
		
		$aCategory = !empty($aVals['category']) ? $aVals['category'] : 0;
		if(isset($aCategory[0]))
		{
			$aInserts['industry'] = !empty($aCategory[0]) ? $aCategory[0] : 0;

			if (isset($aVals['industry']))
			{
				if(isset($aCategory[$aVals['industry']])){
					if($aCategory[$aVals['industry']] != $aVals['industry']){
						$aInserts['industry_child'] = $aCategory[$aVals['industry']];
					}
				}
			}

		}
        return Phpfox::getLib("database")->insert(Phpfox::getT('jobposting_subscribe'),$aInserts);
	}
	
	public function updateSubscribe($aVals){
		$oFilter = Phpfox::getLib('parse.input');
		
		$aUpdates['time_stamp'] = PHPFOX_TIME;
		
		$aUpdates['keywords'] = $oFilter->clean($aVals['keywords']);
		
		$aUpdates['company'] = $oFilter->clean($aVals['company']);
		$aUpdates['location'] = $oFilter->clean($aVals['location']);
		$aUpdates['industry'] = 0;
		$aUpdates['industry_child'] = 0;
	
		$aUpdates['education_prefer'] = $oFilter->clean($aVals['education_prefer']);
		$aUpdates['language_prefer'] = $oFilter->clean($aVals['language_prefer']);
		$aUpdates['working_place'] = $oFilter->clean($aVals['working_place']);
		$aValsDate = @explode("/", $aVals['searchdate']);
		if(count($aValsDate)==3)
		{
			$time_expire = Phpfox::getLib('date')->mktime(0, 0, 0, $aValsDate[0], $aValsDate[1], $aValsDate[2]);
			$aUpdates['time_expire'] = $time_expire;
		}
		
		$aCategory = $aVals['category'];
		if(isset($aCategory[0]))
		{
			$aUpdates['industry'] = (int)$aCategory[0];
			if(isset($aCategory[$aUpdates['industry']])){
				if($aCategory[$aUpdates['industry']] != $aUpdates['industry']){
					$aUpdates['industry_child'] = (!empty($aCategory[$aUpdates['industry']]) ? $aCategory[$aUpdates['industry']] : 0);
				}
			}
		}
			
		return Phpfox::getLib("database")->update(Phpfox::getT('jobposting_subscribe'),$aUpdates,'user_id = '.Phpfox::getUserId());	 	
	}

	public function feature($job_id, $iIsFeatured)
    {
		$this->database()->update(Phpfox::getT('jobposting_job'),array(
			'is_featured' => $iIsFeatured,
		), 'job_id = '.$job_id);
        
        #Notify and email
        if($iIsFeatured){
	        $iOwner = Phpfox::getService('jobposting')->getOwner('job', $job_id);
	        Phpfox::getService('jobposting.process')->addNotification('feature', 'job', $job_id, $iOwner, false, true, false);
	        Phpfox::getService('jobposting.process')->sendEmail('feature', 'job', $job_id, $iOwner);
		}
        return true;
	}
	
	public function processImages($iId,$file)
    { 
		$oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $iFileSizes = 0;
        $sDirImage = Phpfox::getParam('core.dir_pic').'jobposting/';
		$type_support = $this->_aType;
        if ($aImage = $oFile->load($file, $type_support, (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo') === 0 ? null : (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo') / 1024))))
        {
        	
        	$sFileName = Phpfox::getLib('file')->upload($file, $sDirImage, $iId);
            $iFileSizes += filesize($sDirImage.sprintf($sFileName, ''));

                    
           	list($width, $height, $type, $attr) = getimagesize($sDirImage.sprintf($sFileName, ''));
                    
           	foreach($this->_aSize as $iSize)
           	{
           	/*	if ($iSize == 50 || $iSize == 120)
           		{
                	if ($width < $iSize || $height < $iSize)
                	{
                    	$this->resizeImage($sFileName, $width > $iSize ? $iSize : $width, $height > $iSize ? $iSize : $height, '_'.$iSize);
                    }
                    else
                    {
                    	$this->resizeImage($sFileName, $iSize, $iSize, '_'.$iSize);
                    }
                 }
                 else
                 {
                 	$oImage->createThumbnail($sDirImage.sprintf($sFileName, ''), $sDirImage.sprintf($sFileName, '_'.$iSize), $iSize, $iSize);
	          	 }
             */ $oImage->createThumbnail($sDirImage.sprintf($sFileName, ''), $sDirImage.sprintf($sFileName, '_'.$iSize), $iSize, $iSize);          
          		$iFileSizes += filesize($sDirImage.sprintf($sFileName, '_'.$iSize));
            }
        }

        if ($iFileSizes == 0)
        {
            return false;
        }
        
        return array(
            'file_size' => $iFileSizes, 
            'image_path' => $sFileName, 
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
        );
    }


	public function processResume($iId,$file)
    { 
        $oFile = Phpfox::getLib('file');
        $iFileSizes = 0;
        $sDirImage = Phpfox::getParam('core.dir_pic').'jobposting/';
		
		$type_support =  array('zip','pdf','docx','doc');
		
        if ($aImage = $oFile->load($file, $type_support, (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_resume') === 0 ? null : (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_resume') / 1024))))
        {
        	$sFileName = Phpfox::getLib('file')->upload($file, $sDirImage, $iId);
			$sFileName = str_replace("%s", "", $sFileName);
            $iFileSizes += filesize($sDirImage.sprintf($sFileName, ''));
	        return array(
	            'file_size' => $iFileSizes, 
	            'image_path' => $sFileName, 
	            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
	        );
	    }
        
        return false;
	}

 	public function resizeImage($sFilePath, $iThumbWidth, $iThumbHeight, $sSubfix)
    {
        $sRealPath = Phpfox::getParam('core.dir_pic').'jobposting/';
        
        #Resize to Width/Height
        list($iWidth, $iHeight, $sType, $sAttr) = getimagesize($sRealPath . sprintf($sFilePath, ''));
        $iNewWidth = $iWidth;
        $iNewHeight = $iHeight;
        $fSourceRatio = $iWidth / $iHeight;
        $fThumbRatio = $iThumbWidth / $iThumbHeight;
        if($fSourceRatio > $fThumbRatio)
        {
            $iNewHeight = $iThumbHeight;
            $fRatio = $iNewHeight / $iHeight;
            $iNewWidth = $iWidth * $fRatio;
        }
        else
        {
            $iNewWidth = $iThumbWidth;
            $fRatio = $iNewWidth / $iWidth;
            $iNewHeight = $iHeight * $fRatio;                            
        }

        Phpfox::getLib("image")->createThumbnail($sRealPath . sprintf($sFilePath, ""), $sRealPath . sprintf($sFilePath, $sSubfix), $iNewWidth, $iNewHeight, true, false);

        #Crop the resized image
        if($iNewWidth > $iThumbWidth)
        {
            $iX = ceil(($iNewWidth - $iThumbWidth)/2);
            Phpfox::getLib("image")->cropImage($sRealPath . sprintf($sFilePath, $sSubfix), $sRealPath . sprintf($sFilePath, '_temp'), $iThumbWidth, $iThumbHeight, $iX, 0, $iThumbWidth);
            copy($sRealPath . sprintf($sFilePath, '_temp'), $sRealPath . sprintf($sFilePath, $sSubfix));
            unlink($sRealPath . sprintf($sFilePath, '_temp'));
        }
        if($iNewHeight > $iThumbHeight)
        {
            $iY = ceil(($iNewHeight - $iThumbHeight)/2);
            Phpfox::getLib("image")->cropImage($sRealPath . sprintf($sFilePath, $sSubfix), $sRealPath . sprintf($sFilePath, '_temp'), $iThumbWidth, $iThumbHeight, 0, $iY, $iThumbWidth);
            copy($sRealPath . sprintf($sFilePath, '_temp'), $sRealPath . sprintf($sFilePath, $sSubfix));
            unlink($sRealPath . sprintf($sFilePath, '_temp'));
        }
    }

	public function addApplication($job_id, $aVals){
		
		#Submission Form
		$aInserts = array();
		$aInserts['job_id'] = $job_id;
		$aInserts['user_id'] = Phpfox::getUserId();
		$aInserts['name'] = $aVals['name'];
		$aInserts['email'] = $aVals['email'];
		$aInserts['telephone'] = $aVals['telephone'];
		$aInserts['resume'] = ($aVals['resume_type'] == '1' && isset($aVals['list_resume'])) ? $aVals['list_resume'] : null;
		$aInserts['resume_type'] = $aVals['resume_type'];
        $aInserts['time_stamp'] = PHPFOX_TIME;
		$aInserts['status'] = 0;
		$iId = $this->database()->insert(Phpfox::getT('jobposting_application'),$aInserts);
        if(!$iId)
        {
            return false;
        }
        
        #Photos
        $aFiles = $this->processImages($iId,"image");
        if (isset($_FILES['resume']) && !empty($_FILES['resume']['name'])){
        	$aResumeFiles = $this->processResume($iId,"resume");
        } else {
        	$aResumeFiles = null;
        }

        if ((!empty($_FILES['image']['name']) && !$aFiles))
        {
            $this->database()->delete(Phpfox::getT('jobposting_application'), 'application_id = '.$iId);
            return false;
        }
        elseif($aResumeFiles === false)
        {
            $this->database()->delete(Phpfox::getT('jobposting_application'), 'application_id = '.$iId);
            Phpfox_Error::reset();
            return Phpfox_Error::set(_p('not_a_valid_file_extension_we_only_accept_support', array('support' => 'zip, pdf, docx, doc')));
        }

        if(is_array($aFiles))
        {
            $aSql['photo_path'] = $aFiles['image_path'];
            $aSql['server_id'] = $aFiles['server_id'];
			if(strlen($aSql['photo_path'])>0)
				$this->database()->update(Phpfox::getT('jobposting_application'), $aSql, 'application_id = '.$iId);
        }
		if(is_array($aResumeFiles))
        {
			$aSql['resume'] = $aResumeFiles['image_path'];
            $aSql['server_id'] = $aResumeFiles['server_id'];
			//if(strlen($aSql['photo_path'])>0)
			$this->database()->update(Phpfox::getT('jobposting_application'), $aSql, 'application_id = '.$iId);
		}
        
        #Update total application
        $this->database()->update($this->_sTable, array('total_application' => 'total_application + 1'), 'job_id = '.$iId, false);
        
        #Custom fields
        if(isset($aVals['custom'])){
        	Phpfox::getService('jobposting.custom.process')->addValue($aVals['custom'], $iId);
        }        
        
        #Notify and email
        Phpfox::getService('jobposting.process')->addNotification('apply', 'job', $job_id, $aInserts['user_id'], true, false, false);

        // send email for company owner 
        $sTitle = Phpfox::getService('jobposting')->getItemTitle('job', $job_id);
        $job_title = $sTitle;
        $site_title = Phpfox::getParam('core.site_title'); 
        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);
        if(isset($aJob['company_id']) && (int)$aJob['company_id'] > 0){
        	$company_owner = Phpfox::getService('jobposting')->getOwner('company', $aJob['company_id']);
        	if($company_owner){
        		$aCompanyOwner = Phpfox::getService('user')->getUser($company_owner, $sSelect = 'u.*', $bUserName = false);
        		$sCompanyOwnerFullName = $aCompanyOwner['full_name'];
        		$aApplicant = Phpfox::getService('user')->getUser($aInserts['user_id'], $sSelect = 'u.*', $bUserName = false);
        		$applicant_name = $aApplicant['full_name'];
        		$applicant_profile_url = Phpfox::getLib('url')->makeUrl('') . $aApplicant['user_name'];

		        Phpfox::getLib('mail')->to($company_owner)
		            ->subject(_p('apply_job_email_owner_subject', array(
		                'job_title' => $job_title
		            )))
		            ->message(_p('apply_job_email_owner_body', array(
		                'job_title' => $job_title,
		                'applicant_profile_url' => $applicant_profile_url, 
		                'applicant_name' => $applicant_name, 
		            )))
		            ->send();        		
        	}
        }
        
        Phpfox::getLib('mail')->to($aInserts['user_id'])
            ->subject(_p('apply_job_email_subject', array(
                'site_title' => Phpfox::getParam('core.site_title')
            )))
            ->message(_p('apply_job_email_message', array(
                'title' => $sTitle,
                'link' => Phpfox::getLib('url')->permalink('jobposting', $job_id, $sTitle),
                'site_title' => Phpfox::getParam('core.site_title')
            )))
            ->send();
        
        return true;
	}

	public function publish($job_id)
	{
		$aUpdates = array('post_status' => 1,'time_stamp' => PHPFOX_TIME);
		
		if (Phpfox::getUserParam('jobposting.approved_job_before_displayed'))
		{
			$aUpdates['is_approved'] = '0';
		}
		else {
			$aUpdates['is_approved'] = '1';
		}
		$this->database()->update($this->_sTable, $aUpdates , 'job_id = '.(int)$job_id);
        
        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($job_id);

        #Activity feed
        if ($aJob['is_approved'])
        {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->allowGuest()->add('jobposting_job', $job_id, $aJob['privacy'], $aJob['privacy_comment']) : null);
            if(Phpfox::isAppActive('Core_Activity_Points')) {
                Phpfox::getService('activitypoint.process')->updatePoints($aJob['user_id'], 'jobposting_job');
            }
        }
        
        #Notify and email
        if ($aJob['is_approved'])
        {
            if (Phpfox::isModule('notification'))
            {
                $aFollower = Phpfox::getService('jobposting')->getFollowers('company', $aJob['company_id']);
                if (is_array($aFollower) && count($aFollower))
                {
                    foreach ($aFollower as $iFollower)
                    {
                        Phpfox::getService('notification.process')->add('jobposting_addjobfollowedcompany', $job_id, $iFollower, $aJob['user_id']);
                    }
                }
            }
        }
         (($sPlugin = Phpfox_Plugin::get('jobposting.service_job_process_publish_end')) ? eval($sPlugin) : false);
        return true;
	}
	
	public function featureJobs($job_id, $pay = 0, $value = 1)
	{
		$aUpdates = array(
			'is_featured' => $value
		);
		if($pay)
		{
			$aUpdates['is_paid'] = 1;	
		}
		$this->database()->update(Phpfox::getT('jobposting_job'),$aUpdates,'job_id = '.$job_id);
        
        if ($value)
        {
            #Notify and email
            $iOwner = Phpfox::getService('jobposting')->getOwner('job', $job_id);
            Phpfox::getService('jobposting.process')->addNotification('feature', 'job', $job_id, $iOwner, false, true, false);
            Phpfox::getService('jobposting.process')->sendEmail('feature', 'job', $job_id, $iOwner);
        }
        
        return true;
	}

    public function payForFeature($iJobId, $sReturnUrl, $bReturn = false)
    {
        $sGateway = 'paypal';
        $sCurrency = PHpfox::getService('jobposting.helper')->getDefaultCurrency();
        $iFee = Phpfox::getParam("jobposting.fee_feature_job");
        $aInvoice = array('feature' => $iJobId);
        
        if($iFee <= 0)
        {
            return true;
        }
        
        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iJobId);
        
        $aTransaction = array(
            'invoice' => serialize($aInvoice),
            'user_id' => Phpfox::getUserId(),
            'item_id' => $aJob['company_id'],
            'time_stamp' => PHPFOX_TIME,
            'amount' => $iFee,
            'currency' => $sCurrency,
            'status' => Phpfox::getService('jobposting.transaction')->getStatusIdByName('initialized'),
            'payment_type' => 5
        );
		
        $iTransactionId = Phpfox::getService('jobposting.transaction.process')->add($aTransaction);
        /*
        $sPaypalEmail = Phpfox::getParam('jobposting.jobposting_admin_paypal_email');
        // now we get email paypal in phpfox gateway
        if(!$sPaypalEmail)
        {

        }
        */
        //if(Phpfox::isModule('younetpaymentgateways'))
        {
            //if ($oPayment = Phpfox::getService('younetpaymentgateways')->load($sGateway, $aParam))
			//$oPayment = Phpfox::getService('api.gateway')->load($sGateway, $aParam);
			//var_dump($aParam);
			//var_dump();
			/*$aParam = Phpfox::getService('jobposting.ynpaypal')->initParam($iFee,$sCurrency,$iTransactionId,$sGateway,$sReturnUrl);*/
			
			$sCheckoutUrl = Phpfox::getLib('url')->makeUrl('jobposting.payment',array('transactionid'=>$iTransactionId,'sUrl'=>base64_encode($sReturnUrl)));
			if($bReturn)
			{
				return $sCheckoutUrl;
			}
			else
			{
				Phpfox::getLib('url')->forward($sCheckoutUrl);
			}
			/*
			d($oPayment);
			d($oPayment_fox);
			d($sCheckoutUrl);

			d($sUrl);
			*/
			//die();
			//die($sCheckoutUrl);
			/*
			if ($oPayment)
            {

				$sCheckoutUrl = $oPayment->getCheckoutUrl();

				if($bReturn)
				{   
					return $sCheckoutUrl;
				}
				else
				{   
					Phpfox::getLib('url')->forward($sCheckoutUrl);
				}
            }
			*/
        }
        
        return Phpfox_Error::set(_p('can_not_load_payment_gateways_please_try_again_later'));
    }
    
	public function changeHide($iId)
	{
		$this->database()->update($this->_sTable, array('is_hide' => '1 - is_hide'), 'job_id = '.(int)$iId, false);
		return true;
	}
	
	public function delete($iId)
	{
		$aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iId);
        $this->database()->update($this->_sTable, array('is_deleted' => 1), 'job_id = '.(int)$iId);
        
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($aJob['company_id']);
        $total_job = ($aCompany['total_job'] > 0) ? ($aCompany['total_job'] - 1) : 0;
        $this->database()->update(Phpfox::getT('jobposting_company'), array('total_job' => $total_job), 'company_id = '.$aJob['company_id']);
        // Delete follow job
        $this->database()->delete(':jobposting_follow', 'item_type = \'job\' AND item_id = '. (int)$iId);
        $this->database()->delete(':jobposting_favorite', 'item_type = \'job\' AND item_id = '. (int)$iId);
        
        #Notify and email
        Phpfox::getService('jobposting.process')->addNotification('delete', 'job', $iId, Phpfox::getUserId(), false, true, true);        
        Phpfox::getService('jobposting.process')->sendEmail('delete', 'job', $iId, $aJob['user_id']);

        if(Phpfox::isAppActive('Core_Activity_Points') && !empty($aJob['is_approved'])) {
            Phpfox::getService('activitypoint.process')->updatePoints(Phpfox::getUserId(), 'jobposting_job', '-');
        }
        
		return true;
	}
	
	public function approveJob($job_id)
    {
		$this->database()->update($this->_sTable, array('is_approved' => 1),'job_id = '.(int)$job_id);
        
        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($job_id);
        PHpfox::getService('jobposting.company.process')->updateTotalJob($aJob['company_id']);
        #Activity feed
        if ($aJob['post_status'] == 1)
        {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('jobposting_job', $job_id, $aJob['privacy'], $aJob['privacy_comment'],0,$aJob['user_id']) : null);
            if(Phpfox::isAppActive('Core_Activity_Points')) {
                Phpfox::getService('activitypoint.process')->updatePoints($aJob['user_id'], 'jobposting_job');
            }
        }
        
        #Notify
        Phpfox::getService('jobposting.process')->addNotification('approve', 'job', $job_id, Phpfox::getUserId(), true, false, false);
        
        #Notify and email
        if ($aJob['post_status'] == 1)
        {
            if (Phpfox::isModule('notification'))
            {
                $aFollower = Phpfox::getService('jobposting')->getFollowers('company', $aJob['company_id']);
                if (is_array($aFollower) && count($aFollower))
                {
                    foreach ($aFollower as $iFollower)
                    {
                        Phpfox::getService('notification.process')->add('jobposting_addjobfollowedcompany', $job_id, $iFollower, $aJob['user_id']);
                    }
                }
            }

            $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($aJob['company_id']);
            $aIndustries = Phpfox::getService('jobposting.category')->getCategoryIds($aCompany['company_id']);

            $aSubcribers = Phpfox_Database::instance()->select('*')->from(Phpfox::getT('jobposting_subscribe'))
                ->where('keywords LIKE \'%'.$aJob['title'].'%\' 
                        OR company LIKE \'%'.$aCompany['name'].'%\' 
                        OR location LIKE \'%'.$aJob['location'].'%\'
                        OR working_place LIKE \'%'.$aJob['working_place'].'%\'
                        OR education_prefer LIKE \'%'.$aJob['education_prefer'].'%\'
                        OR language_prefer LIKE \'%'.$aJob['language_prefer'].'%\'
                        OR industry IN ('.$aIndustries.')
                        OR industry_child IN ('.$aIndustries.')
                        OR time_expire >= '.$aJob['time_expire'])
                ->order('time_stamp DESC')
                ->execute('getRows');

            foreach ($aSubcribers as $aSubcriber)
            {
                Phpfox::getService('jobposting.process')->sendEmail('subcriber_new', 'job', $aJob['job_id'], $aSubcriber['user_id']);
            }

        }
        (($sPlugin = Phpfox_Plugin::get('jobposting.service_job_process_approve_end')) ? eval($sPlugin) : false);	
		return true;
	}
    
	public function sendExpireNotifications()
	{                
        $aJobs = $this->database()->select('j.job_id, j.user_id')
            ->from($this->_sTable, 'j')
            ->where('j.is_notified = 0 AND j.time_expire < '.PHPFOX_TIME)
            ->execute('getSlaveRows');
        
        if (empty($aJobs))
        {
            return false;
        }
        
        $aUpdate = array();
        foreach ($aJobs as $aJob)
        {
            Phpfox::getService('jobposting.process')->addNotification('expire', 'job', $aJob['job_id'], '0', true, true, true);
            Phpfox::getService('jobposting.process')->sendEmail('expire', 'job', $aJob['job_id'], $aJob['user_id']);
                
            $aUpdate[] = $aJob['job_id'];
        }
        
        $this->database()->update($this->_sTable, array('is_notified' => 1), 'job_id IN ('. implode(',', $aUpdate).')');	
        
        return true;
	}
}

?>