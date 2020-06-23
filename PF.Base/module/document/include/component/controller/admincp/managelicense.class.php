<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Component_Controller_Admincp_Managelicense extends Phpfox_Component
{
     /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        
        if ($aDeleteIds = $this->request()->getArray('id'))
        {
            if (Phpfox::getService('document.license.process')->deleteMultiple($aDeleteIds))
            {
                $this->url()->send('admincp.document.managelicense', null, _p('licenses_successfully_deleted'));
            }
        }
        $aLicenses = Phpfox::getService('document.license.process')->get();
        $licenses = array();
        foreach($aLicenses as $aLicense)
        {
            $aLicense['image_url'] = Phpfox::getParam('core.path_file') . 'module/' . $aLicense['image_url'];
            $aLicense['edit_link'] = Phpfox::getLib('url')->makeUrl('admincp.document.addlicense', array('id_' . $aLicense['license_id']));
            $licenses[] = $aLicense;
        }
        $this->template()->setTitle(_p('manage_licenses'))
            ->setBreadcrumb(_p('manage_licenses'), $this->url()->makeUrl('admincp.document.managelicense'))
            ->assign(array(
                    'aLicenses' => $licenses
                    ))
            ->setHeader('cache', array(
                'quick_edit.js' => 'static_script'            
            ));
    }
}
?>
