<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Add_New_Address extends Phpfox_Component {

    public function process()
    {	
        $this->setParam('country_child_filter',true); 

        $iAddressId = $this->getParam('address_id',0);
        if($iAddressId)
        {
        	$aAddress = Phpfox::getService('ecommerce')->getAddressById($iAddressId);
			$aAddress['contact_name'] = $aAddress['address_user_name'];
            $aAddress['country_iso'] = $aAddress['address_customer_country_iso'];
			$aAddress['country_child_id'] = $aAddress['address_customer_country_child_id'];
			$aAddress['address_street'] = $aAddress['address_customer_street'];
			$aAddress['address_street_2'] = $aAddress['address_customer_street_2'];
			$aAddress['address_city'] = $aAddress['address_customer_city'];
			$aAddress['address_postal_code'] = $aAddress['address_customer_postal_code'];
			$aAddress['address_country_code'] = $aAddress['address_customer_country_code'];
			$aAddress['address_city_code'] = $aAddress['address_customer_city_code'];
			$aAddress['address_phone_number'] = $aAddress['address_customer_phone_number'];
			$aAddress['address_mobile_number'] = $aAddress['address_customer_mobile_number'];

            $this->setParam('country_child_filter',true); 
            $this->setParam(array(
                            'country_child_value' => $aAddress['country_iso'],
                            'country_child_id' => $aAddress['country_child_id']
                        )
            );

            $this->template()->assign(array(
                'iAddressId' => $iAddressId,
            	'aForms' => $aAddress,            
        	));
        }
		else {
			$this->template()->assign(array(
            	'aForms' => array('country_iso' => 0),            
        	));
		}
		
        $sCountry = $this->getParam('country_iso');
        $sCountries = Phpfox::getService('ecommerce.helper')->getSelectCountriesForSearch($sCountry);

        $this->template()->assign(array(
            'sCountries' => $sCountries,            
        ));

        return 'block';
    }
}

?>
