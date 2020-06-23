<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		younet
 * @package 		Phpfox_Component
 * @version 		3.01
 */
 
class Resume_Component_Controller_Import extends Phpfox_Component
{
	public function process()
	{

		$iId = 0;

		$fields = '~:(id,first-name,last-name,maiden-name,formatted-name,phonetic-first-name,phonetic-last-name,formatted-phonetic-name,headline,location,industry,distance,phone-numbers,relation-to-viewer,current-status,current-status-timestamp,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,picture-urls::(original),site-standard-profile-request,api-standard-profile-request,public-profile-url,email-address,last-modified-timestamp,proposal-comments,associations,honors,interests,publications,patents,languages,skills,certifications,educations,courses,volunteer,three-current-positions,three-past-positions,num-recommenders,recommendations-received,mfeed-rss-url,following,job-bookmarks,suggestions,date-of-birth,member-url-resources,related-profile-views,honors-awards,projects,im-accounts,main-address,twitter-accounts,primary-twitter-account)';

		$providerLinkedIn = Phpfox::getService('socialbridge') -> getProvider('linkedin');

		$linkedin_data = $providerLinkedIn -> getProfile($fields);
		$linkedin_data_json = json_encode($linkedin_data);
		$linkedin_data_json = json_decode($linkedin_data_json);

		if ($linkedin_data_json)
		{
			
			$aVals = Phpfox::getService("resume.process") -> import($linkedin_data_json);
                       
			$aVals['linkedin'] = 1;
			$aVals['resume_id'] = $iId = Phpfox::getService("resume.basic.process") -> add($aVals);
		
			Phpfox::getService("resume.summary.process") -> update($aVals);
			
			Phpfox::getService("resume.basic.process") -> updatePositionSection($iId, 9);
			
			Phpfox::getService("resume.process") -> importEducation($linkedin_data_json, $iId);
			
			Phpfox::getService("resume.process") -> importskills($linkedin_data_json, $iId);
			
			Phpfox::getService("resume.process") -> importExperience($linkedin_data_json, $iId);
			
			Phpfox::getService("resume.process") -> importpublications($linkedin_data_json, $iId);
			
			Phpfox::getService("resume.process") -> importlanguages($linkedin_data_json, $iId);
			
			Phpfox::getService("resume.process") -> importcertifications($linkedin_data_json, $iId);
			
			Phpfox::getService("resume.process") -> ImportAddition($linkedin_data_json, $iId);
		}
		
		if($iId)
		{
			Phpfox::getLib('url') -> send('resume.add.id_'.$iId,null, _p('resume.import_resume_successfully'));
			exit;
		}
		
		// if failed.
		$this->template()->assign(array('iErrorCode' => 2));
		Phpfox_Error::set(_p('resume.cannot_import_resume_from_linkedIn'));
	}
	
	
	public function clean()
	{

	}

}