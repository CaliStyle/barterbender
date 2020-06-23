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
class Document_Component_Controller_Add extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('document.can_view_documents', true);

        $scribd_error_message = "";
        if (Phpfox::getParam('document.api_key') == "" || Phpfox::getParam('document.secret_key') == "")
        {
            if (Phpfox::getParam('document.api_viewer'))
            {
                $scribd_error_message = _p('please_set_up_scribd_account');
            }
        }
        $this->template()->assign('scribd_error_message', $scribd_error_message);
        if ($scribd_error_message != "")
            return false;
        $bIsEdit = false;
        $bCanEditPersonalData = true;

        $category_error_message = "";
        $file_error_message = "";
        $image_error_message = 0;
        $sModule = $this->request()->get('module', false);
        $sModuleId = $this->request()->get('module_id', false);
        $iItem = $this->request()->getInt('item', false);

        $aCallback = false;
        if ($sModule !== false && $iItem !== false)
        {
            if (($aCallback = Phpfox::callback('document.getDocumentDetails', array('item_id' => $iItem, 'module_id' => $sModule))))
            {
                $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
                if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'document.share_documents'))
                {
                    return Phpfox_Error::display(_p('unable_to_create_this_item_due_to_privacy_settings'));
                }
            }
        }

        if ($sModule == "")
        {
            $sModule = 'document';
        }

        if (($iEditId = $this->request()->getInt('id')))
        {
            $oDocument = Phpfox::getService('document.process');
            $aRow = $oDocument->getDocumentForEdit($iEditId);
            if(!$aRow)
            {
                return Phpfox_Error::display(_p('unable_to_edit_this_document'));
            }

            // Can edit
            if (!(Phpfox::getUserId() == $aRow['user_id'] && Phpfox::getUserParam('document.can_edit_own_document') || Phpfox::getUserParam('document.can_edit_other_document'))) {
                return Phpfox_Error::display(_p('unable_to_edit_this_document'));
            }

            // for tag cloud feature
            if (Phpfox::isModule('tag'))
            {
                $aTags = Phpfox::getService('tag')->getTagsById('document', $aRow['document_id']);
                if (isset($aTags[$aRow['document_id']]))
                {
                    $aRow['tag_list'] = '';
                    foreach ($aTags[$aRow['document_id']] as $aTag)
                    {
                        $aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
                }
            }

			if (!empty($aRow['module_id']))
			{
				$sModule = $aRow['module_id'];
				$iItem = $aRow['item_id'];
			}

            $bIsEdit = true;
            $showCategories = Phpfox::getService('document.category')->getCategoryIds($aRow['document_id']);
            $this->template()->setHeader(array('<script type="text/javascript">$Behavior.documentEditCategory = function(){var aCategories = explode(\',\', \'' . $showCategories . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }};</script>', ))->assign(array('aForms' => $aRow,'no_image_url' => Phpfox::getParam('core.path_file') . 'module/document/static/image/google_cover.png'));

            (($sPlugin = Phpfox_Plugin::get('document.component_controller_add_process_edit')) ? eval($sPlugin) : false);
        }
        else
        {
            Phpfox::getUserParam('document.add_new_document', true);
        }

		
		
        $aValidation = array(
            'title' => array('def' => 'required', 'title' => _p('fill_title_for_document')),
            'text' => array('def' => 'required', 'title' => _p('add_description_to_document')),
            );

        (($sPlugin = Phpfox_Plugin::get('document.component_controller_add_process_validation')) ? eval($sPlugin) : false);

        $oValid = Phpfox::getLib('validator')->set(array('sFormName' => 'core_js_document_form', 'aParams' => $aValidation));

		if (!$bIsEdit && $aCallback == false && !empty($sModule) && Phpfox::hasCallback($sModule, 'getItem'))
		{
            if($sModuleId == 'groups') {
                $aCallback = Phpfox::callback($sModuleId . '.getItem', $iItem);
            }
            else {
                $iItemId = $iItem;
                $aCallback = Phpfox::callback($sModule . '.getItem', $iItemId);
                $sURL = $this->url()->makeUrl('document.add', array(
                    'module' => 'pages',
                    'item' => $aCallback['page_id'],
                    'id' => $iEditId));
                $this->url()->send($sURL, null, null);
                return true;
            }
		}
		else
		{

		}		
		
        $iMaxFileSize = (Phpfox::getUserParam('document.document_max_file_size') === 0 ? (200 * 1048576) : (Phpfox::getUserParam('document.document_max_file_size') * 1048576));
        $oFile = Phpfox::getLib('file');
        if ($aVals = $this->request()->getArray('val'))
        {

            if ($oValid->isValid($aVals))
            {
                if ($aVals['category'][0] == "")
                {
                    $category_error_message = "true";
                }
                // ADD NEW PUBLISH
                if (isset($aVals['publish']))
                {
                    $server_path = Phpfox::getParam('core.dir_file') . 'document' . PHPFOX_DS;

                    $file_type = Phpfox::getService('document.process')->getFileType($_FILES['uploadedfile']['name']);
                    // $target_path = Phpfox::getLib('file')->getBuiltDir($server_path) . md5(time() . $_FILES['uploadedfile']['name']) . '.' . $file_type;
                    $document = array();
                    if (($_FILES['uploadedfile']['name'] != ""))
                    {
                        $document['document_file_name'] = $_FILES['uploadedfile']['name'];
                        $document['document_file_type'] = $_FILES['uploadedfile']['type'];                        
                        if (!$_FILES['uploadedfile']['error'])
                        {

                            if ($this->checkSupportedFormat($document['document_file_name'], $document['document_file_type']))
                            {
                                $oFile->load('uploadedfile', array("doc", "docx", "ppt", "pptx", "pps", "xls", "xlsx", "pdf", "ps", "odt", "odp", "sxw", "sxi", "txt", "rtf"), null);
                                $sFileName = $oFile->upload('uploadedfile', 
                                    Phpfox::getParam('core.dir_file') . 'document' . PHPFOX_DS, 
                                    md5(time() . $_FILES['uploadedfile']['name']),
                                    true
                                );
                                $sFileName = sprintf($sFileName, '');
                                $target_path = $server_path . $sFileName;
                                $document['document_file_path'] = str_replace($server_path, '', $target_path);
                                // if (is_uploaded_file($_FILES['uploadedfile']['tmp_name']) && move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
                                if ($sFileName)
                                {
                                    if (Phpfox::getParam('core.allow_cdn'))
                                    {
                                        Phpfox::getLib('cdn')->put($target_path);
                                    }
                                    
                                    Phpfox::getService('document.process')->initScribd(Phpfox::getParam('document.api_key'), Phpfox::getUserId());
                                    $url = Phpfox::getParam('core.url_file') . 'document/' . str_replace('\\', '/', $document['document_file_path']);
                                    $doc_type = null;
                                    $visibility = isset($aVals['visibility']) ? $aVals['visibility'] : Phpfox::getParam('document.document_access', 1);
                                    if ($visibility)
                                    {
                                        $access = "public";
                                    }
                                    else
                                    {
                                        $access = "private";
                                    }

                                    if (Phpfox::getParam('document.api_viewer')) {

                                        $oResult = Phpfox::getService('document.process')->uploadFromUrl($url, $doc_type, $access);

                                        if (count($oResult)) {
                                            $document['doc_id'] = $oResult['doc_id'];
                                            $document['access_key'] = $oResult['access_key'];
                                            $document['image_url'] = $oResult['thumbnail_url'];
                                        }
                                    }else
                                    {
                                        // use google viewer not Scribd
                                        $document['doc_id'] = 0;
                                        $document['access_key'] = '';
                                        $document['image_url'] = '';
                                    }
                                    if(isset($sModuleId) && $sModuleId != "")
                                    {
                                        $document['module_id'] =  $sModuleId;
                                    }
                                    else{
                                        $document['module_id'] = (isset($sModule) && $sModule != "") ? $sModule : 'document';
                                    }
                                    $document['item_id'] = (isset($iItem) && $iItem) ? $iItem : 0;
                                    $document['visibility'] = $visibility;
                                    $document['title'] = Phpfox::getLib('parse.input')->clean(strip_tags($aVals['title']), 255);
                                    $document['text'] = $aVals['text'];
                                    $document['category'] = $aVals['category'];

                                    $document['tag_list'] = isset($aVals['tag_list']) ? $aVals['tag_list'] : "";

                                    $document['privacy'] = isset($aVals['privacy']) ? $aVals['privacy'] : 0;
                                    $document['privacy_list'] = isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array();
                                    $document['document_privacy'] = isset($aVals['document_privacy']) ? $aVals['document_privacy'] : 0;
                                    $document['document_license'] = $aVals['document_license'];
                                    $document['allow_comment'] = isset($aVals['allow_comment']) ? $aVals['allow_comment'] : 0;
                                    $document['privacy_comment'] = isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0;
                                    $document['allow_rating'] = isset($aVals['allow_rating']) ? $aVals['allow_rating'] : 1;
                                    $document['allow_download'] = isset($aVals['allow_download']) ? $aVals['allow_download'] : 0;
                                    $document['allow_attach'] = isset($aVals['allow_attach']) ? $aVals['allow_attach'] : 1;
                                    $document['view_id'] = 0;
                                    $document['module_id'] = $sModule;
                                    if(isset($sModuleId) && $sModuleId != "")
                                    {
                                        $document['module_id'] =  $sModuleId;
                                    }
                                    else{
                                        $document['module_id'] = $sModule;
                                    }

                                    $document['temp_file'] = $aVals['temp_file'];
                                    $iDocumentId = Phpfox::getService('document.process')->addDocument($document);

                                    if ($iDocumentId) {
                                        $aDocument = Phpfox::getService('document.process')->getDocumentById($iDocumentId);
                                        // $this->url()->send(Phpfox::getUserBy('user_name'), array('document' , $sDocument), _p('your_document_has_been_added'));
                                        $this->url()->permalink('document', $aDocument['document_id'], $aDocument['title'], true, _p('your_document_has_been_added'));
                                    }
                                }
                                else
                                {
                                    $file_error_message = _p('there_were_errors_when_uploading_files');
                                }
                            }
                            else
                            {
                                $file_error_message = _p('invalid_file_type');
                            }

                        }
                        elseif ($_FILES['uploadedfile']['error'] == 2)
                        {
                            $file_error_message = _p('the_uploaded_file_exceeds_the_maximum_file_size_size_mbytes', array('size' => $iMaxFileSize / 1048576));
                        }
                    }
                    else
                    {
                        $file_error_message = _p('select_a_document_file_to_upload');
                    }
                }

                // Update the document
                if (isset($aVals['update']))
                {
                    if ($iDocumentId = $this->request()->get('id'))
                    {
                        $aOldDocument = Phpfox::getService('document.process')->getDocumentById($iDocumentId);
                        $visibility = isset($aVals['visibility']) ? $aVals['visibility'] : Phpfox::getParam('document.document_access', 1);

                        $access = ($visibility == true ? "public" : "private");
                        Phpfox::getService('document.process')->initScribd(Phpfox::getParam('document.api_key'), $aOldDocument['user_id']);

                        $document['temp_file'] = !empty($aVals['temp_file']) ? $aVals['temp_file'] : '';
                        $document['remove_photo'] = !empty($aVals['remove_photo']) ? $aVals['remove_photo'] : '';
                        $document['document_id'] = $iDocumentId;
                        $document['visibility'] = $visibility;
                        $document['title'] = Phpfox::getLib('parse.input')->clean(strip_tags($aVals['title']), 255);
                        $document['text'] = $aVals['text'];
                        $document['category'] = $aVals['category'];
                        $document['tag_list'] = isset($aVals['tag_list'])?$aVals['tag_list']:"";
                        $document['privacy'] = isset($aVals['privacy']) ? $aVals['privacy'] : 0;
                        $document['privacy_list'] = isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array();
                        $document['document_privacy'] = isset($aVals['document_privacy']) ? $aVals['document_privacy'] : 0;
                        $document['document_license'] = $aVals['document_license'];
                        $document['allow_comment'] = isset($aVals['allow_comment']) ? $aVals['allow_comment'] : 0;
                        $document['privacy_comment'] = isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0;
                        $document['allow_rating'] = isset($aVals['allow_rating']) ? $aVals['allow_rating'] : 1;
                        $document['allow_download'] = isset($aVals['allow_download']) ? $aVals['allow_download'] : $aRow['allow_download'];
                        $document['allow_attach'] = isset($aVals['allow_attach']) ? $aVals['allow_attach'] : 1;
                        $document['user_id'] = $aOldDocument['user_id'];

                        if (Phpfox::getService('document.process')->updateDocument($document)) {
                            $aDocument = Phpfox::getService('document.process')->getDocumentById($document['document_id']);
                            $this->url()->permalink('document', $aDocument['document_id'], $aDocument['title'], true, _p('your_document_has_been_updated'));
                        }
                    }
                }
            }
            else{
            }
        }
        //for upload Document file
        if ($bIsEdit)
        {
            $this->template()->setTitle(_p('edit_document_link_title'))
                ->setBreadcrumb(_p('documents'), ($aCallback === false ? $this->url()->makeUrl('document') : $aCallback['url_home_photo']))
                ->setBreadcrumb(_p('edit_document_link_title'), ($aCallback === false ? $this->url()->makeUrl('document.add', array('id' => $aRow['document_id'])) : $this->url()->makeUrl('document.add', array('module' => $sModule, 'item' => $iItem, 'id' => $aRow['document_id']))), true);
        }
        elseif ($sModuleId == 'groups'){
                $this->template()->setTitle(_p('add_document_link_title'))
                    ->setBreadcrumb($aCallback['module_title'],$aCallback['title'], _p('documents'))
            ->setBreadcrumb(_p('add_document_link_title'), ($aCallback === false ? $this->url()->makeUrl('document.add') : $this->url()->makeUrl('document.add', array('module' => $sModule, 'item' => $iItem))), true);
            Phpfox_Error::reset();
        }
        else
        {
            $this->template()->setTitle(_p('add_document_link_title'))
                ->setBreadcrumb(_p('documents'), ($aCallback === false ? $this->url()->makeUrl('document') : $aCallback['url_home_photo']))
                ->setBreadcrumb(_p('add_document_link_title'), ($aCallback === false ? $this->url()->makeUrl('document.add') : $this->url()->makeUrl('document.add', array('module' => $sModule, 'item' => $iItem))), true);
        }
        $back = $this->request()->get('back');
        $back_link = "";
        if (isset($back) && $back == 'admincp')
        {
            $back_link = $this->url()->makeUrl('admincp.document.manage');
            $back_admincp = true;
        }
        else
        {
            $back_admincp = false;
        }

        $this->template()->assign(array(
            'max_upload_size_photos' => Phpfox::getUserParam('document.document_max_image_size'),
            'sCreateJs' => $oValid->createJS(),
            'sGetJsForm' => $oValid->getJsForm(),
            'bIsEdit' => $bIsEdit,
            'bCanEditPersonalData' => $bCanEditPersonalData,
            'sCategories' => Phpfox::getService('document.category')->get(),
            'max_file_size' => $iMaxFileSize,
            'max_file_size_mb' => $iMaxFileSize / 1048576,
            'bUseScribdViewer' => Phpfox::getParam('document.api_viewer'),
            'document_access_show' => Phpfox::getParam('document.document_access_show'),
            'category_error_message' => $category_error_message,
            'file_error_message' => $file_error_message,
            'image_error_message' =>$image_error_message,
            'license_list' => Phpfox::getService('document.license.process')->get(),
            'sModule' => $sModule,
            'back_admincp' => $back_admincp,
            'back_link' => $back_link))->setEditor(array('wysiwyg' => true))->setHeader(array(
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'switch_legend.js' => 'static_script',
            'switch_menu.js' => 'static_script',
            'quick_edit.js' => 'static_script',
            'pager.css' => 'style_css',
            'document.js' => 'module_document'))->setPhrase(array(
            'document.fill_title_for_document',
            'document.add_description_to_document',
            'document.choose_category_for_your_document'));
        /*
        $this->setParam('attachment_share', array(
        'type' => 'document',
        'id' => 'core_js_document_form'
        ));
        */

        $aFilterMenu = array();
        $iMyDocumentTotal = Phpfox::getService('document')->getMyDocumentsTotal();
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $aFilterMenu = array(_p('all_documents_link_title') => '', _p('my_documents_link_title').'<span class="my count-item">' . ($iMyDocumentTotal > 99 ? '99+' : $iMyDocumentTotal) . '</span>' => 'my');

            if (!Phpfox::getParam('core.friends_only_community'))
            {
                $aFilterMenu[_p('friend_documents')] = 'friend';
            }

            if (Phpfox::getUserParam('document.can_approve_documents'))
            {
                $iPendingTotal = Phpfox::getService('document')->getPendingTotal();

                if ($iPendingTotal)
                {
                    $aFilterMenu[_p('pending') .
                    (Phpfox::getUserParam('document.can_approve_documents') ? ' <span class="pending count-item"> ' . $iPendingTotal . ' </span>' : 0)] = 'pending';
                }
            }
        }

        $this->template()->buildSectionMenu('document', $aFilterMenu);
        if($image_error_message == 1) {
            return false;
        }
    }
    private function showCategory($channelId)
    {
        $script = "";
        $showScript = "";
        $categories = Phpfox::getService('document.category')->getCategory($channelId);
        if (count($categories))
        {
            foreach ($categories as $category)
            {
                $showScript .= '$("#js_mp_holder_' . $category['category_id'] . '").show();
                                $("#js_mp_category_item_' . $category['category_id'] . '").attr("selected",true);
                                ';
            }
        }

        $script = '  
                <script type="text/javascript">
                    Behavior.showCategory = function()
                    {  ' . $showScript . '
                    }
                </script> ';
        return $script;
    }
    private function checkFileType($sFileName)
    {
        $aFile = explode('.', $sFileName);
        if (count($aFile))
        {
            $sFileType = strtolower($aFile[(count($aFile) - 1)]);
            $aTyleList = array("doc", "docx", "ppt", "pptx", "pps", "xls", "xlsx", "pdf", "ps", "odt", "odp", "sxw", "sxi", "txt", "rtf");
            if (in_array($sFileType, $aTyleList))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    private function checkSupportedFormat($sFileName, $sFormat, $isChangedFormat = false)
    {
        return $this->checkFileType($sFileName);

    }
}

?>
