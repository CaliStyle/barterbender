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
/**
 * advanced my category block
 */
class FoxFeedsPro_Component_Block_My_Category_News extends Phpfox_Component
{
	public function process()
	{
		//hide it when not using advanced category
		if(!Phpfox::getParam('foxfeedspro.is_using_advanced_category'))
		{
			return false;
		}
		if(!Phpfox::getUserId())
			return false;
		$sCategory = $this->getParam('sCategory');
		$bIsProfile = false;
		$bIsProfile = $this->getParam('bIsProfile');

		if($bIsProfile) {
			return false;
		}
		
		$html = Phpfox::getService('foxfeedspro.multicat')->toHtml(Phpfox::getUserId(),null, 1);
		if(empty($html))
		{
			return false;
		}
		$this->template()->assign(array(
				'bIsProfile' => $bIsProfile,
				'html'=>$html,
				'sHeader' => _p('foxfeedspro.my_categories'),
				'sCoreUrl'=> Phpfox::getParam('core.path')
		));
		
		return 'block';
		
		
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