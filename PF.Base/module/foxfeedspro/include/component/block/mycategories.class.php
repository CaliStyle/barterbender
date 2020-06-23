<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          2.04
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FoxFeedsPro_Component_Block_MyCategories extends Phpfox_Component
{
	public function process()
	{
		$iItemId = $this->getParam('iItemId');
		$this->template()->assign(array(
			'sCategories' => Phpfox::getService('foxfeedspro.category')->getMyCategories(),
			'iItemId'=>$iItemId
			
		));
		return 'block';
	}			
}
?>