<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_featureinbox extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
        $iBusinessId = $this->getParam('iBusinessId');
        $aEditedBusiness = Phpfox::getService('directory')->getBusinessForEdit($iBusinessId);
        /*get featured or not*/
        if($aEditedBusiness['feature_start_time'] <= PHPFOX_TIME &&  $aEditedBusiness['feature_end_time'] >= PHPFOX_TIME){
            $aEditedBusiness['featured'] = true;
        }
        else{
            $aEditedBusiness['featured'] = false;
        }

        if(4294967295 == $aEditedBusiness['feature_end_time'])
        {
            $aEditedBusiness['is_unlimited'] = 1;   
            $aEditedBusiness['expired_date'] = '';
        } else if($aEditedBusiness['feature_start_time'] <= PHPFOX_TIME && $aEditedBusiness['feature_end_time'] >= PHPFOX_TIME)
        {
            $aEditedBusiness['is_unlimited'] = 0;
            $aEditedBusiness['expired_date'] = Phpfox::getService('directory.helper')->convertTime($aEditedBusiness['feature_end_time']);   
        }

  		$aGlobalSetting =  Phpfox::getService('directory')->getGlobalSetting();
		$this->template()->assign(array(
				'iBusinessId' => $iBusinessId,
				'sFormUrl' => $this->url()->makeUrl('directory.edit') .'id_'.$iBusinessId, 
				'aEditedBusiness' => $aEditedBusiness,
				'iDefaultFeatureFee' => (int)$aGlobalSetting[0]['default_feature_fee'],
				'aCurrentCurrencies' => Phpfox::getService('directory.helper')->getCurrentCurrencies(),
                'sCustomClassName' => 'ync-block'
			)
		);
		return 'block';
	}

}

?>
