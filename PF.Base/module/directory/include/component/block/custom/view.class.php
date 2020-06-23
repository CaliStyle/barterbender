<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
class Directory_Component_Block_Custom_View extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        /*custom field*/
        $iBusinessId = $this->getParam('business_id');
        $aMainCategory = Phpfox::getService('directory')->getBusinessMainCategory($iBusinessId);
        $aCustomFields = Phpfox::getService('directory')->getCustomFieldByCategoryId($aMainCategory['category_id']);
        $keyCustomField = array();
        $aCustomData = array();
        if($iBusinessId){
            $aCustomDataTemp = Phpfox::getService('directory.custom')->getCustomFieldByBusinessId($iBusinessId);
            
                if(count($aCustomFields)){
                    foreach ($aCustomFields as $aField) {
                            foreach ($aCustomDataTemp as $aFieldValue) {
                                if($aField['field_id'] == $aFieldValue['field_id']){
                                    $aCustomData[] = $aFieldValue;
                                }
                            }
                    }
                }
        }

        if(count($aCustomData)){
            $aCustomFields  = $aCustomData; 
        }


		$this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
		));
	}

}

?>
