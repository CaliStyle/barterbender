<?php    
if(phpfox::isModule('socialpublishers') && Phpfox::getUserParam('document.approve_document_before_display'))
{
	$aDocument = PHpfox::getLib("database")->select('*')
			->from(Phpfox::getT('document'))
			->where('document_id = '.$iDocumentId)
			->execute('getRow');
		$sUrl = Phpfox::permalink('document', $iDocumentId, $aDocument['title']);
		$sType = 'document';
		$iUserId = Phpfox::getUserId();
		$sMessage = $aDocument['title'];
		$aVals['url'] = $sUrl;
		$aVals['content'] = $sMessage;
		$aVals['text'] = $aDocument['title'];
		$aVals['title'] = $aDocument['title'];
		$aShareFeedInsert = array(
	         	'sType' => 'document',
	            'iItemId' => $aDocument['document_id'],
	            'bIsCallback' => false,
	            'aCallback' => null,
	            'iPrivacy' => 0,
	            'iPrivacyComment' => 0,
        	);
			$aShareFeedInsert['title'] = $aVals['title'];

			$sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . Phpfox::getUserId());
            Phpfox::getLib('cache')->save($sIdCache, $aShareFeedInsert);
		phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVals);
}
?>