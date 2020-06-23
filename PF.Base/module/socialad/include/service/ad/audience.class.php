<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_Ad_Audience extends Phpfox_Service
{
    public function __construct()
    {
		$this->_sLocationTable = Phpfox::getT('socialad_ad_audience_location');
		

    }

	public function getAffectedAudience($aQuery) {
		$aConds = array(
		    " profile_page_id = 0 "
        );
		if(isset($aQuery['audience_location']) && $aQuery['audience_location']) {
			$sLocation = implode("', '", $aQuery['audience_location']); // it is tricky to get IN ('AE','VN')
			$aConds[] = " country_iso IN ('{$sLocation}') ";
		}

		if(isset($aQuery['audience_gender']) && $aQuery['audience_gender']) {
			$aConds[] = " gender = " . $aQuery['audience_gender'];
		}

		if(isset($aQuery['audience_age_min']) && $aQuery['audience_age_min']) {
			$iMinTS = intval(PHPFOX_TIME) - (intval($aQuery['audience_age_min']) * 365 * 24 * 60 * 60);
			$aConds[] = " birthday_search <= " .  $iMinTS;
		}

		if(isset($aQuery['audience_age_max']) && $aQuery['audience_age_max']) {
			$iMaxTS = intval(PHPFOX_TIME) - (intval($aQuery['audience_age_max']) * 365 * 24 * 60 * 60);
			$aConds[] = " birthday_search >= " .  $iMaxTS;
		}
		$sCond = implode(" AND ", $aConds);

		$iCnt = $this->database()->select("COUNT(user_id)")
			->from(Phpfox::getT('user'))
			->where($sCond)
			->execute("getSlaveField");

		return $iCnt;

	}

	public function getLocationsOfAd($iAdId) {
		$aRows = $this->database()->select("location_id")
			->from($this->_sLocationTable)
			->where(" ad_id = " . $iAdId)
			->execute("getRows");

		$aResult = array();
		if($aRows) {
			foreach($aRows as $aRow) {
				$aResult[] = $aRow["location_id"];
			}
		}

		return $aResult;

	}

	public function addLocation($iAdId, $aLocations) {
		$this->database()->delete($this->_sLocationTable, 'ad_id = ' . $iAdId);
		foreach($aLocations as $sLocation) {
			$aLocationInsert = array( 
				'ad_id' => $iAdId,
				'location_id' => $sLocation,
				'child_id' => 0
			);

			$this->database()->insert($this->_sLocationTable, $aLocationInsert);
		}
	}

	public function getAgeCond($iAge) {
		// implicit assumption is alias of ad table is "ad" 
		return '( ad.audience_age_min <=  ' . $iAge . 
			' AND ad.audience_age_max >= ' . $iAge .')' ; // 0 means any 

	}

	public function getGenderCond($iGender) {

		// implicit assumption is alias of ad table is "ad" 
		return '( ad.audience_gender = ' . $iGender . 
			' OR ad.audience_gender = 0 )' ; // 0 means any 
	}

	public function getLocationCond($sLocation) {
		$this->database()->leftJoin(Phpfox::getT('socialad_ad_audience_location'), 'adal', 'adal.ad_id = ad.ad_id');

		return "( adal.location_id = '" . $sLocation  . "' OR adal.location_id IS NULL )" ;
	}

	public function getLanguageCond($sLang) {
		$this->database()->leftJoin(Phpfox::getT('socialad_ad_audience_language'), 'adlan', 'adlan.ad_id = ad.ad_id');

		return "( adlan.language_id = '" . $sLang  . "' OR adlan.language_id IS NULL )" ;
	}

	public function getUserGroupCond($iUserGroupId) {
		$this->database()->leftJoin(Phpfox::getT('socialad_ad_audience_user_group'), 'adug', 'adug.ad_id = ad.ad_id');

		return "( adug.user_group_id = '" . $iUserGroupId  . "' OR adug.user_group_id IS NULL )" ;
	}
	/**
	 * @params $iUserId id of audience
	 * @return string it is a string of condition in where clause
	 */
	public function getAudienceConds($iUserId) {
		$iGender = Phpfox::getService('socialad.user')->getUserBy('gender', $iUserId);
		$iAge = Phpfox::getService('socialad.user')->getUserBy('age', $iUserId);
		$sLocation = Phpfox::getService('socialad.user')->getUserBy('location', $iUserId);
		$sLang = Phpfox::getService('socialad.user')->getUserBy('language', $iUserId);
		$iUserGroupId = Phpfox::getService('socialad.user')->getUserBy('user_group_id', $iUserId);

		$aConds = array();
		if($iGender) {
			$aConds[] = $this->getGenderCond($iGender);
		}

		if($iAge) {
			$aConds[] = $this->getAgeCond($iAge);
		}

		if($sLocation) {
			$aConds[] = $this->getLocationCond($sLocation);
		}

		if($sLang) {
			$aConds[] = $this->getLanguageCond($sLang);
		}

		if($iUserGroupId) {
			$aConds[] = $this->getUserGroupCond($iUserGroupId);
		}
		// some time a getConds function return false, so we need to remove some empty element

		return implode(' AND ', $aConds);

	}

	public function add($aVals) {

	}

}



