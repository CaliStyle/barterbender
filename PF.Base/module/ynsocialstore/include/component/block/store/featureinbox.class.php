<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Featureinbox extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
        $iStoreId = $this->getParam('iStoreId');
        $aEditedStore = Phpfox::getService('ynsocialstore')->getStoreForEdit($iStoreId);
        /*get featured or not*/
		$aEditedStore['expired_date'] = '';
        if(1 == $aEditedStore['feature_end_time'])
        {
            $aEditedStore['is_unlimited'] = 1;   
            $aEditedStore['expired_date'] = '';
        } else if($aEditedStore['feature_end_time'] >= PHPFOX_TIME)
        {
            $aEditedStore['is_unlimited'] = 0;
            $aEditedStore['expired_date'] = date(Phpfox::getParam('core.global_update_time'),$aEditedStore['feature_end_time']);   
        }

		$this->template()->assign(array(
				'iStoreId' => $iStoreId,
				'sFormUrl' => $this->url()->makeUrl('ynsocialstore.store.add') .'id_'.$iStoreId, 
				'aEditedStore' => $aEditedStore,
				'iDefaultFeatureFee' => $aEditedStore['feature_fee'],
				'aCurrentCurrencies' => Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies(),
			)
		);
		return 'block';
	}

}

?>
