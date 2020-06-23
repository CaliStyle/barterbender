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

class Jobposting_Component_Block_Job_Embed extends Phpfox_Component 
{
    /**
	 * Class process method wnich is used to execute this component.
	 */
    public function process()
    {
        $iId = $this->request()->get('id');
		$en_photo = $this->request()->get('en_photo', 0);
		$en_description = $this->request()->get('en_description', 0);
        
        $aJob = Phpfox::getService('jobposting.job')->getForEmbed($iId);
		if(empty($aJob['job_id']))
		{
			return Phpfox_Error::set(_p('job_not_found'));
		}
		
		$aJob['title_shorten'] = Phpfox::getLib('parse.output')->shorten($aJob['title'], 40, '...');
		$aJob['description_shorten'] = Phpfox::getLib('parse.output')->shorten($aJob['description'], 160, '...');
		$aJob['no_image'] = Phpfox::getParam('core.path_file').'module/jobposting/static/image/default/default_ava.png';
        
        $this->template()->assign(array(
            'aJob' => $aJob,
            'en_photo' => $en_photo,
            'en_description' => $en_description
        ));
    }

}

?>