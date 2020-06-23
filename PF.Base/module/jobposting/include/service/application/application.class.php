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

class JobPosting_Service_Application_Application extends Phpfox_Service
{
    private $_aStatus = array(
        0 => 'pending',
        1 => 'passed',
        2 => 'rejected'
    );
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_application');
    }
    
    public function getStatusNameByKey($iKey)
    {
        if(isset($this->_aStatus[$iKey]))
        {
            return $this->_aStatus[$iKey];
        }
        return false;
    }
    
    /**
     * @return int(iKey) if exist, else return bool(false)
     */
    public function getStatusKeyByName($sName)
    {
        return array_search($sName, $this->_aStatus);
    }
	
    public function get($iId)
    {
        $aApplication = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('application_id = '.(int)$iId)
            ->execute('getSlaveRow');
        
        if($aApplication)
        {
            $this->implement($aApplication);
        }
        
        return $aApplication;
    }
    
    public function getForView($iId)
    {
        $aApplication = $this->database()->select('a.*, j.title as job_title, c.name as company_name, c.image_path, c.server_id as image_server_id, c.location, c.postal_code, c.city, c.country_child_id, c.country_iso, cf.title as form_title, cf.description as form_description, cf.logo_path, cf.server_id as logo_server_id')
            ->from($this->_sTable, 'a')
            ->join(Phpfox::getT('jobposting_job'), 'j', 'j.job_id = a.job_id')
            ->join(Phpfox::getT('jobposting_company'), 'c', 'c.company_id = j.company_id')
            ->join(Phpfox::getT('jobposting_company_form'), 'cf', 'cf.company_id = c.company_id')
            ->where('application_id = '.(int)$iId)
            ->execute('getSlaveRow');
        
        if($aApplication)
        {
            $this->implement($aApplication);
        }
        
        if (!empty($aApplication['location']))
		{
			$aApplication['map_location'] = $aApplication['location'];
			if (!empty($aApplication['city']))
			{
				$aApplication['map_location'] .= ', ' . $aApplication['city'];
			}
			if (!empty($aApplication['postal_code']))
			{
				$aApplication['map_location'] .= ', ' . $aApplication['postal_code'];
			}	
			if (!empty($aApplication['country_child_id']))
			{
				$aApplication['map_location'] .= ', ' . Phpfox::getService('core.country')->getChild($aApplication['country_child_id']);
			}			
			if (!empty($aApplication['country_iso']))
			{
				$aApplication['map_location'] .= ', ' . Phpfox::getService('core.country')->getCountry($aApplication['country_iso']);
			}
		}
        
        if(empty($aApplication['logo_path']) && isset($aApplication['image_path']))
        {
            $aApplication['logo_path'] = $aApplication['image_path'];
            $aApplication['logo_server_id'] = $aApplication['image_server_id'];
        }
        
        $aFields = Phpfox::getService('jobposting.custom')->getByApplicationId($iId);
        if(is_array($aFields) && count($aFields))
        {
            $aApplication['custom_field'] = $aFields;
        }
        
        return $aApplication;
    }
    
    /**
     * Get all items applied to a job
     * @param int $iJobId
     * @param int $iPage = null
     * @param int $iLimit = 10
     * @return array
     */
    public function getByJobId($iJobId, $iPage = null, $iLimit = 10)
    {
        $iCnt = $this->database()->select('COUNT(application_id)')->from($this->_sTable)->where('job_id = '.(int)$iJobId)->execute('getField');
        
        if(!$iCnt)
        {
            return array(0, array());
        }
        
        $aApplications = $this->database()->select('app.*, u.user_name, u.full_name')
            ->from($this->_sTable, 'app')
            ->join(Phpfox::getT('user'),'u','u.user_id = app.user_id')
            ->where('job_id = '.(int)$iJobId)
            ->order('time_stamp DESC')
            ->limit($iPage, $iLimit, $iCnt)
            ->execute('getSlaveRows');
        
        foreach($aApplications as $k => $aApplication)
        {
            $this->implement($aApplications[$k]);
        }
        
        return array($iCnt, $aApplications);
    }
    
    public function implement(&$aApplication)
    {
        $aApplication['time_stamp_text'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aApplication['time_stamp'], false);
        $aApplication['status_name'] = $this->getStatusNameByKey($aApplication['status']);
        
        /*
        $aUser = $this->database()->select('full_name, user_image, email')
            ->from(Phpfox::getT('user'))
            ->where('user_id = '.(int)$aApplication['user_id'])
            ->execute('getSlaveRow');
        
        if($aUser)
        {
            if(empty($aApplication['name']) && !empty($aUser['full_name']))
            {
                $aApplication['name'] = $aUser['full_name'];
            }
            
            if(empty($aApplication['photo_path']) && !empty($aUser['user_image']))
            {
                $aApplication['photo_path'] = $aUser['user_image'];
            }
            
            if(empty($aApplication['email']) && !empty($aUser['email']))
            {
                $aApplication['email'] = $aUser['email'];
            }
        }
        */
    }
    
    public function buildHtmlRow($iId)
    {
        $sHtml = '';
        $aApplication = $this->get($iId);
        if($aApplication)
        {
            $sHtml .= '<td>'.$aApplication['name'].'</td>';
            $sHtml .= '<td class="t_center">'.$aApplication['time_stamp_text'].'</td>';
            $sHtml .= '<td class="t_center">'._p(''.$aApplication['status_name']).'</td>';
            $sHtml .= '<td>';
            if(!empty($aApplication['resume']))
            {
                $sHtml .= '<a class="no_ajax_link" href="'.Phpfox::getParam('core.path_file').'module/jobposting/static/php/download.php?id='.$aApplication['application_id'].'">'._p('download').'</a> | ';
            }            
            $sHtml .= '<a href="#" onclick="ynjobposting.application.view('.$aApplication['application_id'].', \''._p('view_application').'\'); return false;">'._p('view').'</a>';
            $sHtml .= ' | <a href="#" onclick="ynjobposting.application.confirm_delete('.$aApplication['application_id'].', \''._p('core.are_you_sure').'\'); return false;">'._p('delete').'</a>';
            if ($aApplication['status_name']=='pending' || $aApplication['status_name']=='passed')
            {
                $sHtml .= ' | <a href="#" onclick="ynjobposting.application.reject('.$aApplication['application_id'].'); return false;">'._p('reject').'</a>';
            }
            if ($aApplication['status_name']=='pending' || $aApplication['status_name']=='rejected')
            {
                $sHtml .= ' | <a href="#" onclick="ynjobposting.application.pass('.$aApplication['application_id'].'); return false;">'._p('pass').'</a>';
            }
            $sHtml .= '</td>';
        }
        
        return $sHtml;
    }
    
    public function isApplied($iJobId, $iUserId)
    {
        $aApplication = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('job_id = '.(int)$iJobId.' AND user_id = '.(int)$iUserId)
            ->execute('getSlaveRow');
        
        if (!empty($aApplication))
        {
            return true;
        }
        
        return false;
    }
    
    public function getTotalByJobId($iJobId)
    {
        $iCnt = $this->database()->select('COUNT(application_id)')
            ->from($this->_sTable)
            ->where('job_id = '.(int)$iJobId)
            ->execute('getSlaveField');
        
        return $iCnt;
    }
}