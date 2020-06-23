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
class Document_Component_Controller_Admincp_Addlicense extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {  
        $image_error_message = ""; 
        $iId = $this->request()->get('id');
        
        $aValidation = array(
            'name' => _p('provide_license_name')
        );        
        
        $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));    
         
        if ($aVals = $this->request()->getArray('val'))
        {            
            if ($oValid->isValid($aVals))
            {
                $aLicense = array();
                if(isset($aVals['name']))
                {
                    $aLicense['license_name'] = $aVals['name'];
                }
                if(isset($aVals['reference_url']))
                {
                    $aLicense['reference_url'] = $aVals['reference_url'];
                }
                $this->template()->assign('aForms',$aLicense);

                if(isset($aVals['id']) && $aVals['id'])
                {
                    $iId = $aVals['id'];
                  
                    $server_path = PHPFOX_DIR_MODULE . 'document' . PHPFOX_DS . 'static' . PHPFOX_DS . 'image' . PHPFOX_DS; 
                    $file_type = Phpfox::getService('document.process')->getFileType($_FILES['uploadedfile']['name']);
                    $target_path =  $server_path . md5(time() . $_FILES['uploadedfile']['name']) . '.' . $file_type ;
                    if(!empty($_FILES['uploadedfile']['name']))
                    {
                        if(!$_FILES['uploadedfile']['error'])
                        {  
                            if(preg_match('/image\//i', $_FILES['uploadedfile']['type'])) 
                            { 
                                if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) 
                                {
                                    $aVals['image_url'] = 'document/static/image/' . str_replace($server_path, '', $target_path);
                                    if(Phpfox::getService('document.license.process')->update($aVals))
                                    {
                                        $this->url()->send('admincp.document.managelicense', null, _p('license_successfully_updated'));
                                    }       
                                }   
                            }
                            else
                            {
                                $image_error_message = _p('this_is_not_an_image_file_please_choose_another_image_file');
                            }
                        }
                        else
                        {
                            $image_error_message =  _p('there_were_errors_when_uploading_files');
                        }
                    }
                    else
                    {
                        if (Phpfox::getService('document.license.process')->update($aVals))
                        {
                            $this->url()->send('admincp.document.managelicense', null, _p('license_successfully_updated'));
                        }       
                    }
                   
                }
                else
                {
                    $server_path = PHPFOX_DIR_MODULE . 'document' . PHPFOX_DS . 'static' . PHPFOX_DS . 'image' . PHPFOX_DS; 
                    
                    $file_type = Phpfox::getService('document.process')->getFileType($_FILES['uploadedfile']['name']);
                    $target_path =  $server_path . md5(time() . $_FILES['uploadedfile']['name']) . '.' . $file_type ;
                    if (($_FILES['uploadedfile']['name'] != ""))
                    {
                        if (!$_FILES['uploadedfile']['error'])
                        {
                            if(preg_match('/image\//i', $_FILES['uploadedfile']['type'])) 
                            { 
                                if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) 
                                {
                                    $aVals['image_url'] = 'document/static/image/' . str_replace($server_path, '', $target_path);
                                    if(Phpfox::getService('document.license.process')->add($aVals))
                                    {
                                        $this->url()->send('admincp.document.addlicense', null, _p('license_successfully_added'));
                                    }   
                                }
                            }
                            else
                            {
                                $image_error_message = _p('this_is_not_an_image_file_please_choose_another_image_file');
                            }
                        }
                        else
                        {
                            $image_error_message =  _p('there_were_errors_when_uploading_files');
                        }
                    }
                    else
                    {
                            $image_error_message = _p('choose_an_image_file_to_upload');
                    }   
                }
            }
        }        
        if ($iId)
        {
            $aLicense = Phpfox::getService('document.license.process')->getById($iId);
            if (count($aLicense))
            {
                $this->template()->assign('aForms',$aLicense);
            }
            $this->template()->setTitle(_p('edit_license'))
                ->setBreadCrumb(_p('edit_license'), $this->url()->makeUrl('admincp.document.addlicense', array('id_'.$iId)))
                ->assign(array(            
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'max_file_size' => (0.5 * 1048576),
                    'error_message' => $image_error_message
                )
            );            
        }else
        {
            $this->template()->setTitle(_p('add_license'))
                ->setBreadCrumb(_p('add_license'), $this->url()->makeUrl('admincp.document.addlicense'))
                ->assign(array(            
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'max_file_size' => (0.5 * 1048576),
                    'error_message' => $image_error_message
                )
            );            
        }
        
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}

?>