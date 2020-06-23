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

class JobPosting_Service_Application_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('jobposting_application');
    }
    
    public function delete($iId)
    {
        $aApplication = $this->database()->select('*')->from($this->_sTable)->where('application_id = '.(int)$iId)->execute('getSlaveRow');
        if(!$aApplication)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_application_you_want_to_delete'));
        }

        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId($aApplication['job_id']);
        if(isset($aJob['job_id']) && (int)$aJob['total_application'] > 0){
            $this->database()->update(Phpfox::getT('jobposting_job'), array('total_application' => 'total_application - 1'), 'job_id = '.$aApplication['job_id'], false);
        }        
        
        $this->database()->delete($this->_sTable, 'application_id = '.$iId);
        $sTitle = Phpfox::getService('jobposting')->getItemTitle('job',$aApplication['job_id']);
        Phpfox::getService('notification.process')->add('jobposting_deletedapplication', $aApplication['job_id'], $aApplication['user_id']);
        Phpfox::getLib('mail')->to($aApplication['email'])
            ->subject(_p('your_application_job_at_site_title_was_status', array(
                'site_title' => Phpfox::getParam('core.site_title'),
                'status' => strtolower(_p('deleted'))
            )))
            ->message(_p('your_application_on_job_title_at_site_title_was_be_status_for_more_information_please_visit_our_website_a_href_link_link_a', array(
                'title' => $sTitle,
                'status' => strtolower(_p('deleted')),
                'link' => Phpfox::getLib('url')->permalink('jobposting', $aApplication['job_id'], $sTitle),
                'site_title' => Phpfox::getParam('core.site_title')
            )))
            ->send();
        return $this->database()->update($this->_sTable, array('status' => Phpfox::getService('jobposting.application')->getStatusKeyByName($sStatus)), 'application_id = '.$iId);

        return true;
    }
    	
    public function updateStatus($iId, $sStatus)
    {
        $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('jobposting_application'),'jp')
                ->where('application_id = '.$iId)
                ->execute('getSlaveRow');
        $sTitle = Phpfox::getService('jobposting')->getItemTitle('job',$aRow['job_id']);
        #Notify and email
        Phpfox::getService('notification.process')->add('jobposting_'.$sStatus.'application', $aRow['job_id'], $aRow['user_id']);
        Phpfox::getLib('mail')->to($aRow['email'])
                            ->subject(_p('your_application_job_at_site_title_was_status', array(
                                'site_title' => Phpfox::getParam('core.site_title'),
                                'status' => $sStatus
                            )))
                            ->message(_p('your_application_on_job_title_at_site_title_was_be_status_for_more_information_please_visit_our_website_a_href_link_link_a', array(
                                'title' => $sTitle,
                                'status' => ($sStatus == 'passed')? strtolower(_p('passed')): strtolower(_p('rejected')),
                                'link' => Phpfox::getLib('url')->permalink('jobposting', $aRow['job_id'], $sTitle),
                                'site_title' => Phpfox::getParam('core.site_title')
                            )))
                            ->send();
        return $this->database()->update($this->_sTable, array('status' => Phpfox::getService('jobposting.application')->getStatusKeyByName($sStatus)), 'application_id = '.$iId);

    }
    
}