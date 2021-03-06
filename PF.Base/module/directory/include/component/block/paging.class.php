<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_paging extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$aParams = $this->getParam('aPagingParams');
		$iTotalAll = $aParams['total_all_result'];
		$iTotal = $aParams['total_result'];
		$iPage = $aParams['page'];
		$iLimit = $aParams['limit'];
		$bNoResultText = isset($aParams['bNoResultText']) ? $aParams['bNoResultText'] : false;

		if($iTotalAll <= 0) {
			return false;
		}
		$iStartOffset = ($iPage -1) * $iLimit + 1;
		$iEndOffset = $iStartOffset + $iTotal - 1;
		$iHardEndOffset = $iStartOffset + $iLimit - 1;// offset in case total filled up pages

		$bHavingPrevious = $iPage <= 1 ? false : true;
		$bHavingNext = ($iHardEndOffset >= $iTotalAll) ? false : true;

		$sResultPhrase = _p('directory.start_end_of_total_result_s', array(
			'start' => $iStartOffset,
			'end' => $iEndOffset,
			'total' => $iTotalAll 
		));

		$this->template()->assign(array( 
			'iPreviousPage' => $iPage - 1,
			'iNextPage' => $iPage + 1 ,
			'sResultPhrase' => $sResultPhrase,
			'bHavingPrevious' => $bHavingPrevious,
			'bHavingNext' => $bHavingNext,
			'bNoResultText' => $bNoResultText
		));		
	}

}

?>
