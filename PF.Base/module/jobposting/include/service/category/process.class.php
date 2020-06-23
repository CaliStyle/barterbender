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

class Jobposting_Service_Category_Process extends Core_Service_Systems_Category_Process
{
    private $_iStringLengthCategoryName;
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
	    parent::__construct();
	    $this->_iStringLengthCategoryName = 40;
		$this->_sTable = Phpfox::getT('jobposting_category');
	}

	public function checkDelete($iId){
		return $this->database()->select('count(*)')
			->from(Phpfox::getT('jobposting_category_data'))
			->where('category_id = '.$iId)
			->execute('getField');
	}
	
	public function delete($iId)
	{
		if($this->checkDelete($iId)>0)
		{
			Phpfox_Error::set(_p('you_can_only_delete_empty_industry'));
			return false;
		}
		$this->database()->update($this->_sTable, array('parent_id' => 0), 'parent_id = ' . (int) $iId);
		
                /* http://www.phpfox.com/tracker/view/6349/ 
                 * To fix this we can create a setting letting the admin choose 
                 * whether to delete the jobposting belonging to the category being
                 * deleted or to simply remove the category from them
                 */
        if ( true /* Phpfox::getParam('jobposting.keep_[jobposting_after_category_delete') */)
        {
            $this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
            $this->database()->delete(Phpfox::getT('jobposting_category_data'), 'category_id = ' . (int)$iId);
            $this->cache()->remove('jobposting', 'substr');
            return true;
        }

        //Delete phrase of category
        $aCategory = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('category_id=' . (int) $iId)
            ->execute('getSlaveRow');

        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])){
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }
		
		$this->database()->delete($this->_sTable, 'category_id = ' . (int) $iId);
		
		$this->cache()->remove('jobposting', 'substr');
		
		return true;
	}
	
	public function updateOrder($aVals)
	{
		foreach ($aVals as $iId => $iOrder)
		{
			$this->database()->update($this->_sTable, array('ordering' => $iOrder), 'category_id = ' . (int) $iId);
		}
		
		$this->cache()->remove('jobposting', 'substr');
		
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
		if ($sPlugin = Phpfox_Plugin::get('jobposting.service_category_process__call'))
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

        $this->cache()->remove('jobposting', 'substr');
    }
}

