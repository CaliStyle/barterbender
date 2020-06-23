<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FoxFeedsPro_Component_Block_Advanced_Categories_Block extends Phpfox_Component
{
	public function process()
	{
		//hide it when not using advanced category
		if(!Phpfox::getParam('foxfeedspro.is_using_advanced_category'))
		{
			return false;
		}
		$sCategory = $this->getParam('sCategory');
		$bIsProfile = false;
		$bIsProfile = $this->getParam('bIsProfile');

		if($bIsProfile) {
			return false;
		}
		
		$html = Phpfox::getService('foxfeedspro.multicat')->toHtml(0);
		
		$this->template()->assign(array(
				'bIsProfile' => $bIsProfile,
				'html'=>$html,
				'sHeader' => _p('foxfeedspro.categories'),
				'core_url'	=> Phpfox::getParam('core.path')
		));
		
		return 'block';
		
	}
	
	private function updateCategoryLevels($aCategories, $level = 0) {
		if(empty($aCategories))
		{
			return false;
		}
		foreach($aCategories as $key=>$aCategory) {
			$sLIClass = "";
			$aCategories[$key]["level"] = $level;
			if(!empty($aCategory["children"])) {
				$this->updateCategoryLevels($aCategory["children"], $level + 1);
			}
		}
	}
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_block_category_clean')) ? eval($sPlugin) : false);
	}

}

?>