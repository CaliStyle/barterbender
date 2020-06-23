<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		AnNT
 * @package  		Module_jobposting
 */

class Jobposting_Component_Controller_Company_Manage extends Phpfox_Component 
{
    /**
	 * Class process method wnich is used to execute this component.
	 */
    public function process()
    {
        $iJobId = $this->request()->get('job');
        $iPage = $this->request()->get('page');
        $iShowLimit = 50;

        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iJobId);

        if (!$aJob)
        {
            return Phpfox_Error::display(_p('the_job_you_are_looking_for_cannot_be_found'));
        }
        
        list($iCnt, $aApplications) = Phpfox::getService('jobposting.application')->getByJobId($iJobId, $iPage, $iShowLimit);        
        
        Phpfox::getLib('pager')->set(
            array('page' => $this->request()->get('page'),
                'size' => $iShowLimit,
                'count' => $iCnt));


        $this->template()->setTitle(_p('manage_job_posted'))
            ->setBreadcrumb(_p('job_posting'), $this->url()->makeUrl('jobposting'))
            ->setBreadcrumb(_p('managing_company'), $this->url()->makeUrl('jobposting.company.add', array('id' => $aJob['company_id'])))
            ->setBreadcrumb(_p('manage_job_posted'), $this->url()->makeUrl('jobposting.company.add.jobs', array('id' => $aJob['company_id'])))
            ->setBreadcrumb(_p('view_applications'), $this->url()->makeUrl('jobposting.company.manage', array('job' => $iJobId)), true)
            ->setHeader('cache', array(
                'table.css' => 'style_css',
                'jobposting.js' => 'module_jobposting',
            ))
            ->assign(array(
                'page' => $this->request()->get('page'),
                'aApplications' => $aApplications,
                'aJob' => $aJob,
                'urlModule' => Phpfox::getParam('core.path_file').'module/',
                'core_path' => Phpfox::getParam('core.path_file')
            ));
        Phpfox::getService('jobposting.helper')->buildMenu();
    }
    
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('jobposting.jobposting_component_controller_company_manage_clean')) ? eval($sPlugin) : false);
	}
}