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
class FeedBack_Component_Block_Search extends Phpfox_Component
{
	public function process()
	{
		$this->template()->assign(array(
				'sHeader' => _p('feedback.search_filter')
		)
		);
		//return false;
		return 'block';
	}

}

?>