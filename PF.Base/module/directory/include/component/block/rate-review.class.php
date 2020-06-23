<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Rate_Review extends Phpfox_Component
{
	public function process()
	{
		$bCanRate = Phpfox::getUserParam('directory.can_rate_business');
		if(!$bCanRate)
		{
			echo _p('directory.you_cannot_review_this_business');
			exit();
		}

		$iBusinessId = $this->getParam('iBusinessId');
		$aBusiness = phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		if(empty($aBusiness))
		{
			return false;
		}

		if(phpfox::getUserId() == $aBusiness['user_id'])
		{
			echo _p('directory.you_can_not_review_your_own_business');
			exit();
		}
		$aReview = phpfox::getService('directory')->getExistingReview($iBusinessId, phpfox::getUserId());
		
		if(!empty($aReview))
		{

			$bCanEditRate = Phpfox::getUserParam('directory.can_edit_own_review');
			if(!$bCanEditRate)
			{
				echo _p('directory.you_cannot_edit_review_this_business');
				exit();
			}


			$total_score = (int)$aReview['rating']/2;
			$aReview['total_score_text'] = '';
			for ($i=1; $i <=$total_score ; $i++) {
				$aReview['total_score_text'] .= '<div class="star-rating js_rating_star star-rating-applied star-rating-live star-rating-hover" ><a title=""></a></div>'; 
			}
			for ($i=1; $i <= (5-$total_score); $i++) {
				$aReview['total_score_text'] .= '<div class="star-rating js_rating_star star-rating-applied star-rating-live" ><a title=""></a></div>'; 
			}

			$this->template()->assign(array(
										'aOldReview' => $aReview,
									));
		}

		$core_url = phpfox::getParam('core.path');
		$aRatingCallback = array(
			'type' => 'rating',
			'default_rating' => 0,
			'item_id' => $iBusinessId,
			'stars' => array(
				'2' => 2,
				'4' => 4,
				'6' => 6,
				'8' => 8,
				'10' => 10,
			)
		); 
	
		$aStars = array();
		foreach ($aRatingCallback['stars'] as $iKey => $mStar)
		{
			if (is_numeric($mStar))
			{
				$aStars[$mStar] = $mStar;
			}
			else 
			{
				$aStars[$iKey] = $mStar;
			}
		}		
		
		$aRatingCallback['stars'] = $aStars;
		
		$this->template()
			->setPhrase(array(
				"directory.rating",
			))
			->assign(array(
				'aRatingCallback' => $aRatingCallback,
				'core_url' => $core_url,
				'item_id' => $iBusinessId,
				'bCanRate' => $bCanRate
			));
	}
}
?>
