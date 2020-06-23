<?php

class Document_Component_Controller_Download extends Phpfox_Component
{
    public function process()
    {
        if ($iDocument = $this->request()->get('id'))
        {
            $aDocument = Phpfox::getService('document.process')->getDocumentById($iDocument);
            if (count($aDocument))
            {
                $aDocument['download_link'] = Phpfox::getParam('core.dir_file').'document/'.str_replace('\\', '/', $aDocument['document_file_path']);
                Phpfox::getLib('file')->forceDownload($aDocument['download_link'], $aDocument['document_file_name'], '', '-1', $aDocument['doc_server_id']);
                exit;
            }
        }
    }
}

?>
