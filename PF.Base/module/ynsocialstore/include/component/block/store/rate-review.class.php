<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Rate_Review extends Phpfox_Component
{
	public function process()
	{
		$bIsEdit = false;
		$iStoreId = $this->getParam('iStoreId');
		$aStore = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId);
		if(empty($aStore))
		{
			return false;
		}

		if(phpfox::getUserId() == $aStore['user_id'])
		{
			return	Phpfox_Error::set(_p('Cannot review your own store'));
		}
		$aReview = phpfox::getService('ynsocialstore.reviews')->getExistingReview($iStoreId, phpfox::getUserId());
		$iRating = 0;
		if(!empty($aReview))
		{
			$total_score = (int)$aReview['rating'];
			$aReview['total_score_text'] = '';
			for ($i=1; $i <=$total_score ; $i++) {
				$aReview['total_score_text'] .= '<div class="star-rating js_rating_star star-rating-applied star-rating-live star-rating-hover" ><a title=""></a></div>'; 
			}
			for ($i=1; $i <= (5-$total_score); $i++) {
				$aReview['total_score_text'] .= '<div class="star-rating js_rating_star star-rating-applied star-rating-live" ><a title=""></a></div>'; 
			}
			$iRating = $aReview['rating'];

		}
		$value =  floor($iRating+0.4999);
		$result = [];
		for($i =1; $i<=5; ++$i){
			$edit =  ' data-id="'.$iStoreId.'" data-value="'.$i.'"';
			$result[] =  $i <= $value?'<i class="ico ico-star yn-star yn-rating" '.$edit.'></i>':'<i class="ico ico-star yn-star yn-rating-disable" '.$edit.'></i>';
		}

		$sResult = implode('', $result);
		$core_url = phpfox::getParam('core.path');
		$this->template()
			->setPhrase(array(
				"ynsocialstore.rating",
			))
			->assign(array(
				'aReview' => $aReview,
				'core_url' => $core_url,
				'item_id' => $iStoreId,
				'sResult' => $sResult,
				'aStore' => $aStore,
			));
	}
}
?>
