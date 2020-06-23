	
<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
                 
class TourGuides_Component_Block_editstep extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $iId = $this->getParam('id');
		$iOrder = $this->getParam('iOrder');
        $aStep = phpfox::getService('tourguides.steps')->getStep($iId);
        $aStep['delay'] = $aStep['delay']/1000;
		$aLanguages  = Phpfox::getService('language')->getAll();
		$aLang = array();
		foreach($aLanguages as $iKey => $aLanguage)
		{
			$aLang[$aLanguage['language_id']] = $aLanguage['title'];
		}		
		
		$sCurrentLangId = Phpfox::getService('language')->getDefaultLanguage();
		$aCurrentLanguages = Phpfox::getService('language')->getLanguage($sCurrentLangId);
		
        $this->template()->assign(array(
                    'id' =>$iId,
                    'iOrder'=>$iOrder,
                    'aStep' =>$aStep,
					'aLanguages' => $aLang,
					'sCurrentLangId' => $sCurrentLangId,
					'sCurrentLangTitle' => $aCurrentLanguages['title']					
             ));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('tourguides.component_block_view_clean')) ? eval($sPlugin) : false);
	}
}

?>