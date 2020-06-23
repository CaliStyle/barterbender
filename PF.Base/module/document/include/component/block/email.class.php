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
class Document_Component_Block_Email extends Phpfox_Component
{
    public function process()
    {
        $iDocumentId = $this->getParam('id');
        $aDocument = Phpfox::getService('document.process')->getDocumentById($iDocumentId);
        $sText = _p('hi_check_this_out_url', array(
            'url' => Phpfox::permalink('document', $aDocument['document_id'], $aDocument['title']),
            'full_name' => Phpfox::getUserBy('full_name'),
            'user_name' => Phpfox::getUserBy('user_name'),
            'email' => Phpfox::getUserBy('email'),
            'user_id' => Phpfox::getUserBy('user_id')
        ));
        if (count($aDocument))
        {
            $this->template()->assign(array(
                'bCanSendEmails' => true,
                'sTitle' => $aDocument['title'],
                'sMessage' => $sText,
                'allow_download' => ($aDocument['user_id'] == Phpfox::getUserId()) ? 1 : $aDocument['allow_download'],
                'download_link' => Phpfox::getLib('url')->makeUrl('document.download', array('id_'.$aDocument['document_id'])),
                'sFileName' => $aDocument['document_file_name'],
                'iId' => $iDocumentId
            ));
        }
        else
        {
            $this->template()->assign(array(
                'error_message' => _p('invalid_document'),
                'bCanSendEmails' => false
            ));
        }
    }
}

?>
