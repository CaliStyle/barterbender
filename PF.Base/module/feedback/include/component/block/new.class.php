<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FeedBack_Component_Block_New extends Phpfox_Component
{

	public function process()
	{
		$this->template()->assign(array(
				'aFeedbacks' => Phpfox::getService('feedback')->getNew()
			)
		);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('feedback.component_block_new_clean')) ? eval($sPlugin) : false);
	}
}

?>
