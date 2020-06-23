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

class Document_Component_Controller_Admincp_BackupDB extends Phpfox_Component
{
     /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document")));
		echo "\n/*document------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_category")));
		echo "\n/*document_category------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_category_data")));
		echo "\n/*document_category_data------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_embed")));
		echo "\n/*document_embed------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_license")));
		echo "\n/*document_license------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_rating")));
		echo "\n/*document_rating------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_text")));
		echo "\n/*document_text------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("document_track")));
		echo "\n/*document_track------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("user_activity")));
		echo "\n/*document_track------------------------------------------------------------*/\n";
		echo json_encode(PHPFOX::getService("document.process")->getRawTable(PHPFOX::getT("user_field")));
		echo "\n/*document_track------------------------------------------------------------*/\n";
		header("Content-Type: text/plain");
		exit;
    }
}
?>
