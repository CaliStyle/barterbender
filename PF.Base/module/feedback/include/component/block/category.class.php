<?php
class FeedBack_Component_Block_Category extends Phpfox_Component
{
	public function process()
	{
		$bIsProfile = false;
		if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser')))
		{
			$bIsProfile = true;
		}
		$aCategories = phpfox::getService('feedback')->getAllFeedBackCats();
		foreach ($aCategories as $iKey => $aCategory)
		{
            $aCategories[$iKey]['url'] = Phpfox::permalink('feedback.category', $aCategory['category_id'], $this->request()->get('view'),  ((\Core\Lib::phrase()->isPhrase($aCategory['name'])) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name'])));
            $aCategories[$iKey]['name'] = ((\Core\Lib::phrase()->isPhrase($aCategory['name'])) ? _p($aCategory['name']) : Phpfox::getLib('locale')->convert($aCategory['name']));
        }
		$iCategoryBlogView = 0;
		if($this->request()->get('req2') == 'category')
		{
			$iCategoryBlogView = $this->request()->getInt('req3');
		}
	
		$this->template()->assign(array(
				'sHeader' => _p('feedback.categories'),
				'aCategories' => $aCategories,
				'iCategoryBlogView' => $iCategoryBlogView
			)
		);	
		return 'block';
	}
}
?>