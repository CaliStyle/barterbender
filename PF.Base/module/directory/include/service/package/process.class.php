<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Service_Package_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	

    }

	public function add($aVals)
	{
		$oParseInput = Phpfox::getLib('parse.input');
		$aCurrentCurrencies = Phpfox::getService('directory.helper')->getCurrentCurrencies();

		if(!Phpfox::getService('directory.helper')->isNumeric($aVals['expire_number'])){
			return Phpfox_Error::set(_p('directory.valid_period_have_to_be_a_number'));
		}
		if((int)$aVals['expire_number'] <= 0 && $aVals['expire_type'] != 0)
		{
			return Phpfox_Error::set(_p('Valid period have to be greater than 0'));
		}
		if(!Phpfox::getService('directory.helper')->isNumeric($aVals['fee'])){
			return Phpfox_Error::set(_p('directory.package_fee_have_to_be_a_number'));
		}
        elseif($aVals['fee'] < 0)
        {
            return Phpfox_Error::set(_p('Fee have to be greater than or equal to 0'));
        }
		if(!Phpfox::getService('directory.helper')->isNumeric($aVals['max_cover_photo'])){
			return Phpfox_Error::set(_p('directory.maximum_cover_photos_have_to_be_a_number'));
		}
		elseif($aVals['max_cover_photo'] <= 0)
        {
            return Phpfox_Error::set(_p('Maximum cover photos have to be greater than 0'));
        }
		if(isset($aVals['themes']) == false || count($aVals['themes']) <= 0){
			return Phpfox_Error::set(_p('directory.select_least_one_theme'));
		}
		if(isset($aVals['modules']) == false || count($aVals['modules']) <= 0){
			return Phpfox_Error::set(_p('directory.select_least_one_module'));
		}
		
		$iId = $this->database()->insert(Phpfox::getT("directory_package"), array(
				'expire_number' => $aVals['expire_number'],
				'max_cover_photo' => $aVals['max_cover_photo'],
				'name' => $oParseInput->clean($aVals['name'], 255),
				'expire_type' => $aVals['expire_type'],
				'fee' => $aVals['fee'], 
				'currency' => $aCurrentCurrencies[0]['currency_id'], 
			)
		);

		if($iId){
			if(isset($aVals['themes']) && is_array($aVals['themes'])){
				foreach ($aVals['themes'] as $key => $value) {
					$this->database()->insert(Phpfox::getT("directory_package_theme"), array(
									'package_id' => (int)$iId,
									'theme_id' => (int)$value,
								)
					);
				}
			}
			if(isset($aVals['modules']) && is_array($aVals['modules'])){
				foreach ($aVals['modules'] as $key => $value) {
					$this->database()->insert(Phpfox::getT("directory_package_module"), array(
									'package_id' => (int)$iId,
									'module_id' => (int)$value,
								)
					);
				}
			}

			if(isset($aVals['settings']) && is_array($aVals['settings'])){
				foreach ($aVals['settings'] as $key => $value) {
					$this->database()->insert(Phpfox::getT("directory_package_setting_mapping"), array(
									'package_id' => (int)$iId,
									'setting_id' => (int)$value,
								)
					);
				}
			}
		}
		
		return $iId;
	}

	public function update($iId, $aVals)
	{
		$oParseInput = Phpfox::getLib('parse.input');
		$aCurrentCurrencies = Phpfox::getService('directory.helper')->getCurrentCurrencies();

		if(!Phpfox::getService('directory.helper')->isNumeric($aVals['expire_number'])){
			return Phpfox_Error::set(_p('directory.valid_period_have_to_be_a_number'));
		}
		if(!Phpfox::getService('directory.helper')->isNumeric($aVals['fee'])){
			return Phpfox_Error::set(_p('directory.package_fee_have_to_be_a_number'));
		}
		if(!Phpfox::getService('directory.helper')->isNumeric($aVals['max_cover_photo'])){
			return Phpfox_Error::set(_p('directory.maximum_cover_photos_have_to_be_a_number'));
		}
		if(isset($aVals['themes']) == false || count($aVals['themes']) <= 0){
			return Phpfox_Error::set(_p('directory.select_least_one_theme'));
		}
		if(isset($aVals['modules']) == false || count($aVals['modules']) <= 0){
			return Phpfox_Error::set(_p('directory.select_least_one_module'));
		}
		
		$this->database()->update(Phpfox::getT("directory_package"), array(
			'expire_number' => $aVals['expire_number'],
			'max_cover_photo' => $aVals['max_cover_photo'],
			'`name`' => $oParseInput->clean($aVals['name'], 255),
			'expire_type' => $aVals['expire_type'],
			'fee' => $aVals['fee'], 
			'currency' => $aCurrentCurrencies[0]['currency_id'], 
		), 'package_id = ' . (int) $iId);
		
		if($iId){
			$this->database()->delete(Phpfox::getT("directory_package_theme"),'package_id = '.$iId);
			if(isset($aVals['themes']) && is_array($aVals['themes'])){
				foreach ($aVals['themes'] as $key => $value) {
					$this->database()->insert(Phpfox::getT("directory_package_theme"), array(
									'package_id' => (int)$iId,
									'theme_id' => (int)$value,
								)
					);
				}
			}

			$this->database()->delete(Phpfox::getT("directory_package_module"),'package_id = '.$iId);
			if(isset($aVals['modules']) && is_array($aVals['modules'])){
				foreach ($aVals['modules'] as $key => $value) {
					$this->database()->insert(Phpfox::getT("directory_package_module"), array(
									'package_id' => (int)$iId,
									'module_id' => (int)$value,
								)
					);
				}
			}

			$this->database()->delete(Phpfox::getT("directory_package_setting_mapping"),'package_id = '.$iId);
			if(isset($aVals['settings']) && is_array($aVals['settings'])){
				foreach ($aVals['settings'] as $key => $value) {
					$this->database()->insert(Phpfox::getT("directory_package_setting_mapping"), array(
									'package_id' => (int)$iId,
									'setting_id' => (int)$value,
								)
					);
				}
			}
		}

		return true;
	}

	public function delete($iId)
	{
		$this->database()->delete(Phpfox::getT("directory_package_theme"),'package_id = '.$iId);
		$this->database()->delete(Phpfox::getT("directory_package_module"),'package_id = '.$iId);
		$this->database()->delete(Phpfox::getT("directory_package_setting_mapping"),'package_id = '.$iId);
		return $this->database()->delete(Phpfox::getT("directory_package"),'package_id = '.$iId);
	}

	public function activepackage($id, $active){
		return $this->database()->update(Phpfox::getT("directory_package"),array(
			'active' => $active,
		),'package_id = '.$id);
	}

}

?>