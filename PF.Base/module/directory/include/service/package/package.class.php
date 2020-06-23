<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Package_Package extends Phpfox_service {
	
	public function __construct()
	{

    }

	public function getById($package_id)
	{
		$aRow = $this->database()->select('pkg.*')
			->from(Phpfox::getT("directory_package"), 'pkg')
			->where('pkg.package_id = '.(int)$package_id)
			->execute('getSlaveRow');

		$themes = 			$this->database()->select('pth.*')
			->from(Phpfox::getT("directory_package_theme"), 'pth')
			->where('pth.package_id = '.(int)$package_id)
			->execute('getSlaveRows');
		$aRow['themes'] = $themes;
		$modules = 			$this->database()->select('pmd.*, module.*')
			->from(Phpfox::getT("directory_package_module"), 'pmd')
			->join(Phpfox::getT('directory_module'), 'module', 'module.module_id = pmd.module_id')
			->where('pmd.package_id = '.(int)$package_id)
			->execute('getSlaveRows');
		$aRow['modules'] = $modules;
		$settings = 			$this->database()->select('psm.*')
			->from(Phpfox::getT("directory_package_setting_mapping"), 'psm')
			->where('psm.package_id = '.(int)$package_id)
			->execute('getSlaveRows');
		$aRow['settings'] = $settings;

		return $aRow;
	}

	public function getItemCount()
	{			
		$oQuery = $this -> database()
						-> select('count(*)')
						-> from(Phpfox::getT("directory_package"),'pk');
						
		return $oQuery->execute('getSlaveField');
	}

	public function getPackages($iPage = 0, $iLimit = 0, $iCount = 0)
	{						
		$oSelect = $this -> database() 
						 -> select('*')
						 -> from(Phpfox::getT("directory_package"), 'pk');
						 
		$oSelect->limit($iPage, $iLimit, $iCount);

		$aPackages = $oSelect->execute('getSlaveRows');
		
	 	return $aPackages;
	}

}

?>