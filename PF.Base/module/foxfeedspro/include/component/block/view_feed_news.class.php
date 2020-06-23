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
	class FoxFeedsPro_Component_Block_View_Feed_News extends Phpfox_Component
	{
		public function process()
		{
			$iFeedId = $this->request()->get('feed_id');
	
			$aNews = phpfox::getService('foxfeedspro')->getNewsByFeedId($iFeedId);
			$this->template()->assign(array(
									'aNews'=>$aNews));
			return 'block';
		}
		
	} 
?>