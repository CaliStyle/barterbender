<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Admincp_Globalsettings extends Phpfox_Component {

    public function process()
    {
        if ($aVals = $this->request()->getArray('val'))
        {
        	if(!is_numeric($aVals['max_number_cover_photos'])) {
        		return Phpfox_Error::set(_p('please_input_valid_number'));
        	}
			if(!is_numeric($aVals['max_upload_size_cover_photos'])) {
        		return Phpfox_Error::set(_p('please_input_valid_number'));
        	}
			if($aVals['max_number_cover_photos'] <= 0)
			{
				return Phpfox_Error::set(_p('please_input_valid_number'));
			}
			if($aVals['max_upload_size_cover_photos'] <= 0)
			{
				return Phpfox_Error::set(_p('please_input_valid_number'));
			}
			
            $aDefaultVals = array(
            	'max_number_cover_photos' => 8,
            	'max_upload_size_cover_photos' => 500
			);
            Phpfox::getService('auction.process')->deleteGlobalSetting();
            Phpfox::getService('auction.process')->addGlobalSetting($aDefaultVals, $aVals);
        }
        
        $aGlobalSetting = Phpfox::getService('auction')->getGlobalSetting();
		
        $aData = array();
        if (isset($aGlobalSetting['actual_setting']))
        {
            $aData = $aGlobalSetting['actual_setting'];
        }
        elseif (isset($aGlobalSetting['default_setting']))
        {
            $aData = $aGlobalSetting['default_setting'];
        }
        
        $this->template()->setTitle(_p('global_settings'))
                ->setBreadcrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('auction'), $this->url()->makeUrl('admincp.app',['id' => '__module_auction']))
                ->setBreadcrumb(_p('global_settings'))
                ->assign(array(
                    'aForms' => $aData,
                    'core_path' => Phpfox::getParam('core.path')
                        )
        );
    }

}

?>