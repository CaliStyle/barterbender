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

class Document_Component_Controller_Frame extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('document.can_view_documents', true);
        if (Phpfox::getParam('document.api_key') == "" || Phpfox::getParam('document.secret_key') == "")
        {
            echo '<script type="text/javascript">';
            echo 'window.parent.$Core.resetActivityFeedError("' . _p('please_set_up_scribd_account') . '");';
            echo '</script>';
            exit();
        }

        $sModule = "document";
        $server_path = Phpfox::getParam('core.dir_file') . 'document' . PHPFOX_DS;
        $file_type = Phpfox::getService('document.process')->getFileType($_FILES['uploadedfile']['name']);
        $target_path = Phpfox::getLib('file')->getBuiltDir($server_path) . md5(time() . $_FILES['uploadedfile']['name']) . '.' . $file_type;
        $document = array();
        
        if ($aVals = $this->request()->getArray('val'))
        {
            if (!empty($aVals['document_title']))
            {
                if (($_FILES['uploadedfile']['name'] != ""))
                {
                    $document['document_file_name'] = $_FILES['uploadedfile']['name'];
                    $document['document_file_type'] = $_FILES['uploadedfile']['type'];
                    $document['document_file_path'] = str_replace($server_path, '', $target_path);

                    if (!$_FILES['uploadedfile']['error'])
                    {
                        $this->checkFileType($_FILES['uploadedfile']['name']);
                        $this->checkFileSize($_FILES['uploadedfile']['size']);

                        if (is_uploaded_file($_FILES['uploadedfile']['tmp_name']) && move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
                        {
                            if (Phpfox::getParam('core.allow_cdn'))
                            {
                                Phpfox::getLib('cdn')->put($target_path);
                            }
                            
                            Phpfox::getService('document.process')->initScribd(Phpfox::getParam('document.api_key'), Phpfox::getUserId());
                            $url = Phpfox::getParam('core.url_file') . 'document/' . str_replace('\\', '/', $document['document_file_path']);
                            $doc_type = null;
                            $visibility = isset($aVals['visibility']) ? $aVals['visibility'] : Phpfox::getParam('document.document_access');
                            $access = $visibility ? 'public' : 'private';
                            
                            $oResult = Phpfox::getService('document.process')->uploadFromUrl($url, $doc_type, $access);
                            if (count($oResult))
                            {
                                $document['doc_id'] = $oResult['doc_id'];
                                $document['access_key'] = $oResult['access_key'];
                                $document['image_url'] = $oResult['thumbnail_url'];
                                $document['module_id'] = (isset($sModule) && $sModule != "") ? $sModule : 'document';
                                $document['item_id'] = (isset($iItem) && $iItem) ? $iItem : 0;
                                $document['visibility'] = $visibility;
                                $document['title'] = Phpfox::getLib('parse.input')->clean($aVals['document_title'], 255);
                                $document['text'] = $aVals['status_info']; // Get the status info for text
                                $document['category'] = $aVals['category'];
                                $document['tag_list'] = "";
                                $document['privacy'] = isset($aVals['privacy']) ? $aVals['privacy'] : 0;
                                $document['document_privacy'] = isset($aVals['document_privacy']) ? $aVals['document_privacy'] : 0;
                                $document['document_license'] = 0; // None-licensed
                                $document['allow_comment'] = isset($aVals['allow_comment']) ? $aVals['allow_comment'] : 0;
                                $document['privacy_comment'] = isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : 0;
                                $document['allow_rating'] = isset($aVals['allow_rating']) ? $aVals['allow_rating'] : 1;
                                $document['allow_download'] = isset($aVals['allow_download']) ? $aVals['allow_download'] : 0;
                                $document['allow_attach'] = isset($aVals['allow_attach']) ? $aVals['allow_attach'] : 1;
                                $document['view_id'] = 0;
                                $document['module'] = Phpfox::isAdmin(false) ? 'profile' : $sModule;
                            }

                            $iDocumentId = Phpfox::getService('document.process')->addDocument($document);
                            $iFeedId = Phpfox::getLib('database')->select('feed_id')->from(Phpfox::getT('feed'))->where("item_id = $iDocumentId AND type_id = 'document'")->execute("getField");
                            if ($iFeedId)
                            {
                                echo '<script type="text/javascript">';
                                echo 'window.parent.$(".js_mp_category_list").val(0);';
								echo 'window.parent.$(".js_mp_category_list").change();';
                                echo 'window.parent.$.ajaxCall("document.displayFeed", "id=' . $iFeedId . '&document_id='.$iDocumentId.'", "GET"); window.parent.$(".js_no_feed_to_show").hide();';
                                echo '</script>';
                            }
                            else
                            {
                                echo '<script type="text/javascript">';
                                echo 'window.parent.$(".js_mp_category_list").val(0);';
								echo 'window.parent.$(".js_mp_category_list").change();';
                                echo 'window.parent.$.ajaxCall("document.displayFeed", "id=0&document_id='.$iDocumentId.'", "GET");';
                                echo '</script>';
                            }
                        }
                    }
                }
            }
        }
    }

    private function checkFileType($sFileName)
    {
        $aFile = explode('.', $sFileName);
        if (count($aFile))
        {
            $sFileType = strtolower($aFile[(count($aFile) - 1)]);
            $aTyleList = array("doc", "docx", "ppt", "pptx", "pps", "xls", "xlsx", "pdf", "ps", "odt", "odp", "sxw", "sxi", "txt", "rtf");
            if (!in_array($sFileType, $aTyleList))
            {
                echo '<script type="text/javascript">';
                echo 'window.parent.$Core.resetActivityFeedError("' . _p('document_file_type_is_not_valid') . '");';
                echo '</script>';
                exit();
            }
        }
    }

    private function checkFileSize($iFileSize)
    {
        $iMaxFileSize = (Phpfox::getUserParam('document.document_max_file_size') === 0 ? (200 * 1048576) : (Phpfox::getUserParam('document.document_max_file_size') * 1048576));
        if ($iFileSize > $iMaxFileSize)
        {
            echo '<script type="text/javascript">';
            echo 'window.parent.$Core.resetActivityFeedError("' . _p('the_uploaded_file_exceeds_the_maximum_file_size_size_mbytes', array('size' => $iMaxFileSize / 1048576)) . '");';
            echo '</script>';
            exit();
        }
    }
}

?>