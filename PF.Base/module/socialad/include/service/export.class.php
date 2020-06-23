<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

include dirname(__FILE__) . "/lib/PHPExcel/PHPExcel.php";

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_Export extends Phpfox_Service {
	public function export($aData, $sType) {
		$sFilePath = $this->generateFileFromData($aData, $sType);

		$iServerId  = 0;
		$sExt = $sType;
		$sDownloadName = 'report_' .date('m_d_y_G_i_s', PHPFOX_TIME) . '.' . $sExt;
		Phpfox::getlib('phpfox.file')->forceDownload($sFilePath, $sDownloadName, '', '', $iServerId);  	
	}		

	public function generateFileFromData($aData, $sType) {
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->setActiveSheetIndex(0);

		$aSample = array(
			'A' =>_p('start_date'),
			'B' => _p('end_date'),
			'C' => _p('ad'),
			'D' => _p('campaign'),
			'E' => _p('reach'),
			'F' => _p('impressions'),
			'G' => _p('clicks'),
			'H' => _p('unique_clicks'),
		);

		$iRowNum = 1; 

		$sColumn = 'A';
		foreach($aSample as $sKey => $sValue) {
			$objPHPExcel->getActiveSheet()->SetCellValue($sKey . $iRowNum, $sValue);
			$sColumn++;
		}
		$iRowNum++;
		// //generate 
		// $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
		// $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
		// $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
		// $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');
		foreach($aData as $aRow) {
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $iRowNum, $aRow['start_date_text']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $iRowNum, $aRow['end_date_text']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $iRowNum, html_entity_decode($aRow['ad_title']));
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $iRowNum, html_entity_decode($aRow['campaign_name']));
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $iRowNum, $aRow['total_reach']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $iRowNum, $aRow['total_impression']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $iRowNum, $aRow['total_click']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $iRowNum, $aRow['total_unique_click']);
			$iRowNum++;
		}

		$sExt = $sType;
		switch($sType) {
		case 'xls':
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			break;
		case 'csv':
			$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
			break;
		}

		$sFileName = 'ad_report_'. PHPFOX_TIME . '.' . $sExt;
		$sUploadPath = PHPFOX_DIR_CACHE ;
		$sFilePath  = $sUploadPath . $sFileName;
		$objWriter->save($sFilePath);

		return $sFilePath;
	}

}



