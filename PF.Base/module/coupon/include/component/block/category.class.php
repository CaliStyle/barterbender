<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Block_Category extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bIsProfile = false;
		if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser')))
		{
		   $bIsProfile = true;
		}
		
		$sView = $this->request()->get('view');
		
		if($sView =='faq')
		{
			return FALSE;
		}
		
		$aCategories = Phpfox::getService('coupon.category')->getForBrowse();
		if (!is_array($aCategories))
		{
			return false;
		}
		
		if (!$aCategories)
		{
			return false;
		}

		foreach ($aCategories as $iKey => $aCategory)
		{
			$aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.coupon.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['title']) : $this->url()->permalink(array('coupon.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['title']));
			if(isset($aCategory['sub']))
			{
				foreach ($aCategories[$iKey]['sub'] as $iSubKey => $aSubCategory)
				{
					  $aCategories[$iKey]['sub'][$iSubKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.coupon.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['title']) : $this->url()->permalink(array('coupon.category', 'view' => $this->request()->get('view')), $aSubCategory['category_id'], $aSubCategory['title']));
				}
			}
			
		}

		
		$this->template()->assign(array(
				'sHeader' => _p('categories'),
				'aCategories' => $aCategories,
				'iCategoryCouponView' => $this->request()->getInt('req3')
			)
		);	

		(($sPlugin = Phpfox_Plugin::get('coupon.component_block_categories_process')) ? eval($sPlugin) : false);
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'aCategories'
			)
		);
	
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_block_categories_clean')) ? eval($sPlugin) : false);
	}	
}

?>
