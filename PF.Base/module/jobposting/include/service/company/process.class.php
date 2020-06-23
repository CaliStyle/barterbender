<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright        [YOUNET_COPPYRIGHT]
 * @author           AnNT
 * @package          Module_jobposting
 */

class JobPosting_Service_Company_Process extends Phpfox_service
{
    private $_bHasLogo = false;
    private $_aSize = array(50, 120, 150, 200, 240,500,1024);
    private $_aSuffix = array('', '_50', '_120', '_150', '_200', '_240','_500','_1024');
    private $_aType = array('jpg', 'gif', 'png');
    private $_sTableImage;
    
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

    /**
     * Add company
     * @param array $aVals
     */
    public function add($aVals)
    {
        if(!$this->_verify($aVals))
        {
            return false;
        }

        #Check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals);

        $oParseInput = Phpfox::getLib('parse.input');

        $aContactInfo = array(
            'contact_name' => $oParseInput->clean($aVals['contact_name'], 255),
            'contact_phone' => $oParseInput->clean($aVals['contact_phone'], 255),
            'contact_email' => $oParseInput->clean($aVals['contact_email'], 255),
            'contact_fax' => !empty($aVals['contact_fax']) ? $oParseInput->clean($aVals['contact_fax'], 255) : ''
        );

        $aSql = array(
            'user_id' => Phpfox::getUserId(),
            'name' => $oParseInput->clean($aVals['name'], 255),
            'location' => $oParseInput->clean($aVals['location'], 255),
            'country_iso' => !empty($aVals['country_iso']) ? $aVals['country_iso'] : Phpfox::getUserBy('country_iso'),
            'country_child_id' => !empty($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0,
            'city' => !empty($aVals['city']) ? $oParseInput->clean($aVals['city'], 255) : null,
            'postal_code' => !empty($aVals['postal_code']) ? $oParseInput->clean($aVals['postal_code'], 20) : null,
            'gmap' => serialize($aVals['gmap']),
            'website' => !empty($aVals['website']) ? $oParseInput->clean($aVals['website'], 255) : null,
            'size_from' => $aVals['size_from']?$aVals['size_from']:0,
            'size_to' => $aVals['size_to']?$aVals['size_to']:0,
            'contact_info' => serialize($aContactInfo),
            'time_stamp' => PHPFOX_TIME,
            'time_update' => PHPFOX_TIME,
            'post_status' => (isset($aVals['post_status']) ? $aVals['post_status'] : '1'),
            'is_approved' => 1,
            'is_sponsor' => 0,
            'privacy' => !empty($aVals['privacy']) ? $aVals['privacy'] : 0,
            'privacy_comment' => !empty($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0,
            'module_id' => !empty($aVals['module_id']) ? $aVals['module_id'] : 'jobposting',
            'item_id' => !empty($aVals['item_id']) ? $aVals['item_id'] : 0
        );
		
		if (Phpfox::getUserParam('jobposting.approve_company_before_displayed'))
		{
			$aSql['is_approved'] = '0';
		}

        if (!Phpfox_Error::isPassed())
        {
            return false;
        }

        #Insert
        $iId = $this->database()->insert($this->_sTable, $aSql);
        if (!$iId)
        {
            return false;
        }

        #Submission Form
        $this->addDefaultSubmissionForm($aVals, $iId);

        #Text
        $this->database()->insert($this->_sTableText, array(
            'company_id' => $iId,
            'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
            'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
        ));

        #Industry Data
        $this->updateCategoryData($aVals['category'], $iId);

        #Custom privacy
        if (isset($aVals['privacy']) && $aVals['privacy'] == '4')
        {
            Phpfox::getService('privacy.process')->add('jobposting_company', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
		
		#Custom fields for company
        if(isset($aVals['custom'])){
        	Phpfox::getService('jobposting.custom.process')->addValue($aVals['custom'], $iId, 1);
        } 
        
        #Activity Feed
        if ($aSql['is_approved'] && $aSql['post_status'] == '1')
        {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('jobposting_company', $iId, $aSql['privacy'], $aSql['privacy_comment']) : null);
        }
        
        #Email
        Phpfox::getService('jobposting.process')->sendEmail('add', 'company', $iId, $aSql['user_id']);
        
        #Sponsor
		if($aSql['is_approved'] && $aSql['post_status'] == '1' && isset($aVals['sponsor']))
		{
			$iFee = Phpfox::getParam('jobposting.jobposting_fee_to_sponsor_company');
            if ($iFee <= 0)
            {
                $this->sponsor($iId);
            }
            else
            {
                $sUrl = Phpfox::getLib('url')->permalink('jobposting.company', $iId, $aVals['name']);
                $this->payForSponsor($iId, $sUrl);
            }
		}
		
        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            Phpfox::getService('tag.process')->add('jobposting_company', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }

        return $iId;
    }

    /**
     * Update company
     * @param int $iId: company_id
     * @param array $aVals: edit vals
     */
       public function update($iId, $aVals, &$images)
    {
        $p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'jobposting' . PHPFOX_DS;
        if (!is_dir($p)) {
            if (!@mkdir($p, 0777, 1)) {
            }
        }

        if (!$this->_verify($aVals, true)) {
            return false;
        }

        #Check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals);

        $oParseInput = Phpfox::getLib('parse.input');

        $aContactInfo = array(
            'contact_name' => $oParseInput->clean($aVals['contact_name'], 255),
            'contact_phone' => $oParseInput->clean($aVals['contact_phone'], 255),
            'contact_email' => $oParseInput->clean($aVals['contact_email'], 255),
            'contact_fax' => !empty($aVals['contact_fax']) ? $oParseInput->clean($aVals['contact_fax'], 255) : ''
        );

        $aSql = array(
            'name' => $oParseInput->clean($aVals['name'], 255),
            'location' => $oParseInput->clean($aVals['location'], 255),
            'country_iso' => !empty($aVals['country_iso']) ? $aVals['country_iso'] : Phpfox::getUserBy('country_iso'),
            'country_child_id' => !empty($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0,
            'city' => !empty($aVals['city']) ? $oParseInput->clean($aVals['city'], 255) : null,
            'postal_code' => !empty($aVals['postal_code']) ? $oParseInput->clean($aVals['postal_code'], 20) : null,
            'gmap' => serialize($aVals['gmap']),
            'website' => !empty($aVals['website']) ? $oParseInput->clean($aVals['website'], 255) : null,
            'size_from' => $aVals['size_from']?$aVals['size_from']:0,
            'size_to' => $aVals['size_to']?$aVals['size_to']:0,
            'contact_info' => serialize($aContactInfo),
            'time_stamp' => PHPFOX_TIME,
            'time_update' => PHPFOX_TIME,
            'privacy' => !empty($aVals['privacy']) ? $aVals['privacy'] : 0,
            'privacy_comment' => !empty($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0,
            'module_id' => !empty($aVals['module_id']) ? $aVals['module_id'] : 'jobposting',
            'item_id' => !empty($aVals['item_id']) ? $aVals['item_id'] : 0
        );

        if (isset($aVals['post_status']) && $aVals['post_status'] == '1') {
            $aSql['post_status'] = 1;
            $aSql['time_stamp'] = PHPFOX_TIME;
        }

        if (Phpfox::getUserParam('jobposting.approve_company_before_displayed')) {
            $aSql['is_approved'] = '0';
        } else {
            $aSql['is_approved'] = '1';
        }

        #Submission Form
        $this->updateSubmissionForm($aVals, $iId);

        #Photos
        $aFiles = $this->processImages($iId, $images);

        $aCompany =  Phpfox::getService('jobposting.company')->getCompanyById($iId);
        $add_photo = Phpfox::getService('jobposting.company')->isAdminPermission($aCompany['company_id'], Phpfox::getUserId(), 'add_photo');
        $delete_photo = Phpfox::getService('jobposting.company')->isAdminPermission($aCompany['company_id'], Phpfox::getUserId(), 'delete_photo');

        if (Phpfox::getUserId() == $aCompany['user_id'] || $add_photo)
        {

            if(is_array($aFiles))
            {
                Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'jobposting', $aFiles['file_size']);
                $aSql['image_path'] = $aFiles['image_path'];
                $aSql['server_id'] = $aFiles['server_id'];
            }
        }

        #Update
        $this->database()->update($this->_sTable, $aSql, 'company_id = '.$iId);

        #Text
        $this->database()->update($this->_sTableText, array(
            'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
            'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
        ), 'company_id = '.$iId);

        #Industry Data
        $this->updateCategoryData($aVals['category'], $iId);

        #Custom fields for company
        if(isset($aVals['custom'])){
            Phpfox::getService('jobposting.custom.process')->updateValue($aVals['custom'], $iId, 1);
        }

        #Custom privacy
        if (Phpfox::isModule('privacy'))
        {
            if ($aVals['privacy'] == '4')
            {
                Phpfox::getService('privacy.process')->update('jobposting_company', $iId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            }
            else
            {
                Phpfox::getService('privacy.process')->delete('jobposting_company', $iId);
            }
        }

        #Activity feed
        if ($aVals['is_approved'])
        {
            if ($aVals['old_status'] == '2' && isset($aVals['post_status']) && $aVals['post_status'] == '1')
            {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('jobposting_company', $iId, $aSql['privacy'], $aSql['privacy_comment']) : null);
            }
            else
            {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('jobposting_company', $iId, $aSql['privacy'], $aSql['privacy_comment'], 0, $aVals['user_id']) : null);
            }
        }

        #Admins
        //$this->database()->delete($this->_sTableAdmin, 'company_id = '.(int)$iId);
        $aAdminInDb = Phpfox::getService('jobposting.company')->getAdmins((int)$iId);
        $aAdmins = Phpfox::getLib('request')->getArray('admins');

        //delete admin if have
        $arrDeletes  = array_diff($aAdminInDb, $aAdmins);
        if(count($arrDeletes))
        {
            foreach($arrDeletes as $userId)
            {
                $this->database()->delete($this->_sTableAdmin, 'company_id = '.(int)$iId .' AND user_id ='.(int)$userId);
            }
        }

        //add admin if have
        $arrAdds = array_diff($aAdmins, $aAdminInDb);
        if (count($arrAdds))
        {
            foreach ($arrAdds as $userId)
            {
                $this->database()->insert($this->_sTableAdmin, array('company_id' => $iId, 'user_id' => $userId));
                Phpfox::getService('notification.process')->add('jobposting_admin_assignment', $iId, $userId);
            }
        }

        #Sponsor
        if(isset($aVals['sponsor']))
        {
            $iFee = Phpfox::getParam('jobposting.jobposting_fee_to_sponsor_company');
            if ($iFee <= 0)
            {
                $this->sponsor($iId);
            }
            else
            {
                $sUrl = Phpfox::getLib('url')->permalink('jobposting.company', $iId, $aVals['name']);
                $this->payForSponsor($iId, $sUrl);
            }
        }

        #Packages
        if(isset($aVals['packages']))
        {
            $sUrl = Phpfox::getLib('url')->permalink('jobposting.company.add.packages', 'id_'.$iId);
            Phpfox::getService('jobposting.package.process')->pay($aVals['packages'], $iId, $sUrl);
        }

        #update company admin permission
        if(isset($aVals['permission']))
        {

            $aUpdate = array(
                'add_photo' => $aVals['permission']['add_photo'] ,
                'delete_photo' => $aVals['permission']['delete_photo'],
                'buy_packages' => $aVals['permission']['buy_packages'],
                'edit_submission_form' => $aVals['permission']['edit_submission_form'],
                'add_job' => $aVals['permission']['add_job'],
                'edit_job' => $aVals['permission']['edit_job'],
                'delete_job' => $aVals['permission']['delete_job'],
                'view_application' => $aVals['permission']['view_application'],
                'download_resumes' => $aVals['permission']['download_resumes']
            );

            //change user_id in company admin and set full permission
            $this->database()->update(Phpfox::getT('jobposting_company_admin'), $aUpdate,'company_id = '.$iId .' AND user_id ='.$aVals['permission']['admin_permission']);
        }


        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            $aCompany = $this->database()->select('c.company_id, c.user_id')
                ->from(Phpfox::getT('jobposting_company'), 'c')
                ->where('c.company_id = '.$iId.' AND c.is_deleted = 0')
                ->execute('getSlaveRow');

            if(isset($aCompany['company_id'])){
                Phpfox::getService('tag.process')->update('jobposting_company', $iId, $aCompany['user_id'], $aVals['description'], true);
            }
        }

        return $iId;
    }

    /**
     * Update Industry data
     * @param array $aCats
     * @param int $iCompanyId
     */
    public function updateCategoryData($aCats, $iCompanyId)
    {
        $sTableCategoryData = Phpfox::getT('jobposting_category_data');

        #Remove old data
        $this->database()->delete($sTableCategoryData, 'company_id = '.$iCompanyId);

        #Add new data
        foreach ($aCats as $k1 => $aCat)
        {
            foreach ($aCat as $k2 => $v2)
            {
                if (!empty($v2))
                {
                    $this->database()->insert($sTableCategoryData, array(
                        'company_id' => $iCompanyId,
                        'no' => $k1,
                        'category_id' => $v2
                    ));
                }
            }
        }
    }
    
    /**
     * Process uploaded images
     * @param int $iId: company_id
     * @return array(file_size, image_path, server_id)
     */
    public function processImages($iId,&$images)
    {
        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $iFileSizes = 0;
        $sDirImage = Phpfox::getParam('core.dir_pic').'jobposting/';
        if(!isset($_FILES['image'])) return false;
        foreach ($_FILES['image']['error'] as $iKey => $sError)
        {
        		
            if ($sError == UPLOAD_ERR_OK)
            {
            	
                if ($aImage = $oFile->load('image['.$iKey.']', $this->_aType, (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo') === 0 ? null : (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo') / 1024))))
                {
                    $sFileName = Phpfox::getLib('file')->upload('image['.$iKey.']', $sDirImage, $iId);
                    $iFileSizes += filesize($sDirImage.sprintf($sFileName, ''));

                    $this->database()->insert($this->_sTableImage, array(
                        'company_id' => $iId,
                        'image_path' => $sFileName,
                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                    ));
                    
                    list($width, $height, $type, $attr) = getimagesize($sDirImage.sprintf($sFileName, ''));
                    
                    foreach($this->_aSize as $iSize)
                    {
                        if ($iSize == 50 || $iSize == 120)
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
                        
                        $iFileSizes += filesize($sDirImage.sprintf($sFileName, '_'.$iSize));
                    }
                }
				else {
					$images = "File is invalid!";
					
				}
            }	
        }
        
        if ($iFileSizes === 0)
        {
        
            return false;
        }
        
        return array(
            'file_size' => $iFileSizes, 
            'image_path' => $sFileName, 
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
        );
    }
    
    /**
     * Resize Image
     * @todo improve performance
     */
    public function resizeImage($sFilePath, $iThumbWidth, $iThumbHeight, $sSubfix)
    {
        $sRealPath = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS;
        
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

        $sDestination = $sRealPath . sprintf($sFilePath, $sSubfix);
        $sTemp1 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp1');
        $sTemp2 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp2');
        $sTemp3 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp3');
        
        Phpfox::getLib("image")->createThumbnail($sRealPath . sprintf($sFilePath, ""), $sTemp1, $iNewWidth, $iNewHeight, true, false);

        #Crop the resized image
        if($iNewWidth > $iThumbWidth)
        {
            $iX = ceil(($iNewWidth - $iThumbWidth)/2);
            Phpfox::getLib("image")->cropImage($sTemp1, $sTemp2, $iThumbWidth, $iThumbHeight, $iX, 0, $iThumbWidth);
        }
        else
        {
            @copy($sTemp1, $sTemp2);
        }
        
        if($iNewHeight > $iThumbHeight)
        {
            $iY = ceil(($iNewHeight - $iThumbHeight)/2);
            Phpfox::getLib("image")->cropImage($sTemp2, $sTemp3, $iThumbWidth, $iThumbHeight, 0, $iY, $iThumbWidth);
        }
        else
        {
            @copy($sTemp2, $sTemp3);
        }
        
        @copy($sTemp3, $sDestination);
        if (Phpfox::getParam('core.allow_cdn'))
		{
			Phpfox::getLib('cdn')->put($sDestination);
		}
        
        @unlink($sTemp1);
        @unlink($sTemp2);
        @unlink($sTemp3);
    }
    
    /**
     * Set default image
     * @param int $iImageId
     * @return bool
     */
    public function setDefaultImage($iImageId)
    {
        $aImage = $this->database()->select('image_path, server_id, company_id')->from($this->_sTableImage)->where('image_id = ' . $iImageId)->execute('getSlaveRow');

        if (!$aImage) {
            return Phpfox_Error::set(_p('unable_to_find_the_image'));
        }

        return $this->database()->update($this->_sTable,
            array(
                'image_path' => $aImage['image_path'],
                'server_id' => $aImage['server_id']
            ), 'company_id = ' . $aImage['company_id']);
    }

    /**
     * Delete image
     * @param int $iImageId
     * @return bool
     */
    public function deleteImage($iImageId)
    {
        $iNewImage = 0;
        $aImage = $this->database()->select('ci.image_id, ci.image_path, ci.server_id, c.company_id, c.image_path AS default_image_path, c.user_id')
            ->from($this->_sTableImage, 'ci')
            ->join($this->_sTable, 'c', 'c.company_id = ci.company_id')
            ->where('ci.image_id = '.$iImageId)
            ->execute('getSlaveRow');

        if (!$aImage)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_image'));
        }

        if ($aImage['default_image_path'] == $aImage['image_path'])
        {
            $aNewDefault = $this->database()->select('image_path, server_id, image_id')
                ->from($this->_sTableImage)
                ->where('company_id = '.$aImage['company_id'].' AND image_id != '.$aImage['image_id'])
                ->execute('getSlaveRow');

            $this->database()->update($this->_sTable, array(
                'image_path' => isset($aNewDefault['image_path']) ? $aNewDefault['image_path'] : '',
                'server_id' => isset($aNewDefault['server_id']) ? $aNewDefault['server_id'] : '0'
            ), 'company_id = '.$aImage['company_id']);

            $iNewImage = isset($aNewDefault['image_id']) ? $aNewDefault['image_id'] : 0;
        }

        $iFileSizes = 0;
        foreach ($this->_aSuffix as $sSize)
        {
            $sImage = Phpfox::getParam('core.dir_pic') . 'jobposting' . PHPFOX_DS . sprintf($aImage['image_path'], $sSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);
                @unlink($sImage);
            }
        }

        if ($iFileSizes > 0) {
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'jobposting', $iFileSizes, '-');
        }

        $this->database()->delete($this->_sTableImage, 'image_id = '.$aImage['image_id']);
        return $iNewImage;
    }
    
    /**
     * Delete Logo
     * @param int $iCompanyId
     */
    public function deleteLogo($iCompanyId)
    {
        $aLogo = $this->database()->select('logo_path, server_id')->from($this->_sTableForm)->where('company_id = '.$iCompanyId)->execute('getSlaveRow');
        
        if (!$aLogo)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_image'));
        }
        
        $this->database()->update($this->_sTableForm, array('logo_path' => '', 'server_id' => '0'), 'company_id = '.$iCompanyId);
        
        foreach ($this->_aSuffix as $sSize)
        {
            $sImage = Phpfox::getParam('core.dir_pic').'jobposting/'.sprintf($aLogo['logo_path'], $sSize);
            if (file_exists($sImage))
            {
                @unlink($sImage);
            }
        }
        
        return true;
    }
    
    public function addDefaultSubmissionForm($aVals, $iCompanyId)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        
        $aSql = array(
            'company_id' => $iCompanyId,
            'title' => $oParseInput->clean($aVals['name'], 255),
            'description' => null,
            'job_title_enable' => '1',
            'candidate_name_enable' => '1',
            'candidate_name_require' => '1',
            'candidate_photo_enable' => '1',
            'candidate_photo_require' => '1',
            'candidate_email_enable' => '1',
            'candidate_email_require' => '1',
            'candidate_telephone_enable' => '1',
            'candidate_telephone_require' => '1',
            'resume_enable' => '1'
        );
        
        return $this->database()->insert($this->_sTableForm, $aSql);
    }

    /**
     * Add or update Submission Form
     * @param array $aVals
     * @param int $iCompanyId
     */
    public function updateSubmissionForm($aVals, $iCompanyId)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        
        $aSql = array(
            'company_id' => $iCompanyId,
            'title' => $oParseInput->clean($aVals['form_title'], 255),
            'description' => empty($aVals['form_description']) ? null : $oParseInput->clean($aVals['form_description']),
            'job_title_enable' => empty($aVals['job_title_enable']) ? '0' : '1',
            'candidate_name_enable' => empty($aVals['candidate_name_enable']) ? '0' : '1',
            'candidate_name_require' => empty($aVals['candidate_name_require']) ? '0' : '1',
            'candidate_photo_enable' => empty($aVals['candidate_photo_enable']) ? '0' : '1',
            'candidate_photo_require' => empty($aVals['candidate_photo_require']) ? '0' : '1',
            'candidate_email_enable' => empty($aVals['candidate_email_enable']) ? '0' : '1',
            'candidate_email_require' => empty($aVals['candidate_email_require']) ? '0' : '1',
            'candidate_telephone_enable' => empty($aVals['candidate_telephone_enable']) ? '0' : '1',
            'candidate_telephone_require' => empty($aVals['candidate_telephone_require']) ? '0' : '1',
            'resume_enable' => empty($aVals['resume_enable']) ? '0' : '1'
        );
        
        $aForm = $this->database()->select('form_id, logo_path, server_id')->from($this->_sTableForm)->where('company_id = '.$iCompanyId)->execute('getSlaveRow');
        
        if ($this->_bHasLogo)
        {
            $oImage = Phpfox::getLib('image');
            $sDirImage = Phpfox::getParam('core.dir_pic').'jobposting/';
            
            $sFileName = Phpfox::getLib('file')->upload('company_logo', $sDirImage, $iCompanyId);
            $iFileSizes = filesize($sDirImage.sprintf($sFileName, ''));
            
            $aSql['logo_path'] = $sFileName;
            $aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            
            #Create thumbnail
            foreach($this->_aSize as $iSize)
            {
                $oImage->createThumbnail($sDirImage.sprintf($sFileName, ''), $sDirImage.sprintf($sFileName, '_'.$iSize), $iSize, $iSize);
                $iFileSizes += filesize($sDirImage.sprintf($sFileName, '_'.$iSize));
            }
            
            #Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'jobposting', $iFileSizes);
            
            #Delete old images
            if (!empty($aForm['logo_path']))
            {
                foreach ($this->_aSuffix as $sSize)
                {
                    $sImage = Phpfox::getParam('core.dir_pic').'jobposting/'.sprintf($aForm['logo_path'], $sSize);
                    if (file_exists($sImage))
                    {
                        @unlink($sImage);
                    }
                }
            }
        }
        
        if (!empty($aForm['form_id']))
        {
            return $this->database()->update($this->_sTableForm, $aSql, 'form_id = '.$aForm['form_id']);
        }
        else
        {
            return $this->database()->insert($this->_sTableForm, $aSql);
        }
    }
    
    /**
     * Sponsor company
     * @param int $iId
     */
    public function sponsor($iId, $value = 1)
    {
        $this->database()->update($this->_sTable, array('is_sponsor' => $value), 'company_id = '.(int)$iId);
        
        if ($value)
        {
            #Notify and email
            $iOwner = Phpfox::getService('jobposting')->getOwner('company', $iId);
            Phpfox::getService('jobposting.process')->addNotification('sponsor', 'company', $iId, $iOwner, false, true, false);
            Phpfox::getService('jobposting.process')->sendEmail('sponsor', 'company', $iId, $iOwner);
        }
        
        return true;
    }
    
    /**
     * Pay to sponsor cpmpany
     * @param int $iId
     */
    public function payForSponsor($iId, $sReturnUrl, $bReturn = false)
    {
        $iFee = Phpfox::getParam('jobposting.jobposting_fee_to_sponsor_company');
        $sGateway = 'paypal';
        $sCurrency = PHpfox::getService('jobposting.helper')->getDefaultCurrency();
        $aInvoice = array('sponsor' => $iId);
        
        if ($iFee <= 0)
        {
            return true;
        }
        
        $aInsert = array(
            'invoice' => serialize($aInvoice),
            'user_id' => Phpfox::getUserId(),
            'item_id' => $iId,
            'time_stamp' => PHPFOX_TIME,
            'amount' => $iFee,
            'currency' => $sCurrency,
            'status' => Phpfox::getService('jobposting.transaction')->getStatusIdByName('initialized'),
            'payment_type' => 1 //sponsor
        );
        
        $iTransactionId = Phpfox::getService('jobposting.transaction.process')->add($aInsert);
        /*
        $sPaypalEmail = Phpfox::getParam('jobposting.jobposting_admin_paypal_email');
        if(!$sPaypalEmail)
        {

        }

        $sPaypalEmail = "";
        $aParam = array(
            'paypal_email' => $sPaypalEmail,
            'amount' => $iFee,
            'currency_code' => $sCurrency,
            'custom' => 'jobposting|' . $iTransactionId,
            'return' => Phpfox::getParam('core.url_module') . 'jobposting/static/php/paymentcb.php?location='.$sReturnUrl,
            'recurring' => 0
        );
        /*
        if(Phpfox::isModule('younetpaymentgateways'))
        {
            if ($oPayment = Phpfox::getService('younetpaymentgateways')->load($sGateway, $aParam))
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
        }
        */
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
   			
        return Phpfox_Error::set(_p('can_not_load_payment_gateways_please_try_again_later'));
    }
    
    /**
     * Verify inputed values
     * @param array $aVals
     * @param boolean $bIsUpdate
     */
    private function _verify($aVals, $bIsUpdate = false)
    {
        #verify website
        if($aVals['website']!='')
        {
            $url_pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
            if(!preg_match($url_pattern, $aVals['website']))
            {
                return Phpfox_Error::set(_p('website_format_is_not_valid'));
            }
        }

        if(empty($aVals['size_from']) == false && empty($aVals['size_to']) == false){
            if(!is_numeric($aVals['size_from']) || !is_numeric($aVals['size_to']))
            {
                return Phpfox_Error::set(_p('company_size_is_not_valid_adding'));
            }
            #verify company size
            if($aVals['size_from'] <= 0 || $aVals['size_from'] >= $aVals['size_to'])
            {
                return Phpfox_Error::set(_p('company_size_is_not_valid'));
            }
        }
        
        #verify industry
        $aCats = $aVals['category'];
        $iLimit = count($aCats);
        
        $bIsEmpty = true;
        foreach($aCats as $k1=>$aCat)
        {
            foreach($aCat as $k2=>$v2)
            {
                if(!empty($v2))
                {
                    $bIsEmpty = false;
                }
                else
                {
                    unset($aCats[$k1][$k2]);
                }
            }
        }
        
        if($bIsEmpty)
        {
            return Phpfox_Error::set(_p('industry_is_required'));
        }
        
        $bIsSame = false;
        for($i=0; $i<$iLimit-1; $i++)
        {
            for($j=$i+1; $j<$iLimit; $j++)
            {
                if(!empty($aCats[$i]) && !empty($aCats[$j]) && $aCats[$i]==$aCats[$j])
                {
                    $bIsSame = true;
                }
            }
        }
        
        if($bIsSame)
        {
            return Phpfox_Error::set(_p('two_industries_must_be_different'));
        }
        
        #verify email
        $email_pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i';
        if(!preg_match($email_pattern, $aVals['contact_email']))
        {
            return Phpfox_Error::set(_p('email_format_is_not_valid'));
        }
        
        #verify company_logo
        if (isset($_FILES['company_logo']['name']) && ($_FILES['company_logo']['name'] != ''))
        {
            $aImage = Phpfox::getLib('file')->load('company_logo', $this->_aType, (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo') === 0 ? null : (Phpfox::getParam('jobposting.jobposting_maximum_upload_size_photo') / 1024)));
            if ($aImage === false)
            {
                return false;
            }
            
            $this->_bHasLogo = true;
        }
        
        return true;
    }

    public function feature($company_id, $is_Sponsor){
        $this->database()->update(Phpfox::getT('jobposting_company'),array(
            'is_sponsor' => $is_Sponsor,
        ),'company_id = '.$company_id);
    }
	
	public function delete($iCompany)
    {
        $iOwner = $this->database()->select('user_id')
	        ->from($this->_sTable)
	        ->where('company_id = '.(int)$iCompany)
	        ->execute('getSlaveField');
        
        $aJobs = $this->database()->select('job_id')
        	->from(Phpfox::getT('jobposting_job'))
        	->where('is_deleted = 0 AND company_id = '.(int)$iCompany)
        	->execute('getSlaveRows');
		
        $this->database()->update($this->_sTable, array('is_deleted' => 1), 'company_id = '.(int)$iCompany);
        // Delete follow and favorite company
        $this->database()->delete(':jobposting_follow', 'item_type = \'company\' AND item_id = ' . (int)$iCompany);
        $this->database()->delete(':jobposting_favorite', 'item_type = \'company\' AND item_id = '. (int)$iCompany);

        $aJobId = array();
		if(!empty($aJobs))
		{
			foreach($aJobs as $aJob)
			{
				$aJobId[] = $aJob['job_id'];
			}
			
			$this->database()->update(Phpfox::getT('jobposting_job'), array('is_deleted' => 1), 'job_id IN ('.implode(',', $aJobId).')');	
		}
		
        #Notify and email
        Phpfox::getService('jobposting.process')->addNotification('delete', 'company', $iCompany, Phpfox::getUserId(), false, true, true);
        
        if(!empty($aJobs))
        {
    		$aJobFollower = $this->database()->select('DISTINCT user_id')
    			->from(Phpfox::getT('jobposting_follow'))
    			->where('item_type = \'job\' AND item_id IN ('.implode(',', $aJobId).')')
    			->execute('getSlaveRows');
		}
        
		if(!empty($aJobFollower))
		{
			foreach($aJobFollower as $iFollower)
			{
				Phpfox::getService('notification.process')->add('jobposting_deletecompanyfollowedjob', $iCompany, $iFollower, Phpfox::getUserId());
			}

            // Delete follow and favorite job
            $this->database()->delete(':jobposting_follow', 'item_type = \'job\' AND item_id IN ('.implode(',', $aJobId).')');
			$this->database()->delete(':jobposting_favorite', 'item_type = \'job\' AND item_id IN ('.implode(',', $aJobId).')');
		}
		
        Phpfox::getService('jobposting.process')->sendEmail('delete', 'company', $iCompany, $iOwner);
		return true;
	}

	public function approveCompany($iCompany)
    {
		$this->database()->update($this->_sTable, array('is_approved' => 1),'company_id = '.(int)$iCompany);
        
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($iCompany);
        
        #Activity feed
        if ($aCompany['post_status'] == 1)
        {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('jobposting_company', $iCompany, $aCompany['privacy'], $aCompany['privacy_comment'], 0, $aCompany['user_id']) : null);
        }
        
        #Notify
        Phpfox::getService('jobposting.process')->addNotification('approve', 'company', $iCompany, Phpfox::getUserId(), true, false, false);
        
		return true;
	}
	
	public function updateTotalJob($company_id,$type = "company"){
		$total_job = PHpfox::getService('jobposting.job')->iCountJobByCompanyId($company_id);
		$total_follow = PHpfox::getService('jobposting.company')->iCounttotalfollow($company_id,$type);
		
		$this->database()->update(Phpfox::getT('jobposting_company'),array(
			'total_job' => $total_job,
			'total_follow' => $total_follow,
		),'company_id = '.$company_id);
	}
	
	/**
     * Activate company
     * @param int $iId
     */
    public function activate($iId, $value = 1)
    {
    	//update company
        $this->database()->update($this->_sTable, array('is_activated' => $value), 'company_id = '.(int)$iId);
		
		//update job
        $this->database()->update(Phpfox::getT('jobposting_job'), array('is_activated' => $value), 'company_id = '.(int)$iId);
        
        #Notify and email
        $iOwner = Phpfox::getService('jobposting')->getOwner('company', $iId);
		$action = 'deactivate';
		if($value)
		{
			$action = 'activate';
		}
		
		Phpfox::getService('jobposting.process')->addNotification($action, 'company', $iId, $iOwner, true, true, false);
    	Phpfox::getService('jobposting.process')->sendEmail($action, 'company', $iId, $iOwner);
        
        return true;
    }
}

?>