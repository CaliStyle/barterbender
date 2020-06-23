<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Help_Process extends Phpfox_Service 
{
	private $_sDirPetition = "";
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('petition_help');
		$this->_sDirPetition = Phpfox::getParam('core.dir_pic'). 'petition/';
	}
	
	public function delete($iId)
	{
		$aHelp = Phpfox::getService('petition.help')->getHelpForEdit($iId);
		
		if (!isset($aHelp['help_id']))
		{
			return false;
		}

		if(!empty($aHelp['image_path']))
		{
			$aImages = array(
					    Phpfox::getParam('core.dir_pic') . sprintf($aHelp['image_path'], ''),
					    Phpfox::getParam('core.dir_pic') . sprintf($aHelp['image_path'], '_80'),
					    Phpfox::getParam('core.dir_pic') . sprintf($aHelp['image_path'], '_200'),					    
					    );
			$iFileSizes = 0;
			
			foreach ($aImages as $sImage)
			{
				if (file_exists($sImage))
				{
					$iFileSizes += filesize($sImage);
					@unlink($sImage);
				}
			}
			
			if ($iFileSizes > 0)
			{
				if ($sPlugin = Phpfox_Plugin::get('petition.service_process_delete__pre_space_update')){return eval($sPlugin);}
				Phpfox::getService('user.space')->update($aHelp['user_id'], 'petition', $iFileSizes, '-');
			}
		}
				
		$this->database()->delete(Phpfox::getT('petition_help'), "help_id = " . (int) $iId);		
		
		return true;
	}
	
	public function add($aVals)
	{
		$oFile = Phpfox::getLib('file');
		$oImage = Phpfox::getLib('image');		
		$oFilter = Phpfox::getLib('parse.input');		
				
		// Check if links in titles
		if (!Phpfox::getLib('validator')->check($aVals['title'], array('url')))
		{
			return Phpfox_Error::set(Phpfox::getPhrase('petition.we_do_not_allow_links_in_titles'));
		}
		
		$sTitle = $oFilter->clean($aVals['title'], 255);	
		$aInsert = array(
			'title' 		=> $sTitle,
			'content'		=> $oFilter->clean($aVals['content']),
			'content_parsed'	=> $oFilter->prepare($aVals['content']),
			'image_path' => '',
			'server_id' => 0
		);
		if(!$aVals['help_id'])
		{
			$iOrder = $this->database()->select('MAX(ph.ordering)')
						   ->from($this->_sTable,'ph')						
						   ->execute('getSlaveField');
						   
			$aInsert['ordering'] = $iOrder + 1;
		}		
			
		if (isset($_FILES['icon']['name']) && !empty($_FILES['icon']['name']))
		{
			if (!$oFile->load('icon', array('jpg', 'gif', 'png'),Phpfox::getParam('petition.help_icon_file_size_limit')/1024))
			{
				return false;
			}
			
			$sFileName = $oFile->upload('icon', $this->_sDirPetition , '');
			$aInsert['image_path'] = 'petition/'.$sFileName;
			$aInsert['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
			if ($aInsert['image_path'] == false)
			{
				return Phpfox_Error::set('Could not upload files');
			}

			//Remove old image
			if($iId = $aVals['help_id'])
			{
				$aHelp = Phpfox::getService('petition.help')->getHelpForEdit($iId);
				if(!empty($aHelp['image_path']))
				{
					$aImages = array(
							    Phpfox::getParam('core.dir_pic') . sprintf($aHelp['image_path'], ''),
							    Phpfox::getParam('core.dir_pic') . sprintf($aHelp['image_path'], '_80'),
							    Phpfox::getParam('core.dir_pic') . sprintf($aHelp['image_path'], '_200'),					    
							    );
					$iFileSizes = 0;
					foreach ($aImages as $sImage)
					{
						if (file_exists($sImage))
						{
							$iFileSizes += filesize($sImage);
							@unlink($sImage);
						}
					}
					
					if ($iFileSizes > 0)
					{
						if ($sPlugin = Phpfox_Plugin::get('petition.service_process_delete__pre_space_update')){return eval($sPlugin);}
						Phpfox::getService('user.space')->update($aHelp['user_id'], 'petition', $iFileSizes, '-');
					}
				}
			}
			$oImage->createThumbnail($this->_sDirPetition . sprintf($sFileName, ''), $this->_sDirPetition . sprintf($sFileName, '_' . 500), 500, 500);			
			$oImage->createThumbnail($this->_sDirPetition . sprintf($sFileName, ''), $this->_sDirPetition . sprintf($sFileName, '_' . 200), 200, 200);
			$oImage->createThumbnail($this->_sDirPetition . sprintf($sFileName, ''), $this->_sDirPetition . sprintf($sFileName, '_' . 80), 80, 80);			
		}
		
		if($iId = $aVals['help_id'])
		{			
			$this->database()->update($this->_sTable, $aInsert, 'help_id = ' . (int) $iId);			
		}
		else
		{
			$iId = $this->database()->insert($this->_sTable,$aInsert);
		}		
		return $iId;
	}
		
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('petition.service_category_process__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>