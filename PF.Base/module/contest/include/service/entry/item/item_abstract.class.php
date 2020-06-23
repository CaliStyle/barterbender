<?php

defined('PHPFOX') or exit('NO DICE!');

interface Contest_Service_Entry_Item_Item_Abstract {
	public function getAddNewItemLink($iContestId, $iSourceId = 1);

	public function getItemsOfCurrentUser($iLimit = 5, $iPage = 0, $iSourceId = 2);

	public function getItemFromFox($iItemId);

	public function getTemplateViewPath();

	public function getDataToInsertIntoEntry($iItemId, $iSourceId = 2);

	public function getDataFromFoxAdaptedWithContestEntryData($iItemId, $iSourceId = 2);

}