<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Service_Category_Process extends Core_Service_Systems_Category_Process
{
	/**
	 * Class constructor
	 */
	private $_iStringLengthCategoryName;

	public function __construct()
	{
	    parent::__construct();
		$this->_sTable = Phpfox::getT('channel_category');
		$this->_iStringLengthCategoryName = 255;
	}
	
	public function add($aVals)
	{
	    if(empty($this->_aLanguages[0])){
	        return false;
        }
        $defaultLanguageId = $this->_aLanguages[0]['language_id'];
        //validate phrases
        foreach ($this->_aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($name = strip_tags($aVals['name_' . $aLanguage['language_id']]))){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                $aVals['name_' . $aLanguage['language_id']] = $name;
            }
            else {
                $aVals['name_' . $aLanguage['language_id']] = $aVals['name_'. $defaultLanguageId];
            }
            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                return Phpfox_Error::set(_p('category_language_name_name_must_beLess_than_limit', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
            }
        }
        $this->setModule('videochannel');
        $this->setTable($this->_sTable);
        $categoryId = parent::add($aVals);
		
		$this->cache()->remove('videochannel');
		$this->cache()->remove('videochannel_category_browse');
		return $categoryId;
	}
	
	public function update($iId, $aVals)
	{
        if ($iId == $aVals['parent_id']) {
            return Phpfox_Error::set(_p('parent_category_and_child_is_the_same'));
        }

        $aLanguages = $this->_aLanguages;

        if(empty($aLanguages[0])){
            return false;
        }
        $defaultLanguageId = $aLanguages[0]['language_id'];

        foreach ($aLanguages as $aLanguage){
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($name = strip_tags($aVals['name_' . $aLanguage['language_id']]))){
                Phpfox::getService('ban')->checkAutomaticBan($aVals['name_' . $aLanguage['language_id']]);
                Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'], $aVals['name'], $name);
            }
            else {
                $aVals['name_' . $aLanguage['language_id']] = $aVals['name_'. $defaultLanguageId];
            }

            if (strlen($aVals['name_' . $aLanguage['language_id']]) > $this->_iStringLengthCategoryName) {
                return Phpfox_Error::set(_p('category_language_name_name_must_beLess_than_limit', ['limit' => $this->_iStringLengthCategoryName, 'language_name' => $aLanguage['title']]));
            }
        }
        $this->setModule('videochannel');
        $this->setTable($this->_sTable);
        $this->setTableData(Phpfox::getT('channel_category_data'));
        $this->_sItemId = 'video_id';
        $aVals['edit_id'] = $iId;
        $update = parent::update($aVals);

        if(empty($update)) {
            return false;
        }

		$this->cache()->remove('videochannel');
		
		return true;
	}
	
	public function delete($iId)
	{
	  
		$this->database()->update($this->_sTable, array('parent_id' => 0), 'parent_id = ' . (int) $iId);
		
                /* http://www.phpfox.com/tracker/view/6349/ 
                 * To fix this we can create a setting letting the admin choose 
                 * whether to delete the videos belonging to the category being
                 * deleted or to simply remove the category from them
                 */
                if ( false /* Phpfox::getParam('videochannel.keep_video_after_category_delete') */)
                {
                    $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
                    $this->database()->delete(Phpfox::getT('channel_category_data'), 'category_id = ' . (int)$iId);
                    $this->cache()->remove('videochannel');
                    return true;
                }
		$aVideos = $this->database()->select('m.video_id, m.user_id, m.image_path')
			->from(Phpfox::getT('channel_category_data'), 'mcd')
			->join(Phpfox::getT('channel_video'), 'm', 'm.video_id = mcd.video_id')
			->where('mcd.category_id = ' . (int) $iId)
			->execute('getRows');		
			
		foreach ($aVideos as $aVideo)
		{
			Phpfox::getService('videochannel.process')->delete($aVideo['video_id'], $aVideo);
		}
		$aVideos = $this->database()->select('m.video_id, m.user_id, m.image_path')
			->from(Phpfox::getT('channel_category_data'), 'mcd')
			->join(Phpfox::getT('channel_video'), 'm', 'm.video_id = mcd.video_id')
			->where('mcd.category_id = ' . (int) $iId)
			->execute('getRows');		
			
		foreach ($aVideos as $aVideo)
		{
			Phpfox::getService('videochannel.process')->delete($aVideo['video_id'], $aVideo);
		}
		
		$aChannels = $this->database()->select('m.channel_id')
			->from(Phpfox::getT('channel_category_data'), 'mcd')
			->join(Phpfox::getT('channel_channel'), 'm', 'm.channel_id = mcd.channel_id')
			->where('mcd.category_id = ' . (int) $iId)
			->execute('getRows');
		
		foreach ($aChannels as $aChannel)
		{
			Phpfox::getService('videochannel.channel.process')->deleteChannel($aChannel['channel_id'],true);
		}
			
		$this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
		
		$this->cache()->remove('videochannel');
        $this->cache()->remove('videochannel_category_browse');
		return true;
	}
	
	public function updateOrder($aVals)
	{
		foreach ($aVals as $iId => $iOrder)
		{
			$this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int) $iId);
		}
		
		$this->cache()->remove('videochannel');
		
		return true;
	}
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('videochannel.service_category_process__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

    public function updateActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        $this->database()->update(($this->_sTable), array('is_active' => (int)($iType == '1' ? 1 : 0)), 'category_id' . ' = ' . (int)$iId);

        $this->cache()->remove('videochannel_category_browse');
    }
}

?>