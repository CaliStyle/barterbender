<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Controller_Admincp_Settinggapi extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEdit = false;
        $aForms = array(
			'oauth2_client_id' => '',
            'oauth2_client_secret' => '',
            'developer_key' => '',
        );
        
        if($aGapi = Phpfox::getService('fevent.gapi')->getForManage()) {
            $bIsEdit = true;
            $aForms['oauth2_client_id'] = $aGapi['oauth2_client_id'];
            $aForms['oauth2_client_secret'] = $aGapi['oauth2_client_secret'];
            $aForms['developer_key'] = $aGapi['developer_key'];
        }
        
        $aValidation = array(
            'developer_key' => _p('provide_api_key'),
            'oauth2_client_secret' => _p('provide_client_secret'),
			'oauth2_client_id' => _p('provide_client_id'),
		);
        
        $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
        
        if(($aVals = $this->request()->get('val')) && $oValid) {
            if($bIsEdit) {
                if(Phpfox::getService('fevent.gapi.process')->update($aVals, $aGapi['id'])) {
                    $this->url()->send('current', null, _p('google_api_details_successfully_updated'));
                }
            } else {
                if(Phpfox::getService('fevent.gapi.process')->add($aVals)) {
                    $this->url()->send('current', null, _p('google_api_details_successfully_added'));
                }
            }
        }
        $sCorePath = Phpfox::getParam('core.path_file');
        
        $this->template()->setTitle(_p('setting_google_api'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app',['id' => '__module_fevent']))
            ->setBreadcrumb(_p('setting_google_api'), $this->url()->makeUrl('admincp.fevent.settinggapi'))
            ->setPhrase(array(
                'fevent.view',
                'fevent.hide',
            ))
            ->assign(array(
                'aForms' => $aForms,
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
                'sCoreHost' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . Phpfox::getParam('core.host'),
                'sRedirectUri' => $sCorePath.'module/fevent/static/gcalendar.php',
                'sCorePath' => $sCorePath.'module/fevent/static',
            ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_index_clean')) ?
            eval($sPlugin) : false);
    }
}

?>
